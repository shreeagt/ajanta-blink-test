import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Correctly formatted blinkAnalysisSet with all 10 languages
correct_blink_set = """
        const blinkAnalysisSet = {
            en: { optimal: { tier: 'Optimal', status: 'Highly stable tear film', analysis: 'Your eyes are exceptionally well-lubricated.' }, excellent: { tier: 'Excellent', status: 'Very healthy moisture retention', analysis: 'You have great tear stability.' }, healthy: { tier: 'Healthy Average', status: 'Normal tear film function', analysis: 'Ideal range for most healthy adults.' }, mild: { tier: 'Mild/Borderline', status: 'Possible early moisture evaporation', analysis: 'You may be starting to experience moisture loss.' }, moderate: { tier: 'Moderate', status: 'Signs of lipid layer disruption', analysis: 'Blinking has increased as tears evaporate faster.' }, high: { tier: 'High Chance', status: 'Strong signs of screen-dry eye', analysis: 'Strong likelihood of dry eyes.' }, severe: { tier: 'Severe/Chronic', status: 'Highly unstable tear film', analysis: 'Highly unstable tear film. Constant discomfort.' } },
            hi: { optimal: { tier: 'उत्तम', status: 'अत्यधिक स्थिर अश्रु फिल्म', analysis: 'आपकी आंखें असाधारण रूप से अच्छी तरह से चिकनी हैं।' }, excellent: { tier: 'उत्कृष्ट', status: 'बहुत स्वस्थ नमी प्रतिधारण', analysis: 'आपकी आंखों में नमी बहुत अच्छी है।' }, healthy: { tier: 'स्वस्थ औसत', status: 'सामान्य अश्रु फिल्म कार्य', analysis: 'यह अधिकांश स्वस्थ वयस्कों के लिए आदर्श है।' }, mild: { tier: 'हल्का/सीमावर्ती', status: 'नमी का जल्दी वाष्पीकरण संभव', analysis: 'आप नमी की कमी महसूस करना शुरू कर सकते हैं।' }, moderate: { tier: 'मध्यम', status: 'लिपिड परत व्यवधान के लक्षण', analysis: 'आंसू जल्दी सूखने से पलकें झपकना बढ़ गया है।' }, high: { tier: 'उच्च संभावना', status: 'स्क्रीन-ड्राय आई के स्पष्ट लक्षण', analysis: 'इसकी प्रबल संभावना है कि आपकी आंखें शुष्क हैं।' }, severe: { tier: 'गंभीर/क्रोनिक', status: 'अत्यधिक अस्थिर अश्रु फिल्म', analysis: 'आपकी अश्रु फिल्म अत्यधिक अस्थिर है।' } },
            mr: { optimal: { tier: 'उत्तम', status: 'अत्यंत स्थिर अश्रू फिल्म', analysis: 'तुमचे डोळे अपवादात्मकरीत्या ओले आहेत.' }, excellent: { tier: 'उत्कृष्ट', status: 'आरोग्यदायी ओलावा टिकवून ठेवणे', analysis: 'तुमच्या डोळ्यात ओलावा खूप चांगला आहे.' }, healthy: { tier: 'आरोग्यदायी सरासरी', status: 'सामान्य अश्रू फिल्म कार्य', analysis: 'बहुतेक निरोगी प्रौढांसाठी ही आदर्श श्रेणी आहे.' }, mild: { tier: 'सौम्य', status: 'ओलावा लवकर कमी होण्याची शक्यता', analysis: 'तुम्हाला ओलावा कमी झाल्याचे जाणवू शकते.' }, moderate: { tier: 'मध्यम', status: 'लिपिड थरामध्ये व्यत्ययाची चिन्हे', analysis: 'तुमचे डोळे झपकण्याचे प्रमाण वाढले आहे.' }, high: { tier: 'उच्च शक्यता', status: 'स्क्रीन-ड्राय आयची तीव्र लक्षणे', analysis: 'तुमचे डोळे कोरडे असण्याची दाट शक्यता आहे.' }, severe: { tier: 'गंभीर', status: 'अत्यंत अस्थिर अश्रू फिल्म', analysis: 'तुमची अश्रू फिल्म अत्यंत अस्थिर आहे.' } },
            gu: { optimal: { tier: 'ઉત્તમ', status: 'અત્યંત સ્થિર ટીયર ફિલ્મ', analysis: 'તમારી આંખો અસાધારણ રીતે સારી રીતે લ્યુબ્રિકેટેડ છે.' }, excellent: { tier: 'ઉત્કૃષ્ટ', status: 'ખૂબ સ્વસ્થ ભેજ જાળવણી', analysis: 'તમારી આંખોમાં ભેજ ખૂબ જ સારો છે.' }, healthy: { tier: 'સ્વસ્થ સરેરાશ', status: 'સામાન્ય ટીયર ફિલ્મ કાર્ય', analysis: 'મોટાભાગના સ્વસ્થ પુખ્ત વયના લોકો માટે આ આદર્શ રેન્જ છે.' }, mild: { tier: 'હળવું', status: 'ભેજનું વહેલું બાષ્પીભવન શક્ય', analysis: 'તમે ભેજની અછત અનુભવવાનું શરૂ કરી શકો છો.' }, moderate: { tier: 'મધ્યમ', status: 'લિપિડ સ્તરમાં વિક્ષેપના ચિહ્નો', analysis: 'તમારી આંખો પલકાવવાનું પ્રમાણ વધી ગયું છે.' }, high: { tier: 'ઉચ્ચ સંભાવના', status: 'સ્ક્રીન-ડ્રાય આઈના મજબૂત ચિહ્નો', analysis: 'તમારી આંખો સૂકી હોવાની પ્રબળ શક્યતા છે.' }, severe: { tier: 'ગંભીર', status: 'અત્યંત અસ્થિર ટીયર ફિલ્મ', analysis: 'તમારી ટીયર ફિલ્મ અત્યંત અસ્થિર છે.' } },
            or: { optimal: { tier: 'ଉତ୍ତମ', status: 'ଅତ୍ୟଧିକ ସ୍ଥିର ଲୁହ ସ୍ତର', analysis: 'ଆପଣଙ୍କ ଆଖି ଅସାଧାରଣ ଭାବରେ ଭଲ ଅଛି |' }, excellent: { tier: 'ଉତ୍କୃଷ୍ଟ', status: 'ସୁସ୍ଥ ଆର୍ଦ୍ରତା ଧାରଣ', analysis: 'ଆପଣଙ୍କ ଆଖିରେ ଆର୍ଦ୍ରତା ବହୁତ ଭଲ ଅଛି |' }, healthy: { tier: 'ସୁସ୍ଥ ହାରାହାରି', status: 'ସାଧାରଣ ଲୁହ ସ୍ତର କାର୍ଯ୍ୟ', analysis: 'ଏହା ଅଧିକାଂଶ ସୁସ୍ଥ ବୟସ୍କଙ୍କ ପାଇଁ ଆଦର୍ଶ ପରିସର |' }, mild: { tier: 'ସାମାନ୍ୟ', status: 'ଶୀଘ୍ର ଆର୍ଦ୍ରତା ବାଷ୍ପୀଭବନ ସମ୍ଭବ', analysis: 'ଆପଣ ଆର୍ଦ୍ରତା ଅଭାବ ଅନୁଭବ କରିବା ଆରମ୍ଭ କରିପାରନ୍ତି |' }, moderate: { tier: 'ମଧ୍ୟମ', status: 'ଲିପିଡ ସ୍ତର ବ୍ୟାହତ ହେବାର ଲକ୍ଷଣ', analysis: 'ଆପଣଙ୍କ ଆଖି ପତା ପକାଇବା ବୃଦଧି ପାଇଛି |' }, high: { tier: 'ଉଚ୍ଚ ସମ୍ଭାବନା', status: 'ସ୍କ୍ରିନ୍-ଡ୍ରାଏ ଆଖିର ଦୃଢ଼ ଲକ୍ଷଣ', analysis: 'ଆପଣଙ୍କ ଆଖି ଶୁଖିଲା ହେବାର ପ୍ରବଳ ସମ୍ଭାବନା ଅଛି |' }, severe: { tier: 'ଗୁରୁତର', status: 'ଅତ୍ୟଧିକ ଅସ୍ଥିର ଲୁହ ସ୍ତର', analysis: 'ଆପଣଙ୍କ ଲୁହ ସ୍ତର ଅତ୍ୟଧିକ ଅସ୍ଥିର ଅଟେ |' } },
            te: { optimal: { tier: 'అత్యుత్తమ', status: 'చాలా స్థిరమైన కన్నీటి పొర', analysis: 'మీ కళ్ళు అసాధారణంగా బాగా లూబ్రికేట్ చేయబడ్డాయి.' }, excellent: { tier: 'అద్భుతమైన', status: 'చాలా ఆరోగ్యకరమైన తేమ నిలుపుదల', analysis: 'మీ కళ్లలో తేమ చాలా బాగుంది.' }, healthy: { tier: 'ఆరోగ్యకరమైన సగటు', status: 'సాధారణ కన్నీటి పొర పనితీరు', analysis: 'చాలా మంది ఆరోగ్యవంతులైన పెద్దలకు ఇది ఆదర్శవంతమైన పరిధి.' }, mild: { tier: 'తేలికపాటి', status: 'తేమ త్వరగా ఆవిరైపోయే అవకాశం', analysis: 'మీరు తేమ లేకపోవడాన్ని గమనించడం ప్రారంభించవచ్చు.' }, moderate: { tier: 'మితమైన', status: 'లిపిడ్ పొరలో అంతరాయం సంకేతాలు', analysis: 'కన్నీళ్లు వేగంగా ఆవిరైపోతుండటంతో మీరు కనురెప్పలు వేగంగా వేస్తున్నారు.' }, high: { tier: 'ఎక్కువ అవకాశం', status: 'స్క్రీన్-డ్రై ఐ యొక్క బలమైన సంకేతాలు', analysis: 'మీ కళ్ళు పొడిగా ఉండటానికి బలమైన అవకాశం ఉంది.' }, severe: { tier: 'తీవ్రమైన', status: 'చాలా అస్థిరమైన కన్నీటి పొర', analysis: 'మీ కన్నీటి పొర చాలా అస్థిరంగా ఉంది.' } },
            ta: { optimal: { tier: 'சிறந்தது', status: 'மிகவும் நிலையான கண்ணீர் படலம்', analysis: 'உங்கள் கண்கள் விதிவிலக்காக நன்கு ஈரப்பதமாக உள்ளன.' }, excellent: { tier: 'மிகச் சிறந்தது', status: 'மிகவும் ஆரோக்கியமான ஈரப்பதம் தக்கவைப்பு', analysis: 'உங்கள் கண்களில் ஈரப்பதம் மிகவும் நன்றாக உள்ளது.' }, healthy: { tier: 'ஆரோக்கியமான சராசரி', status: 'சாதாரண கண்ணீர் படல செயல்பாடு', analysis: 'பெரும்பாலான ஆரோக்கியமான பெரியவர்களுக்கு இது சிறந்த வரம்பாகும்.' }, mild: { tier: 'மிதமானது', status: 'ஈரப்பதம் விரைவில் ஆவியாக வாய்ப்பு', analysis: 'நீங்கள் ஈரப்பதம் குறைவதை உணர ஆரம்பிக்கலாம்.' }, moderate: { tier: 'நடுத்தரம்', status: 'லிப்பிட் அடுக்கில் இடையூறுக்கான அறிகுறிகள்', analysis: 'கண்ணீர் விரைவாக ஆவியாவதால் நீங்கள் அடிக்கடி கண் சிமிட்டுகிறீர்கள்.' }, high: { tier: 'அதிக வாய்ப்பு', status: 'ஸ்கிரீன்-டிரை ஐ-ன் வலுவான அறிகுறிகள்', analysis: 'உங்கள் கண்கள் வறண்டு இருக்க அதிக வாய்ப்பு உள்ளது.' }, severe: { tier: 'கடுமையானது', status: 'மிகவும் நிலையற்ற கண்ணீர் படலம்', analysis: 'உங்கள் கண்ணீர் படலம் மிகவும் நிலையற்றது.' } },
            kn: { optimal: { tier: 'ಅತ್ಯುತ್ತಮ', status: 'ಹೆಚ್ಚು ಸ್ಥಿರವಾದ ಕಣ್ಣೀರಿನ ಪದರ', analysis: 'ನಿಮ್ಮ ಕಣ್ಣುಗಳು ಅಸಾಧಾರಣವಾಗಿ ಚೆನ್ನಾಗಿ ತೇವವಾಗಿವೆ.' }, excellent: { tier: 'ಉತ್ಕೃಷ್ಟ', status: 'ಬಹಳ ಆರೋಗ್ಯಕರ ತೇವಾಂಶ ಉಳಿಸಿಕೊಳ್ಳುವಿಕೆ', analysis: 'ನಿಮ್ಮ ಕಣ್ಣುಗಳಲ್ಲಿ ತೇವಾಂಶವು ತುಂಬಾ ಚೆನ್ನಾಗಿದೆ.' }, healthy: { tier: 'ಆರೋಗ್ಯಕರ ಸರಾಸರಿ', status: 'ಸಾಮಾನ್ಯ ಕಣ್ಣೀರಿನ ಪದರ ಕಾರ್ಯ', analysis: 'ಹೆಚ್ಚಿನ ಆರೋಗ್ಯವಂತ ವಯಸ್ಕರಿಗೆ ಇದು ಆದರ್ಶ ಶ್ರೇಣಿಯಾಗಿದೆ.' }, mild: { tier: 'ಸೌಮ್ಯ', status: 'ತೇವಾಂಶವು ಬೇಗನೆ ಆವಿಯಾಗುವ ಸಾಧ್ಯತೆ', analysis: 'ನೀವು ತೇವಾಂಶದ ಕೊರತೆಯನ್ನು ಅನುಭವಿಸಲು ಪ್ರಾರಂಭಿಸಬಹುದು.' }, moderate: { tier: 'ಮಧ್ಯಮ', status: 'ಲಿಪಿಡ್ ಪದರದಲ್ಲಿ ಅಡಚಣೆಯ ಲಕ್ಷಣಗಳು', analysis: 'ಕಣ್ಣೀರು ಬೇಗನೆ ಆವಿಯಾಗುವುದರಿಂದ ನಿಮ್ಮ ಕಣ್ಣು ಮಿಟುಕಿಸುವುದು ಹೆಚ್ಚಾಗಿದೆ.' }, high: { tier: 'ಹೆಚ್ಚಿನ ಸಾಧ್ಯತೆ', status: 'ಸ್ಕ್ರೀನ್-ಡ್ರೈ ಐ ನ ಪ್ರಬಲ ಲಕ್ಷಣಗಳು', analysis: 'ನಿಮ್ಮ ಕಣ್ಣುಗಳು ಒಣಗಿರುವ ಪ್ರಬಲ ಸಾಧ್ಯತೆಯಿದೆ.' }, severe: { tier: 'ತೀವ್ರ', status: 'ಹೆಚ್ಚು ಅಸ್ಥಿರವಾದ ಕಣ್ಣೀರಿನ ಪದರ', analysis: 'ನಿಮ್ಮ ಕಣ್ಣೀರಿನ ಪದರವು ಹೆಚ್ಚು ಅಸ್ಥಿರವಾಗಿದೆ.' } }
        };
"""

# Replace the messy blinkAnalysisSet
content = re.sub(r'const blinkAnalysisSet = \{.*?\};', correct_blink_set, content, flags=re.DOTALL)

# Also fix the translations object which likely has or: block duplicated
translations_fix = """
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
        or: {
            freq: 'ବାରମ୍ବାରତା', intens: 'ତୀବ୍ରତା', never: 'କେବେ ନୁହେଁ', occas: 'ବେଳେବେଳେ', often: 'ସବୁବେଳେ',
            moderate: 'ମଧ୍ୟମ', intense: 'ତୀବ୍ର', complete_assessment: 'ମୂଲ୍ୟାଙ୍କନ ଶେଷ କରନ୍ତୁ',
            cvs_title: 'CVS ସ୍କ୍ରିନିଂ', symptom_assessment: 'ଲକ୍ଷଣ ମୂଲ୍ୟାଙ୍କନ',
            cvs_subtitle: 'ଡିଜିଟାଲ୍ ଡିଭାଇସ୍ ବ୍ୟବହାର ଆଧାରରେ ଲକ୍ଷଣଗୁଡ଼ିକର ମୂଲ୍ୟାଙ୍କନ କରନ୍ତୁ |',
            current_cvs_score: 'ସାମ୍ପ୍ରତିକ ସ୍କୋର', screening_guide: 'ସ୍କ୍ରିନିଂ ମାର୍ଗଦର୍ଶିକା',
            step1_title: 'AI ବ୍ଲିଙ୍କ୍ ବିଶ୍ଳେଷଣ', step2_title: 'CVS ଲକ୍ଷଣ ଯାଞ୍ଚ', step3_title: 'ମିଳିତ ପ୍ରମାଣପତ୍ର',
            privacy_title: 'ଗୋପନୀୟତା ସୁନିଶ୍ଚିତ', accept_proceed: 'ମୂଲ୍ୟାଙ୍କନ ଆରମ୍ଭ କରନ୍ତୁ', analyzing_blinks: 'ବିଶ୍ଳେଷଣ ଚାଲିଛି...',
            assessment_complete_title: 'ମୂଲ୍ୟାଙ୍କନ ସମାପ୍ତ', download_cert: 'ପ୍ରମାଣପତ୍ର ଡାଉନଲୋଡ୍ କରନ୍ତୁ',
            back_home: 'ମୁଖ୍ୟ ପୃଷ୍ଠା', rep_id: 'ପ୍ରତିନିଧି ID', next_cvs: 'ପରବର୍ତ୍ତୀ: CVS ସ୍କ୍ରିନିଂ', skip_finish: 'ଛାଡିଦିଅନ୍ତୁ ଏବଂ ଶେଷ କରନ୍ତୁ'
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
"""
content = re.sub(r'en: \{.*?\}\n\s+gu: \{.*?\}', translations_fix, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed major syntax error in blinkAnalysisSet and cleaned up translations.")
