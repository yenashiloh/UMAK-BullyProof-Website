import sys
import os
import json
import re
import pandas as pd
import numpy as np
import joblib
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import classification_report, confusion_matrix, accuracy_score
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler

# Logging setup
script_dir = os.path.dirname(os.path.abspath(__file__))
log_file = os.path.join(script_dir, 'cyberbullying_debug.log')

def log_debug(message):
    with open(log_file, 'a', encoding='utf-8', errors='replace') as f:
        f.write(f"{message}\n")

log_debug("\n\n--- New Detection Run ---")

# Text Preprocessing
def preprocess_text(text):
    if not isinstance(text, str):
        return ""

    text = text.encode('utf-8', errors='replace').decode('utf-8')
    text = text.lower()
    text = re.sub(r'https?://\S+|www\.\S+', '', text)
    text = re.sub(r'[^\w\s]', ' ', text)
    text = ' '.join(text.split())

    log_debug(f"Preprocessed text: {text}")
    return text

class CyberbullyingDetector:
    def __init__(self, dataset_path):
        # Load dataset
        self.df = pd.read_csv(dataset_path)
        self.df.dropna(subset=['text', 'label'], inplace=True)
        self.df['text'] = self.df['text'].astype(str)
        self.df.drop_duplicates(subset=['text'], inplace=True)

        self.X = self.df['text']
        self.y = self.df['label']
        self.X_train, self.X_test, self.y_train, self.y_test = train_test_split(
            self.X, self.y, test_size=0.2, random_state=42
        )

        # Machine Learning Pipeline
        self.pipeline = Pipeline([
            ('tfidf', TfidfVectorizer(max_features=5000, stop_words='english')),
            ('scaler', StandardScaler(with_mean=False)),
            ('classifier', LogisticRegression(max_iter=1000, class_weight='balanced'))
        ])

        self.train_model()

    def train_model(self):
        self.pipeline.fit(self.X_train, self.y_train)
        joblib.dump(self.pipeline, os.path.join(script_dir, 'cyberbullying_model.pkl'))
        log_debug("Model trained and saved.")

    def load_model(self):
        try:
            self.pipeline = joblib.load(os.path.join(script_dir, 'cyberbullying_model.pkl'))
            log_debug("Model loaded successfully.")
        except Exception as e:
            log_debug(f"Error loading model: {str(e)}")
            sys.exit(1)

    def predict(self, text):
        processed_text = preprocess_text(text)
        prediction = self.pipeline.predict([processed_text])[0]
        probability = self.pipeline.predict_proba([processed_text])[0]

        return {
            "error": None,
            "result": "Cyberbullying Detected" if prediction == 1 else "No Cyberbullying Detected",
            "probability": round(max(probability) * 100, 2)
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
