import sys
print("Python Path:")
for path in sys.path:
    print(path)

print("\nTesting imports:")
try:
    import joblib
    print("joblib imported successfully")
except ImportError as e:
    print(f"joblib import failed: {e}")

try:
    import sklearn
    print("sklearn imported successfully")
except ImportError as e:
    print(f"sklearn import failed: {e}")

try:
    import nltk
    print("nltk imported successfully")
except ImportError as e:
    print(f"nltk import failed: {e}")