import sys
import joblib
import re
import nltk
import os
from nltk.stem import WordNetLemmatizer
from sklearn.feature_extraction.text import ENGLISH_STOP_WORDS
import json

# get the directory containing the script
script_dir = os.path.dirname(os.path.abspath(__file__))

# Set up logging to a file
log_file = os.path.join(script_dir, 'cyberbullying_debug.log')


def log_debug(message):
    try:
        with open(log_file, 'a', encoding='utf-8', errors='replace') as f:
            f.write(f"{message}\n")
    except Exception as e:
        # Fall back to a simpler logging approach if the above fails
        with open(log_file, 'a', encoding='utf-8', errors='replace') as f:
            f.write(f"Error logging message: {str(e)}\n")


log_debug("\n\n--- New Detection Run ---")

# download required NLTK data
try:
    nltk.download('wordnet', quiet=True)
    nltk.download('stopwords', quiet=True)
except Exception as e:
    error_msg = f"Failed to download NLTK data: {str(e)}"
    log_debug(error_msg)
    print(json.dumps({
        "error": error_msg,
        "result": None,
        "probability": None
    }))
    sys.exit(1)

# load model and vectorizer
try:
    model_path = os.path.join(script_dir, 'models',
                              'logistic_regression_model.pkl')
    vectorizer_path = os.path.join(
        script_dir, 'models', 'tfidf_vectorizer.pkl')

    model = joblib.load(model_path)
    vectorizer = joblib.load(vectorizer_path)
except FileNotFoundError as e:
    error_msg = f"Model files not found. Path: {model_path}"
    log_debug(error_msg)
    print(json.dumps({
        "error": error_msg,
        "result": None,
        "probability": None
    }))
    sys.exit(1)
except Exception as e:
    error_msg = f"Error loading models: {str(e)}"
    log_debug(error_msg)
    print(json.dumps({
        "error": error_msg,
        "result": None,
        "probability": None
    }))
    sys.exit(1)

# stopwords and keywords
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

# Expanded cyberbullying keywords list
cyberbullying_keywords = {
    "bobo", "pangit", "hate", "bully", "stupid", "kill", "yourself", "worthless",
    "loser", "shit", "fucking", "fuck", "die", "ugly", "idiot", "dumb", "bitch",
    "whore", "slut", "retard", "retarded", "moron", "pathetic", "useless", "attack",
    "harass", "harassed", "offensive", "degrad", "harm", "profanity", "insult",
    "tanga", "gago", "inutil", "pakyu", "puta", "bulok", "ulol", "tarantado",
    "engot", "boba", "abnormal", "putangina", "hayop", "walang kwenta", "timang",
    "tarantado", "hinayupak"
}


def preprocess_text(text):
    if not isinstance(text, str):
        return ""

    # Sanitize input by replacing invalid unicode
    text = text.encode('utf-8', errors='replace').decode('utf-8')

    log_debug(f"Original text: {text}")

    # Less aggressive preprocessing to preserve offensive words
    text = text.lower()

    # Remove URLs
    text = re.sub(r'https?://\S+|www\.\S+', '', text)

    # Keep alphanumeric characters and spaces
    text = re.sub(r'[^\w\s]', ' ', text)

    log_debug(f"After basic cleaning: {text}")

    tokens = text.split()

    # Filter stopwords but keep offensive words even if they're in stopwords
    filtered_tokens = []
    for word in tokens:
        if word in cyberbullying_keywords or word not in stopwords:
            filtered_tokens.append(lemmatizer.lemmatize(word))

    processed_text = ' '.join(filtered_tokens)
    log_debug(f"After preprocessing: {processed_text}")

    return processed_text


def detect_cyberbullying(text):
    try:
        # Sanitize input
        text = text.encode('utf-8', errors='replace').decode('utf-8')
        
        log_debug(f"Input text for detection: {text}")
        base_probability = 0
        lower_text = text.lower()
        detected_reasons = []

        # Check for extreme phrases - highest severity
        extreme_phrases = ["kill yourself",
                           "go kill yourself", "go die", "commit suicide"]
        for phrase in extreme_phrases:
            if phrase in lower_text:
                log_debug(f"Extreme phrase detected: {phrase}")
                detected_reasons.append(f"extreme phrase: '{phrase}'")
                # 95% base probability
                base_probability = max(base_probability, 0.95)

        # Check for keywords directly in original text - high severity
        for word in cyberbullying_keywords:
            if re.search(r'\b' + word + r'\b', lower_text):
                log_debug(f"Cyberbullying keyword found directly: {word}")
                detected_reasons.append(f"keyword: '{word}'")
                # 85% base probability if higher than current
                base_probability = max(base_probability, 0.85)

        # Continue with ML-based detection
        processed_text = preprocess_text(text)
        if not processed_text:
            log_debug("Empty processed text")
            return {
                "error": "Empty or invalid input text after preprocessing",
                "result": None,
                "probability": None
            }

        input_tokens = set(processed_text.split())
        matched_non_cyberbullying = input_tokens.intersection(
            non_cyberbullying_keywords)
        matched_cyberbullying = input_tokens.intersection(
            cyberbullying_keywords)

        log_debug(f"Non-cyberbullying matches: {matched_non_cyberbullying}")
        log_debug(f"Cyberbullying matches: {matched_cyberbullying}")

        # Add matched cyberbullying keywords to detection reasons
        if matched_cyberbullying:
            log_debug(
                f"Cyberbullying detected by keywords: {matched_cyberbullying}")
            detected_reasons.append(
                f"keyword set: {', '.join(matched_cyberbullying)}")
            # 80% base probability if higher than current
            base_probability = max(base_probability, 0.80)

        # Only consider non-cyberbullying keywords if no cyberbullying is detected
        if matched_non_cyberbullying and not detected_reasons:
            log_debug(
                "Non-cyberbullying keywords found, no cyberbullying keywords")
            return {
                "error": None,
                "result": "No Cyberbullying Detected",
                "probability": 0
            }

        # Always use ML model to refine or establish probability
        input_vector = vectorizer.transform([processed_text])
        ml_probability = model.predict_proba(input_vector)[0][1]
        prediction_label = model.predict(input_vector)[0]

        log_debug(
            f"ML prediction: label={prediction_label}, probability={ml_probability}")

        # If we already detected something with rules
        if detected_reasons:
            # Combine rule-based and ML probabilities (weighted average)
            # You can adjust the weights to give more importance to rules or ML
            final_probability = (0.7 * base_probability) + \
                (0.3 * ml_probability)
            result = "Cyberbullying Detected"
        else:
            # If no rule-based detection, rely on ML model
            final_probability = ml_probability
            result = "Cyberbullying Detected" if prediction_label == 1 else "No Cyberbullying Detected"

        # Only return probability > 0 if cyberbullying is detected
        probability = final_probability * 100 if result == "Cyberbullying Detected" else 0

        log_debug(
            f"Detection reasons: {', '.join(detected_reasons) if detected_reasons else 'ML model only'}")
        log_debug(f"Final result: {result}, probability: {probability}")

        return {
            "error": None,
            "result": result,
            "probability": round(probability, 2),
            "reasons": detected_reasons if result == "Cyberbullying Detected" else []
        }

    except Exception as e:
        error_msg = f"Detection error: {str(e)}"
        log_debug(f"ERROR: {error_msg}")
        return {
            "error": error_msg,
            "result": None,
            "probability": None
        }


if __name__ == "__main__":
    text = ""

    # Check if there are command line arguments
    if len(sys.argv) > 1:
        text = sys.argv[1]
        log_debug(f"Received text from command line: {text}")
    else:
        # Read from stdin with error handling
        try:
            # Replace invalid Unicode characters during reading
            text = sys.stdin.read().encode('utf-8', errors='replace').decode('utf-8').strip()
            log_debug(f"Received text from stdin: {text}")
        except Exception as e:
            error_msg = f"Error reading input: {str(e)}"
            log_debug(error_msg)
            print(json.dumps({
                "error": error_msg,
                "result": None,
                "probability": None
            }))
            sys.exit(1)

    # If still empty, report an error
    if not text:
        error_msg = "No input text provided"
        log_debug(error_msg)
        print(json.dumps({
            "error": error_msg,
            "result": None,
            "probability": None
        }))
        sys.exit(1)

    result = detect_cyberbullying(text)
    log_debug(f"Final result: {json.dumps(result)}")
    print(json.dumps(result))