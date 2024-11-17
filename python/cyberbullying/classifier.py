import sys
import json
import os
import nltk
import re
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
import joblib

def ensure_nltk_data(nltk_data_path):
    """Ensure NLTK data is downloaded and available"""
    # Set NLTK data path
    if not os.path.exists(nltk_data_path):
        os.makedirs(nltk_data_path, exist_ok=True)
    
    nltk.data.path.append(nltk_data_path)
    
    # Required NLTK downloads
    required_packages = ['punkt', 'stopwords', 'wordnet']
    for package in required_packages:
        try:
            nltk.data.find(f'tokenizers/{package}' if package == 'punkt' else f'corpora/{package}')
        except LookupError:
            nltk.download(package, download_dir=nltk_data_path, quiet=True)

def preprocess_text(text):
    """Preprocess the input text by cleaning and lemmatizing"""
    # Convert to lowercase
    text = text.lower()
    
    # Remove unwanted characters
    text = re.sub(r'[^a-z\s]', '', text)
    
    # Tokenize
    tokens = word_tokenize(text)
    
    # Get stopwords
    stop_words = set(stopwords.words('english'))
    
    # Lemmatize
    lemmatizer = WordNetLemmatizer()
    tokens = [lemmatizer.lemmatize(word) for word in tokens if word not in stop_words]
    
    return ' '.join(tokens)

def detect_cyberbullying(text, nltk_data_path):
    try:
        # Initialize NLTK
        ensure_nltk_data(nltk_data_path)
        
        # Get the base path
        base_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
        
        # Construct paths
        model_path = os.path.join(base_path, 'storage', 'app', 'models', 'logistic_regression_model.pkl')
        vectorizer_path = os.path.join(base_path, 'storage', 'app', 'models', 'tfidf_vectorizer.pkl')
        
        # Check if files exist
        if not os.path.exists(model_path):
            raise FileNotFoundError(f"Model file not found at: {model_path}")
        if not os.path.exists(vectorizer_path):
            raise FileNotFoundError(f"Vectorizer file not found at: {vectorizer_path}")
        
        # Load model and vectorizer
        model = joblib.load(model_path)
        vectorizer = joblib.load(vectorizer_path)
        
        # Preprocess the text
        processed_text = preprocess_text(text)
        
        # Transform text using vectorizer
        text_vector = vectorizer.transform([processed_text])
        
        # Get prediction and probability
        prediction = model.predict(text_vector)[0]
        probability = model.predict_proba(text_vector)[0][1] * 100
        
        return {
            'result': 'Cyberbullying Detected' if prediction == 1 else 'No Cyberbullying Detected',
            'probability': float(probability)
        }
        
    except Exception as e:
        return {
            'result': 'Analysis Failed',
            'probability': 0,
            'error': str(e)
        }

def main():
    try:
        # Add the custom site-packages to Python path
        custom_site_packages = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..', 'storage', 'python', 'site-packages'))
        sys.path.append(custom_site_packages)
        
        # Set NLTK data path
        nltk_data_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..', 'storage', 'python', 'nltk_data'))
        
        if len(sys.argv) > 1:
            input_text = sys.argv[1]
            result = detect_cyberbullying(input_text, nltk_data_path)
            print(json.dumps(result))
        else:
            print(json.dumps({'result': 'No input text provided', 'probability': 0}))
            
    except Exception as e:
        error_result = {
            'result': 'Analysis Failed',
            'probability': 0,
            'error': str(e)
        }
        print(json.dumps(error_result))
        sys.exit(1)

if __name__ == '__main__':
    main()