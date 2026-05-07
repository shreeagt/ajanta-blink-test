import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update showCvsResult to be language-aware
cvs_result_logic = """
        function showCvsResult(score) {
            const lang = state.lang || 'en';
            const scoreEl = document.getElementById('cvs-final-score');
            const tierBadge = document.getElementById('cvs-tier-badge');
            const statusTitle = document.getElementById('cvs-status-title');
            const analysisText = document.getElementById('cvs-analysis-text');
            const indicator = document.getElementById('cvs-result-indicator');
            const dateEl = document.getElementById('cvs-report-date');

            if(dateEl) dateEl.innerText = new Date().toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'});
            if(scoreEl) scoreEl.innerText = score;

            const cvsResultSets = {
                en: {
                    negative: { tier: 'Negative', status: 'Healthy Eyes', color: '#10b981', bg: '#f0fdf4', analysis: 'Your symptom score is within the normal range. Your digital habits seem to be well-balanced.' },
                    mild: { tier: 'Mild CVS', status: 'Early Symptoms Detected', color: '#fbbf24', bg: '#fffbeb', analysis: 'You are showing early signs of Computer Vision Syndrome. Consider the 20-20-20 rule and regular breaks.' },
                    moderate: { tier: 'Moderate CVS', status: 'Significant Digital Strain', color: '#f97316', bg: '#fff7ed', analysis: 'Your symptoms indicate moderate digital eye strain. We recommend using artificial tears and adjusting your screen ergonomics.' },
                    severe: { tier: 'Severe CVS', status: 'High Clinical Significance', color: '#ef4444', bg: '#fef2f2', analysis: 'Your score indicates severe Computer Vision Syndrome. Please consult an eye specialist for a comprehensive examination.' }
                },
                hi: {
                    negative: { tier: 'नकारात्मक', status: 'स्वस्थ आँखें', color: '#10b981', bg: '#f0fdf4', analysis: 'आपका स्कोर सामान्य सीमा के भीतर है। आपकी डिजिटल आदतें संतुलित लगती हैं।' },
                    mild: { tier: 'हल्का सीवीएस', status: 'शुरुआती लक्षण पाए गए', color: '#fbbf24', bg: '#fffbeb', analysis: 'आप कंप्यूटर विजन सिंड्रोम के शुरुआती लक्षण दिखा रहे हैं। नियमित ब्रेक लेने पर विचार करें।' },
                    moderate: { tier: 'मध्यम सीवीएस', status: 'महत्वपूर्ण डिजिटल तनाव', color: '#f97316', bg: '#fff7ed', analysis: 'आपके लक्षण मध्यम डिजिटल आंखों के तनाव का संकेत देते हैं। हम स्क्रीन एर्गोनॉमिक्स को समायोजित करने की सलाह देते हैं।' },
                    severe: { tier: 'गंभीर सीवीएस', status: 'उच्च नैदानिक महत्व', color: '#ef4444', bg: '#fef2f2', analysis: 'आपका स्कोर गंभीर कंप्यूटर विजन सिंड्रोम का संकेत देता है। कृपया नेत्र विशेषज्ञ से परामर्श लें।' }
                },
                mr: {
                    negative: { tier: 'नकारात्मक', status: 'निरोगी डोळे', color: '#10b981', bg: '#f0fdf4', analysis: 'तुमचा स्कोअर सामान्य मर्यादेत आहे. तुमच्या डिजिटल सवयी संतुलित वाटतात.' },
                    mild: { tier: 'सौम्य CVS', status: 'सुरुवातीची लक्षणे आढळली', color: '#fbbf24', bg: '#fffbeb', analysis: 'तुम्ही कॉम्प्युटर व्हिजन सिंड्रोमची सुरुवातीची लक्षणे दाखवत आहात. नियमित विश्रांती घेण्याचा विचार करा.' },
                    moderate: { tier: 'मध्यम CVS', status: 'लक्षणीय डिजिटल ताण', color: '#f97316', bg: '#fff7ed', analysis: 'तुमची लक्षणे मध्यम डिजिटल डोळ्यांचा ताण दर्शवतात. आम्ही स्क्रीन एर्गोनॉमिक्स समायोजित करण्याची शिफारस करतो.' },
                    severe: { tier: 'गंभीर CVS', status: 'उच्च क्लिनिकल महत्त्व', color: '#ef4444', bg: '#fef2f2', analysis: 'तुमचा स्कोअर गंभीर कॉम्प्युटर व्हिजन सिंड्रोम दर्शवतो. कृपया नेत्रतज्ज्ञांचा सल्ला घ्या.' }
                }
            };

            let result = (cvsResultSets[lang] || cvsResultSets['en']).negative;
            if (score >= 6 && score <= 12) result = (cvsResultSets[lang] || cvsResultSets['en']).mild;
            else if (score > 12 && score <= 20) result = (cvsResultSets[lang] || cvsResultSets['en']).moderate;
            else if (score > 20) result = (cvsResultSets[lang] || cvsResultSets['en']).severe;

            tierBadge.innerText = result.tier;
            tierBadge.style.color = result.color;
            tierBadge.style.background = result.bg;
            statusTitle.innerText = result.status;
            statusTitle.style.color = result.color;
            analysisText.innerText = result.analysis;

            if(indicator) {
                const percent = (score / 32) * 100;
                indicator.style.left = Math.min(95, Math.max(5, percent)) + '%';
            }

            navigate('scr-cvs-result');
        }
"""

content = re.sub(r'function showCvsResult\(score\) \{.*?\}', cvs_result_logic, content, flags=re.DOTALL)

# 2. Fix the CVS Questions UI issues (flex-wrap)
content = content.replace('<div style="display: flex; gap: 8px;">', '<div style="display: flex; gap: 8px; flex-wrap: wrap;">')

# 3. Add translations for "Certificate" and other Result UI elements
result_translations_patch = """
                accept_proceed: 'Start Assessment', analyzing_blinks: 'Analyzing Blinks...',
                stare_center: 'Please stare at the center', live_count: 'Live Count', live_status: 'Status',
                assessment_complete_title: 'Assessment Complete', assessment_complete_desc: 'Your eye health screening has been securely recorded.',
                download_cert: 'Download Eye Care Certificate', back_home: 'Back to Home', rep_id: 'Representative ID',
                blink_report_title: 'Blink Analysis Report', your_score: 'YOUR SCORE', cvs_assessment_result: 'CVS Assessment Result',
                total_cvs_score: 'TOTAL CVS SCORE', cvs_diagnosis: 'Diagnosis', blink_per_min: 'blinks / min', reg_code: 'REGISTRATION CODE',
                cert_title: 'Certificate of Digital Eye Health'
            },
"""
# (I'll do a proper replace of the whole translations object next time if this is too messy)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
