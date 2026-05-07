import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

missing_trans = """
        te: {
            freq: 'ఫ్రీక్వెన్సీ', intens: 'తీవ్రత', never: 'ఎప్పుడూ కాదు', occas: 'అప్పుడప్పుడు', often: 'తరచుగా',
            moderate: 'మితమైన', intense: 'తీవ్రమైన', complete_assessment: 'మూల్యాంకనం పూర్తి చేయండి',
            cvs_title: 'CVS స్క్రీనింగ్', symptom_assessment: 'లక్షణ మూల్యాంకనం',
            cvs_subtitle: 'డిజిటల్ పరికరాల వినియోగం ఆధారంగా లక్షణాలను రేట్ చేయండి.',
            current_cvs_score: 'ప్రస్తుత స్కోరు', screening_guide: 'స్క్రీనింగ్ గైడ్',
            step1_title: 'AI బ్లింక్ విశ్లేషణ', step2_title: 'CVS లక్షణ తనిఖీ', step3_title: 'కంబైన్డ్ సర్టిఫికేట్',
            privacy_title: 'గోప్యత హామీ', accept_proceed: 'మూల్యాంకనాన్ని ప్రారంభించండి', analyzing_blinks: 'బ్లింక్‌లను విశ్లేషిస్తోంది...',
            assessment_complete_title: 'మూల్యాంకనం పూర్తయింది', download_cert: 'సర్టిఫికేట్ డౌన్‌లోడ్ చేయండి',
            back_home: 'హోమ్‌కు తిరిగి వెళ్లండి', rep_id: 'ప్రతినిధి ID', next_cvs: 'తదుపరి: CVS స్క్రీనింగ్', skip_finish: 'వదిలేయండి & పూర్తి చేయండి'
        },
        ta: {
            freq: 'அதிர்வெண்', intens: 'தீவிரம்', never: 'ஒருபோதும் இல்லை', occas: 'அவ்வப்போது', often: 'அடிக்கடி',
            moderate: 'மிதமானது', intense: 'தீவிரமானது', complete_assessment: 'மதிப்பீட்டை முடிக்கவும்',
            cvs_title: 'CVS ஸ்கிரீனிங்', symptom_assessment: 'அறிகுறி மதிப்பீடு',
            cvs_subtitle: 'டிஜிட்டல் சாதனப் பயன்பாட்டின் அடிப்படையில் அறிகுறிகளை மதிப்பிடவும்.',
            current_cvs_score: 'தற்போதைய மதிப்பெண்', screening_guide: 'ஸ்கிரீனிங் வழிகாட்டி',
            step1_title: 'AI கண் சிமிட்டல் பகுப்பாய்வு', step2_title: 'CVS அறிகுறி சரிபார்ப்பு', step3_title: 'கூட்டுச் சான்றிதழ்',
            privacy_title: 'தனியுரிமை உறுதி', accept_proceed: 'மதிப்பீட்டைத் தொடங்கவும்', analyzing_blinks: 'கண் சிமிட்டல்களைப் பகுப்பாய்வு செய்கிறது...',
            assessment_complete_title: 'மதிப்பீடு முடிந்தது', download_cert: 'சான்றிதழைப் பதிவிறக்கவும்',
            back_home: 'முகப்புக்குத் திரும்பு', rep_id: 'பிரதிநிதி ஐடி', next_cvs: 'அடுத்து: CVS ஸ்கிரீனிங்', skip_finish: 'தவிர் & முடி'
        },
        kn: {
            freq: 'ಆವರ್ತನ', intens: 'ತೀವ್ರತೆ', never: 'ಎಂದಿಗೂ ಇಲ್ಲ', occas: 'ಅಪರೂಪಕ್ಕೆ', often: 'ಪದೇ ಪದೇ',
            moderate: 'ಮಧ್ಯಮ', intense: 'ತೀವ್ರ', complete_assessment: 'ಮೌಲ್ಯಮಾಪನ ಪೂರ್ಣಗೊಳಿಸಿ',
            cvs_title: 'CVS ಸ್ಕ್ರೀನಿಂಗ್', symptom_assessment: 'ಲಕ್ಷಣ ಮೌಲ್ಯಮಾಪನ',
            cvs_subtitle: 'ಡಿಜಿಟಲ್ ಸಾಧನ ಬಳಕೆಯ ಆಧಾರದ ಮೇಲೆ ಲಕ್ಷಣಗಳನ್ನು ರೇಟ್ ಮಾಡಿ.',
            current_cvs_score: 'ಪ್ರಸ್ತುತ ಸ್ಕೋರ್', screening_guide: 'ಸ್ಕ್ರೀನಿంగ్ ಮಾರ್ಗದರ್ಶಿ',
            step1_title: 'AI ಬ್ಲಿಂಕ್ ವಿಶ್ಲೇಷಣೆ', step2_title: 'CVS ಲಕ್ಷಣ ಪರಿಶೀಲನೆ', step3_title: 'ಸಂಯೋಜಿತ ಪ್ರಮಾಣಪತ್ರ',
            privacy_title: 'ಗೌಪ್ಯತೆ ಖಾತರಿ', accept_proceed: 'ಮೌಲ್ಯಮಾಪನ ಪ್ರಾರಂಭಿಸಿ', analyzing_blinks: 'ಬ್ಲಿಂಕ್‌ಗಳನ್ನು ವಿಶ್ಲೇಷಿಸಲಾಗುತ್ತಿದೆ...',
            assessment_complete_title: 'ಮೌಲ್ಯಮಾಪನ ಪೂರ್ಣಗೊಂಡಿದೆ', download_cert: 'ಪ್ರಮಾಣಪತ್ರ ಡೌನ್‌ಲೋಡ್ ಮಾಡಿ',
            back_home: 'ಹೋಮ್‌ಗೆ ಹಿಂತಿರುಗಿ', rep_id: 'ಪ್ರತಿನಿಧಿ ID', next_cvs: 'ಮುಂದೆ: CVS ಸ್ಕ್ರೀನಿಂಗ್', skip_finish: 'ಬಿಟ್ಟುಬಿಡಿ ಮತ್ತು ಮುಗಿಸಿ'
        },
        ml: {
            freq: 'ആവൃത്തി', intens: 'തീവ്രത', never: 'ഒരിക്കലുമില്ല', occas: 'അപൂർവ്വമായി', often: 'പലപ്പോഴും',
            moderate: 'മിതമായ', intense: 'തീവ്രമായ', complete_assessment: 'മൂല്യനിർണ്ണയം പൂർത്തിയാക്കുക',
            cvs_title: 'CVS സ്ക്രീനിംഗ്', symptom_assessment: 'ലക്ഷണങ്ങളുടെ വിലയിരുത്തൽ',
            cvs_subtitle: 'ഡിജിറ്റൽ ഉപകരണങ്ങളുടെ ഉപയോഗത്തെ അടിസ്ഥാനമാക്കി ലക്ഷണങ്ങൾ വിലയിരുത്തുക.',
            current_cvs_score: 'നിലവിലെ സ്കോർ', screening_guide: 'സ്ക്രീനിംഗ് ഗൈഡ്',
            step1_title: 'AI ബ്ലിങ്ക് വിശകലനം', step2_title: 'CVS ലക്ഷണ പരിശോധന', step3_title: 'സംയോജിത സർട്ടിഫിക്കറ്റ്',
            privacy_title: 'സ്വകാര്യത ഉറപ്പ്', accept_proceed: 'മൂല്യനിർണ്ണയം ആരംഭിക്കുക', analyzing_blinks: 'വിശകലനം ചെയ്യുന്നു...',
            assessment_complete_title: 'മൂല്യനിർണ്ണയം പൂർത്തിയായി', download_cert: 'സർട്ടിഫിക്കറ്റ് ഡൗൺലോഡ് ചെയ്യുക',
            back_home: 'ഹോമിലേക്ക് മടങ്ങുക', rep_id: 'പ്രതിനിധി ഐഡി', next_cvs: 'അടുത്തത്: CVS സ്ക്രീനിംഗ്', skip_finish: 'ഒഴിവാക്കി പൂർത്തിയാക്കുക'
        }
"""

content = content.replace('gu: {', missing_trans + '        gu: {')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Added Telugu, Tamil, Kannada, and Malayalam translations to the main object.")
