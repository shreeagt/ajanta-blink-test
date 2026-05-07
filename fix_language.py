import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add Language Selection Screen
language_html = """
    <!-- Language Selection Screen -->
    <div id="scr-language" class="screen" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); display: flex; flex-direction: column; justify-content: center; padding: 20px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="display: inline-block; padding: 10px 24px; background: white; border-radius: 50px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); margin-bottom: 25px; border: 1px solid #f1f5f9;">
                <span style="font-size: 14px; font-weight: 900; color: var(--primary); letter-spacing: 2px; text-transform: uppercase;">Ajanta Eye Care</span>
            </div>
            <h1 style="font-size: 32px; font-weight: 900; color: #1e293b; letter-spacing: -1px; margin-bottom: 10px;">Choose Language</h1>
            <p style="font-size: 16px; color: #64748b; font-weight: 600;">Select your preferred language to continue</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px; max-width: 400px; margin: 0 auto; width: 100%;">
            <div onclick="selectLanguage('en')" style="background: white; padding: 24px; border-radius: 24px; border: 2px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.05); cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0;">English</h3>
                    <p style="font-size: 12px; color: #94a3b8; font-weight: 600; margin: 5px 0 0;">International Standard</p>
                </div>
                <div style="width: 32px; height: 32px; background: #eff6ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary);"><i class="fas fa-chevron-right"></i></div>
            </div>
            <div onclick="selectLanguage('hi')" style="background: white; padding: 24px; border-radius: 24px; border: 2px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.05); cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0;">हिंदी</h3>
                    <p style="font-size: 12px; color: #94a3b8; font-weight: 600; margin: 5px 0 0;">Hindi Language</p>
                </div>
                <div style="width: 32px; height: 32px; background: #fff7ed; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #f59e0b;"><i class="fas fa-chevron-right"></i></div>
            </div>
            <div onclick="selectLanguage('or')" style="background: white; padding: 24px; border-radius: 24px; border: 2px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.05); cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0;">ଓଡ଼ିଆ</h3>
                    <p style="font-size: 12px; color: #94a3b8; font-weight: 600; margin: 5px 0 0;">Odia Language</p>
                </div>
                <div style="width: 32px; height: 32px; background: #f0fdf4; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #10b981;"><i class="fas fa-chevron-right"></i></div>
            </div>
        </div>
    </div>
"""

if 'id="scr-language"' not in content:
    content = content.replace('<div id="scr-login" class="screen"', language_html + '\n    <div id="scr-login" class="screen"')

# 2. Update navigate and state
content = content.replace("'scr-cvs-result']", "'scr-cvs-result', 'scr-language']")
content = content.replace("historyPage: 0", "historyPage: 0,\n        lang: sessionStorage.getItem('lang') || null")

# 3. Add CVS Translations and logic
cvs_trans_js = r"""
        const cvsSymptoms = [
            { id: 'burning', en: 'Burning', hi: 'जलन', or: 'ପୋଡ଼ାଜଳା' },
            { id: 'itching', en: 'Itching', hi: 'खुजली', or: 'କୁଣ୍ଡାଇ ହେବା' },
            { id: 'foreign_body', en: 'Feeling of a foreign body', hi: 'आंख में कुछ होने का अहसास', or: 'ଆଖିରେ କିଛି ପଡିଲା ପରି ଲାଗିବା' },
            { id: 'tearing', en: 'Tearing', hi: 'आंसू आना', or: 'ଆଖିରୁ ପାଣି ବାହାରିବା' },
            { id: 'blinking', en: 'Excessive blinking', hi: 'पलकें अधिक झपकना', or: 'ବାରମ୍ବାର ଆଖି ପତା ପକାଇବା' },
            { id: 'redness', en: 'Eye redness', hi: 'आंखों का लाल होना', or: 'ଆଖି ନାଲି ପଡିବା' },
            { id: 'pain', en: 'Eye pain', hi: 'आंखों में दर्द', or: 'ଆଖିରେ ଯନ୍ତ୍ରଣା' },
            { id: 'heavy_eyelids', en: 'Heavy eyelids', hi: 'पलकों का भारीपन', or: 'ଆଖି ପତା ଭାରି ଲାଗିବା' },
            { id: 'dryness', en: 'Dryness', hi: 'सूखापन', or: 'ଶୁଖିଲା ପଣ' },
            { id: 'blurred_vision', en: 'Blurred vision', hi: 'धुंधली दृष्टि', or: 'ଝାପ୍ସା ଦେଖାଯିବା' },
            { id: 'double_vision', en: 'Double vision', hi: 'दोहरा दिखाई देना', or: 'ଦୁଇଟି ଦୁଇଟି ଦେଖାଯିବା' },
            { id: 'near_vision', en: 'Difficulty focusing for near vision', hi: 'पास की दृष्टि पर ध्यान केंद्रित करने में कठिनाई', or: 'ପାଖ ଜିନିଷ ଦେଖିବାରେ ଅସୁବିଧା' },
            { id: 'light_sensitivity', en: 'Increased sensitivity to light', hi: 'प्रकाश के प्रति संवेदनशीलता', or: 'ଆଲୁଅକୁ ଚାହିଁବାରେ ଅସୁବିଧା' },
            { id: 'halos', en: 'Coloured halos around objects', hi: 'वस्तुओं के चारों ओर रंगीन घेरे', or: 'ଜିନିଷ ଚାରିପଟେ ରଙ୍ଗୀନ ବଳୟ ଦେଖାଯିବା' },
            { id: 'worsening_eyesight', en: 'Feeling that eyesight is worsening', hi: 'दृष्टि खराब होने का अहसास', or: 'ଦୃଷ୍ଟି ଶକ୍ତି କମିଲା ପରି ଲାଗିବା' },
            { id: 'headache', en: 'Headache', hi: 'सिरदर्द', or: 'ମୁଣ୍ଡ ବିନ୍ଧା' }
        ];

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
            }
        };

        function updateTranslations() {
            const lang = state.lang || 'en';
            const trans = translations[lang];
            if(!trans) return;

            document.querySelectorAll('[data-t]').forEach(el => {
                const key = el.getAttribute('data-t');
                if(trans[key]) el.innerText = trans[key];
            });
        }

        window.selectLanguage = function(lang) {
            state.lang = lang;
            sessionStorage.setItem('lang', lang);
            updateTranslations();
            
            if (state.isPatientMode) {
                navigate('scr-disclaimer');
            } else if(state.isLoggedIn) {
                navigate('scr-dashboard');
            } else {
                navigate('scr-login');
            }
        };
"""

content = re.sub(r'const cvsSymptoms = \[.*?\];', cvs_trans_js, content, flags=re.DOTALL)
content = content.replace('${s.name}', '${s[state.lang || "en"]}')

# 4. Update DOMContentLoaded
content = content.replace('if (state.isPatientMode)', 'if (!state.lang) {\n            navigate("scr-language");\n        } else if (state.isPatientMode)')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
