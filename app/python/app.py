import sys
import joblib
import re
import nltk
import os
from nltk.stem import WordNetLemmatizer
from sklearn.feature_extraction.text import ENGLISH_STOP_WORDS
import json

# Get the directory containing the script
script_dir = os.path.dirname(os.path.abspath(__file__))

# Download required NLTK data
try:
    nltk.download('wordnet', quiet=True)
    nltk.download('stopwords', quiet=True)
except Exception as e:
    print(json.dumps({
        "error": f"Failed to download NLTK data: {str(e)}",
        "result": None,
        "probability": None
    }))
    sys.exit(1)

# Load model and vectorizer
try:
    model_path = os.path.join(script_dir, 'models', 'logistic_regression_model.pkl')
    vectorizer_path = os.path.join(script_dir, 'models', 'tfidf_vectorizer.pkl')
    
    model = joblib.load(model_path)
    vectorizer = joblib.load(vectorizer_path)
except FileNotFoundError as e:
    print(json.dumps({
        "error": f"Model files not found. Path: {model_path}",
        "result": None,
        "probability": None
    }))
    sys.exit(1)
except Exception as e:
    print(json.dumps({
        "error": f"Error loading models: {str(e)}",
        "result": None,
        "probability": None
    }))
    sys.exit(1)

# Define stopwords and keywords
stopwords = set(ENGLISH_STOP_WORDS).union({
    "ako", "ikaw", "siya", "kami", "kayo", "sila", "ito", "iyan", "iyan", "mga", 
    "ng", "sa", "para", "ang", "ngunit", "habang", "dahil", "at", "o", "na", 
    "mula", "tungkol", "upang", "hindi", "lahat", "isa", "ito", "iyon", "pati", 
    "bukod", "tama", "mali", "wala", "may", "di", "naman"
})

lemmatizer = WordNetLemmatizer()

non_cyberbullying_keywords = {
    "ganda", "bait", "maganda", "friendly", "kamukha", "ligtas"
}

cyberbullying_keywords = {
    "bobo", "pangit", "hate", "bully"
}

def preprocess_text(text):
    if not isinstance(text, str):
        return ""
    text = text.lower()
    text = re.sub(r'\[.*?\]', '', text)
    text = re.sub(r'[^a-z\s]', '', text)
    tokens = text.split()
    tokens = [lemmatizer.lemmatize(word) for word in tokens if word not in stopwords]
    return ' '.join(tokens)

def detect_cyberbullying(text):
    try:
        processed_text = preprocess_text(text)
        if not processed_text:
            return {
                "error": "Empty or invalid input text after preprocessing",
                "result": None,
                "probability": None
            }

        input_tokens = set(processed_text.split())
        matched_non_cyberbullying = input_tokens.intersection(non_cyberbullying_keywords)
        matched_cyberbullying = input_tokens.intersection(cyberbullying_keywords)

        if matched_non_cyberbullying and not matched_cyberbullying:
            return {
                "error": None,
                "result": "No Cyberbullying Detected",
                "probability": 0
            }

        input_vector = vectorizer.transform([processed_text])
        prediction_prob = model.predict_proba(input_vector)[0][1]
        prediction_label = model.predict(input_vector)[0]

        result = "Cyberbullying Detected" if prediction_label == 1 else "No Cyberbullying Detected"
        probability = prediction_prob * 100 if prediction_label == 1 else 0

        return {
            "error": None,
            "result": result,
            "probability": round(probability, 2)
        }

    except Exception as e:
        return {
            "error": f"Detection error: {str(e)}",
            "result": None,
            "probability": None
        }

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({
            "error": "No input text provided",
            "result": None,
            "probability": None
        }))
        sys.exit(1)

    text = sys.argv[1]
    result = detect_cyberbullying(text)
    print(json.dumps(result))