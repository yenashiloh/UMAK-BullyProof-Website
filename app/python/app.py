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
   "ang", "sa", "ng", "para", "at", "ito", "ay", "na", "ako", "ni",
    "is", "kung", "hindi", "mga", "may", "ko", "kami", "kayo", "nila",
    "naman", "pa", "si", "ngayon", "dahil", "wala", "doon", "dito",
    "isang", "o", "lahat", "tungkol", "pero", "alam", "huwag", "tama",
    "baka", "pag", "ganun", "sana", "iyan", "iyong", "nito", "akin",
    "kanila", "sila", "amin", "atin", "ikaw", "tao", "bakit", "ano",
    "paano", "ganoon", "ganito", "noon", "ngayong", "iyo", "nga",
    "nang", "muli", "kasama", "bago", "pagkatapos", "habang", "sapagkat",
    "kaya", "upang", "mula", "hanggang", "din", "rin", "man", "talaga",
    "walang", "sino", "alin", "kanino", "bawat", "maging", "subalit",
    "bagamat", "dati", "lagi", "pala", "muna", "halos", "lamang",
    "siyang", "niya", "kahit", "daw", "raw", "pati", "iyon", "diyan",
    "kay", "kaniya", "tulad", "niyo", "kailan", "magkaroon", "gawa",
    "tayo", "siya", "iba", "ibang", "kahiyan", "kailanman", "karamihan",
    "katulad", "kaysa", "kayat", "lalo", "lalo na", "lalung",
    "magkagayunman", "magkagayun", "marahil", "masyado", "matapos",
    "naging", "napaka", "narito", "nasaan", "ngunit", "pareho", "pwede",
    "pwedeng", "saan", "saanman", "sanhi", "tila", "tuloy", "tuloy-tuloy",
    "yata", "yun", "yung", "makipag", "maibsan", "maitala", "makita",
    "malaki", "maliit", "mamaya", "mas", "mayron", "mayroon", "mula sa",
    "nasa", "nitong", "noon", "palagay", "patungkol", "puwede",
    "samantalang", "tapos", "yari", "kahit na", "mo", "ka"
})

lemmatizer = WordNetLemmatizer()

non_cyberbullying_keywords = {
    "ganda", "bait", "maganda", "friendly", "kamukha", "ligtas", "cute", "gwapo", "pogi"
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