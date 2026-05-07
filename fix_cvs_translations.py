import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update renderCvsQuestions to use a fallback for translations
content = content.replace('${s[state.lang || "en"]}', '${s[state.lang] || s.en}')

# 2. Add full translations for all 10 languages
translations_patch = """
        const translations = {
            en: {
                freq: 'Frequency', intens: 'Intensity', never: 'Never', occas: 'Occasionally', often: 'Often/Always',
                moderate: 'Moderate', intense: 'Intense', complete_assessment: 'Complete Assessment',
                cvs_title: 'CVS Screening', symptom_assessment: 'Symptom Assessment',
                cvs_subtitle: 'Please rate the following symptoms based on your experience during digital device use.',
                current_cvs_score: 'Current CVS Score'
            },
            hi: {
                freq: 'आवृत्ति', intens: 'तीव्रता', never: 'कभी नहीं', occas: 'कभी-कभी', often: 'अक्सर/हमेशा',
                moderate: 'सामान्य', intense: 'तीव्र', complete_assessment: 'आकलन पूरा करें',
                cvs_title: 'सीवीएस स्क्रीनिंग', symptom_assessment: 'लक्षण आकलन',
                cvs_subtitle: 'कृपया डिजिटल डिवाइस उपयोग के दौरान अपने अनुभव के आधार पर निम्नलिखित लक्षणों को रेट करें।',
                current_cvs_score: 'वर्तमान सीवीएस स्कोर'
            },
            or: {
                freq: 'ବାରମ୍ବାରତା', intens: 'ତୀବ୍ରତା', never: 'କେବେ ନୁହେଁ', occas: 'ମଝିରେ ମଝિରେ', often: 'ସବୁବେଳେ',
                moderate: 'ମଧ୍ୟମ', intense: 'ଅଧିକ', complete_assessment: 'ଆକଳନ ସମାପ୍ତ କରନ୍ତୁ',
                cvs_title: 'CVS ସ୍କ୍ରିନିଂ', symptom_assessment: 'ଲକ୍ଷଣ ଆକଳନ',
                cvs_subtitle: 'ଡିଜિଟାଲ୍ ଉପକରଣ ବ୍ୟବହାର ସମୟରେ ଆପଣଙ୍କର ଅନુଭୂତି ଅନୁଯାୟୀ ନିମ୍ନଲିଖିତ ଲକ୍ଷଣଗୁଡିକର ମୂଲ୍ୟାୟନ କରନ୍ତୁ |',
                current_cvs_score: 'ବର୍ତ୍ତମାନର ସ୍କୋର୍'
            },
            mr: { freq: 'वारंवारता', intens: 'तीव्रता', never: 'कधीही नाही', occas: 'कधीकधी', often: 'नेहमी/सतत', moderate: 'मध्यम', intense: 'तीव्र', complete_assessment: 'मूल्यांकन पूर्ण करा', cvs_title: 'CVS स्क्रीनिंग', symptom_assessment: 'लक्षण मूल्यांकन', current_cvs_score: 'वर्तमान स्कोअर' },
            gu: { freq: 'વારંવારતા', intens: 'તીવ્રતા', never: 'ક્યારેય નહીં', occas: 'ક્યારેક', often: 'હંમેશા/સતત', moderate: 'મધ્યમ', intense: 'તીવ્ર', complete_assessment: 'મૂલ્યાંકન પૂર્ણ કરો', cvs_title: 'CVS સ્ક્રીનિંગ', symptom_assessment: 'લક્ષણ મૂલ્યાંકન', current_cvs_score: 'વર્તમાન સ્કોર' },
            ta: { freq: 'அதிர்வெண்', intens: 'தீவிரம்', never: 'ஒருபோதும் இல்லை', occas: 'எப்போதாவது', often: 'அடிக்கடி/எப்போதும்', moderate: 'மிதமான', intense: 'தீவிரமான', complete_assessment: 'மதிப்பீட்டை முடிக்கவும்', cvs_title: 'CVS ஸ்கிரீனிங்', symptom_assessment: 'அறிகுறி மதிப்பீடு', current_cvs_score: 'தற்போதைய மதிப்பெண்' },
            te: { freq: 'ఫ్రీక్వెన్సీ', intens: 'తీవ్రత', never: 'ఎప్పుడూ కాదు', occas: 'అప్పుడప్పుడు', often: 'తరచుగా/ఎల్లప్పుడూ', moderate: 'మితమైన', intense: 'తీవ్రమైన', complete_assessment: 'అంచనా పూర్తి చేయండి', cvs_title: 'CVS స్క్రీనింగ్', symptom_assessment: 'లక్షణ అంచనా', current_cvs_score: 'ప్రస్తుత స్కోరు' },
            kn: { freq: 'ಆವರ್ತನ', intens: 'ತೀವ್ರತೆ', never: 'ಎಂದಿಗೂ ಇಲ್ಲ', occas: 'ಕೆಲವೊಮ್ಮೆ', often: 'ಪದೇ ಪದೇ/ಯಾವಾಗಲೂ', moderate: 'ಮಧ್ಯಮ', intense: 'ತೀವ್ರ', complete_assessment: 'ಮೌಲ್ಯಮಾಪನ ಪೂರ್ಣಗೊಳಿಸಿ', cvs_title: 'CVS ಸ್ಕ್ರೀನಿಂಗ್', symptom_assessment: 'ಲಕ್ಷಣ ಮೌಲ್ಯಮಾಪನ', current_cvs_score: 'ಪ್ರಸ್ತುತ ಸ್ಕೋರ್' },
            ml: { freq: 'ആവൃത്തി', intens: 'തീവ്രത', never: 'ഒരിക്കലുമില്ല', occas: 'ഇടയ്ക്കിടെ', often: 'പലപ്പോഴും/എപ്പോഴും', moderate: 'മിതമായ', intense: 'തീവ്രമായ', complete_assessment: 'വിലയിരുത്തൽ പൂർത്തിയാക്കുക', cvs_title: 'CVS സ്ക്രീനിംഗ്', symptom_assessment: 'ലക്ഷണ വിലയിരുത്തൽ', current_cvs_score: 'നിലവിലെ സ്കോർ' },
            bn: { freq: 'ফ্রিকোয়েন্সি', intens: 'তীব্রতা', never: 'কখনই না', occas: 'মাঝে মাঝে', often: 'প্রায়ই/সবসময়', moderate: 'মাঝারি', intense: 'তীব্র', complete_assessment: 'মূল্যায়ন সম্পন্ন করুন', cvs_title: 'CVS স্ক্রীনিং', symptom_assessment: 'লক্ষণ মূল্যায়ন', current_cvs_score: 'বর্তমান স্কোর' }
        };
"""

content = re.sub(r'const translations = \{.*?\};', translations_patch, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
