import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update onTestFinish to populate Thank You screen
ontestfinish_logic = """
    function onTestFinish() {
        const blinkCount = document.getElementById('scaled-blink-count').innerText;
        const blinkStatus = document.getElementById('result-tier-badge').innerText;
        const cvsScore = document.getElementById('cvs-final-score').innerText;
        const cvsStatus = document.getElementById('cvs-tier-badge').innerText;

        document.getElementById('ty-blink-score').innerText = blinkCount;
        document.getElementById('ty-blink-status').innerText = blinkStatus;
        document.getElementById('ty-cvs-score').innerText = cvsScore;
        document.getElementById('ty-cvs-status').innerText = cvsStatus;
        document.getElementById('thank-you-so-code').innerText = state.empCode;

        if (state.isPatientMode) {
            navigate('scr-thank-you');
        } else {
            navigate('scr-dashboard');
        }
    }
"""
content = re.sub(r'function onTestFinish\(.*?\n\s+navigate\(.*?\n\s+\}', ontestfinish_logic, content, flags=re.DOTALL)

# 2. Restore Full PDF Logic
pdf_logic = """
        function downloadCombinedPDF() {
            const element = document.getElementById('pdf-template');
            const blinkCount = document.getElementById('scaled-blink-count').innerText;
            const blinkTier = document.getElementById('result-tier-badge');
            const cvsScore = document.getElementById('cvs-final-score').innerText;
            const cvsTier = document.getElementById('cvs-tier-badge');

            // Populate Template
            document.getElementById('pdf-blink-count').innerText = blinkCount;
            document.getElementById('pdf-date').innerText = new Date().toLocaleDateString('en-GB');
            document.getElementById('pdf-so-name').innerText = state.empName || state.empCode;
            document.getElementById('pdf-report-id').innerText = 'CERT-' + new Date().getTime().toString().substr(-6);
            
            const pdfTier = document.getElementById('pdf-tier-badge');
            pdfTier.innerText = blinkTier.innerText;
            pdfTier.style.color = blinkTier.style.color;
            pdfTier.style.background = blinkTier.style.background;
            
            document.getElementById('pdf-status').innerText = document.getElementById('result-status').innerText;
            document.getElementById('pdf-status').style.color = blinkTier.style.color;
            document.getElementById('pdf-analysis').innerText = document.getElementById('result-analysis').innerText;

            element.style.display = 'block'; 
            
            const opt = {
                margin: 10,
                filename: `Ajanta_Eye_Health_${state.empCode}_${new Date().getTime()}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            html2pdf().set(opt).from(element).save().then(() => {
                element.style.display = 'none';
            });
        }
"""
content = re.sub(r'function downloadCombinedPDF\(.*?\}', pdf_logic, content, flags=re.DOTALL)

# 3. Add Global Language Icon to Header and CSS
# Find globe-btn in HTML and ensure it's always there
if 'openLanguageModal()' not in content:
    # Header was already viewing globe-btn in previous turn, but let's be sure
    pass

# Add floating button CSS if needed
content = content.replace('</style>', """
        .floating-lang-btn {
            position: fixed; top: 15px; right: 15px; z-index: 9999;
            width: 45px; height: 45px; background: white; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1.5px solid #eff6ff;
            cursor: pointer; color: var(--primary); font-size: 18px;
        }
    </style>
""")

# Add the button to body
content = content.replace('<body>', '<body>\n<div class=\"floating-lang-btn\" onclick=\"openLanguageModal()\">\n    <i class=\"fas fa-globe\"></i>\n</div>')

# 4. Save results to DB
# Update endBlinkTest to save blink test
blink_save_logic = """
        // Save to DB
        fetch(`{{ route('blink_test.save') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ emp_code: state.empCode, blink_count: oneMinuteCount })
        }).then(res => res.json()).then(data => {
            if(data.success) state.lastBlinkTestId = data.test.id;
        });

        tierBadge.innerText = result.tier;
"""
content = content.replace('tierBadge.innerText = result.tier;', blink_save_logic)

# Update showCvsResult to save CVS
cvs_save_logic = """
            // Save to DB
            fetch(`{{ route('cvs_test.save') }}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ 
                    emp_code: state.empCode, 
                    blink_test_id: state.lastBlinkTestId || null,
                    symptom_data: window.cvsScores,
                    total_score: score,
                    has_cvs: score >= 6
                })
            });

            tierBadge.innerText = result.tier;
"""
content = content.replace('tierBadge.innerText = result.tier;', cvs_save_logic)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
