import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Dictionary of new keys to add
new_translations = {
    "en": {"home_nav": "Home", "finish_close": "Finish & Close"},
    "hi": {"home_nav": "मुख्य पृष्ठ", "finish_close": "पूर्ण करें और बंद करें"},
    "mr": {"home_nav": "मुख्य पृष्ठ", "finish_close": "पूर्ण करा आणि बंद करा"},
    "bn": {"home_nav": "হোম", "finish_close": "শেষ করুন এবং বন্ধ করুন"},
    "te": {"home_nav": "హోమ్", "finish_close": "పూర్తి చేసి మూసివేయి"},
    "ta": {"home_nav": "முகப்பு", "finish_close": "முடித்து மூடவும்"},
    "kn": {"home_nav": "ಹೋಮ್", "finish_close": "ಪೂರ್ಣಗೊಳಿಸಿ ಮತ್ತು ಮುಚ್ಚಿ"},
    "gu": {"home_nav": "હોમ", "finish_close": "પૂર્ણ કરો અને બંધ કરો"},
    "ml": {"home_nav": "ഹോം", "finish_close": "പൂർത്തിയാക്കി അടയ്ക്കുക"},
    "or": {"home_nav": "ହୋମ୍", "finish_close": "ସମାପ୍ତ କରନ୍ତୁ ଏବଂ ବନ୍ଦ କରନ୍ତୁ"},
    "as": {"home_nav": "হোম", "finish_close": "সমাপ্ত কৰি বন্ধ কৰক"}
}

# Find each language block in translations and insert the new keys
for lang, keys in new_translations.items():
    # Find the block for the language
    # Example: "en": { ... }
    pattern = rf'"{lang}":\s*\{{(.*?)\}}'
    match = re.search(pattern, content, re.DOTALL)
    if match:
        block_content = match.group(1)
        # Add new keys if they don't exist
        for key, value in keys.items():
            if f'"{key}":' not in block_content:
                # Add before the last closing brace (not really needed since we are replacing the inner content)
                # Let's just append to the beginning of the block
                block_content = f'\n                "{key}": "{value}",' + block_content
        
        # Replace the old block content with the new one
        content = content.replace(match.group(1), block_content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
