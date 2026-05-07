import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

clean_js_block = """
    function endBlinkTest() {
        clearInterval(testInterval);
        if(camera) camera.stop();
        
        const oneMinuteCount = blinkCount * 4;
        document.getElementById('final-blink-count').innerText = blinkCount;
        document.getElementById('scaled-blink-count').innerText = oneMinuteCount;
        
        const reportDateEl = document.getElementById('report-date');
        if(reportDateEl) reportDateEl.innerText = new Date().toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'});
        
        const reportSoEl = document.getElementById('report-so-name');
        if(reportSoEl && state.empName) reportSoEl.innerText = state.empName;
        
        const tierBadge = document.getElementById('result-tier-badge');
        const statusEl = document.getElementById('result-status');
        const analysisEl = document.getElementById('result-analysis');
        
        const lang = state.lang || 'en';
        const blinkAnalysisSet = {
            en: {
                optimal: { tier: 'Optimal', status: 'Highly stable tear film', analysis: 'Your eyes are exceptionally well-lubricated. The oily (lipid) layer of your tear film is very thick, preventing your tears from evaporating.' },
                excellent: { tier: 'Excellent', status: 'Very healthy moisture retention', analysis: 'You have great tear stability. You likely do not suffer from symptoms even in dry environments.' },
                healthy: { tier: 'Healthy Average', status: 'Normal tear film function', analysis: 'This is the ideal range for most healthy adults. Your eyes refresh themselves at a natural pace.' },
                mild: { tier: 'Mild/Borderline', status: 'Possible early moisture evaporation', color: '#f59e0b', bg: '#fffbeb', analysis: 'You may be starting to experience moisture loss. This is often triggered by modern life.' },
                moderate: { tier: 'Moderate', status: 'Signs of lipid layer disruption', analysis: 'Your blinking has increased because your tears are evaporating faster than they should.' },
                high: { tier: 'High Chance', status: 'Strong signs of screen-dry eye', analysis: 'There is a strong likelihood that you have dry eyes. Your eyes are forcing you to blink frequently.' },
                severe: { tier: 'Severe/Chronic', status: 'Highly unstable tear film', analysis: 'You have a highly unstable tear film. Your eyes feel constant discomfort. Please consult a professional.' }
            },
            hi: {
                optimal: { tier: 'उत्तम', status: 'अत्यधिक स्थिर अश्रु फिल्म', analysis: 'आपकी आंखें असाधारण रूप से अच्छी तरह से चिकनी हैं।' },
                excellent: { tier: 'उत्कृष्ट', status: 'बहुत स्वस्थ नमी प्रतिधारण', analysis: 'आपकी आंखों में नमी बहुत अच्छी है।' },
                healthy: { tier: 'स्वस्थ औसत', status: 'सामान्य अश्रु फिल्म कार्य', analysis: 'यह अधिकांश स्वस्थ वयस्कों के लिए आदर्श सीमा है।' },
                mild: { tier: 'हल्का/सीमावर्ती', status: 'नमी का जल्दी वाष्पीकरण संभव', analysis: 'आप नमी की कमी महसूस करना शुरू कर सकते हैं।' },
                moderate: { tier: 'मध्यम', status: 'लिपिड परत व्यवधान के लक्षण', analysis: 'आपकी पलकें झपकना बढ़ गया है क्योंकि आपके आंसू बहुत जल्दी सूख रहे हैं।' },
                high: { tier: 'उच्च संभावना', status: 'स्क्रीन-ड्राय आई के स्पष्ट लक्षण', analysis: 'इसकी प्रबल संभावना है कि आपकी आंखें शुष्क हैं।' },
                severe: { tier: 'गंभीर/क्रोनिक', status: 'अत्यधिक अस्थिर अश्रु फिल्म', analysis: 'आपकी अश्रु फिल्म अत्यधिक अस्थिर है। कृपया विशेषज्ञ से मिलें।' }
            }
        };

        let resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).healthy;
        if (oneMinuteCount <= 6) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).optimal;
        else if (oneMinuteCount <= 10) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).excellent;
        else if (oneMinuteCount <= 13) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).healthy;
        else if (oneMinuteCount <= 16) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).mild;
        else if (oneMinuteCount <= 18) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).moderate;
        else if (oneMinuteCount <= 20) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).high;
        else resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).severe;

        if (oneMinuteCount <= 10) { resObj.color = '#10b981'; resObj.bg = '#f0fdf4'; }
        else if (oneMinuteCount <= 13) { resObj.color = '#38bdf8'; resObj.bg = '#f0f9ff'; }
        else if (oneMinuteCount <= 16) { resObj.color = '#f59e0b'; resObj.bg = '#fffbeb'; }
        else if (oneMinuteCount <= 18) { resObj.color = '#f97316'; resObj.bg = '#fff7ed'; }
        else if (oneMinuteCount <= 20) { resObj.color = '#ef4444'; resObj.bg = '#fef2f2'; }
        else { resObj.color = '#991b1b'; resObj.bg = '#fef2f2'; }

        tierBadge.innerText = resObj.tier;
        tierBadge.style.color = resObj.color;
        tierBadge.style.background = resObj.bg;
        statusEl.innerText = resObj.status;
        statusEl.style.color = resObj.color;
        analysisEl.innerText = resObj.analysis;
        
        const indicator = document.getElementById('result-indicator');
        if(indicator) {
            let percent = ((oneMinuteCount - 3) / (20 - 3)) * 90 + 5;
            indicator.style.left = Math.min(95, Math.max(5, percent)) + '%';
        }
        
        state.blinkCount = oneMinuteCount;
        state.blinkTier = resObj.tier;
        state.blinkStatus = resObj.status;

        fetch(`{{ route('blink_test.save') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ emp_code: state.empCode, blink_count: oneMinuteCount })
        }).then(res => res.json()).then(data => {
            if(data.success) state.lastBlinkTestId = data.test.id;
        });

        navigate('scr-test-result');
    }

    function downloadCombinedPDF() {
        const element = document.getElementById('pdf-template');
        const blinkCount = state.blinkCount || '0';
        const cvsScore = state.cvsScore || '0';

        document.getElementById('pdf-blink-count').innerText = blinkCount;
        document.getElementById('pdf-date').innerText = new Date().toLocaleDateString('en-GB');
        document.getElementById('pdf-so-name').innerText = state.empName || state.empCode;
        document.getElementById('pdf-report-id').innerText = 'CERT-' + new Date().getTime().toString().substr(-6);
        
        const pdfTier = document.getElementById('pdf-tier-badge');
        pdfTier.innerText = state.blinkTier || '---';
        
        const pdfCvsScore = document.getElementById('pdf-cvs-score');
        if(pdfCvsScore) pdfCvsScore.innerText = cvsScore;
        
        const pdfCvsTier = document.getElementById('pdf-cvs-tier');
        if(pdfCvsTier) pdfCvsTier.innerText = state.cvsTier || '---';

        element.style.display = 'block'; 
        const opt = {
            margin: 10,
            filename: `Ajanta_Eye_Health_${state.empCode}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save().then(() => { element.style.display = 'none'; });
    }

    async function shareToWhatsApp() {
        const num = document.getElementById('whatsapp-num').value.trim();
        const msg = `🩺 *Ajanta Eye Health Screening Complete*\\n\\nBlink Rate: ${state.blinkCount}\\nCVS Score: ${state.cvsScore}\\n\\nFacilitated by: ${state.empName || state.empCode}`;
        if (!num) return showToast("Please enter a WhatsApp number");
        window.open(`https://wa.me/${num}?text=${encodeURIComponent(msg)}`, '_blank');
    }

    function onTestFinish() {
        document.getElementById('ty-blink-score').innerText = state.blinkCount || '0';
        document.getElementById('ty-blink-status').innerText = state.blinkTier || '---';
        document.getElementById('ty-cvs-score').innerText = state.cvsScore || '0';
        document.getElementById('ty-cvs-status').innerText = state.cvsTier || '---';
        document.getElementById('thank-you-so-code').innerText = state.empCode;

        if (state.isPatientMode) navigate('scr-thank-you');
        else navigate('scr-dashboard');
    }

    const cvsSymptoms = [
        { id: 'burning', en: 'Burning', hi: 'जलन', mr: 'डोळ्यांची जळजळ' },
        { id: 'itching', en: 'Itching', hi: 'खुजली', mr: 'खाज येणे' },
        { id: 'foreign_body', en: 'Feeling of a foreign body', hi: 'आंख में कुछ होने का अहसास', mr: 'डोळ्यात काहीतरी गेल्यासारखे वाटणे' },
        { id: 'tearing', en: 'Tearing', hi: 'आंसू आना', mr: 'पाणी येणे' },
        { id: 'blinking', en: 'Excessive blinking', hi: 'पलकें अधिक झपकना', mr: 'पापण्यांची जास्त हालचाल' },
        { id: 'redness', en: 'Eye redness', hi: 'आंखों का लाल होना', mr: 'डोळे लाल होणे' },
        { id: 'pain', en: 'Eye pain', hi: 'आंखों में दर्द', mr: 'डोळ्यात दुखणे' },
        { id: 'heavy_eyelids', en: 'Heavy eyelids', hi: 'पलकों का भारीपन', mr: 'पापण्या जड होणे' },
        { id: 'dryness', en: 'Dryness', hi: 'सूखापन', mr: 'कोरडेपणा' },
        { id: 'blurred_vision', en: 'Blurred vision', hi: 'धुंधली दृष्टि', mr: 'अंधुक दिसणे' },
        { id: 'double_vision', en: 'Double vision', hi: 'दोहरा दिखाई देना', mr: 'दोन-दोन दिसणे' },
        { id: 'near_vision', en: 'Difficulty focusing for near vision', hi: 'पास की दृष्टि पर ध्यान केंद्रित करने में कठिनाई', mr: 'जवळचे पाहण्यात अडचण' },
        { id: 'light_sensitivity', en: 'Increased sensitivity to light', hi: 'प्रकाश के प्रति संवेदनशीलता', mr: 'प्रकाशाचा त्रास होणे' },
        { id: 'halos', en: 'Coloured halos around objects', hi: 'वस्तुओं के चारों ओर रंगीन घेरे', mr: 'वस्तूंभोवती रंगीन कडा दिसणे' },
        { id: 'worsening_eyesight', en: 'Feeling that eyesight is worsening', hi: 'दृष्टि खराब होने का अहसास', mr: 'दृष्टी कमी होत असल्याचे वाटणे' },
        { id: 'headache', en: 'Headache', hi: 'सिरदर्द', mr: 'डोकेदुखी' }
    ];

    const translations = {
        en: {
            freq: 'Frequency', intens: 'Intensity', never: 'Never', occas: 'Occasionally', often: 'Often/Always',
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
        }
    };

    function updateTranslations() {
        const trans = translations[state.lang || 'en'];
        if(!trans) return;
        document.querySelectorAll('[data-t]').forEach(el => {
            const key = el.getAttribute('data-t');
            if(trans[key]) el.innerText = trans[key];
        });
    }

    window.openLanguageModal = () => { document.getElementById('language-modal').style.display = 'flex'; };
    window.closeLanguageModal = () => { document.getElementById('language-modal').style.display = 'none'; };

    window.selectLanguage = (lang) => {
        const wasInitial = !state.lang;
        state.lang = lang;
        sessionStorage.setItem('lang', lang);
        updateTranslations();
        closeLanguageModal();
        fetch(`{{ route('blink.set_language') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ emp_code: state.empCode, language: lang })
        });
        if (wasInitial) {
            if (state.isPatientMode) navigate('scr-disclaimer');
            else if (state.isLoggedIn) navigate('scr-dashboard');
            else navigate('scr-login');
        }
    };

    window.cvsScores = {};
    function renderCvsQuestions() {
        const container = document.getElementById('cvs-questions-container');
        if(!container) return;
        container.innerHTML = '';
        window.cvsScores = {}; 
        cvsSymptoms.forEach((s, index) => {
            const item = document.createElement('div');
            item.style.padding = '20px'; item.style.background = '#fff'; item.style.borderRadius = '20px';
            item.innerHTML = `
                <div style="font-weight: 800; margin-bottom: 10px;">${index + 1}. ${s[state.lang] || s.en}</div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; gap: 5px;">
                        <button onclick="setCvsValue('${s.id}', 'freq', 0, this)" class="cvs-opt-btn" data-t="never">Never</button>
                        <button onclick="setCvsValue('${s.id}', 'freq', 1, this)" class="cvs-opt-btn" data-t="occas">Occasionally</button>
                        <button onclick="setCvsValue('${s.id}', 'freq', 2, this)" class="cvs-opt-btn" data-t="often">Often</button>
                    </div>
                    <div id="intens-${s.id}" style="display: none; gap: 5px;">
                        <button onclick="setCvsValue('${s.id}', 'intens', 1, this)" class="cvs-opt-btn" data-t="moderate">Moderate</button>
                        <button onclick="setCvsValue('${s.id}', 'intens', 2, this)" class="cvs-opt-btn" data-t="intense">Intense</button>
                    </div>
                </div>
            `;
            container.appendChild(item);
            window.cvsScores[s.id] = { freq: null, intens: null };
        });
        updateTranslations();
    }

    window.setCvsValue = (sId, type, val, btn) => {
        btn.parentElement.querySelectorAll('.cvs-opt-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        window.cvsScores[sId][type] = val;
        if (type === 'freq') {
            document.getElementById(`intens-${sId}`).style.display = (val === 0) ? 'none' : 'flex';
            if(val === 0) window.cvsScores[sId].intens = null;
        }
        calculateCvsScore();
    };

    function calculateCvsScore() {
        let total = 0; let answered = 0;
        Object.keys(window.cvsScores).forEach(id => {
            const s = window.cvsScores[id];
            if (s.freq === 0) answered++;
            else if (s.freq !== null && s.intens !== null) {
                answered++; total += (s.freq * s.intens);
            }
        });
        document.getElementById('cvs-running-score').innerText = total;
        return { total, answered };
    }

    window.startCvsScreening = () => { navigate('scr-cvs-screening'); renderCvsQuestions(); };

    window.submitCvsScreening = () => {
        const { total, answered } = calculateCvsScore();
        if (answered < cvsSymptoms.length) return showToast("Please answer all questions");
        showCvsResult(total);
    };

    function showCvsResult(score) {
        state.cvsScore = score;
        const resSets = {
            en: { neg: 'Negative', mild: 'Mild CVS', mod: 'Moderate CVS', sev: 'Severe CVS' },
            hi: { neg: 'नकारात्मक', mild: 'हल्का सीवीएस', mod: 'मध्यम सीवीएस', sev: 'गंभीर सीवीएस' }
        };
        const lang = state.lang || 'en';
        let tier = resSets[lang].neg;
        if (score >= 6 && score <= 12) tier = resSets[lang].mild;
        else if (score > 12 && score <= 20) tier = resSets[lang].mod;
        else if (score > 20) tier = resSets[lang].sev;

        state.cvsTier = tier;
        document.getElementById('cvs-final-score').innerText = score;
        document.getElementById('cvs-tier-badge').innerText = tier;
        
        fetch(`{{ route('cvs_test.save') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ emp_code: state.empCode, blink_test_id: state.lastBlinkTestId || null, symptom_data: window.cvsScores, total_score: score, has_cvs: score >= 6 })
        });
        navigate('scr-cvs-result');
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateTranslations();
        if (state.empName) {
            const display = document.getElementById('so-name-display');
            if (display) display.innerText = state.empName;
            const container = document.getElementById('so-facilitator');
            if (container) container.style.display = 'inline-block';
        }
        let target = state.isPatientMode ? 'scr-disclaimer' : (state.isLoggedIn ? 'scr-dashboard' : 'scr-login');
        navigate(target);
        if (!state.lang) openLanguageModal();
    });
"""

# Find indices for replacement
start_idx = content.find('function endBlinkTest()')
end_idx = content.rfind('</script>')

if start_idx != -1 and end_idx != -1:
    new_content = content[:start_idx] + clean_js_block + content[end_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
else:
    print('Indices not found')
