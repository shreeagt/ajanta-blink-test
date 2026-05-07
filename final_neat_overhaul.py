import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Full Translations
full_translations = """
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
                stare_center: 'Please stare at the center', live_count: 'Live Count', live_status: 'Status',
                assessment_complete_title: 'Assessment Complete', assessment_complete_desc: 'Your eye health screening has been securely recorded.',
                download_cert: 'Download Eye Care Certificate', back_home: 'Back to Home', rep_id: 'Representative ID',
                blink_report_title: 'Blink Analysis Report', your_score: 'YOUR SCORE', cvs_assessment_result: 'CVS Assessment Result',
                total_cvs_score: 'TOTAL CVS SCORE', cvs_diagnosis: 'Diagnosis', blink_per_min: 'blinks / min', reg_code: 'REGISTRATION CODE',
                cert_title: 'Certificate of Digital Eye Health', date: 'Date', representative: 'Representative', 
                report_id: 'Report ID', dry_eye_campaign: 'Dry Eye Awareness Campaign', comparison_scale: 'Blink Rate Comparison Scale',
                verified_by: 'Verified By Representative', share_patient: 'Share with Patient', next_cvs: 'Next: CVS Screening', skip_finish: 'Skip & Finish'
            },
            hi: {
                freq: 'आवृत्ति', intens: 'तीव्रता', never: 'कभी नहीं', occas: 'कभी-कभी', often: 'अक्सर/हमेशा',
                moderate: 'सामान्य', intense: 'तीव्र', complete_assessment: 'आकलन पूरा करें',
                cvs_title: 'सीवीएस स्क्रीनिंग', symptom_assessment: 'लक्षण आकलन',
                cvs_subtitle: 'कृपया डिजिटल डिवाइस उपयोग के दौरान अपने अनुभव के आधार पर निम्नलिखित लक्षणों को रेट करें।',
                current_cvs_score: 'वर्तमान स्कोर',
                screening_guide: 'स्क्रीनिंग गाइड', blink_analysis_desc: 'दो-चरणीय नेत्र स्वास्थ्य मूल्यांकन',
                step1_title: 'एआई ब्लिंक विश्लेषण', step1_desc: 'आपकी प्राकृतिक पलक झपकने की दर और आंखों के लुब्रिकेशन का पता लगाने के लिए 15-सेकंड का एआई स्कैन।',
                step2_title: 'सीवीएस लक्षण जांच', step2_desc: 'कंप्यूटर विजन सिंड्रोम और डिजिटल तनाव की पहचान करने के लिए त्वरित मूल्यांकन।',
                step3_title: 'संयुक्त प्रमाणपत्र', step3_desc: 'नेत्र देखभाल युक्तियों के साथ एक व्यापक मेडिकल-ग्रेड रिपोर्ट प्राप्त करें।',
                privacy_title: 'गोपनीयता की गारंटी', privacy_desc: 'वीडियो आपके डिवाइस पर स्थानीय रूप से संसाधित किया जाता है। कोई बायोमेट्रिक डेटा संग्रहीत नहीं किया जाता है।',
                accept_proceed: 'मूल्यांकन शुरू करें', analyzing_blinks: 'पलकों का विश्लेषण...',
                stare_center: 'कृपया केंद्र की ओर देखें', live_count: 'लाइव काउंट', live_status: 'स्थिति',
                assessment_complete_title: 'मूल्यांकन पूर्ण', assessment_complete_desc: 'आपका नेत्र स्वास्थ्य परीक्षण सुरक्षित रूप से दर्ज कर लिया गया है।',
                download_cert: 'आई केयर सर्टिफिकेट डाउनलोड करें', back_home: 'मुख्य पृष्ठ पर वापस जाएं', rep_id: 'प्रतिनिधि आईडी',
                blink_report_title: 'ब्लिंक विश्लेषण रिपोर्ट', your_score: 'आपका स्कोर', cvs_assessment_result: 'सीवीएस मूल्यांकन परिणाम',
                total_cvs_score: 'कुल सीवीएस स्कोर', cvs_diagnosis: 'निदान', blink_per_min: 'पलकें / मिनट', reg_code: 'पंजीकरण कोड',
                cert_title: 'डिजिटल नेत्र स्वास्थ्य का प्रमाणपत्र', date: 'दिनांक', representative: 'प्रतिनिधि', 
                report_id: 'रिपोर्ट आईडी', dry_eye_campaign: 'शुष्क नेत्र जागरूकता अभियान', comparison_scale: 'ब्लिंक दर तुलना स्केल',
                verified_by: 'प्रतिनिधि द्वारा सत्यापित', share_patient: 'मरीज के साथ साझा करें', next_cvs: 'अगला: सीवीएस स्क्रीनिंग', skip_finish: 'छोड़ें और समाप्त करें'
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
                stare_center: 'कृपया मध्यभागी पहा', live_count: 'लाइव्ह मोजणी', live_status: 'स्थिती',
                assessment_complete_title: 'मूल्यांकन पूर्ण', assessment_complete_desc: 'तुमची डोळे आरोग्य तपासणी सुरक्षितपणे नोंदवली गेली आहे.',
                download_cert: 'आय केअर प्रमाणपत्र डाउनलोड करा', back_home: 'मुख्यपृष्ठावर परत जा', rep_id: 'प्रतिनिधी आयडी',
                blink_report_title: 'ब्लिंक विश्लेषण अहवाल', your_score: 'तुमचा स्कोअर', cvs_assessment_result: 'CVS मूल्यांकन निकाल',
                total_cvs_score: 'एकूण CVS स्कोअर', cvs_diagnosis: 'निदान', blink_per_min: 'पापण्या / मिनिट', reg_code: 'नोंदणी कोड',
                cert_title: 'डिजिटल नेत्र आरोग्याचे प्रमाणपत्र', date: 'दिनांक', representative: 'प्रतिनिधी', 
                report_id: 'अहवाल आयडी', dry_eye_campaign: 'ड्राय आय जागरूकता मोहीम', comparison_scale: 'ब्लिंक रेट तुलना स्केल',
                verified_by: 'प्रतिनिधीद्वारे सत्यापित', share_patient: 'रुग्णासह शेअर करा', next_cvs: 'पुढील: CVS स्क्रीनिंग', skip_finish: 'वगळा आणि समाप्त करा'
            }
        };
"""

content = re.sub(r'const translations = \{.*?\};', full_translations, content, flags=re.DOTALL)

correct_lang_js = """
    window.openLanguageModal = function() {
        document.getElementById('language-modal').style.display = 'flex';
        document.querySelectorAll('.lang-card').forEach(c => c.classList.remove('active'));
        const currentLang = state.lang || 'en';
        const activeCard = document.getElementById(`lang-${currentLang}`);
        if(activeCard) activeCard.classList.add('active');
    };
"""

content = re.sub(r'window\.openLanguageModal = function\(.*?\};', correct_lang_js, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
