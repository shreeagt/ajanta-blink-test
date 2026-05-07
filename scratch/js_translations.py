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
            "start_motivation": "Start your first screening to ignite your daily goal!",
            "progress_motivation": "Great start! Only {count} more to reach your daily target.",
            "goal_motivation": "Goal achieved! You're making a real impact on eye health today.",
            "no_screenings_title": "No Screenings Yet",
            "no_screenings_desc": "Start your first AI blink test to see patient insights here.",
            "start_new_test": "Start New Test",
            "blink_screening_label": "Blink Test Screening",
            "blinks_count_label": "{count} Blinks",
            "page_label": "Page {count}",
            "init_camera": "Initializing Camera..."
        },
        "hi": {
            "start_motivation": "अपना दैनिक लक्ष्य शुरू करने के लिए अपनी पहली स्क्रीनिंग शुरू करें!",
            "progress_motivation": "शानदार शुरुआत! अपने दैनिक लक्ष्य तक पहुँचने के लिए केवल {count} और शेष हैं।",
            "goal_motivation": "लक्ष्य प्राप्त हुआ! आज आप आंखों के स्वास्थ्य पर वास्तविक प्रभाव डाल रहे हैं।",
            "no_screenings_title": "अभी तक कोई स्क्रीनिंग नहीं",
            "no_screenings_desc": "मरीज की जानकारी देखने के लिए अपना पहला एआई ब्लिंक टेस्ट शुरू करें।",
            "start_new_test": "नया टेस्ट शुरू करें",
            "blink_screening_label": "ब्लिंक टेस्ट स्क्रीनिंग",
            "blinks_count_label": "{count} ब्लिंक",
            "page_label": "पृष्ठ {count}",
            "init_camera": "कैमरा शुरू हो रहा है..."
        },
        "as": {
            "start_motivation": "আপোনাৰ দৈনিক লক্ষ্যত উপনীত হবলৈ প্ৰথম স্ক্ৰীনিং আৰম্ভ কৰক!",
            "progress_motivation": "সুন্দৰ আৰম্ভণি! আপোনাৰ লক্ষ্যত উপনীত হবলৈ কেৱল {count} টা বাকী আছে।",
            "goal_motivation": "লক্ষ্য অৰ্জন কৰা হ’ল! আজি আপুনি চকুৰ স্বাস্থ্যৰ ওপৰত এক প্ৰকৃত প্ৰভাৱ পেলাইছে।",
            "no_screenings_title": "এতিয়ালৈকে কোনো স্ক্ৰীনিং নাই",
            "no_screenings_desc": "ৰোগীৰ তথ্য চাবলৈ আপোনাৰ প্ৰথম AI ব্লিংক টেষ্ট আৰম্ভ কৰক।",
            "start_new_test": "নতুন পৰীক্ষা আৰম্ভ কৰক",
            "blink_screening_label": "ব্লিংক টেষ্ট স্ক্ৰীনিং",
            "blinks_count_label": "{count} টা ব্লিংক",
            "page_label": "পৃষ্ঠা {count}",
            "init_camera": "কেমেৰা সক্ৰিয় কৰা হৈছে..."
        },
        "mr": {
            "start_motivation": "तुमचे दैनंदिन ध्येय गाठण्यासाठी पहिले स्क्रीनिंग सुरू करा!",
            "progress_motivation": "उत्तम सुरुवात! तुमचे ध्येय गाठण्यासाठी फक्त {count} शिल्लक आहेत.",
            "goal_motivation": "ध्येय गाठले! आज तुम्ही डोळ्यांच्या आरोग्यावर वास्तविक प्रभाव टाकत आहात.",
            "no_screenings_title": "अद्याप कोणतेही स्क्रीनिंग नाही",
            "no_screenings_desc": "रुग्णाची माहिती पाहण्यासाठी तुमची पहिली AI ब्लिंक टेस्ट सुरू करा.",
            "start_new_test": "नवीन टेस्ट सुरू करा",
            "blink_screening_label": "ब्लिंक टेस्ट स्क्रीनिंग",
            "blinks_count_label": "{count} ब्लिंक्स",
            "page_label": "पृष्ठ {count}",
            "init_camera": "कॅमेरा सुरू होत आहे..."
        },
        "gu": {
            "start_motivation": "તમારો દૈનિક ધ્યેય શરૂ કરવા માટે તમારું પ્રથમ સ્ક્રિનિંગ શરૂ કરો!",
            "progress_motivation": "સરસ શરૂઆત! તમારા દૈનિક લક્ષ્ય સુધી પહોંચવા માટે માત્ર {count} બાકી છે.",
            "goal_motivation": "ધ્યેય પ્રાપ્ત થયો! આજે તમે આંખના સ્વાસ્થ્ય પર વાસ્તવિક પ્રભાવ પાડી રહ્યા છો.",
            "no_screenings_title": "હજુ સુધી કોઈ સ્ક્રિનિંગ નથી",
            "no_screenings_desc": "દર્દીની માહિતી જોવા માટે તમારી પ્રથમ AI બ્લિંક ટેસ્ટ શરૂ કરો.",
            "start_new_test": "નવી ટેસ્ટ શરૂ કરો",
            "blink_screening_label": "બ્લિંક ટેસ્ટ સ્ક્રિનિંગ",
            "blinks_count_label": "{count} બ્લિંક્સ",
            "page_label": "પૃષ્ઠ {count}",
            "init_camera": "કેમેરા શરૂ થઈ રહ્યો છે..."
        },
        "or": {
            "start_motivation": "ଆପଣଙ୍କର ଦୈନିକ ଲକ୍ଷ୍ୟ ଆରମ୍ଭ କରିବାକୁ ଆପଣଙ୍କର ପ୍ରଥମ ସ୍କ୍ରିନିଂ ଆରମ୍ଭ କରନ୍ତୁ!",
            "progress_motivation": "ଉତ୍ତମ ଆରମ୍ଭ! ଆପଣଙ୍କର ଦୈନିକ ଲକ୍ଷ୍ୟରେ ପହଞ୍ଚିବା ପାଇଁ କେବଳ {count} ବାକି ଅଛି |",
            "goal_motivation": "ଲକ୍ଷ୍ୟ ହାସଲ ହେଲା! ଆଜି ଆପଣ ଆଖି ସ୍ୱାସ୍ଥ୍ୟ ଉପରେ ଏକ ପ୍ରକୃତ ପ୍ରଭାବ ପକାଉଛନ୍ତି |",
            "no_screenings_title": "ଏପର୍ଯ୍ୟନ୍ତ କୌଣସି ସ୍କ୍ରିନିଂ ହୋଇନାହିଁ",
            "no_screenings_desc": "ରୋଗୀର ତଥ୍ୟ ଦେଖିବାକୁ ଆପଣଙ୍କର ପ୍ରଥମ AI ବ୍ଲିଙ୍କ୍ ଟେଷ୍ଟ ଆରମ୍ଭ କରନ୍ତୁ |",
            "start_new_test": "ନୂତନ ପରୀକ୍ଷା ଆରମ୍ଭ କରନ୍ତୁ",
            "blink_screening_label": "ବ୍ଲିଙ୍କ୍ ଟେଷ୍ଟ ସ୍କ୍ରିନିଂ",
            "blinks_count_label": "{count} ବ୍ଲିଙ୍କ୍",
            "page_label": "ପୃଷ୍ଠା {count}",
            "init_camera": "କ୍ୟାମେରା ଆରମ୍ଭ ହେଉଛି..."
        },
        "te": {
            "start_motivation": "మీ రోజువారీ లక్ష్యాన్ని ప్రారంభించడానికి మీ మొదటి స్క్రీనింగ్‌ను ప్రారంభించండి!",
            "progress_motivation": "గొప్ప ప్రారంభం! మీ లక్ష్యాన్ని చేరుకోవడానికి కేవలం {count} మాత్రమే మిగిలి ఉన్నాయి.",
            "goal_motivation": "లక్ష్యం సాధించబడింది! ఈరోజు మీరు కంటి ఆరోగ్యంపై నిజమైన ప్రభావం చూపుతున్నారు.",
            "no_screenings_title": "ఇంకా స్క్రీనింగ్‌లు లేవు",
            "no_screenings_desc": "రోగి సమాచారాన్ని చూడటానికి మీ మొదటి AI బ్లింక్ టెస్ట్‌ను ప్రారంభించండి.",
            "start_new_test": "కొత్త టెస్ట్ ప్రారంభించండి",
            "blink_screening_label": "బ్లింక్ టెస్ట్ స్క్రీనింగ్",
            "blinks_count_label": "{count} బ్లింక్‌లు",
            "page_label": "పేజీ {count}",
            "init_camera": "కెమెరా ప్రారంభించబడుతోంది..."
        },
        "ta": {
            "start_motivation": "உங்கள் தினசரி இலக்கைத் தொடங்க உங்கள் முதல் ஸ்கிரீனிங்கைத் தொடங்குங்கள்!",
            "progress_motivation": "சிறந்த ஆரம்பம்! உங்கள் இலக்கை அடைய இன்னும் {count} மட்டுமே மீதமுள்ளது.",
            "goal_motivation": "இலக்கு எட்டப்பட்டது! இன்று நீங்கள் கண் ஆரோக்கியத்தில் உண்மையான தாக்கத்தை ஏற்படுத்துகிறீர்கள்.",
            "no_screenings_title": "இன்னும் ஸ்கிரீனிங் இல்லை",
            "no_screenings_desc": "நோயாளி தகவலைக் காண உங்கள் முதல் AI கண் சிமிட்டல் சோதனையைத் தொடங்குங்கள்.",
            "start_new_test": "புதிய சோதனையைத் தொடங்கவும்",
            "blink_screening_label": "கண் சிமிட்டல் சோதனை ஸ்கிரீனிங்",
            "blinks_count_label": "{count} கண் சிமிட்டல்கள்",
            "page_label": "பக்கம் {count}",
            "init_camera": "கேமரா தொடங்குகிறது..."
        },
        "kn": {
            "start_motivation": "ನಿಮ್ಮ ದೈನಂದಿನ ಗುರಿಯನ್ನು ಪ್ರಾರಂಭಿಸಲು ನಿಮ್ಮ ಮೊದಲ ಸ್ಕ್ರೀನಿಂಗ್ ಪ್ರಾರಂಭಿಸಿ!",
            "progress_motivation": "ಉತ್ತಮ ಆರಂಭ! ನಿಮ್ಮ ಗುರಿಯನ್ನು ತಲುಪಲು ಕೇವಲ {count} ಬಾಕಿ ಇದೆ.",
            "goal_motivation": "ಗುರಿ ಸಾಧಿಸಲಾಗಿದೆ! ಇಂದು ನೀವು ಕಣ್ಣಿನ ಆರೋಗ್ಯದ ಮೇಲೆ ನೈಜ ಪ್ರಭಾವ ಬೀರುತ್ತಿದ್ದೀರಿ.",
            "no_screenings_title": "ಇನ್ನೂ ಯಾವುದೇ ಸ್ಕ್ರೀನಿಂಗ್ ಇಲ್ಲ",
            "no_screenings_desc": "ರೋಗಿಯ ಮಾಹಿತಿಯನ್ನು ನೋಡಲು ನಿಮ್ಮ ಮೊದಲ AI ಬ್ಲಿಂಕ್ ಟೆಸ್ಟ್ ಪ್ರಾರಂಭಿಸಿ.",
            "start_new_test": "ಹೊಸ ಟೆಸ್ಟ್ ಪ್ರಾರಂಭಿಸಿ",
            "blink_screening_label": "ಬ್ಲಿಂಕ್ ಟೆಸ್ಟ್ ಸ್ಕ್ರೀನಿಂಗ್",
            "blinks_count_label": "{count} ಬ್ಲಿಂಕ್‌ಗಳು",
            "page_label": "ಪುಟ {count}",
            "init_camera": "ಕ್ಯಾಮೆರಾ ಪ್ರಾರಂಭವಾಗುತ್ತಿದೆ..."
        },
        "ml": {
            "start_motivation": "നിങ്ങളുടെ ദൈനംദിന ലക്ഷ്യം ആരംഭിക്കുന്നതിന് നിങ്ങളുടെ ആദ്യ സ്ക്രീനിംഗ് തുടങ്ങുക!",
            "progress_motivation": "മികച്ച തുടക്കം! നിങ്ങളുടെ ലക്ഷ്യത്തിലെത്താൻ ഇനി {count} എണ്ണം കൂടി മാത്രം.",
            "goal_motivation": "ലക്ഷ്യം കൈവരിച്ചു! ഇന്ന് നിങ്ങൾ കണ്ണ് ആരോഗ്യത്തിൽ വലിയ മാറ്റമുണ്ടാക്കുന്നു.",
            "no_screenings_title": "സ്ക്രീനിംഗുകൾ ഒന്നുമില്ല",
            "no_screenings_desc": "രോഗിയുടെ വിവരങ്ങൾ കാണാൻ നിങ്ങളുടെ ആദ്യ AI ബ്ലിങ്ക് ടെസ്റ്റ് ആരംഭിക്കുക.",
            "start_new_test": "പുതിയ ടെസ്റ്റ് തുടങ്ങുക",
            "blink_screening_label": "ബ്ലിങ്ക് ടെസ്റ്റ് സ്ക്രീനിംഗ്",
            "blinks_count_label": "{count} ബ്ലിങ്കുകൾ",
            "page_label": "പേജ് {count}",
            "init_camera": "ക്യാമറ ആരംഭിക്കുന്നു..."
        },
        "bn": {
            "start_motivation": "আপনার দৈনিক লক্ষ্য শুরু করার জন্য আপনার প্রথম স্ক্রিনিং শুরু করুন!",
            "progress_motivation": "চমৎকার শুরু! আপনার লক্ষ্যে পৌঁছাতে কেবল {count}টি বাকি আছে।",
            "goal_motivation": "লক্ষ্য অর্জিত হয়েছে! আজ আপনি চোখের স্বাস্থ্যের ওপর এক প্রকৃত প্রভাব ফেলছেন।",
            "no_screenings_title": "এখনও পর্যন্ত কোন স্ক্রিনিং নেই",
            "no_screenings_desc": "রোগীর তথ্য দেখতে আপনার প্রথম এআই ব্লিংক টেস্ট শুরু করুন।",
            "start_new_test": "নতুন পরীক্ষা শুরু করুন",
            "blink_screening_label": "ব্লিংক টেস্ট স্ক্রিনিং",
            "blinks_count_label": "{count}টি ব্লিংক",
            "page_label": "পৃষ্ঠা {count}",
            "init_camera": "ক্যামেরা চালু হচ্ছে..."
        }
    }
    
    for lang, keys in extra_keys.items():
        if lang in translations:
            translations[lang].update(keys)
            
    translations_js = "const translations = " + json.dumps(translations, ensure_ascii=False, indent=8) + ";"
    content = re.sub(r'const translations = \{.*?\};', translations_js, content, flags=re.DOTALL)

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
