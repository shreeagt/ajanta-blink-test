import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update pdf-template to include CVS Section
cvs_pdf_section = """
            <!-- CVS Section -->
            <div style="display: flex; gap: 30px; align-items: flex-start; margin-bottom: 50px;">
                <div style="width: 200px; height: 200px; background: #fffbeb; border-radius: 30px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px solid #fef3c7;">
                    <p style="font-size: 12px; font-weight: 800; color: #d97706; text-transform: uppercase; margin-bottom: 5px;">CVS Score</p>
                    <div style="font-size: 72px; font-weight: 900; color: #92400e;" id="pdf-cvs-score">0</div>
                    <p style="font-size: 12px; font-weight: 700; color: #b45309;">(out of 32)</p>
                </div>
                <div style="flex: 1; padding-top: 20px;">
                    <div id="pdf-cvs-tier" style="display: inline-block; padding: 10px 25px; border-radius: 50px; font-size: 24px; font-weight: 900; text-transform: uppercase; margin-bottom: 15px;">-</div>
                    <h3 id="pdf-cvs-status" style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 15px;">-</h3>
                    <p id="pdf-cvs-analysis" style="font-size: 15px; color: #475569; font-weight: 600; line-height: 1.6;"></p>
                </div>
            </div>
"""
# Insert it after the first result section (line 450 approx)
content = content.replace('<!-- Visual Scale (Health Bar) -->', cvs_pdf_section + '\n            <!-- Visual Scale (Health Bar) -->')

# 2. Update downloadCombinedPDF to populate CVS fields
pdf_populate_logic = """
            // CVS Population
            document.getElementById('pdf-cvs-score').innerText = cvsScore;
            const pdfCvsTier = document.getElementById('pdf-cvs-tier');
            pdfCvsTier.innerText = cvsTier.innerText;
            pdfCvsTier.style.color = cvsTier.style.color;
            pdfCvsTier.style.background = cvsTier.style.background;
            
            document.getElementById('pdf-cvs-status').innerText = document.getElementById('cvs-status-title').innerText;
            document.getElementById('pdf-cvs-status').style.color = cvsTier.style.color;
            document.getElementById('pdf-cvs-analysis').innerText = document.getElementById('cvs-analysis-text').innerText;
"""
content = content.replace("document.getElementById('pdf-status').style.color = blinkTier.style.color;", "document.getElementById('pdf-status').style.color = blinkTier.style.color;\n" + pdf_populate_logic)

# 3. Update endBlinkTest to store in state
content = content.replace("state.lastBlinkTestId = data.test.id;", "state.lastBlinkTestId = data.test.id; state.blinkCount = oneMinuteCount; state.blinkTier = result.tier; state.blinkStatus = result.status;")

# 4. Update showCvsResult to store in state
content = content.replace("navigate('scr-cvs-result');", "state.cvsScore = score; state.cvsTier = result.tier; state.cvsStatus = result.status; navigate('scr-cvs-result');")

# 5. Improve Floating Language Button (add micro-animation)
content = content.replace('.floating-lang-btn {', """
        .floating-lang-btn {
            position: fixed; top: 15px; right: 15px; z-index: 9999;
            width: 45px; height: 45px; background: white; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1.5px solid #eff6ff;
            cursor: pointer; color: var(--primary); font-size: 18px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .floating-lang-btn:hover { transform: scale(1.1) rotate(15deg); background: var(--primary); color: white; }
""")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
