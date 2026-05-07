import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update the Header to be more premium and include a prominent Globe
header_new = """
        <div style="display:flex; gap:12px; align-items:center;">
            <div onclick="openLanguageModal()" id="globe-btn" style="color: var(--primary); cursor: pointer; font-size: 20px; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: white; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,94,184,0.1); border: 1.5px solid #eff6ff; transition: 0.3s;">
                <i class="fas fa-globe"></i>
            </div>
"""

content = re.sub(r'<div style="display:flex; gap:15px; align-items:center;">\s+<div onclick="openLanguageModal\(\)".*?</div>', header_new, content, flags=re.DOTALL)

# 2. Update the full translations object
translations_new = """
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
                freq: 'ବାରମ୍ବାରତା', intens: 'ତୀବ୍ରତା', never: 'କେବେ ନୁହେଁ', occas: 'ମଝିରେ ମଝିରେ', often: 'ସବୁବେଳେ',
                moderate: 'ମଧ୍ୟମ', intense: 'ଅଧିକ', complete_assessment: 'ଆକଳନ ସମାପ୍ତ କରନ୍ତୁ',
                cvs_title: 'CVS ସ୍କ୍ରିନିଂ', symptom_assessment: 'ଲକ୍ଷଣ ଆକଳନ',
                cvs_subtitle: 'ଡିଜିଟାଲ୍ ଉପକରଣ ବ୍ୟବହାର ସମୟରେ ଆପଣଙ୍କର ଅନୁଭୂତି ଅନୁଯାୟୀ ନିମ୍ନଲିଖିତ ଲକ୍ଷଣଗୁଡିକର ମୂଲ୍ୟାୟନ କରନ୍ତୁ |',
                current_cvs_score: 'ବର୍ତ୍ତମାନର ସ୍କୋର୍'
            },
            mr: { 
                freq: 'वारंवारता', intens: 'तीव्रता', never: 'कधीही नाही', occas: 'कधीकधी', often: 'नेहमी/सतत', 
                moderate: 'मध्यम', intense: 'तीव्र', complete_assessment: 'मूल्यांकन पूर्ण करा', 
                cvs_title: 'CVS स्क्रीनिंग', symptom_assessment: 'लक्षण मूल्यांकन', 
                cvs_subtitle: 'कृपया डिजिटल उपकरण वापरादरम्यान आपल्या अनुभवावर आधारित खालील लक्षणांचे मूल्यांकन करा.', 
                current_cvs_score: 'वर्तमान स्कोअर' 
            },
            gu: { 
                freq: 'વારંવારતા', intens: 'તીવ્રતા', never: 'ક્યારેય નહીં', occas: 'ક્યારેક', often: 'હંમેશા/સતત', 
                moderate: 'મધ્યમ', intense: 'તીવ્ર', complete_assessment: 'મૂલ્યાંકન પૂર્ણ કરો', 
                cvs_title: 'CVS સ્ક્રીનિંગ', symptom_assessment: 'લક્ષણ મૂલ્યાંકન', 
                cvs_subtitle: 'કૃપા કરીને ડિજિટલ ઉપકરણના ઉપયોગ દરમિયાન તમારા અનુભવના આધારે નીચેના લક્ષણોને રેટ કરો.', 
                current_cvs_score: 'વર્તમાન સ્કોર' 
            },
            ta: { freq: 'அதிர்வெண்', intens: 'தீவிரம்', never: 'ஒருபோதும் இல்லை', occas: 'எப்போதாவது', often: 'அடிக்கடி/எப்போதும்', moderate: 'மிதமான', intense: 'தீவிரமான', complete_assessment: 'மதிப்பீட்டை முடிக்கவும்', cvs_title: 'CVS ஸ்கிரீனிங்', symptom_assessment: 'அறிகுறி மதிப்பீடு', cvs_subtitle: 'டிஜிட்டல் சாதனத்தைப் பயன்படுத்தும் போது உங்கள் அனுபவத்தின் அடிப்படையில் பின்வரும் அறிகுறிகளை மதிப்பிடவும்.', current_cvs_score: 'தற்போதைய மதிப்பெண்' },
            te: { freq: 'ఫ్రీక్వెన్సీ', intens: 'తీవ్రత', never: 'ఎప్పుడూ కాదు', occas: 'అప్పుడప్పుడు', often: 'తరచుగా/ఎల్లప్పుడూ', moderate: 'మితమైన', intense: 'తీవ్రమైన', complete_assessment: 'అంచనా పూర్తి చేయండి', cvs_title: 'CVS స్క్రీనింగ్', symptom_assessment: 'లక్షణ అంచనా', cvs_subtitle: 'డిజిటల్ పరికరాన్ని ఉపయోగిస్తున్నప్పుడు మీ అనుభవం ఆధారంగా కింది లక్షణాలను రేట్ చేయండి.', current_cvs_score: 'ప్రస్తుత స్కోరు' },
            kn: { freq: 'ಆವರ್ತನ', intens: 'ತೀವ್ರತೆ', never: 'ಎಂದಿಗೂ ಇಲ್ಲ', occas: 'ಕೆಲವೊಮ್ಮೆ', often: 'ಪದೇ ಪದೇ/ಯಾವಾಗಲೂ', moderate: 'ಮಧ್ಯಮ', intense: 'ತೀವ್ರ', complete_assessment: 'ಮೌಲ್ಯಮಾಪನ ಪೂರ್ಣಗೊಳಿಸಿ', cvs_title: 'CVS ಸ್ಕ್ರೀನಿಂಗ್', symptom_assessment: 'ಲಕ್ಷಣ ಮೌಲ್ಯಮಾಪನ', cvs_subtitle: 'ಡಿಜಿಟಲ್ ಸಾಧನವನ್ನು ಬಳಸುವಾಗ ನಿಮ್ಮ ಅನುಭವದ ಆಧಾರದ ಮೇಲೆ ಈ ಕೆಳಗಿನ ಲಕ್ಷಣಗಳನ್ನು ರೇಟ್ ಮಾಡಿ.', current_cvs_score: 'ಪ್ರಸ್ತುत ಸ್ಕೋರ್' },
            ml: { freq: 'ആവൃത്തി', intens: 'തീവ്രത', never: 'ഒരിക്കലുമില്ല', occas: 'ഇടയ്ക്കിടെ', often: 'പലപ്പോഴും/എപ്പോഴും', moderate: 'മിതമായ', intense: 'തീവ്രമായ', complete_assessment: 'വിലയിருത്തൽ പൂർത്തിയാക്കുക', cvs_title: 'CVS സ്ക്രീനിംഗ്', symptom_assessment: 'ലക്ഷണ വിലയിരുത്തൽ', cvs_subtitle: 'ഡിജിറ്റൽ ഉപകരണം ഉപയോഗിക്കുമ്പോഴുള്ള നിങ്ങളുടെ അനുഭവത്തെ അടിസ്ഥാനമാക്കി താഴെ പറയുന്ന ലക്ഷണങ്ങൾ വിലയിരുത്തുക.', current_cvs_score: 'നിലവിലെ സ്കോർ' },
            bn: { freq: 'ফ্রিকোয়েন্সি', intens: 'তীব্রতা', never: 'কখনই না', occas: 'মাঝে মাঝে', often: 'প্রায়ই/সবসময়', moderate: 'মাঝারি', intense: 'তীব্র', complete_assessment: 'মূল্যায়ন সম্পন্ন করুন', cvs_title: 'CVS স্ক্রীনিং', symptom_assessment: 'লক্ষণ মূল্যায়ন', cvs_subtitle: 'ডিজিটাল ডিভাইস ব্যবহারের সময় আপনার অভিজ্ঞতার ভিত্তিতে নিম্নলিখিত লক্ষণগুলি রেট করুন।', current_cvs_score: 'বর্তমান স্কোর' }
        };
"""

content = re.sub(r'const translations = \{.*?\};', translations_new, content, flags=re.DOTALL)

# 3. Fix the renderCvsQuestions logic to ensure it uses the fallback properly
# (I'll re-verify it's there)

# 4. Improve DOMContentLoaded logic
content = content.replace("document.addEventListener('DOMContentLoaded', () => {", "document.addEventListener('DOMContentLoaded', () => {\n        updateTranslations(); // Call initially")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
