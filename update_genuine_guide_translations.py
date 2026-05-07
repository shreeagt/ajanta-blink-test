import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Update translations object with new "Genuine Guide" content
translations_genuine = """
        const translations = {
            en: {
                freq: 'Frequency', intens: 'Intensity', never: 'Never', occas: 'Occasionally', often: 'Often/Always',
                moderate: 'Moderate', intense: 'Intense', complete_assessment: 'Complete Assessment',
                cvs_title: 'CVS Screening', symptom_assessment: 'Symptom Assessment',
                cvs_subtitle: 'Please rate the following symptoms based on your experience during digital device use.',
                current_cvs_score: 'Current CVS Score',
                screening_guide: 'Screening Guide', blink_analysis_desc: 'Dual-Stage Eye Health Assessment',
                step1_title: 'AI Blink Analysis', step1_desc: 'A 15-second AI scan to detect your natural blink rate and eye lubrication.',
                step2_title: 'CVS Symptom Check', step2_desc: 'Quick assessment to identify Computer Vision Syndrome and digital strain.',
                step3_title: 'Combined Certificate', step3_desc: 'Get a comprehensive medical-grade report with personalized eye care tips.',
                privacy_title: 'Privacy Guaranteed', privacy_desc: 'Video is processed locally on your device. No biometric data is stored.',
                accept_proceed: 'Start Assessment', analyzing_blinks: 'Analyzing Blinks...',
                stare_center: 'Please stare at the center', live_count: 'Live Count', live_status: 'Status'
            },
            hi: {
                freq: 'आवृत्ति', intens: 'तीव्रता', never: 'कभी नहीं', occas: 'कभी-कभी', often: 'अक्सर/हमेशा',
                moderate: 'सामान्य', intense: 'तीव्र', complete_assessment: 'आकलन पूरा करें',
                cvs_title: 'सीवीएस स्क्रीनिंग', symptom_assessment: 'लक्षण आकलन',
                cvs_subtitle: 'कृपया डिजिटल डिवाइस उपयोग के दौरान अपने अनुभव के आधार पर निम्नलिखित लक्षणों को रेट करें।',
                current_cvs_score: 'वर्तमान सीवीएस स्कोर',
                screening_guide: 'स्क्रीनिंग गाइड', blink_analysis_desc: 'दो-चरणीय नेत्र स्वास्थ्य मूल्यांकन',
                step1_title: 'एआई ब्लिंक विश्लेषण', step1_desc: 'आपकी प्राकृतिक पलक झपकने की दर और आंखों के लुब्रिकेशन का पता लगाने के लिए 15-सेकंड का एआई स्कैन।',
                step2_title: 'सीवीएस लक्षण जांच', step2_desc: 'कंप्यूटर विजन सिंड्रोम और डिजिटल तनाव की पहचान करने के लिए त्वरित मूल्यांकन।',
                step3_title: 'संयुक्त प्रमाणपत्र', step3_desc: 'नेत्र देखभाल युक्तियों के साथ एक व्यापक मेडिकल-ग्रेड रिपोर्ट प्राप्त करें।',
                privacy_title: 'गोपनीयता की गारंटी', privacy_desc: 'वीडियो आपके डिवाइस पर स्थानीय रूप से संसाधित किया जाता है। कोई बायोमेट्रिक डेटा संग्रहीत नहीं किया जाता है।',
                accept_proceed: 'मूल्यांकन शुरू करें', analyzing_blinks: 'पलकों का विश्लेषण...',
                stare_center: 'कृपया केंद्र की ओर देखें', live_count: 'लाइव काउंट', live_status: 'स्थिति'
            },
            or: {
                freq: 'ବାରମ୍ବାରତା', intens: 'ତୀବ୍ରତା', never: 'କେବେ ନୁହେଁ', occas: 'ମଝିରେ ମଝିରେ', often: 'ସବୁବେଳେ',
                moderate: 'ମଧ୍ୟମ', intense: 'ଅଧିକ', complete_assessment: 'ଆକଳନ ସମାପ୍ତ କରନ୍ତୁ',
                cvs_title: 'CVS ସ୍କ୍ରିନିଂ', symptom_assessment: 'ଲକ୍ଷଣ ଆକଳନ',
                cvs_subtitle: 'ଡିଜିଟାଲ୍ ଉପକରଣ ବ୍ୟବହାର ସମୟରେ ଆପଣଙ୍କର ଅନୁଭୂତି ଅନୁଯାୟୀ ନିମ୍ନଲିଖିତ ଲକ୍ଷଣଗୁଡିକର ମୂଲ୍ୟାୟନ କରନ୍ତୁ |',
                current_cvs_score: 'ବର୍ତ୍ତମାନର ସ୍କୋର୍',
                screening_guide: 'ସ୍କ୍ରିନିଂ ଗାଇଡ୍', blink_analysis_desc: 'ଦୁଇ-ପର୍ଯ୍ୟାୟ ଚକ୍ଷୁ ସ୍ୱାସ୍ଥ୍ୟ ମୂଲ୍ୟାଙ୍କନ',
                step1_title: 'AI ବ୍ଲିଙ୍କ ବିଶ୍ଳେଷଣ', step1_desc: 'ଆପଣଙ୍କର ପ୍ରାକୃତିକ ପଲକ ପକାଇବା ହାର ଏବଂ ଆଖିର ଓଦାପଣ ଚିହ୍ନଟ କରିବା ପାଇଁ ଏକ ୧୫-ସେକେଣ୍ଡର AI ସ୍କାନ୍ ।',
                step2_title: 'CVS ଲକ୍ଷଣ ଯାଞ୍ଚ', step2_desc: 'କମ୍ପ୍ୟୁଟର ଭିଜନ ସିଣ୍ଡ୍ରୋମ ଏବଂ ଡିଜିଟାଲ୍ ଚାପ ଚିହ୍ନଟ କରିବା ପାଇଁ କ୍ଷିପ୍ର ମୂଲ୍ୟାଙ୍କନ ।',
                step3_title: 'ମିଳିତ ପ୍ରମାଣପତ୍ର', step3_desc: 'ଆଖିର ଯତ୍ନ ପାଇଁ ପରାମର୍ଶ ସହିତ ଏକ ବିସ୍ତୃତ ମେଡିକାଲ୍-ଗ୍ରେଡ୍ ରିପୋର୍ଟ ପାଆନ୍ତୁ ।',
                privacy_title: 'ଗୋପନୀୟତା ନିଶ୍ଚିତ', privacy_desc: 'ଭିଡିଓ ଆପଣଙ୍କ ଡିଭାଇସ୍‌ରେ ସ୍ଥାନୀୟ ଭାବରେ ପ୍ରକ୍ରିୟାକରଣ ହୁଏ | କୌଣସି ବାୟୋମେଟ୍ରିକ୍ ଡାଟା ସଂରକ୍ଷିତ ହୁଏ ନାହିଁ |',
                accept_proceed: 'ମୂଲ୍ୟାଙ୍କନ ଆରମ୍ଭ କରନ୍ତୁ', analyzing_blinks: 'ପଲକ ବିଶ୍ଳେଷଣ ଚାଲିଛି...',
                stare_center: 'ଦୟาକରି କେନ୍ଦ୍ରକୁ ଚାହାଁନ୍ତୁ', live_count: 'ଲାଇଭ୍ ଗଣନା', live_status: 'ସ୍ଥିତି'
            },
            mr: { 
                freq: 'वारंवारता', intens: 'तीव्रता', never: 'कधीही नाही', occas: 'कधीकधी', often: 'नेहमी/सतत', 
                moderate: 'मध्यम', intense: 'तीव्र', complete_assessment: 'मूल्यांकन पूर्ण करा', 
                cvs_title: 'CVS स्क्रीनिंग', symptom_assessment: 'लक्षण मूल्यांकन', 
                cvs_subtitle: 'कृपया डिजिटल उपकरण वापरादरम्यान आपल्या अनुभवावर आधारित खालील लक्षणांचे मूल्यांकन करा.', 
                current_cvs_score: 'वर्तमान स्कोअर',
                screening_guide: 'स्क्रीनिंग मार्गदर्शिका', blink_analysis_desc: 'द्वंद्व-चरणीय डोळे आरोग्य तपासणी',
                step1_title: 'AI ब्लिंक विश्लेषण', step1_desc: 'तुमचा नैसर्गिक पापण्या झपकण्याचा दर तपासण्यासाठी १५-सेकंदांचे AI स्कॅन.',
                step2_title: 'CVS लक्षण तपासणी', step2_desc: 'डिजिटल ताण आणि कॉम्प्युटर व्हिजन सिंड्रोम ओळखण्यासाठी जलद आकलन.',
                step3_title: 'एकत्रित प्रमाणपत्र', step3_desc: 'डोळ्यांच्या काळजीच्या टिप्ससह सर्वसमावेशक वैद्यकीय-दर्जाचा अहवाल मिळवा.',
                privacy_title: 'गोपनीयतेची खात्री', privacy_desc: 'व्हिडिओ तुमच्या डिव्हाइसवर स्थानिक पातळीवर प्रक्रिया केला जातो. कोणताही बायोमेट्रिक डेटा साठवला जात नाही.',
                accept_proceed: 'मूल्यांकन सुरू करा', analyzing_blinks: 'पापण्यांचे विश्लेषण करत आहे...',
                stare_center: 'कृपया मध्यभागी पहा', live_count: 'लाइव्ह मोजणी', live_status: 'स्थिती'
            },
            gu: { 
                freq: 'વારંવારતા', intens: 'તીવ્રતા', never: 'ક્યારેય નહીં', occas: 'ક્યારેક', often: 'હંમેશા/સતત', 
                moderate: 'મધ્યમ', intense: 'તીવ્ર', complete_assessment: 'મૂલ્યાંકન પૂર્ણ કરો', 
                cvs_title: 'CVS સ્ક્રીનિંગ', symptom_assessment: 'લક્ષણ મૂલ્યાંકન', 
                cvs_subtitle: 'કૃપા કરીને ડિજિટલ ઉપકરણના ઉપયોગ દરમિયાન તમારા અનુભવના આધારે નીચેના લક્ષણોને રેટ કરો.', 
                current_cvs_score: 'વર્તમાન સ્કોર',
                screening_guide: 'સ્ક્રીનિંગ માર્ગદર્શિકા', blink_analysis_desc: 'દ્વિ-તબક્કાની આંખના સ્વાસ્થ્યનું મૂલ્યાંકન',
                step1_title: 'AI બ્લિંક વિશ્લેષણ', step1_desc: 'તમારી કુદરતી પલક ઝપકાવવાની ગતિ અને આંખના લ્યુબ્રિકેશનને શોધવા માટે 15-સેકન્ડનું AI સ્કેન.',
                step2_title: 'CVS લક્ષણ તપાસ', step2_desc: 'કોમ્પ્યુટર વિઝન સિન્ડ્રોમ અને ડિજિટલ તણાવને ઓળખવા માટે ઝડપી મૂલ્યાંકન.',
                step3_title: 'સંયુક્ત પ્રમાણપત્ર', step3_desc: 'આંખની સંભાળની ટીપ્સ સાથે વ્યાપક તબીબી-ગ્રેડ રિપોર્ટ મેળવો.',
                privacy_title: 'ગોપનીયતાની ખાતરી', privacy_desc: 'વિડિઓ તમારા ઉપકરણ પર સ્થાનિક રીતે પ્રક્રિયા કરવામાં આવે છે. કોઈ બાયોમેટ્રિક ડેટા સાચવવામાં આવતો નથી.',
                accept_proceed: 'મૂલ્યાંકન શરૂ કરો', analyzing_blinks: 'પલકોનું વિશ્લેષણ કરી રહ્યું છે...',
                stare_center: 'કૃપા કરીને કેન્દ્રમાં જુઓ', live_count: 'લાઇવ ગણતરી', live_status: 'સ્થિતિ'
            }
        };
"""

content = re.sub(r'const translations = \{.*?\};', translations_genuine, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
