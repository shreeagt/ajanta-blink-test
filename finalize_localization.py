import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Update translations object with comprehensive keys for all screens
translations_full = """
        const translations = {
            en: {
                freq: 'Frequency', intens: 'Intensity', never: 'Never', occas: 'Occasionally', often: 'Often/Always',
                moderate: 'Moderate', intense: 'Intense', complete_assessment: 'Complete Assessment',
                cvs_title: 'CVS Screening', symptom_assessment: 'Symptom Assessment',
                cvs_subtitle: 'Please rate the following symptoms based on your experience during digital device use.',
                current_cvs_score: 'Current CVS Score',
                screening_guide: 'Screening Guide', blink_analysis_desc: 'Quick 15-second blink analysis',
                step1_title: 'Position Yourself', step1_desc: 'Stare at the center target on screen.',
                step2_title: 'Natural Blinking', step2_desc: 'Blink naturally as you normally would.',
                step3_title: 'AI Analysis', step3_desc: 'Review your instant medical-grade report.',
                privacy_title: 'Privacy First', privacy_desc: 'Video is processed locally. No data is uploaded or saved.',
                accept_proceed: 'Accept & Proceed', analyzing_blinks: 'Analyzing Blinks...',
                stare_center: 'Please stare at the center', live_count: 'Live Count', live_status: 'Status'
            },
            hi: {
                freq: 'आवृत्ति', intens: 'तीव्रता', never: 'कभी नहीं', occas: 'कभी-कभी', often: 'अक्सर/हमेशा',
                moderate: 'सामान्य', intense: 'तीव्र', complete_assessment: 'आकलन पूरा करें',
                cvs_title: 'सीवीएस स्क्रीनिंग', symptom_assessment: 'लक्षण आकलन',
                cvs_subtitle: 'कृपया डिजिटल डिवाइस उपयोग के दौरान अपने अनुभव के आधार पर निम्नलिखित लक्षणों को रेट करें।',
                current_cvs_score: 'वर्तमान सीवीएस स्कोर',
                screening_guide: 'स्क्रीनिंग गाइड', blink_analysis_desc: 'त्वरित 15-सेकंड ब्लिंक विश्लेषण',
                step1_title: 'स्वयं को व्यवस्थित करें', step1_desc: 'स्क्रीन पर केंद्र लक्ष्य की ओर देखें।',
                step2_title: 'प्राकृतिक पलक झपकना', step2_desc: 'प्राकृतिक रूप से पलकें झपकाएं जैसे आप सामान्य रूप से करते हैं।',
                step3_title: 'एआई विश्लेषण', step3_desc: 'अपनी तत्काल मेडिकल-ग्रेड रिपोर्ट की समीक्षा करें।',
                privacy_title: 'गोपनीयता सर्वोपरि', privacy_desc: 'वीडियो स्थानीय रूप से संसाधित किया जाता है। कोई डेटा अपलोड या सहेजा नहीं जाता है।',
                accept_proceed: 'स्वीकार करें और आगे बढ़ें', analyzing_blinks: 'पलकों का विश्लेषण...',
                stare_center: 'कृपया केंद्र की ओर देखें', live_count: 'लाइव काउंट', live_status: 'स्थिति'
            },
            or: {
                freq: 'ବାରମ୍ବାରତା', intens: 'ତୀବ୍ରତା', never: 'କେବେ ନୁହେଁ', occas: 'ମଝିରେ ମଝିରେ', often: 'ସବୁବେଳେ',
                moderate: 'ମଧ୍ୟମ', intense: 'ଅଧିକ', complete_assessment: 'ଆକଳନ ସମାପ୍ତ କରନ୍ତୁ',
                cvs_title: 'CVS ସ୍କ୍ରିନିଂ', symptom_assessment: 'ଲକ୍ଷଣ ଆକଳନ',
                cvs_subtitle: 'ଡିଜିଟାଲ୍ ଉପକରଣ ବ୍ୟବହାର ସମୟରେ ଆପଣଙ୍କର ଅନୁଭୂତି ଅନୁଯାୟୀ ନିମ୍ନଲିଖିତ ଲକ୍ଷଣଗୁଡିକର ମୂଲ୍ୟାୟନ କରନ୍ତୁ |',
                current_cvs_score: 'ବର୍ତ୍ତମାନର ସ୍କୋର୍',
                screening_guide: 'ସ୍କ୍ରିନିଂ ଗାଇଡ୍', blink_analysis_desc: 'କ୍ଷିପ୍ର ୧୫-ସେକେଣ୍ଡ ବ୍ଲିଙ୍କ ବିଶ୍ଳେଷଣ',
                step1_title: 'ନିଜକୁ ସଠିକ୍ ଭାବେ ରଖନ୍ତୁ', step1_desc: 'ସ୍କ୍ରିନ୍‌ର କେନ୍ଦ୍ର ଲକ୍ଷ୍ୟକୁ ଚାହାଁନ୍ତୁ ।',
                step2_title: 'ପ୍ରାକୃତିକ ପଲକ ପକାଇବା', step2_desc: 'ସାଧାରଣ ପରି ପ୍ରାକୃତିକ ଭାବେ ପଲକ ପକାନ୍ତୁ ।',
                step3_title: 'AI ବିଶ୍ଳେଷଣ', step3_desc: 'ଆପଣଙ୍କର ତୁରନ୍ତ ମେଡିକାଲ୍-ଗ୍ରେଡ୍ ରିପୋର୍ଟ ଦେଖନ୍ତୁ ।',
                privacy_title: 'ଗୋପନୀୟତା ପ୍ରଥମ', privacy_desc: 'ଭିଡିଓ ସ୍ଥାନୀୟ ଭାବରେ ପ୍ରକ୍ରିୟାକରଣ ହୁଏ | କୌଣସି ଡାଟା ଅପଲୋଡ୍ କିମ୍ବା ସେଭ୍ ହୁଏ ନାହିଁ |',
                accept_proceed: 'ଗ୍ରହଣ କରନ୍ତୁ ଏବଂ ଆଗକୁ ବଢନ୍ତୁ', analyzing_blinks: 'ପଲକ ବିଶ୍ଳେଷଣ ଚାଲିଛି...',
                stare_center: 'ଦୟାକରି କେନ୍ଦ୍ରକୁ ଚାହାଁନ୍ତୁ', live_count: 'ଲାଇଭ୍ ଗଣନା', live_status: 'ସ୍ଥିତି'
            },
            mr: { 
                freq: 'वारंवारता', intens: 'तीव्रता', never: 'कधीही नाही', occas: 'कधीकधी', often: 'नेहमी/सतत', 
                moderate: 'मध्यम', intense: 'तीव्र', complete_assessment: 'मूल्यांकन पूर्ण करा', 
                cvs_title: 'CVS स्क्रीनिंग', symptom_assessment: 'लक्षण मूल्यांकन', 
                cvs_subtitle: 'कृपया डिजिटल उपकरण वापरादरम्यान आपल्या अनुभवावर आधारित खालील लक्षणांचे मूल्यांकन करा.', 
                current_cvs_score: 'वर्तमान स्कोअर',
                screening_guide: 'स्क्रीनिंग मार्गदर्शिका', blink_analysis_desc: '१५-सेकंदांचे जलद ब्लिंक विश्लेषण',
                step1_title: 'स्वतःला व्यवस्थित स्थितीत ठेवा', step1_desc: 'स्क्रीनवरील मध्यवर्ती लक्ष्याकडे पहा.',
                step2_title: 'नैसर्गिकरित्या पापण्या उघडा-झाका', step2_desc: 'तुम्ही सहसा जसे करता तसे नैसर्गिकरित्या पापण्या झपकावा.',
                step3_title: 'AI विश्लेषण', step3_desc: 'तुमचा त्वरित वैद्यकीय-दर्जाचा अहवाल पहा.',
                privacy_title: 'गोपनीयता प्रथम', privacy_desc: 'व्हिडिओ स्थानिक पातळीवर प्रक्रिया केला जातो. कोणताही डेटा अपलोड किंवा सेव्ह केला जात नाही.',
                accept_proceed: 'स्वीकारा आणि पुढे जा', analyzing_blinks: 'पापण्यांचे विश्लेषण करत आहे...',
                stare_center: 'कृपया मध्यभागी पहा', live_count: 'लाइव्ह मोजणी', live_status: 'स्थिती'
            },
            gu: { 
                freq: 'વારંવારતા', intens: 'તીવ્રતા', never: 'ક્યારેય નહીં', occas: 'ક્યારેક', often: 'હંમેશા/સતત', 
                moderate: 'મધ્યમ', intense: 'તીવ્ર', complete_assessment: 'મૂલ્યાંકન પૂર્ણ કરો', 
                cvs_title: 'CVS સ્ક્રીનિંગ', symptom_assessment: 'લક્ષણ મૂલ્યાંકન', 
                cvs_subtitle: 'કૃપા કરીને ડિજિટલ ઉપકરણના ઉપયોગ દરમિયાન તમારા અનુભવના આધારે નીચેના લક્ષણોને રેટ કરો.', 
                current_cvs_score: 'વર્તમાન સ્કોર',
                screening_guide: 'સ્ક્રીનિંગ માર્ગદર્શિકા', blink_analysis_desc: '૧૫-સેકન્ડનું ઝડપી બ્લિંક વિશ્લેષણ',
                step1_title: 'તમારી જાતને યોગ્ય રીતે ગોઠવો', step1_desc: 'સ્ક્રીન પર કેન્દ્રના લક્ષ્ય તરફ જુઓ.',
                step2_title: 'કુદરતી પલક ઝપકાવવી', step2_desc: 'તમે સામાન્ય રીતે કરો છો તેમ કુદરતી રીતે પલકો ઝપકાવો.',
                step3_title: 'AI વિશ્લેષણ', step3_desc: 'તમારા ત્વરિત તબીબી-ગ્રેડ રિપોર્ટની સમીક્ષા કરો.',
                privacy_title: 'ગોપનીયતા પ્રથમ', privacy_desc: 'વિડિઓ સ્થાનિક રીતે પ્રક્રિયા કરવામાં આવે છે. કોઈ ડેટા અપલોડ અથવા સાચવવામાં આવતો નથી.',
                accept_proceed: 'સ્વીકારો અને આગળ વધો', analyzing_blinks: 'પલકોનું વિશ્લેષણ કરી રહ્યું છે...',
                stare_center: 'કૃપા કરીને કેન્દ્રમાં જુઓ', live_count: 'લાઇવ ગણતરી', live_status: 'સ્થિતિ'
            }
        };
"""

content = re.sub(r'const translations = \{.*?\};', translations_full, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
