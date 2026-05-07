import sys
import re
import json

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

translations = {
    "en": {
        "freq": "Frequency", "intens": "Intensity", "never": "Never", "occas": "Occasionally", "often": "Often",
        "moderate": "Moderate", "intense": "Intense", "complete_assessment": "Complete Assessment",
        "cvs_title": "CVS Screening", "symptom_assessment": "Symptom Assessment",
        "cvs_subtitle": "Please rate symptoms based on digital device use.",
        "current_cvs_score": "Current CVS Score", "screening_guide": "Screening Guide",
        "step1_title": "AI Blink Analysis", "step1_desc": "A 15-second AI scan to detect your natural blink rate and eye lubrication.",
        "step2_title": "CVS Symptom Check", "step2_desc": "Quick assessment to identify Computer Vision Syndrome and digital strain.",
        "step3_title": "Combined Certificate", "step3_desc": "Get a comprehensive medical-grade report with personalized eye care tips.",
        "privacy_title": "Privacy Guaranteed", "privacy_desc": "Video is processed locally on your device. No biometric data is stored.",
        "accept_proceed": "Start Assessment", "analyzing_blinks": "Analyzing Blinks...",
        "assessment_complete_title": "Assessment Complete", "assessment_complete_desc": "Your eye health screening has been securely recorded.",
        "download_cert": "Download Eye Care Certificate", "back_home": "Back to Home",
        "rep_id": "Representative ID", "next_cvs": "Next: CVS Screening", "skip_finish": "Skip & Finish"
    },
    "hi": {
        "freq": "आवृत्ति", "intens": "तीव्रता", "never": "कभी नहीं", "occas": "कभी-कभी", "often": "अक्सर/हमेशा",
        "moderate": "सामान्य", "intense": "तीव्र", "complete_assessment": "आकलन पूरा करें",
        "cvs_title": "सीवीएस स्क्रीनिंग", "symptom_assessment": "लक्षण आकलन",
        "cvs_subtitle": "डिजिटल डिवाइस उपयोग के आधार पर लक्षणों को रेट करें।",
        "current_cvs_score": "वर्तमान स्कोर", "screening_guide": "स्क्रीनिंग गाइड",
        "step1_title": "एआई ब्लिंक विश्लेषण", "step1_desc": "आपकी प्राकृतिक पलक झपकने की दर का पता लगाने के लिए 15 सेकंड का एआई स्कैन।",
        "step2_title": "सीवीएस लक्षण जांच", "step2_desc": "कंप्यूटर विजन सिंड्रोम और डिजिटल तनाव की पहचान करने के लिए त्वरित मूल्यांकन।",
        "step3_title": "संयुक्त प्रमाणपत्र", "step3_desc": "व्यक्तिगत आंखों की देखभाल के सुझावों के साथ एक विस्तृत रिपोर्ट प्राप्त करें।",
        "privacy_title": "गोपनीयता की गारंटी", "privacy_desc": "वीडियो आपके डिवाइस पर स्थानीय रूप से संसाधित होता है। कोई डेटा संग्रहीत नहीं किया जाता है।",
        "accept_proceed": "मूल्यांकन शुरू करें", "analyzing_blinks": "ब्लिंक विश्लेषण हो रहा है...",
        "assessment_complete_title": "मूल्यांकन पूर्ण", "assessment_complete_desc": "आपकी आंखों की जांच सुरक्षित रूप से रिकॉर्ड कर ली गई है।",
        "download_cert": "सर्टिफिकेट डाउनलोड करें", "back_home": "मुख्य पृष्ठ", "rep_id": "प्रतिनिधि आईडी", "next_cvs": "अगला: सीवीएस स्क्रीनिंग", "skip_finish": "छोड़ें और समाप्त करें"
    },
    "as": {
        "freq": "সঘনাই দেখা দিয়ে (Frequency)", "intens": "তীব্ৰতা (Intensity)", "never": "কেতিয়াও নহয়", "occas": "মাজে মাজে", "often": "প্ৰায়েই বা সদায়",
        "moderate": "মধ্যমীয়া", "intense": "তীব্ৰ", "complete_assessment": "মূল্যায়ন সম্পূৰ্ণ কৰক",
        "cvs_title": "কম্পিউটাৰ ভিজন চিনড্ৰম প্ৰশ্নাৱলী (CVS-Q)", "symptom_assessment": "লক্ষণৰ মূল্যায়ন",
        "cvs_subtitle": "ডিজিটেল ডিভাইচ ব্যৱহাৰৰ ওপৰত ভিত্তি কৰি লক্ষণসমূহ মূল্যায়ন কৰক।",
        "current_cvs_score": "বৰ্তমানৰ চিভিএছ স্ক’ৰ", "screening_guide": "স্ক্ৰীনিং নিৰ্দেশিকা",
        "step1_title": "AI ব্লিংক বিশ্লেষণ", "step1_desc": "আপোনাৰ স্বাভাৱিক ব্লিংকৰ হাৰ ধৰা পেলাবলৈ ১৫ ছেকেণ্ডৰ AI স্কেন।",
        "step2_title": "CVS লক্ষণ পৰীক্ষা", "step2_desc": "ডিজিটেল চকুৰ চাপ চিনাক্ত কৰিবলৈ দ্ৰুত মূল্যায়ন।",
        "step3_title": "সংযুক্ত প্ৰমাণপত্ৰ", "step3_desc": "ব্যক্তিগত চকুৰ যতনৰ পৰামৰ্শৰ সৈতে এক প্ৰতিবেদন লাভ কৰক।",
        "privacy_title": "গোপনীয়তা নিশ্চিত", "privacy_desc": "ভিডিঅ’ আপোনাৰ ডিভাইচত স্থানীয়ভাৱে প্ৰক্ৰিয়াকৰণ কৰা হয়।",
        "accept_proceed": "মূল্যায়ন আৰম্ভ কৰক", "analyzing_blinks": "ব্লিংক বিশ্লেষণ কৰি থকা হৈছে...",
        "assessment_complete_title": "মূল্যায়ন সম্পূৰ্ণ হ’ল", "assessment_complete_desc": "আপোনাৰ চকুৰ স্বাস্থ্য পৰীক্ষা সুৰক্ষিতভাৱে ৰেকৰ্ড কৰা হৈছে।",
        "download_cert": "প্ৰমাণপত্ৰ ডাউনলোড কৰক", "back_home": "ঘৰলৈ উভতি যাওক", "rep_id": "প্ৰতিনিধিৰ পৰিচয়", "next_cvs": "পৰৱৰ্তী: CVS স্ক্ৰীনিং", "skip_finish": "এৰিব আৰু শেষ কৰক"
    },
    "mr": {
        "freq": "वारंवारता", "intens": "तीव्रता", "never": "कधीही नाही", "occas": "कधीकधी", "often": "नेहमी",
        "moderate": "मध्यम", "intense": "तीव्र", "complete_assessment": "मूल्यांकन पूर्ण करा",
        "cvs_title": "CVS स्क्रीनिंग", "symptom_assessment": "लक्षण मूल्यांकन",
        "cvs_subtitle": "डिजिटल उपकरणांच्या वापराच्या आधारे लक्षणांचे मूल्यांकन करा.",
        "current_cvs_score": "वर्तमान स्कोअर", "screening_guide": "स्क्रीनिंग मार्गदर्शिका",
        "step1_title": "AI ब्लिंक विश्लेषण", "step1_desc": "तुमच्या नैसर्गिक पापण्या झपकण्याचा दर शोधण्यासाठी १५ सेकंदांचे AI स्कॅन.",
        "step2_title": "CVS लक्षण तपासणी", "step2_desc": "डिजिटल डोळ्यांचा ताण ओळखण्यासाठी त्वरित मूल्यांकन.",
        "step3_title": "एकत्रित प्रमाणपत्र", "step3_desc": "डोळ्यांच्या काळजीच्या टिप्ससह सविस्तर अहवाल मिळवा.",
        "privacy_title": "गोपनीयतेची खात्री", "privacy_desc": "व्हिडिओ तुमच्या डिव्हाइसवर स्थानिक पातळीवर प्रक्रिया केला जातो.",
        "accept_proceed": "मूल्यांकन सुरू करा", "analyzing_blinks": "विश्लेषण करत आहे...",
        "assessment_complete_title": "मूल्यांकन पूर्ण", "assessment_complete_desc": "तुमच्या डोळ्यांची आरोग्य तपासणी यशस्वीरित्या रेकॉर्ड केली आहे.",
        "download_cert": "प्रमाणपत्र डाउनलोड करा", "back_home": "मुख्यपृष्ठ", "rep_id": "प्रतिनिधी आयडी", "next_cvs": "पुढील: CVS स्क्रीनिंग", "skip_finish": "वगळा आणि समाप्त करा"
    },
    "gu": {
        "freq": "આવૃત્તિ", "intens": "તીવ્રતા", "never": "ક્યારેય નહીં", "occas": "ક્યારેક", "often": "વારંવાર",
        "moderate": "મધ્યમ", "intense": "તીવ્ર", "complete_assessment": "મૂલ્યાંકન પૂર્ણ કરો",
        "cvs_title": "CVS સ્ક્રિનિંગ", "symptom_assessment": "લક્ષણ મૂલ્યાંકન",
        "cvs_subtitle": "ડિજિટલ ઉપકરણના ઉપયોગ પર આધારિત લક્ષણોને રેટ કરો.",
        "current_cvs_score": "વર્તમાન સ્કોર", "screening_guide": "સ્ક્રિનિંગ માર્ગદર્શિકા",
        "step1_title": "AI બ્લિંક વિશ્લેષણ", "step1_desc": "તમારા કુદરતી પલક ઝપકાવવાના દરને શોધવા માટે ૧૫ સેકન્ડનું AI સ્કેન.",
        "step2_title": "CVS લક્ષણ તપાસ", "step2_desc": "ડિજિટલ આંખના તાણને ઓળખવા માટે ઝડપી મૂલ્યાંકન.",
        "step3_title": "સંયુક્ત પ્રમાણપત્ર", "step3_desc": "આંખની સંભાળની ટિપ્સ સાથે વિગતવાર અહેવાલ મેળવો.",
        "privacy_title": "ગોપનીયતાની ખાતરી", "privacy_desc": "વિડિયો તમારા ઉપકરણ પર સ્થાનિક રીતે પ્રક્રિયા કરવામાં આવે છે.",
        "accept_proceed": "મૂલ્યાંકન શરૂ કરો", "analyzing_blinks": "વિશ્લેષણ કરી રહ્યા છીએ...",
        "assessment_complete_title": "મૂલ્યાંકન પૂર્ણ", "assessment_complete_desc": "તમારી આંખની તપાસ સફળતાપૂર્વક રેકોર્ડ કરવામાં આવી છે.",
        "download_cert": "પ્રમાણપત્ર ડાઉનલોડ કરો", "back_home": "મુખ્ય પૃષ્ઠ", "rep_id": "પ્રતિનિધિ ID", "next_cvs": "આગળ: CVS સ્ક્રિનિંગ", "skip_finish": "છોડો અને સમાપ્ત કરો"
    },
    "or": {
        "freq": "ବାରମ୍ବାରତା", "intens": "ତୀବ୍ରତା", "never": "କେବେ ନୁହେଁ", "occas": "ବେଳେବେଳେ", "often": "ସବୁବେଳେ",
        "moderate": "ମଧ୍ୟମ", "intense": "ତୀବ୍ର", "complete_assessment": "ମୂଲ୍ୟାଙ୍କନ ଶେଷ କରନ୍ତୁ",
        "cvs_title": "CVS ସ୍କ୍ରିନିଂ", "symptom_assessment": "ଲକ୍ଷଣ ମୂଲ୍ୟାଙ୍କନ",
        "cvs_subtitle": "ଡିଜିଟାଲ୍ ଡିଭାଇସ୍ ବ୍ୟବହାର ଆଧାରରେ ଲକ୍ଷଣଗୁଡ଼ିକର ମୂଲ୍ୟାଙ୍କନ କରନ୍ତୁ |",
        "current_cvs_score": "ସାମ୍ପ୍ରତିକ ସ୍କୋର", "screening_guide": "ସ୍କ୍ରିନିଂ ମାର୍ଗଦର୍ଶିକା",
        "step1_title": "AI ବ୍ଲିଙ୍କ୍ ବିଶ୍ଳେଷଣ", "step1_desc": "ଆପଣଙ୍କର ପ୍ରାକୃତିକ ପଲକ ହାର ଚିହ୍ନଟ କରିବାକୁ ୧୫ ସେକେଣ୍ଡର AI ସ୍କାନ |",
        "step2_title": "CVS ଲକ୍ଷଣ ଯାଞ୍ଚ", "step2_desc": "ଡିଜିଟାଲ୍ ଆଖି ଚାପ ଚିହ୍ନଟ କରିବାକୁ ଶୀଘ୍ର ମୂଲ୍ୟାଙ୍କନ |",
        "step3_title": "ମିଳିତ ପ୍ରମାଣପତ୍ର", "step3_desc": "ଆଖି ଯତ୍ନ ପରାମର୍ଶ ସହିତ ଏକ ବିସ୍ତୃତ ରିପୋର୍ଟ ପାଆନ୍ତୁ |",
        "privacy_title": "ଗୋପନୀୟତା ସୁନିଶ୍ଚିତ", "privacy_desc": "ଭିଡିଓ ଆପଣଙ୍କ ଡିଭାଇସରେ ସ୍ଥାନୀୟ ଭାବରେ ପ୍ରକ୍ରିୟାକରଣ ହୁଏ |",
        "accept_proceed": "ମୂଲ୍ୟାଙ୍କନ ଆରମ୍ଭ କରନ୍ତୁ", "analyzing_blinks": "ବିଶ୍ଳେଷଣ ଚାଲିଛି...",
        "assessment_complete_title": "ମୂଲ୍ୟାଙ୍କନ ସମାପ୍ତ", "assessment_complete_desc": "ଆପଣଙ୍କର ଆଖି ପରୀକ୍ଷା ସଫଳତାର ସହିତ ରେକର୍ଡ କରାଯାଇଛି |",
        "download_cert": "ପ୍ରମାଣପତ୍ର ଡାଉନଲୋଡ୍ କରନ୍ତୁ", "back_home": "ମୁଖ୍ୟ ପୃଷ୍ଠା", "rep_id": "ପ୍ରତିନିଧି ID", "next_cvs": "ପରବର୍ତ୍ତୀ: CVS ସ୍କ୍ରିନିଂ", "skip_finish": "ଛାଡିଦିঅନ୍ତୁ ଏବଂ ଶେଷ କରନ୍ତୁ"
    },
    "te": {
        "freq": "ఫ్రీక్వెన్సీ", "intens": "తీవ్రత", "never": "ఎప్పుడూ కాదు", "occas": "అప్పుడప్పుడు", "often": "తరచుగా",
        "moderate": "మితమైన", "intense": "తీవ్రమైన", "complete_assessment": "మూల్యాంకనం పూర్తి చేయండి",
        "cvs_title": "CVS స్క్రీనింగ్", "symptom_assessment": "లక్షణ మూల్యాంకనం",
        "cvs_subtitle": "డిజిటల్ పరికరాల వినియోగం ఆధారంగా లక్షణాలను రేట్ చేయండి.",
        "current_cvs_score": "ప్రస్తుత స్కోరు", "screening_guide": "స్క్రీనింగ్ గైడ్",
        "step1_title": "AI బ్లింక్ విశ్లేషణ", "step1_desc": "మీ సహజమైన కనురెప్పల రేటును గుర్తించడానికి 15 సెకన్ల AI స్కాన్.",
        "step2_title": "CVS లక్షణ తనిఖీ", "step2_desc": "డిజిటల్ కంటి ఒత్తిడిని గుర్తించడానికి వేగవంతమైన మూల్యాంకనం.",
        "step3_title": "కంబైన్డ్ సర్టిఫికేట్", "step3_desc": "కంటి సంరక్షణ చిట్కాలతో కూడిన వివరణాత్మక నివేదికను పొందండి.",
        "privacy_title": "గోప్యత హామీ", "privacy_desc": "వీడియో మీ పరికరంలో స్థానికంగా ప్రాసెస్ చేయబడుతుంది.",
        "accept_proceed": "మూల్యాంకనాన్ని ప్రారంభించండి", "analyzing_blinks": "విశ్లేషిస్తోంది...",
        "assessment_complete_title": "మూల్యాంకనం పూర్తయింది", "assessment_complete_desc": "మీ కంటి ఆరోగ్య పరీక్ష విజయవంతంగా నమోదు చేయబడింది.",
        "download_cert": "సర్టిఫికేట్ డౌన్‌లోడ్ చేయండి", "back_home": "హోమ్‌కు తిరిగి వెళ్లండి", "rep_id": "ప్రతినిధి ID", "next_cvs": "తదుపరి: CVS స్క్రీనింగ్", "skip_finish": "వదిలేయండి & పూర్తి చేయండి"
    },
    "ta": {
        "freq": "அதிர்வெண்", "intens": "தீவிரம்", "never": "ஒருபோதும் இல்லை", "occas": "அவ்வப்போது", "often": "அடிக்கடி",
        "moderate": "மிதமானது", "intense": "தீவிரமானது", "complete_assessment": "மதிப்பீட்டை முடிக்கவும்",
        "cvs_title": "CVS ஸ்கிரீனிங்", "symptom_assessment": "அறிகுறி மதிப்பீடு",
        "cvs_subtitle": "டிஜிட்டல் சாதனப் பயன்பாட்டின் அடிப்படையில் அறிகுறிகளை மதிப்பிடவும்.",
        "current_cvs_score": "தற்போதைய மதிப்பெண்", "screening_guide": "ஸ்கிரீனிங் வழிகாட்டி",
        "step1_title": "AI கண் சிமிட்டல் பகுப்பாய்வு", "step1_desc": "உங்கள் இயல்பான கண் சிமிட்டல் வீதத்தைக் கண்டறிய 15 வினாடி AI ஸ்கேன்.",
        "step2_title": "CVS அறிகுறி சரிபார்ப்பு", "step2_desc": "டிஜிட்டல் கண் அழுத்தத்தைக் கண்டறிய விரைவான மதிப்பீடு.",
        "step3_title": "கூட்டுச் சான்றிதழ்", "step3_desc": "கண் பராமரிப்பு உதவிக்குறிப்புகளுடன் விரிவான அறிக்கையைப் பெறுங்கள்.",
        "privacy_title": "தனியுரிமை உறுதி", "privacy_desc": "வீடியோ உங்கள் சாதனத்தில் உள்ளூர் ரீதியாக செயலாக்கப்படுகிறது.",
        "accept_proceed": "மதிப்பீட்டைத் தொடங்கவும்", "analyzing_blinks": "பகுப்பாய்வு செய்கிறது...",
        "assessment_complete_title": "மதிப்பீடு முடிந்தது", "assessment_complete_desc": "உங்கள் கண் சுகாதார பரிசோதனை வெற்றிகரமாக பதிவு செய்யப்பட்டுள்ளது.",
        "download_cert": "சான்றிதழைப் பதிவிறக்கவும்", "back_home": "முகப்புக்குத் திரும்பு", "rep_id": "பிரதிநிதி ஐடி", "next_cvs": "அடுத்து: CVS ஸ்கிரீனிங்", "skip_finish": "தவிர் & முடி"
    },
    "kn": {
        "freq": "ಆವರ್ತನ", "intens": "ತೀವ್ರತೆ", "never": "ಎಂದಿಗೂ ಇಲ್ಲ", "occas": "ಅಪರೂಪಕ್ಕೆ", "often": "ಪದೇ ಪದೇ",
        "moderate": "ಮಧ್ಯಮ", "intense": "ತೀವ್ರ", "complete_assessment": "ಮೌಲ್ಯಮಾಪನ ಪೂರ್ಣಗೊಳಿಸಿ",
        "cvs_title": "CVS ಸ್ಕ್ರೀನಿಂಗ್", "symptom_assessment": "ಲಕ್ಷಣ ಮೌಲ್ಯಮಾಪನ",
        "cvs_subtitle": "ಡಿಜಿಟಲ್ ಸಾಧನ ಬಳಕೆಯ ಆಧಾರದ ಮೇಲೆ ಲಕ್ಷಣಗಳನ್ನು ರೇಟ್ ಮಾಡಿ.",
        "current_cvs_score": "ಪ್ರಸ್ತುತ ಸ್ಕೋರ್", "screening_guide": "ಸ್ಕ್ರೀನಿಂಗ್ ಮಾರ್ಗದರ್ಶಿ",
        "step1_title": "AI ಬ್ಲಿಂಕ್ ವಿಶ್ಲೇಷಣೆ", "step1_desc": "ನಿಮ್ಮ ನೈಸರ್ಗಿಕ ಕಣ್ಣು ಮಿಟುಕಿಸುವ ದರವನ್ನು ಪತ್ತೆಹಚ್ಚಲು 15 ಸೆಕೆಂಡುಗಳ AI ಸ್ಕ್ಯಾನ್.",
        "step2_title": "CVS ಲಕ್ಷಣ ಪರಿಶೀಲನೆ", "step2_desc": "ಡಿಜಿಟಲ್ ಕಣ್ಣಿನ ಒತ್ತಡವನ್ನು ಗುರುತಿಸಲು ತ್ವರಿತ ಮೌಲ್ಯಮಾಪನ.",
        "step3_title": "ಸಂಯೋಜಿತ ಪ್ರಮಾಣಪತ್ರ", "step3_desc": "ಕಣ್ಣಿನ ಆರೈಕೆ ಸಲಹೆಗಳೊಂದಿಗೆ ವಿವರವಾದ ವರದಿಯನ್ನು ಪಡೆಯಿರಿ.",
        "privacy_title": "ಗೌಪ್ಯತೆ ಖಾತರಿ", "privacy_desc": "ವೀಡಿಯೊ ನಿಮ್ಮ ಸಾಧನದಲ್ಲಿ ಸ್ಥಳೀಯವಾಗಿ ಪ್ರಕ್ರಿಯೆಗೊಳಿಸಲ್ಪಡುತ್ತದೆ.",
        "accept_proceed": "ಮೌಲ್ಯಮಾಪನ ಪ್ರಾರಂಭಿಸಿ", "analyzing_blinks": "ವಿಶ್ಲೇಷಿಸಲಾಗುತ್ತಿದೆ...",
        "assessment_complete_title": "ಮೌಲ್ಯಮಾಪನ ಪೂರ್ಣಗೊಂಡಿದೆ", "assessment_complete_desc": "ನಿಮ್ಮ ಕಣ್ಣಿನ ಆರೋಗ್ಯ ತಪಾಸಣೆಯನ್ನು ಯಶಸ್ವಿಯಾಗಿ ದಾಖಲಿಸಲಾಗಿದೆ.",
        "download_cert": "ಪ್ರಮಾಣಪತ್ರ ಡೌನ್‌ಲೋಡ್ ಮಾಡಿ", "back_home": "ಹೋಮ್‌ಗೆ ಹಿಂತಿರುಗಿ", "rep_id": "ಪ್ರತಿನಿಧಿ ID", "next_cvs": "ಮುಂದೆ: CVS ಸ್ಕ್ರೀನಿಂಗ್", "skip_finish": "ಬಿಟ್ಟುಬಿಡಿ ಮತ್ತು ಮುಗಿಸಿ"
    },
    "ml": {
        "freq": "ആവൃത്തി", "intens": "തീവ്രത", "never": "ഒരിക്കലുമില്ല", "occas": "അപൂർവ്വമായി", "often": "പലപ്പോഴും",
        "moderate": "മിതമായ", "intense": "തീവ്രമായ", "complete_assessment": "മൂല്യനിർണ്ണയം പൂർത്തിയാക്കുക",
        "cvs_title": "CVS സ്ക്രീനിംഗ്", "symptom_assessment": "ലക്ഷണങ്ങളുടെ വിലയിരുത്തൽ",
        "cvs_subtitle": "ഡിജിറ്റൽ ഉപകരണങ്ങളുടെ ഉപയോഗത്തെ അടിസ്ഥാനമാക്കി ലക്ഷണങ്ങൾ വിലയിരുത്തുക.",
        "current_cvs_score": "നിലവിലെ സ്കോർ", "screening_guide": "സ്ക്രീനിംഗ് ഗൈഡ്",
        "step1_title": "AI ബ്ലിങ്ക് വിശകലനം", "step1_desc": "നിങ്ങളുടെ സ്വാഭാവിക കണ്ണ് ചിമ്മൽ നിരക്ക് കണ്ടെത്തുന്നതിന് 15 സെക്കൻഡ് AI സ്കാൻ.",
        "step2_title": "CVS ലക്ഷണ പരിശോധന", "step2_desc": "ഡിജിറ്റൽ കണ്ണിന്റെ ആയാസം തിരിച്ചറിയുന്നതിനുള്ള ദ്രുത വിലയിരുത്തൽ.",
        "step3_title": "സംയോജിത സർട്ടിഫിക്കറ്റ്", "step3_desc": "കണ്ണ് സംരക്ഷണ നുറുങ്ങുകൾ അടങ്ങിയ വിശദമായ റിപ്പോർട്ട് നേടുക.",
        "privacy_title": "സ്വകാര്യത ഉറപ്പ്", "privacy_desc": "വീഡിയോ നിങ്ങളുടെ ഉപകരണത്തിൽ പ്രാദേശികമായി പ്രോസസ്സ് ചെയ്യുന്നു.",
        "accept_proceed": "മൂല്യനിർണ്ണയം ആരംഭിക്കുക", "analyzing_blinks": "വിശകലനം ചെയ്യുന്നു...",
        "assessment_complete_title": "മൂല്യനിർണ്ണയം പൂർത്തിയായി", "assessment_complete_desc": "നിങ്ങളുടെ കണ്ണ് ആരോഗ്യ പരിശോധന വിജയകരമായി രേഖപ്പെടുത്തി.",
        "download_cert": "സർട്ടിഫിക്കറ്റ് ഡൗൺಲೋഡ് ചെയ്യുക", "back_home": "ഹോമിലേക്ക് മടങ്ങുക", "rep_id": "പ്രതിനിധി ഐഡി", "next_cvs": "അടുത്തത്: CVS സ്ക്രീനിംഗ്", "skip_finish": "ഒഴിവാക്കി പൂർത്തിയാക്കുക"
    },
    "bn": {
        "freq": "ফ্রিকোয়েন্সি", "intens": "তীব্রতা", "never": "কখনও না", "occas": "মাঝে মাঝে", "often": "প্রায়ই",
        "moderate": "মাঝারি", "intense": "তীব্র", "complete_assessment": "মূল্যায়ন সম্পন্ন করুন",
        "cvs_title": "সিভিএস স্ক্রিনিং", "symptom_assessment": "উপসর্গ মূল্যায়ন",
        "cvs_subtitle": "ডিজিটাল ডিভাইস ব্যবহারের উপর ভিত্তি করে উপসর্গ রেট করুন।",
        "current_cvs_score": "বর্তমান স্কোর", "screening_guide": "স্ক্রিনিং গাইড",
        "step1_title": "এআই ব্লিংক বিশ্লেষণ", "step1_desc": "আপনার স্বাভাবিক ব্লিংকের হার সনাক্ত করতে ১৫ সেকেন্ডের এআই স্ক্যান।",
        "step2_title": "সিভিএস উপসর্গ পরীক্ষা", "step2_desc": "ডিজিটাল চোখের চাপ সনাক্ত করতে দ্রুত মূল্যায়ন।",
        "step3_title": "সম্মিলিত শংসাপত্র", "step3_desc": "চোখের যত্নের টিপস সহ একটি বিস্তারিত প্রতিবেদন পান।",
        "privacy_title": "গোপনীয়তা নিশ্চিত", "privacy_desc": "ভিডিও আপনার ডিভাইসে স্থানীয়ভাবে প্রক্রিয়া করা হয়।",
        "accept_proceed": "মূল্যায়ন শুরু করুন", "analyzing_blinks": "বিশ্লেষণ করা হচ্ছে...",
        "assessment_complete_title": "মূল্যায়ন সম্পন্ন", "assessment_complete_desc": "আপনার চোখের স্বাস্থ্য স্ক্রীনিং সফলভাবে রেকর্ড করা হয়েছে।",
        "download_cert": "শংসাপত্র ডাউনলোড করুন", "back_home": "হোমে ফিরে যান", "rep_id": "প্রতিনিধি আইডি", "next_cvs": "পরবর্তী: সিভিএস স্ক্রিনিং", "skip_finish": "এড়িয়ে যান এবং শেষ করুন"
    }
}

blinkAnalysisSet = {
    "en": {
        "optimal": { "tier": "Optimal", "status": "Highly stable tear film", "analysis": "Your eyes are exceptionally well-lubricated." },
        "excellent": { "tier": "Excellent", "status": "Very healthy moisture retention", "analysis": "You have great tear stability." },
        "healthy": { "tier": "Healthy Average", "status": "Normal tear film function", "analysis": "Ideal range for most healthy adults." },
        "mild": { "tier": "Mild/Borderline", "status": "Possible early moisture evaporation", "analysis": "You may be starting to experience moisture loss." },
        "moderate": { "tier": "Moderate", "status": "Signs of lipid layer disruption", "analysis": "Blinking has increased as tears evaporate faster." },
        "high": { "tier": "High Chance", "status": "Strong signs of screen-dry eye", "analysis": "Strong likelihood of dry eyes. Blinking frequently." },
        "severe": { "tier": "Severe/Chronic", "status": "Highly unstable tear film", "analysis": "Highly unstable tear film. Constant discomfort." }
    },
    "hi": {
        "optimal": { "tier": "उत्तम", "status": "अत्यधिक स्थिर अश्रु फिल्म", "analysis": "आपकी आंखें असाधारण रूप से चिकनी हैं।" },
        "excellent": { "tier": "उत्कृष्ट", "status": "बहुत स्वस्थ नमी प्रतिधारण", "analysis": "आपकी आंखों में नमी बहुत अच्छी है।" },
        "healthy": { "tier": "स्वस्थ औसत", "status": "सामान्य अश्रु फिल्म कार्य", "analysis": "यह अधिकांश स्वस्थ वयस्कों के लिए आदर्श है।" },
        "mild": { "tier": "हल्का/सीमावर्ती", "status": "नमी का जल्दी वाष्पीकरण संभव", "analysis": "आप नमी की कमी महसूस करना शुरू कर सकते हैं।" },
        "moderate": { "tier": "मध्यम", "status": "लिपिड परत व्यवधान के लक्षण", "analysis": "आंसू जल्दी सूखने से पलकें झपकना बढ़ गया है।" },
        "high": { "tier": "उच्च संभावना", "status": "स्क्रीन-ड्राय आई के स्पष्ट लक्षण", "analysis": "इसकी प्रबल संभावना है कि आपकी आंखें शुष्क हैं।" },
        "severe": { "tier": "गंभीर/क्रोनिक", "status": "अत्यधिक अस्थिर अश्रु फिल्म", "analysis": "आपकी अश्रु फिल्म अत्यधिक अस्थिर है।" }
    },
    "as": {
        "optimal": { "tier": "সৰ্বোত্তম", "status": "অত্যন্ত সুস্থ চকুৰ তৰপ", "analysis": "আপোনাৰ চকু দুটা অতি সুন্দৰভাৱে লুব্ৰিকেটেড।" },
        "excellent": { "tier": "উৎকৃষ্ট", "status": "খুব ভাল আৰ্দ্ৰতা ধৰি ৰখা", "analysis": "আপোনাৰ চকুৰ স্থিৰতা অতি ভাল।" },
        "healthy": { "tier": "সুস্থ গড়", "status": "সাধাৰণ চকুৰ তৰপৰ কাৰ্য", "analysis": "অধিকাংশ সুস্থ প্ৰাপ্তবয়স্কৰ বাবে আদৰ্শ পৰিসৰ।" },
        "mild": { "tier": "সামান্য", "status": "আৰ্দ্ৰতা সোনকালে শুকাই যোৱাৰ সম্ভাৱনা", "analysis": "আপুনি আৰ্দ্ৰতাৰ অভাৱ অনুভৱ কৰিবলৈ আৰম্ভ কৰিব পাৰে।" },
        "moderate": { "tier": "মধ্যমীয়া", "status": "লিপিড স্তৰত ব্যাঘাতৰ লক্ষণ", "analysis": "চকুলো সোনকালে শুকাই যোৱাৰ বাবে ব্লিংকৰ হাৰ বৃদ্ধি পাইছে।" },
        "high": { "tier": "উচ্চ সম্ভাৱনা", "status": "স্ক্ৰীণ-ড্ৰাই আইৰ তীব্ৰ লক্ষণ", "analysis": "আপোনাৰ চকু শুকাই যোৱাৰ প্ৰবল সম্ভাৱনা আছে।" },
        "severe": { "tier": "গম্ভীৰ", "status": "অত্যন্ত অস্থিৰ চকুৰ তৰপ", "analysis": "আপোনাৰ চকুৰ তৰপ অত্যন্ত অস্থিৰ।" }
    },
    "mr": {
        "optimal": { "tier": "उत्तम", "status": "अत्यंत स्थिर अश्रू फिल्म", "analysis": "तुमचे डोळे अपवादात्मकरीत्या ओले आहेत." },
        "excellent": { "tier": "उत्कृष्ट", "status": "आरोग्यदायी ओलावा टिकवून ठेवणे", "analysis": "तुमच्या डोळ्यात ओलावा खूप चांगला आहे." },
        "healthy": { "tier": "आरोग्यदायी सरासरी", "status": "सामान्य अश्रू फिल्म कार्य", "analysis": "बहुतेक निरोगी प्रौढांसाठी ही आदर्श श्रेणी आहे." },
        "mild": { "tier": "सौम्य", "status": "ओलावा लवकर कमी होण्याची शक्यता", "analysis": "तुम्हाला ओलावा कमी झाल्याचे जाणवू शकते." },
        "moderate": { "tier": "मध्यम", "status": "लिपिड थरामध्ये व्यत्ययाची चिन्हे", "analysis": "तुमचे डोळे झपकण्याचे प्रमाण वाढले आहे." },
        "high": { "tier": "उच्च शक्यता", "status": "स्क्रीन-ड्राय आयची तीव्र लक्षणे", "analysis": "तुमचे डोळे कोरडे असण्याची दाट शक्यता आहे." },
        "severe": { "tier": "गंभीर", "status": "अत्यंत अस्थिर अश्रू फिल्म", "analysis": "तुमची अश्रू फिल्म अत्यंत अस्थिर आहे." }
    },
    "gu": {
        "optimal": { "tier": "ઉત્તમ", "status": "અત્યંત સ્થિર ટીયર ફિલ્મ", "analysis": "તમારી આંખો અસાધારણ રીતે સારી રીતે લ્યુબ્રિકેટેડ છે." },
        "excellent": { "tier": "ઉત્કૃષ્ટ", "status": "ખૂબ સ્વસ્થ ભેજ જાળવણી", "analysis": "તમારી આંખોમાં ભેજ ખૂબ જ સારો છે." },
        "healthy": { "tier": "સ્વસ્થ સરેરાશ", "status": "સામાન્ય ટીયર ફિલ્મ કાર્ય", "analysis": "મોટાભાગના સ્વસ્થ પુખ્ત વયના લોકો માટે આ આદર્શ રેન્જ છે." },
        "mild": { "tier": "હળવું", "status": "ભેજનું વહેલું બાષ્પીભવન શક્ય", "analysis": "તમે ભેજની અછત અનુભવવાનું શરૂ કરી શકો છો." },
        "moderate": { "tier": "મધ્યમ", "status": "લિપિડ સ્તરમાં વિક્ષેપના ચિહ્નો", "analysis": "તમારી આંખો પલકાવવાનું પ્રમાણ વધી ગયું છે." },
        "high": { "tier": "ઉચ્ચ સંભાવના", "status": "સ્ક્રીન-ડ્રાય આઈના મજબૂત ચિહ્નો", "analysis": "તમારી આંખો સૂકી હોવાની પ્રબળ શક્યતા છે." },
        "severe": { "tier": "গંભીર", "status": "અત્યંત અસ્થિર ટીયર ફિલ્મ", "analysis": "તમારી ટીયર ફિલ્મ અત્યંત અસ્થિર છે." }
    },
    "or": {
        "optimal": { "tier": "ସର୍ବୋତ୍ତମ", "status": "ଅତ୍ୟନ୍ତ ସ୍ଥିର ଅଶ୍ରୁ ଫିଲ୍ମ", "analysis": "ଆପଣଙ୍କ ଆଖି ଅସାଧାରଣ ଭାବରେ ଲୁବ୍ରିକେଟ୍ ହୋଇଛି |" },
        "excellent": { "tier": "ଉତ୍କୃଷ୍ଟ", "status": "ବହୁତ ସୁସ୍ଥ ଆର୍ଦ୍ରତା ରକ୍ଷଣାବେକ୍ଷଣ", "analysis": "ଆପଣଙ୍କର ଆଖିର ସ୍ଥିରତା ବହୁତ ଭଲ |" },
        "healthy": { "tier": "ସୁସ୍ଥ ହାରାହାରି", "status": "ସାଧାରଣ ଅଶ୍ରୁ ଫିଲ୍ମ କାର୍ଯ୍ୟ", "analysis": "ଅଧିକାଂଶ ସୁସ୍ଥ ବୟସ୍କଙ୍କ ପାଇଁ ଆଦର୍ଶ ପରିସର |" },
        "mild": { "tier": "ସାମାନ୍ୟ", "status": "ଆର୍ଦ୍ରତା ଶୀଘ୍ର ବାଷ୍ପୀଭୂତ ହେବାର ସମ୍ଭାବନା", "analysis": "ଆପଣ ଆର୍ଦ୍ରତାର ଅଭାବ ଅନୁଭବ କରିବାକୁ ଆରମ୍ଭ କରିପାରନ୍ତି |" },
        "moderate": { "tier": "ମଧ୍ୟମ", "status": "ଲିପିଡ୍ ସ୍ତରରେ ବ୍ୟାଘାତର ଲକ୍ଷଣ", "analysis": "ଲୁହ ଶୀଘ୍ର ଶୁଖିଯାଉଥିବାରୁ ପଲକ ପକାଇବା ବୃଦ୍ଧି ପାଇଛି |" },
        "high": { "tier": "ଉଚ୍ଚ ସମ୍ଭାବନା", "status": "ସ୍କ୍ରିନ୍-ଡ୍ରାଇ ଆଇର ସ୍ପଷ୍ଟ ଲକ୍ଷଣ", "analysis": "ଆପଣଙ୍କ ଆଖି ଶୁଖିଲା ହେବାର ପ୍ରବଳ ସମ୍ଭାବନା ଅଛି |" },
        "severe": { "tier": "ଗମ୍ଭୀର", "status": "ଅତ୍ୟନ୍ତ ଅସ୍ଥିର ଅଶ୍ରୁ ଫିଲ୍ମ", "analysis": "ଆପଣଙ୍କର ଅଶ୍ରୁ ଫିଲ୍ମ ଅତ୍ୟନ୍ତ ଅସ୍ଥିର |" }
    },
    "te": {
        "optimal": { "tier": "అత్యుత్తమం", "status": "చాలా స్థిరమైన కంటి తడి", "analysis": "మీ కళ్ళు అసాధారణంగా బాగా లూబ్రికేట్ చేయబడ్డాయి." },
        "excellent": { "tier": "అద్భుతం", "status": "చాలా ఆరోగ్యకరమైన తేమను కలిగి ఉండటం", "analysis": "మీ కంటి తడి చాలా బాగుంది." },
        "healthy": { "tier": "ఆరోగ్యకరమైన సగటు", "status": "సాధారణ కంటి తడి పనితీరు", "analysis": "చాలా మంది ఆరోగ్యవంతులైన పెద్దలకు ఇది ఆదర్శవంతమైన పరిధి." },
        "mild": { "tier": "తేలికపాటి", "status": "తేమ త్వరగా ఆవిరైపోయే అవకాశం ఉంది", "analysis": "మీరు తేమ లేకపోవడాన్ని అనుభవించడం ప్రారంభించవచ్చు." },
        "moderate": { "tier": "మితమైన", "status": "లిపిడ్ పొరలో ఆటంకం సంకేతాలు", "analysis": "కన్నీళ్లు త్వరగా ఆవిరైపోవడం వల్ల మీ కనురెప్పల పటుత్వం పెరిగింది." },
        "high": { "tier": "అధిక అవకాశం", "status": "స్క్రీన్-డ్రై ఐ యొక్క బలమైన సంకేతాలు", "analysis": "మీ కళ్ళు పొడిబారడానికి బలమైన అవకాశం ఉంది." },
        "severe": { "tier": "తీవ్రమైన", "status": "చాలా అస్థిరమైన కంటి తడి పొర", "analysis": "మీ కంటి తడి పొర చాలా అస్థిరంగా ఉంది." }
    },
    "ta": {
        "optimal": { "tier": "மிகச்சிறந்தது", "status": "மிகவும் நிலையான கண்ணீர் படலம்", "analysis": "உங்கள் கண்கள் விதிவிலக்காக நன்கு ஈரப்பதத்துடன் உள்ளன." },
        "excellent": { "tier": "அருமை", "status": "மிகவும் ஆரோக்கியமான ஈரப்பதம் தக்கவைப்பு", "analysis": "உங்களுக்கு சிறந்த கண்ணீர் நிலைத்தன்மை உள்ளது." },
        "healthy": { "tier": "ஆரோக்கியமான சராசரி", "status": "சாதாரண கண்ணீர் படல செயல்பாடு", "analysis": "பெரும்பாலான ஆரோக்கியமான பெரியவர்களுக்கு இது சிறந்த வரம்பாகும்." },
        "mild": { "tier": "லேசானது", "status": "ஈரப்பதம் விரைவாக ஆவியாக வாய்ப்புள்ளது", "analysis": "நீங்கள் ஈரப்பதம் இழப்பை அனுபவிக்கத் தொடங்கலாம்." },
        "moderate": { "tier": "மிதமானது", "status": "லிப்பிட் அடுக்கில் இடையூறுக்கான அறிகுறிகள்", "analysis": "கண்ணீர் வேகமாக ஆவியாவதால் உங்கள் கண் சிமிட்டல் அதிகரித்துள்ளது." },
        "high": { "tier": "அதிக வாய்ப்பு", "status": "ஸ்கிரீன்-டிரை ஐ-ன் வலுவான அறிகுறிகள்", "analysis": "கண்கள் வறண்டு போக அதிக வாய்ப்பு உள்ளது." },
        "severe": { "tier": "தீவிரமானது", "status": "மிகவும் நிலையற்ற கண்ணீர் படலம்", "analysis": "உங்கள் கண்ணீர் படலம் மிகவும் நிலையற்றது." }
    },
    "kn": {
        "optimal": { "tier": "ಅತ್ಯುತ್ತಮ", "status": "ಹೆಚ್ಚು ಸ್ಥಿರವಾದ ಕಣ್ಣೀರಿನ ಪದರ", "analysis": "ನಿಮ್ಮ ಕಣ್ಣುಗಳು ಅಸಾಧಾರಣವಾಗಿ ಚೆನ್ನಾಗಿ ತೇವವಾಗಿವೆ." },
        "excellent": { "tier": "ಅದ್ಭುತ", "status": "ಬಹಳ ಆರೋಗ್ಯಕರ ತೇವಾಂಶ ಉಳಿಸಿಕೊಳ್ಳುವಿಕೆ", "analysis": "ನೀವು ಉತ್ತಮ ಕಣ್ಣೀರಿನ ಸ್ಥಿರತೆಯನ್ನು ಹೊಂದಿದ್ದೀರಿ." },
        "healthy": { "tier": "ಆರೋಗ್ಯಕರ ಸರಾಸರಿ", "status": "ಸಾಮಾನ್ಯ ಕಣ್ಣೀರಿನ ಪದರದ ಕಾರ್ಯ", "analysis": "ಹೆಚ್ಚಿನ ಆರೋಗ್ಯವಂತ ವಯಸ್ಕರಿಗೆ ಇದು ಆದರ್ಶ ಶ್ರೇಣಿಯಾಗಿದೆ." },
        "mild": { "tier": "ಸೌಮ್ಯ", "status": "ತೇವಾಂಶವು ಬೇಗನೆ ಆವಿಯಾಗುವ ಸಾಧ್ಯತೆ", "analysis": "ನೀವು ತೇವಾಂಶದ ಕೊರತೆಯನ್ನು ಅನುಭವಿಸಲು ಪ್ರಾರಂಭಿಸಬಹುದು." },
        "moderate": { "tier": "ಮಧ್ಯಮ", "status": "ಲಿಪಿಡ್ ಪದರದಲ್ಲಿ ಅಡಚಣೆಯ ಲಕ್ಷಣಗಳು", "analysis": "ಕಣ್ಣೀರು ಬೇಗನೆ ಆವಿಯಾಗುವುದರಿಂದ ನಿಮ್ಮ ಕಣ್ಣು ಮಿಟುಕಿಸುವುದು ಹೆಚ್ಚಾಗಿದೆ." },
        "high": { "tier": "ಹೆಚ್ಚಿನ ಸಾಧ್ಯತೆ", "status": "ಸ್ಕ್ರೀನ್-ಡ್ರೈ ಐ ನ ಪ್ರಬಲ ಲಕ್ಷಣಗಳು", "analysis": "ನಿಮ್ಮ ಕಣ್ಣುಗಳು ಒಣಗಿರುವ ಪ್ರಬಲ ಸಾಧ್ಯತೆಯಿದೆ." },
        "severe": { "tier": "ತೀವ್ರ", "status": "ಹೆಚ್ಚು ಅಸ್ಥಿರವಾದ ಕಣ್ಣೀರಿನ ಪದರ", "analysis": "ನಿಮ್ಮ ಕಣ್ಣೀರಿನ ಪದರವು ಹೆಚ್ಚು ಅಸ್ಥಿರವಾಗಿದೆ." }
    },
    "ml": {
        "optimal": { "tier": "അത്യുത്തമം", "status": "വളരെ സ്ഥിരതയുള്ള കണ്ണുനീർ പാളി", "analysis": "നിങ്ങളുടെ കണ്ണുകൾ അസാധാരണമായി നന്നായി നനഞ്ഞിരിക്കുന്നു." },
        "excellent": { "tier": "മികച്ചത്", "status": "വളരെ ആരോഗ്യകരമായ ഈർപ്പം നിലനിർത്തൽ", "analysis": "നിങ്ങളുടെ കണ്ണുകളിൽ ഈർപ്പം വളരെ നല്ലതാണ്." },
        "healthy": { "tier": "ആരോഗ്യകരമായ ശരാശരി", "status": "സാധാരണ കണ്ണുനീർ പാളി പ്രവർത്തനം", "analysis": "മിക്ക ആരോഗ്യവാനായ മുതിർന്നവർക്കും ഇത് അനുയോജ്യമായ പരിധിയാണ്." },
        "mild": { "tier": "മിതമായത്", "status": "ഈർപ്പം വേഗത്തിൽ ബാഷ്പീകരിക്കപ്പെടാൻ സാധ്യതയുണ്ട്", "analysis": "ഈർപ്പത്തിന്റെ കുറവ് നിങ്ങൾക്ക് അനുഭവപ്പെടാൻ തുടങ്ങിയേക്കാം." },
        "moderate": { "tier": "ഇടത്തരം", "status": "ലിപിഡ് പാളിയിലെ തടസ്സത്തിന്റെ ലക്ഷണങ്ങൾ", "analysis": "കണ്ണുനീർ വേഗത്തിൽ ബാഷ്പീകരിക്കപ്പെടുന്നതിനാൽ നിങ്ങളുടെ കണ്ണ് ചിമ്മുന്നത് കൂടിയിട്ടുണ്ട്." },
        "high": { "tier": "കൂടുതൽ സാധ്യത", "status": "സ്ക്രീൻ-ഡ്രൈ ഐയുടെ വ്യക്തമായ ലക്ഷണങ്ങൾ", "analysis": "നിങ്ങളുടെ കണ്ണുകൾ വരണ്ടതായിരിക്കാൻ വലിയ സാധ്യതയുണ്ട്." },
        "severe": { "tier": "ഗുരുതരം", "status": "വളരെ അസ്ഥിരമായ കണ്ണുനീർ പാളി", "analysis": "നിങ്ങളുടെ കണ്ണുനീർ പാളി വളരെ അസ്ഥിരമാണ്." }
    },
    "bn": {
        "optimal": { "tier": "সর্বোত্তম", "status": "অত্যন্ত স্থিতিশীল টিয়ার ফিল্ম", "analysis": "আপনার চোখ অসাধারণভাবে লুব্রিকেটেড।" },
        "excellent": { "tier": "চমৎকার", "status": "খুব স্বাস্থ্যকর আর্দ্রতা ধরে রাখা", "analysis": "আপনার চোখের আর্দ্রতা খুব ভাল।" },
        "healthy": { "tier": "স্বাস্থ্যকর গড়", "status": "স্বাভাবিক টিয়ার ফিল্ম ফাংশন", "analysis": "এটি বেশিরভাগ স্বাস্থ্যকর প্রাপ্তবয়স্কদের জন্য আদর্শ পরিসর।" },
        "mild": { "tier": "সামান্য", "status": "আর্দ্রতা দ্রুত বাষ্পীভূত হওয়ার সম্ভাবনা", "analysis": "আপনি আর্দ্রতার অভাব অনুভব করতে শুরু করতে পারেন।" },
        "moderate": { "tier": "মাঝারি", "status": "লিপিড স্তরে ব্যাঘাতের লক্ষণ", "analysis": "আপনার চোখের পলক ফেলার হার বেড়েছে।" },
        "high": { "tier": "উচ্চ সম্ভাবনা", "status": "স্ক্রিন-ড্রাই আই-এর স্পষ্ট লক্ষণ", "analysis": "আপনার চোখ শুষ্ক হওয়ার প্রবল সম্ভাবনা রয়েছে।" },
        "severe": { "tier": "গুরুতর", "status": "অত্যন্ত অস্থির টিয়ার ফিল্ম", "analysis": "আপনার টিয়ার ফিল্ম অত্যন্ত অস্থির।" }
    }
}

# Stringify with nice formatting
translations_js = "const translations = " + json.dumps(translations, ensure_ascii=False, indent=8) + ";"
blink_set_js = "const blinkAnalysisSet = " + json.dumps(blinkAnalysisSet, ensure_ascii=False, indent=8) + ";"

# Replacement
content = re.sub(r'const translations = \{.*?\};', translations_js, content, flags=re.DOTALL)
content = re.sub(r'const blinkAnalysisSet = \{.*?\};', blink_set_js, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
