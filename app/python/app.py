import sys
import os
import json
import re
import pandas as pd
import numpy as np
import joblib
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import SGDClassifier
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler
from sklearn.utils.class_weight import compute_class_weight
from sklearn.model_selection import GridSearchCV
from nltk.stem import WordNetLemmatizer
from nltk.corpus import stopwords
import nltk

nltk.download('wordnet', quiet=True)
nltk.download('stopwords', quiet=True)

# Logging setup
script_dir = os.path.dirname(os.path.abspath(__file__))
log_file = os.path.join(script_dir, 'cyberbullying_debug.log')

def log_debug(message):
    with open(log_file, 'a', encoding='utf-8', errors='replace') as f:
        f.write(f"{message}\n")

log_debug("\n\n--- New Detection Run ---")

# 🚨 Enhanced Threat Word List (Regex-based for flexibility)
THREAT_PATTERNS = [
    r"\bkill you\b", r"\bmurder you\b", r"\bshoot you\b", r"\bstab you\b",
    r"\bi will kill\b", r"\byou should die\b", r"\bhang yourself\b",
    r"\bno one loves you\b", r"\bjump off a bridge\b", r"\bnobody will miss you\b",
    r"\bpapatayin kita\b", r"\bbabarilin kita\b", r"\bsasaksakin kita\b",
    r"\bmagpakamatay ka na\b", r"\bwala kang kwenta\b", r"\bhindi ka mahalaga\b"
]

# List of common positive phrases that should NOT be flagged as bullying
POSITIVE_PHRASES = [
    "ang cute mo", "you're beautiful", "you're nice", "you're amazing",
    "you're kind", "you're smart", "you're wonderful", "ang bait mo",
    "you're the best", "you're awesome", "you're a good person", "ganda",
    "bait", "maganda", "friendly", "kamukha", "ligtas", "gwapo",
    "pogi"
]

lemmatizer = WordNetLemmatizer()
stop_words = set(stopwords.words('english'))

def preprocess_text(text):
    if not isinstance(text, str):
        return ""

    text = text.encode('utf-8', errors='replace').decode('utf-8')
    text = text.lower()

    # 🚨 Check if the text contains any positive phrases
    if any(phrase in text for phrase in POSITIVE_PHRASES):
        log_debug(f"Positive phrase detected and bypassed: {text}")
        return "NON_BULLYING"

    text = re.sub(r'https?://\S+|www\.\S+', '', text)  # Remove links
    text = re.sub(r'[^\w\s]', ' ', text)  # Remove special characters
    text = ' '.join(lemmatizer.lemmatize(word) for word in text.split() if word not in stop_words)  # Lemmatization

    log_debug(f"Preprocessed text: {text}")

    # 🚨 Check for threats with regex matching
    detected_threats = [pattern for pattern in THREAT_PATTERNS if re.search(pattern, text)]
    if detected_threats:
        log_debug(f"Threat detected: {text} → {detected_threats}")
        return "THREAT_DETECTED"

    return text

class CyberbullyingDetector:
    def __init__(self, dataset_path):
        self.df = pd.read_csv(dataset_path)
        self.df.dropna(subset=['text', 'label'], inplace=True)
        self.df['text'] = self.df['text'].astype(str)
        self.df.drop_duplicates(subset=['text'], inplace=True)

        self.X = self.df['text']
        self.y = self.df['label']
        self.X_train, self.X_test, self.y_train, self.y_test = train_test_split(
            self.X, self.y, test_size=0.2, random_state=42
        )

        self.pipeline = Pipeline([
            ('tfidf', TfidfVectorizer(max_features=10000, stop_words='english')),
            ('classifier', SGDClassifier(loss='hinge', max_iter=1000, class_weight='balanced'))
        ])

        self.train_model()

    def train_model(self):
        param_grid = {
            'classifier__alpha': [1e-4, 1e-3, 1e-2],
            'classifier__penalty': ['l2', 'l1']
        }
        grid_search = GridSearchCV(self.pipeline, param_grid, cv=3, scoring='accuracy')
        grid_search.fit(self.X_train, self.y_train)
        self.pipeline = grid_search.best_estimator_
        joblib.dump(self.pipeline, os.path.join(script_dir, 'cyberbullying_model.pkl'))
        log_debug("Model trained with hyperparameter tuning and saved.")

    def load_model(self):
        try:
            self.pipeline = joblib.load(os.path.join(script_dir, 'cyberbullying_model.pkl'))
            log_debug("Model loaded successfully.")
        except Exception as e:
            log_debug(f"Error loading model: {str(e)}")
            sys.exit(1)

    def predict(self, text):
        processed_text = preprocess_text(text)

        if processed_text == "THREAT_DETECTED":
            return {
                "error": None,
                "result": "Cyberbullying Detected",
                "probability": 100  # Force 100% certainty
            }
        elif processed_text == "NON_BULLYING":
            return {
                "error": None,
                "result": "No Cyberbullying Detected",
                "probability": 100  # Force 0% certainty
            }

        prediction = self.pipeline.predict([processed_text])[0]
        probability = self.pipeline.decision_function([processed_text])[0]

        return {
            "error": None,
            "result": "Cyberbullying Detected" if prediction == 1 else "No Cyberbullying Detected",
            "probability": round(1 / (1 + np.exp(-probability)) * 100, 2)  # Convert to probability
        }

if __name__ == "__main__":
    text = ""
    if len(sys.argv) > 1:
        text = sys.argv[1]
    else:
        try:
            text = sys.stdin.read().strip()
        except Exception as e:
            print(json.dumps({"error": f"Error reading input: {str(e)}", "result": None, "probability": None}))
            sys.exit(1)

    if not text:
        print(json.dumps({"error": "No input text provided", "result": None, "probability": None}))
        sys.exit(1)

    dataset_path = os.path.join(script_dir, 'enhanced_cyberbullying_dataset.csv')
    detector = CyberbullyingDetector(dataset_path)
    detector.load_model()
    result = detector.predict(text)
    log_debug(f"Final result: {json.dumps(result)}")
    print(json.dumps(result))
