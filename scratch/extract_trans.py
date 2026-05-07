import re
import json

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Extract translations object
trans_match = re.search(r'const translations = (\{.*?\});', content, re.DOTALL)
if trans_match:
    trans_json = trans_match.group(1)
    # Fix potential trailing commas or other JS-isms for JSON parsing
    # But wait, it's easier to just use regex to find the keys and values if it's not valid JSON
    print("Translations object found.")
else:
    print("Translations object NOT found.")

# Extract blinkAnalysisSet object
analysis_match = re.search(r'const blinkAnalysisSet = (\{.*?\});', content, re.DOTALL)
if analysis_match:
    analysis_json = analysis_match.group(1)
    print("BlinkAnalysisSet object found.")
else:
    print("BlinkAnalysisSet object NOT found.")

# I'll just print them out so I can see them
if trans_match:
    with open('trans_raw.txt', 'w', encoding='utf-8') as f:
        f.write(trans_match.group(1))

if analysis_match:
    with open('analysis_raw.txt', 'w', encoding='utf-8') as f:
        f.write(analysis_match.group(1))
