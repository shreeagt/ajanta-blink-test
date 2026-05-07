import sys
import re
import json

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

match = re.search(r'const translations = (\{.*?\});', content, flags=re.DOTALL)
if match:
    translations = json.loads(match.group(1))
    
    extra_keys = {
        "en": {
            "hello_rep": "Hello, Representative", "stat_today": "Today", "stat_month": "Month", "stat_total": "Total",
            "daily_progress": "Daily Progress", "recent_screenings": "Recent Screenings", "share_link": "Share Link",
            "prev": "Prev", "next": "Next", "stats_nav": "Stats", "dry_eye_nav": "Dry Eye", "cvs_test_nav": "CVS Test"
        },
        "hi": {
            "hello_rep": "नमस्ते, प्रतिनिधि", "stat_today": "आज", "stat_month": "महीना", "stat_total": "कुल",
            "daily_progress": "दैनिक प्रगति", "recent_screenings": "हालिया स्क्रीनिंग", "share_link": "लिंक साझा करें",
            "prev": "पिछला", "next": "अगला", "stats_nav": "आंकड़े", "dry_eye_nav": "ड्राई आई", "cvs_test_nav": "सीवीएस टेस्ट"
        },
        "as": {
            "hello_rep": "নমস্কাৰ, প্ৰতিনিধি", "stat_today": "আজি", "stat_month": "মাহ", "stat_total": "মুঠ",
            "daily_progress": "দৈনিক অগ্ৰগতি", "recent_screenings": "শেহতীয়া স্ক্ৰীনিং", "share_link": "লিংক শ্বেয়াৰ কৰক",
            "prev": "পূৰ্বৱৰ্তী", "next": "পৰৱৰ্তী", "stats_nav": "পৰিসংখ্যা", "dry_eye_nav": "ড্ৰাই আই", "cvs_test_nav": "CVS টেষ্ট"
        },
        "mr": {
            "hello_rep": "नमस्कार, प्रतिनिधी", "stat_today": "आज", "stat_month": "महिना", "stat_total": "एकूण",
            "daily_progress": "दैनिक प्रगती", "recent_screenings": "अलीकडील स्क्रीनिंग", "share_link": "लिंक शेअर करा",
            "prev": "मागे", "next": "पुढे", "stats_nav": "आकडेवारी", "dry_eye_nav": "ड्राय आय", "cvs_test_nav": "CVS टेस्ट"
        },
        "gu": {
            "hello_rep": "નમસ્તે, પ્રતિનિધિ", "stat_today": "આજે", "stat_month": "મહિનો", "stat_total": "કુલ",
            "daily_progress": "દૈનિક પ્રગતિ", "recent_screenings": "તાજેતરના સ્ક્રિનિંગ", "share_link": "લિંક શેર કરો",
            "prev": "પાછળ", "next": "આગળ", "stats_nav": "આંકડા", "dry_eye_nav": "ડ્રાય આઈ", "cvs_test_nav": "CVS ટેસ્ટ"
        },
        "or": {
            "hello_rep": "ନମସ୍କାର, ପ୍ରତିନିଧି", "stat_today": "ଆଜି", "stat_month": "ମାସ", "stat_total": "ମୋଟ",
            "daily_progress": "ଦୈନିକ ପ୍ରଗତି", "recent_screenings": "ସାମ୍ପ୍ରତିକ ସ୍କ୍ରିନିଂ", "share_link": "ଲିଙ୍କ୍ ସେୟାର୍ କରନ୍ତୁ",
            "prev": "ପୂର୍ବବର୍ତ୍ତୀ", "next": "ପରବର୍ତ୍ତୀ", "stats_nav": "ପରିସଂଖ୍ୟାନ", "dry_eye_nav": "ଡ୍ରାଏ ଆଇ", "cvs_test_nav": "CVS ଟେଷ୍ଟ"
        },
        "te": {
            "hello_rep": "నమస్కారం, ప్రతినిధి", "stat_today": "ఈరోజు", "stat_month": "నెల", "stat_total": "మొత్తం",
            "daily_progress": "రోజువారీ పురోగతి", "recent_screenings": "ఇటీవలి స్క్రీనింగ్లు", "share_link": "లింక్‌ను భాగస్వామ్యం చేయండి",
            "prev": "మునుపటి", "next": "తదుపరి", "stats_nav": "గణాంకాలు", "dry_eye_nav": "డ్రై ఐ", "cvs_test_nav": "CVS టెస్ట్"
        },
        "ta": {
            "hello_rep": "வணக்கம், பிரதிநிதி", "stat_today": "இன்று", "stat_month": "மாதம்", "stat_total": "மொத்தம்",
            "daily_progress": "தினசரி முன்னேற்றம்", "recent_screenings": "சமீபத்திய ஸ்கிரீனிங்", "share_link": "இணைப்பைப் பகிரவும்",
            "prev": "முந்தைய", "next": "அடுத்தது", "stats_nav": "புள்ளிவிவரங்கள்", "dry_eye_nav": "உலர்ந்த கண்", "cvs_test_nav": "CVS சோதனை"
        },
        "kn": {
            "hello_rep": "ನಮಸ್ಕಾರ, ಪ್ರತಿನಿಧಿ", "stat_today": "ಇಂದು", "stat_month": "ತಿಂಗಳು", "stat_total": "ಒಟ್ಟು",
            "daily_progress": "ದೈನಂದಿನ ಪ್ರಗತಿ", "recent_screenings": "ಇತ್ತೀಚಿನ ಸ್ಕ್ರೀನಿಂಗ್ಗಳು", "share_link": "ಲಿಂಕ್ ಹಂಚಿಕೊಳ್ಳಿ",
            "prev": "ಹಿಂದಿನ", "next": "ಮುಂದಿನ", "stats_nav": "ಅಂಕಿಅಂಶಗಳು", "dry_eye_nav": "ಡ್ರೈ ಐ", "cvs_test_nav": "CVS ಟೆಸ್ಟ್"
        },
        "ml": {
            "hello_rep": "നമസ്കാരം, പ്രതിനിധി", "stat_today": "ഇന്ന്", "stat_month": "മാസം", "stat_total": "ആകെ",
            "daily_progress": "ദൈനംദിന പുരോഗതി", "recent_screenings": "സമീപകാല സ്ക്രീനിംഗുകൾ", "share_link": "ലിങ്ക് പങ്കിടുക",
            "prev": "മുൻപത്തെ", "next": "അടുത്തത്", "stats_nav": "സ്ഥിതിവിവരക്കണക്കുകൾ", "dry_eye_nav": "ഡ്രൈ ഐ", "cvs_test_nav": "CVS ടെസ്റ്റ്"
        },
        "bn": {
            "hello_rep": "নমস্কার, প্রতিনিধি", "stat_today": "আজ", "stat_month": "মাস", "stat_total": "মোট",
            "daily_progress": "দৈনিক অগ্রগতি", "recent_screenings": "সাম্প্রতিক স্ক্রিনিং", "share_link": "লিঙ্ক শেয়ার করুন",
            "prev": "আগের", "next": "পরের", "stats_nav": "পরিসংখ্যান", "dry_eye_nav": "ড্রাই আই", "cvs_test_nav": "সিভিএস টেস্ট"
        }
    }
    
    for lang, keys in extra_keys.items():
        if lang in translations:
            translations[lang].update(keys)
            
    translations_js = "const translations = " + json.dumps(translations, ensure_ascii=False, indent=8) + ";"
    content = re.sub(r'const translations = \{.*?\};', translations_js, content, flags=re.DOTALL)

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
