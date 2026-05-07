import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add CVS Result Screen before scr-thank-you
cvs_result_html = """
    <!-- CVS Result Screen -->
    <div id="scr-cvs-result" class="screen anim-screen" style="background: #f1f5f9; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; width: 100%; padding: 30px 24px; border-radius: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.06); border: 1px solid #fff; position: relative;">
            <div style="text-align: center; margin-bottom: 25px;">
                <span style="font-size: 12px; font-weight: 900; color: #f59e0b; text-transform: uppercase; letter-spacing: 2px; background: #fffbeb; padding: 8px 20px; border-radius: 50px;">CVS Assessment Result</span>
            </div>
            
            <div style="text-align: center; margin-bottom: 25px;">
                <p style="font-size: 14px; color: #64748b; font-weight: 700; margin-bottom: 10px;">TOTAL CVS SCORE</p>
                <div style="display: flex; align-items: baseline; justify-content: center; gap: 8px;">
                    <h2 style="font-size: 80px; font-weight: 900; color: #0f172a; line-height: 1;" id="cvs-final-score">0</h2>
                    <span style="font-size: 20px; font-weight: 800; color: #94a3b8;">/ 32</span>
                </div>
            </div>

            <div style="margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 900; color: #94a3b8; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px;">
                    <span>Severity Scale</span>
                    <span id="cvs-report-date"></span>
                </div>
                <div style="position: relative; height: 16px; background: #f1f5f9; border-radius: 20px; display: flex; overflow: hidden; border: 1px solid #e2e8f0;">
                    <div style="flex: 6; background: #10b981;"></div> <!-- 0-6: Low -->
                    <div style="flex: 12; background: #fbbf24;"></div> <!-- 6-18: Mid -->
                    <div style="flex: 14; background: #ef4444;"></div> <!-- 18-32: High -->
                    
                    <div id="cvs-result-indicator" style="position: absolute; top: -6px; left: 0%; width: 28px; height: 28px; background: white; border-radius: 50%; border: 6px solid #f59e0b; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: all 1s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
                </div>
            </div>

            <div style="text-align: center; margin-bottom: 30px;">
                <div id="cvs-tier-badge" style="display: inline-block; padding: 8px 25px; border-radius: 50px; font-size: 16px; font-weight: 900; text-transform: uppercase; margin-bottom: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">Negative</div>
                <h3 id="cvs-status-title" style="font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 12px; line-height: 1.2;">Normal Findings</h3>
                <p id="cvs-analysis-text" style="font-size: 15px; color: #64748b; font-weight: 600; line-height: 1.6; margin: 0;">Your symptom score is within the normal range. Continue following healthy digital habits.</p>
            </div>

            <div style="margin-top: 30px; padding-top: 25px; border-top: 2px dashed #f1f5f9; display: flex; flex-direction: column; gap: 10px;">
                <button class="btn btn-primary" onclick="window.onTestFinish()" style="font-size: 16px; padding: 16px; border-radius: 16px; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    Finish & Close <i class="fas fa-check"></i>
                </button>
            </div>
        </div>
    </div>
"""

if 'id="scr-cvs-result"' not in content:
    content = content.replace('    <div id="scr-thank-you"', cvs_result_html + '\n    <div id="scr-thank-you"')

# 2. Update hideChrome to include the new result screen
content = content.replace("'scr-test-result', 'scr-cvs-screening']", "'scr-test-result', 'scr-cvs-screening', 'scr-cvs-result']")

# 3. Update JS Logic
cvs_js_replacement = r'''
        const cvsSymptoms = [
            { id: 'burning', name: 'Burning' },
            { id: 'itching', name: 'Itching' },
            { id: 'foreign_body', name: 'Feeling of a foreign body' },
            { id: 'tearing', name: 'Tearing' },
            { id: 'blinking', name: 'Excessive blinking' },
            { id: 'redness', name: 'Eye redness' },
            { id: 'pain', name: 'Eye pain' },
            { id: 'heavy_eyelids', name: 'Heavy eyelids' },
            { id: 'dryness', name: 'Dryness' },
            { id: 'blurred_vision', name: 'Blurred vision' },
            { id: 'double_vision', name: 'Double vision' },
            { id: 'near_vision', name: 'Difficulty focusing for near vision' },
            { id: 'light_sensitivity', name: 'Increased sensitivity to light' },
            { id: 'halos', name: 'Coloured halos around objects' },
            { id: 'worsening_eyesight', name: 'Feeling that eyesight is worsening' },
            { id: 'headache', name: 'Headache' }
        ];

        window.cvsScores = {};

        function renderCvsQuestions() {
            const container = document.getElementById('cvs-questions-container');
            if(!container) return;
            container.innerHTML = '';
            
            window.cvsScores = {}; // Reset

            cvsSymptoms.forEach((s, index) => {
                const item = document.createElement('div');
                item.style.padding = '24px';
                item.style.background = '#fff';
                item.style.borderRadius = '24px';
                item.style.border = '1px solid #f1f5f9';
                item.style.boxShadow = '0 10px 25px rgba(0,0,0,0.02)';
                item.id = `cvs-q-${s.id}`;
                
                item.innerHTML = `
                    <div style="font-weight: 800; color: #1e293b; margin-bottom: 12px; font-size: 15px;">${index + 1}. ${s.name}</div>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div>
                            <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;" data-t="freq">Frequency</div>
                            <div style="display: flex; gap: 8px;">
                                <button onclick="window.setCvsValue('${s.id}', 'freq', 0, this)" class="cvs-opt-btn" data-t="never">Never</button>
                                <button onclick="window.setCvsValue('${s.id}', 'freq', 1, this)" class="cvs-opt-btn" data-t="occas">Occasionally</button>
                                <button onclick="window.setCvsValue('${s.id}', 'freq', 2, this)" class="cvs-opt-btn" data-t="often">Often/Always</button>
                            </div>
                        </div>
                        <div id="intens-container-${s.id}" style="display: none; transition: 0.3s;">
                            <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;" data-t="intens">Intensity</div>
                            <div style="display: flex; gap: 8px;">
                                <button onclick="window.setCvsValue('${s.id}', 'intens', 1, this)" class="cvs-opt-btn" data-t="moderate">Moderate</button>
                                <button onclick="window.setCvsValue('${s.id}', 'intens', 2, this)" class="cvs-opt-btn" data-t="intense">Intense</button>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(item);
                
                window.cvsScores[s.id] = { freq: null, intens: null };
            });
            
            if(typeof updateTranslations === 'function') updateTranslations();
            calculateCvsScore();
        }

        function calculateCvsScore() {
            let total = 0;
            let answeredCount = 0;

            Object.keys(window.cvsScores).forEach(id => {
                const s = window.cvsScores[id];
                if (s.freq !== null) {
                    if (s.freq === 0) {
                        answeredCount++;
                    } else if (s.intens !== null) {
                        answeredCount++;
                        const res = s.freq * s.intens;
                        let severity = 0;
                        if (res === 1 || res === 2) severity = 1;
                        else if (res === 4) severity = 2;
                        total += severity;
                    }
                }
            });
            
            const scoreEl = document.getElementById('cvs-running-score');
            if(scoreEl) scoreEl.innerText = total;
            return { total, answeredCount };
        }

        window.setCvsValue = function(sId, type, val, btn) {
            const btns = btn.parentElement.querySelectorAll('.cvs-opt-btn');
            btns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            window.cvsScores[sId][type] = val;

            if (type === 'freq') {
                const intensContainer = document.getElementById(`intens-container-${sId}`);
                if (val === 0) {
                    intensContainer.style.display = 'none';
                    window.cvsScores[sId].intens = null;
                    intensContainer.querySelectorAll('.cvs-opt-btn').forEach(b => b.classList.remove('active'));
                } else {
                    intensContainer.style.display = 'block';
                }
            }
            
            calculateCvsScore();
        };

        window.startCvsScreening = function() {
            renderCvsQuestions();
            navigate('scr-cvs-screening');
        };

        window.submitCvsScreening = function() {
            const { total, answeredCount } = calculateCvsScore();
            
            if (answeredCount < cvsSymptoms.length) {
                showToast(`Please answer all ${cvsSymptoms.length} questions.`);
                const firstS = cvsSymptoms.find(s => {
                    const sc = window.cvsScores[s.id];
                    return sc.freq === null || (sc.freq > 0 && sc.intens === null);
                });
                if(firstS) document.getElementById(`cvs-q-${firstS.id}`).scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            const hasCvs = total >= 6;
            
            const payload = {
                emp_code: state.empCode,
                blink_test_id: state.lastTestId || null,
                symptom_data: window.cvsScores,
                total_score: total,
                has_cvs: hasCvs ? 1 : 0
            };
            
            fetch("{{ route('cvs_test.save') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            }).then(() => {
                showCvsResult(total);
            });
        };

        function showCvsResult(score) {
            const scoreEl = document.getElementById('cvs-final-score');
            const tierBadge = document.getElementById('cvs-tier-badge');
            const statusTitle = document.getElementById('cvs-status-title');
            const analysisText = document.getElementById('cvs-analysis-text');
            const indicator = document.getElementById('cvs-result-indicator');
            const dateEl = document.getElementById('cvs-report-date');

            if(dateEl) dateEl.innerText = new Date().toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'});
            if(scoreEl) scoreEl.innerText = score;

            let result = { tier: 'Negative', status: 'Healthy Eyes', color: '#10b981', bg: '#f0fdf4', analysis: 'Your symptom score is within the normal range. Your digital habits seem to be well-balanced.' };

            if (score >= 6 && score <= 12) {
                result = { tier: 'Mild CVS', status: 'Early Symptoms Detected', color: '#fbbf24', bg: '#fffbeb', analysis: 'You are showing early signs of Computer Vision Syndrome. Consider the 20-20-20 rule and regular breaks.' };
            } else if (score > 12 && score <= 20) {
                result = { tier: 'Moderate CVS', status: 'Significant Digital Strain', color: '#f97316', bg: '#fff7ed', analysis: 'Your symptoms indicate moderate digital eye strain. We recommend using artificial tears and adjusting your screen ergonomics.' };
            } else if (score > 20) {
                result = { tier: 'Severe CVS', status: 'High Clinical Significance', color: '#ef4444', bg: '#fef2f2', analysis: 'Your score indicates severe Computer Vision Syndrome. Please consult an eye specialist for a comprehensive examination.' };
            }

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
'''

block_regex = re.compile(r'const cvsSymptoms = \[.*?fetch\("{{ route\(\'cvs_test\.save\'\) \}}", \{.*?\}\)\s*;', re.DOTALL)
content = block_regex.sub(cvs_js_replacement, content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
