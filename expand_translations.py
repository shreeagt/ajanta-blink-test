import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Expanded blinkAnalysisSet
expanded_blink_set = """
        const blinkAnalysisSet = {
            en: {
                optimal: { tier: 'Optimal', status: 'Highly stable tear film', analysis: 'Your eyes are exceptionally well-lubricated.' },
                excellent: { tier: 'Excellent', status: 'Very healthy moisture retention', analysis: 'You have great tear stability.' },
                healthy: { tier: 'Healthy Average', status: 'Normal tear film function', analysis: 'Ideal range for most healthy adults.' },
                mild: { tier: 'Mild/Borderline', status: 'Possible early moisture evaporation', analysis: 'You may be starting to experience moisture loss.' },
                moderate: { tier: 'Moderate', status: 'Signs of lipid layer disruption', analysis: 'Blinking has increased as tears evaporate faster.' },
                high: { tier: 'High Chance', status: 'Strong signs of screen-dry eye', analysis: 'Strong likelihood of dry eyes. Blinking frequently.' },
                severe: { tier: 'Severe/Chronic', status: 'Highly unstable tear film', analysis: 'Highly unstable tear film. Constant discomfort.' }
            },
            hi: {
                optimal: { tier: 'उत्तम', status: 'अत्यधिक स्थिर अश्रु फिल्म', analysis: 'आपकी आंखें असाधारण रूप से चिकनी हैं।' },
                excellent: { tier: 'उत्कृष्ट', status: 'बहुत स्वस्थ नमी प्रतिधारण', analysis: 'आपकी आंखों में नमी बहुत अच्छी है।' },
                healthy: { tier: 'स्वस्थ औसत', status: 'सामान्य अश्रु फिल्म कार्य', analysis: 'यह अधिकांश स्वस्थ वयस्कों के लिए आदर्श है।' },
                mild: { tier: 'हल्का/सीमावर्ती', status: 'नमी का जल्दी वाष्पीकरण संभव', analysis: 'आप नमी की कमी महसूस करना शुरू कर सकते हैं।' },
                moderate: { tier: 'मध्यम', status: 'लिपिड परत व्यवधान के लक्षण', analysis: 'आंसू जल्दी सूखने से पलकें झपकना बढ़ गया है।' },
                high: { tier: 'उच्च संभावना', status: 'स्क्रीन-ड्राय आई के स्पष्ट लक्षण', analysis: 'इसकी प्रबल संभावना है कि आपकी आंखें शुष्क हैं।' },
                severe: { tier: 'गंभीर/क्रोनिक', status: 'अत्यधिक अस्थिर अश्रु फिल्म', analysis: 'आपकी अश्रु फिल्म अत्यधिक अस्थिर है।' }
            },
            mr: {
                optimal: { tier: 'उत्तम', status: 'अत्यंत स्थिर अश्रू फिल्म', analysis: 'तुमचे डोळे अपवादात्मकरीत्या ओले आहेत.' },
                excellent: { tier: 'उत्कृष्ट', status: 'आरोग्यदायी ओलावा टिकवून ठेवणे', analysis: 'तुमच्या डोळ्यात ओलावा खूप चांगला आहे.' },
                healthy: { tier: 'आरोग्यदायी सरासरी', status: 'सामान्य अश्रू फिल्म कार्य', analysis: 'बहुतेक निरोगी प्रौढांसाठी ही आदर्श श्रेणी आहे.' },
                mild: { tier: 'सौम्य', status: 'ओलावा लवकर कमी होण्याची शक्यता', analysis: 'तुम्हाला ओलावा कमी झाल्याचे जाणवू शकते.' },
                moderate: { tier: 'मध्यम', status: 'लिपिड थरामध्ये व्यत्ययाची चिन्हे', analysis: 'तुमचे डोळे झपकण्याचे प्रमाण वाढले आहे.' },
                high: { tier: 'उच्च शक्यता', status: 'स्क्रीन-ड्राय आयची तीव्र लक्षणे', analysis: 'तुमचे डोळे कोरडे असण्याची दाट शक्यता आहे.' },
                severe: { tier: 'गंभीर', status: 'अत्यंत अस्थिर अश्रू फिल्म', analysis: 'तुमची अश्रू फिल्म अत्यंत अस्थिर आहे.' }
            },
            gu: {
                optimal: { tier: 'ઉત્તમ', status: 'અત્યંત સ્થિર ટીયર ફિલ્મ', analysis: 'તમારી આંખો અસાધારણ રીતે સારી રીતે લ્યુબ્રિકેટેડ છે.' },
                excellent: { tier: 'ઉત્કૃષ્ટ', status: 'ખૂબ સ્વસ્થ ભેજ જાળવણી', analysis: 'તમારી આંખોમાં ભેજ ખૂબ જ સારો છે.' },
                healthy: { tier: 'સ્વસ્થ સરેરાશ', status: 'સામાન્ય ટીયર ફિલ્મ કાર્ય', analysis: 'મોટાભાગના સ્વસ્થ પુખ્ત વયના લોકો માટે આ આદર્શ રેન્જ છે.' },
                mild: { tier: 'હળવું', status: 'ભેજનું વહેલું બાષ્પીભવન શક્ય', analysis: 'તમે ભેજની અછત અનુભવવાનું શરૂ કરી શકો છો.' },
                moderate: { tier: 'મધ્યમ', status: 'લિપિડ સ્તરમાં વિક્ષેપના ચિહ્નો', analysis: 'તમારી આંખો પલકાવવાનું પ્રમાણ વધી ગયું છે.' },
                high: { tier: 'ઉચ્ચ સંભાવના', status: 'સ્ક્રીન-ડ્રાય આઈના મજબૂત ચિહ્નો', analysis: 'તમારી આંખો સૂકી હોવાની પ્રબળ શક્યતા છે.' },
                severe: { tier: 'ગંભીર', status: 'અત્યંત અસ્થિર ટીયર ફિલ્મ', analysis: 'તમારી ટીયર ફિલ્મ અત્યંત અસ્થિર છે.' }
            }
        };
"""

# Expanded translations
expanded_translations = """
    const translations = {
        en: {
            freq: 'Frequency', intens: 'Intensity', never: 'Never', occas: 'Occasionally', often: 'Often',
            moderate: 'Moderate', intense: 'Intense', complete_assessment: 'Complete Assessment',
            cvs_title: 'CVS Screening', symptom_assessment: 'Symptom Assessment',
            cvs_subtitle: 'Please rate symptoms based on digital device use.',
            current_cvs_score: 'Current CVS Score', screening_guide: 'Screening Guide',
            step1_title: 'AI Blink Analysis', step2_title: 'CVS Symptom Check', step3_title: 'Combined Certificate',
            privacy_title: 'Privacy Guaranteed', accept_proceed: 'Start Assessment', analyzing_blinks: 'Analyzing Blinks...',
            assessment_complete_title: 'Assessment Complete', download_cert: 'Download Eye Care Certificate',
            back_home: 'Back to Home', rep_id: 'Representative ID', next_cvs: 'Next: CVS Screening', skip_finish: 'Skip & Finish'
        },
        hi: {
            freq: 'आवृत्ति', intens: 'तीव्रता', never: 'कभी नहीं', occas: 'कभी-कभी', अक्सर: 'अक्सर/हमेशा',
            moderate: 'सामान्य', intense: 'तीव्र', complete_assessment: 'आकलन पूरा करें',
            cvs_title: 'सीवीएस स्क्रीनिंग', symptom_assessment: 'लक्षण आकलन',
            cvs_subtitle: 'डिजिटल डिवाइस उपयोग के आधार पर लक्षणों को रेट करें।',
            current_cvs_score: 'वर्तमान स्कोर', screening_guide: 'स्क्रीनिंग गाइड',
            step1_title: 'एआई ब्लिंक विश्लेषण', step2_title: 'सीवीएस लक्षण जांच', step3_title: 'संयुक्त प्रमाणपत्र',
            privacy_title: 'गोपनीयता की गारंटी', accept_proceed: 'मूल्यांकन शुरू करें',
            assessment_complete_title: 'मूल्यांकन पूर्ण', download_cert: 'सर्टिफिकेट डाउनलोड करें',
            back_home: 'मुख्य पृष्ठ', rep_id: 'प्रतिनिधि आईडी', next_cvs: 'अगला: सीवीएस स्क्रीनिंग', skip_finish: 'छोड़ें और समाप्त करें'
        },
        mr: {
            freq: 'वारंवारता', intens: 'तीव्रता', never: 'कधीही नाही', occas: 'कधीकधी', often: 'नेहमी',
            moderate: 'मध्यम', intense: 'तीव्र', complete_assessment: 'मूल्यांकन पूर्ण करा',
            cvs_title: 'CVS स्क्रीनिंग', symptom_assessment: 'लक्षण मूल्यांकन',
            cvs_subtitle: 'डिजिटल उपकरणांच्या वापराच्या आधारे लक्षणांचे मूल्यांकन करा.',
            current_cvs_score: 'वर्तमान स्कोअर', screening_guide: 'स्क्रीनिंग मार्गदर्शिका',
            step1_title: 'AI ब्लिंक विश्लेषण', step2_title: 'CVS लक्षण तपासणी', step3_title: 'एकत्रित प्रमाणपत्र',
            privacy_title: 'गोपनीयतेची खात्री', accept_proceed: 'मूल्यांकन सुरू करा',
            assessment_complete_title: 'मूल्यांकन पूर्ण', download_cert: 'प्रमाणपत्र डाउनलोड करा',
            back_home: 'मुख्यपृष्ठ', rep_id: 'प्रतिनिधी आयडी', next_cvs: 'पुढील: CVS स्क्रीनिंग', skip_finish: 'वगळा आणि समाप्त करा'
        },
        gu: {
            freq: 'આવૃત્તિ', intens: 'તીવ્રતા', never: 'ક્યારેય નહીં', occas: 'ક્યારેક', often: 'વારંવાર',
            moderate: 'મધ્યમ', intense: 'તીવ્ર', complete_assessment: 'મૂલ્યાંકન પૂર્ણ કરો',
            cvs_title: 'CVS સ્ક્રિનિંગ', symptom_assessment: 'લક્ષણ મૂલ્યાંકન',
            cvs_subtitle: 'ડિજિટલ ઉપકરણના ઉપયોગ પર આધારિત લક્ષણોને રેટ કરો.',
            current_cvs_score: 'વર્તમાન સ્કોર', screening_guide: 'સ્ક્રિનિંગ માર્ગદર્શિકા',
            step1_title: 'AI બ્લિંક વિશ્લેષણ', step2_title: 'CVS લક્ષણ તપાસ', step3_title: 'સંયુક્ત પ્રમાણપત્ર',
            privacy_title: 'ગોપનીયતાની ખાતરી', accept_proceed: 'મૂલ્યાંકન શરૂ કરો',
            assessment_complete_title: 'મૂલ્યાંકન પૂર્ણ', download_cert: 'પ્રમાણપત્ર ડાઉનલોડ કરો',
            back_home: 'મુખ્ય પૃષ્ઠ', rep_id: 'પ્રતિનિધિ ID', next_cvs: 'આગળ: CVS સ્ક્રિનિંગ', skip_finish: 'છોડો અને સમાપ્ત કરો'
        }
    };
"""

# Inject expanded sets
content = re.sub(r'const blinkAnalysisSet = \{.*?\};', expanded_blink_set, content, flags=re.DOTALL)
content = re.sub(r'const translations = \{.*?\};', expanded_translations, content, flags=re.DOTALL)

# Add fallback for missing languages
content = content.replace("const trans = translations[state.lang || 'en'];", "const trans = translations[state.lang] || translations['hi'] || translations['en'];")
content = content.replace("let resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).healthy;", "let resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['hi'] || blinkAnalysisSet['en']).healthy;")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
