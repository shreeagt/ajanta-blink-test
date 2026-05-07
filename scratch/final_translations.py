import sys
import re
import json

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# I'll extract the current translations and update them
match = re.search(r'const translations = (\{.*?\});', content, flags=re.DOTALL)
if match:
    translations = json.loads(match.group(1))
    
    extra_keys = {
        "en": {"language_label": "Language", "change_lang": "Change Language", "stare_center": "Please stare at the center", "live_count": "Live Count"},
        "hi": {"language_label": "भाषा", "change_lang": "भाषा बदलें", "stare_center": "कृपया केंद्र में देखें", "live_count": "लाइव काउंट"},
        "as": {"language_label": "ভাষা", "change_lang": "ভাষা পৰিৱৰ্তন কৰক", "stare_center": "অনুগ্ৰহ কৰি কেন্দ্ৰলৈ চাওক", "live_count": "লাইভ কাউণ্ট"},
        "mr": {"language_label": "भाषा", "change_lang": "भाषा बदला", "stare_center": "कृपया मध्यभागी पहा", "live_count": "थेट गणना"},
        "gu": {"language_label": "ભાષા", "change_lang": "ભાષા બદલો", "stare_center": "કૃપા કરીને કેન્દ્રમાં જુઓ", "live_count": "લાઇવ કાઉન્ટ"},
        "or": {"language_label": "ଭାଷା", "change_lang": "ଭାଷା ପରିବର୍ତ୍ତନ କରନ୍ତୁ", "stare_center": "ଦୟାକରି କେନ୍ଦ୍ରକୁ ଚାହାଁନ୍ତୁ", "live_count": "ଲାଇଭ୍ ଗଣନା"},
        "te": {"language_label": "భాష", "change_lang": "భాషను మార్చండి", "stare_center": "దయచేసి మధ్యలో చూడండి", "live_count": "లైవ్ కౌంట్"},
        "ta": {"language_label": "மொழி", "change_lang": "மொழியை மாற்றவும்", "stare_center": "மையத்தைப் பார்க்கவும்", "live_count": "நேரடி எண்ணிக்கை"},
        "kn": {"language_label": "ಭಾಷೆ", "change_lang": "ಭಾಷೆ ಬದಲಾಯಿಸಿ", "stare_center": "ದಯವಿಟ್ಟು ಕೇಂದ್ರವನ್ನು ನೋಡಿ", "live_count": "ಲೈವ್ ಕೌಂಟ್"},
        "ml": {"language_label": "ഭാഷ", "change_lang": "ഭാഷ മാറ്റുക", "stare_center": "ദയവായി മധ്യഭാഗത്തേക്ക് നോക്കുക", "live_count": "ലൈവ് കൗണ്ട്"},
        "bn": {"language_label": "ভাষা", "change_lang": "ভাষা পরিবর্তন করুন", "stare_center": "অনুগ্রহ করে কেন্দ্রের দিকে তাকান", "live_count": "লাইভ কাউন্ট"}
    }
    
    for lang, keys in extra_keys.items():
        if lang in translations:
            translations[lang].update(keys)
            
    translations_js = "const translations = " + json.dumps(translations, ensure_ascii=False, indent=8) + ";"
    content = re.sub(r'const translations = \{.*?\};', translations_js, content, flags=re.DOTALL)

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
