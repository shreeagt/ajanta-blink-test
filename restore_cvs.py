import sys

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update navigate function
old_hide_chrome = "const hideChrome = ['scr-login', 'scr-thank-you', 'scr-blink-test', 'scr-disclaimer', 'scr-test-result'];"
new_hide_chrome = "const hideChrome = ['scr-login', 'scr-thank-you', 'scr-blink-test', 'scr-disclaimer', 'scr-test-result', 'scr-cvs-screening'];"
content = content.replace(old_hide_chrome, new_hide_chrome)

# 2. Add Next CVS button to test result screen
old_test_result_footer = """                        <button class="btn btn-primary" onclick="endBlinkTest()" style="font-size: 16px; padding: 16px; border-radius: 16px; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px;">Next: Report <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>"""
new_test_result_footer = """                        <button class="btn btn-primary" onclick="endBlinkTest()" style="font-size: 16px; padding: 16px; border-radius: 16px; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px;">Next: Report <i class="fas fa-arrow-right"></i></button>
                        <button class="btn btn-primary" onclick="startCvsScreening()" style="font-size: 16px; padding: 16px; border-radius: 16px; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px;">Next: CVS Screening <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

        <!-- CVS Screening Screen -->
        <div id="scr-cvs-screening" class="screen" style="background: #f8fafc; padding: 20px;">
            <div style="background: white; padding: 30px 24px; border-radius: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.06); border: 1px solid #fff;">
                <div style="text-align: center; margin-bottom: 25px;">
                    <span style="font-size: 12px; font-weight: 900; color: #f59e0b; text-transform: uppercase; letter-spacing: 2px; background: #fffbeb; padding: 8px 20px; border-radius: 50px;" data-t="cvs_title">CVS Screening</span>
                    <h2 style="font-size: 24px; font-weight: 900; color: #1e293b; margin-top: 15px;" data-t="symptom_assessment">Symptom Assessment</h2>
                    <p style="font-size: 14px; color: #64748b; font-weight: 600;" data-t="cvs_subtitle">Please rate the following symptoms based on your experience during digital device use.</p>
                </div>

                <div id="cvs-questions-container" style="display: flex; flex-direction: column; gap: 20px;">
                    <!-- Symptoms will be injected here via JS -->
                </div>

                <div style="margin-top: 40px; padding-top: 25px; border-top: 2px dashed #f1f5f9;">
                    <div style="background: #f8fafc; padding: 20px; border-radius: 24px; margin-bottom: 25px; text-align: center;">
                        <p style="font-size: 13px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px;" data-t="current_cvs_score">Current CVS Score</p>
                        <div id="cvs-running-score" style="font-size: 40px; font-weight: 900; color: var(--primary);">0</div>
                    </div>
                    <button class="btn btn-primary" onclick="submitCvsScreening()" style="height: 64px; font-size: 18px;" data-t="complete_assessment">
                        Complete Assessment <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>"""
content = content.replace(old_test_result_footer, new_test_result_footer)

# 3. Add CVS button to Dashboard bottom nav
old_bottom_nav = """            <div class="nav-btn active" id="nav-dash" onclick="navigate('scr-dashboard')">
                <i class="fas fa-chart-line"></i>
                <span>Stats</span>
            </div>
            <div class="nav-btn" onclick="navigate('scr-disclaimer')">
                <i class="fas fa-eye"></i>
                <span>Dry Eye</span>
            </div>"""
new_bottom_nav = """            <div class="nav-btn active" id="nav-dash" onclick="navigate('scr-dashboard')">
                <i class="fas fa-chart-line"></i>
                <span>Stats</span>
            </div>
            <div class="nav-btn" onclick="navigate('scr-disclaimer')">
                <i class="fas fa-eye"></i>
                <span>Dry Eye</span>
            </div>
            <div class="nav-btn" onclick="startCvsScreening()" id="main-cvs-btn">
                <i class="fas fa-laptop-medical"></i>
                <span>CVS Test</span>
            </div>"""
content = content.replace(old_bottom_nav, new_bottom_nav)

# 4. Add translations
cvs_trans_keys = """                cvs_title: "CVS Screening",
                symptom_assessment: "Symptom Assessment",
                cvs_subtitle: "Please rate the following symptoms based on your experience during digital device use.",
                current_cvs_score: "Current CVS Score",
                complete_assessment: "Complete Assessment",
                freq: "Frequency",
                intens: "Intensity",
                never: "Never",
                occas: "Occasionally",
                often: "Often/Always",
                moderate: "Moderate",
                intense: "Intense\""""

content = content.replace('privacy_desc: "Video is processed locally. No data is uploaded or saved.",', 
                        'privacy_desc: "Video is processed locally. No data is uploaded or saved.",\n' + cvs_trans_keys + ',')

# 5. Add JS functions
cvs_js = r'''
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
            
            cvsSymptoms.forEach((s, index) => {
                const item = document.createElement('div');
                item.style.padding = '15px';
                item.style.background = '#fff';
                item.style.borderRadius = '15px';
                item.style.border = '1px solid #f1f5f9';
                
                item.innerHTML = `
                    <div style="font-weight: 800; color: #1e293b; margin-bottom: 12px; font-size: 15px;">${index + 1}. ${s.name}</div>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div>
                            <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;" data-t="freq">Frequency</div>
                            <div style="display: flex; gap: 8px;">
                                <button onclick="setCvsValue('${s.id}', 'freq', 0, this)" class="cvs-opt-btn active" data-t="never">Never</button>
                                <button onclick="setCvsValue('${s.id}', 'freq', 1, this)" class="cvs-opt-btn" data-t="occas">Occasionally</button>
                                <button onclick="setCvsValue('${s.id}', 'freq', 2, this)" class="cvs-opt-btn" data-t="often">Often/Always</button>
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;" data-t="intens">Intensity</div>
                            <div style="display: flex; gap: 8px;">
                                <button onclick="setCvsValue('${s.id}', 'intens', 1, this)" class="cvs-opt-btn active" data-t="moderate">Moderate</button>
                                <button onclick="setCvsValue('${s.id}', 'intens', 2, this)" class="cvs-opt-btn" data-t="intense">Intense</button>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(item);
                
                // Init scores
                window.cvsScores[s.id] = { freq: 0, intens: 1 };
            });
            
            if(typeof updateTranslations === 'function') updateTranslations();
        }

        function calculateCvsScore() {
            let total = 0;
            Object.values(window.cvsScores).forEach(s => {
                const res = s.freq * s.intens;
                let severity = 0;
                if (res === 1 || res === 2) severity = 1;
                else if (res === 4) severity = 2;
                total += severity;
            });
            
            const scoreEl = document.getElementById('cvs-running-score');
            if(scoreEl) scoreEl.innerText = total;
            return total;
        }

        window.setCvsValue = function(sId, type, val, btn) {
            const btns = btn.parentElement.querySelectorAll('.cvs-opt-btn');
            btns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            window.cvsScores[sId][type] = val;
            calculateCvsScore();
        };

        window.startCvsScreening = function() {
            renderCvsQuestions();
            navigate('scr-cvs-screening');
        };

        window.submitCvsScreening = function() {
            const totalScore = calculateCvsScore();
            const hasCvs = totalScore >= 6;
            
            const payload = {
                emp_code: state.empCode,
                blink_test_id: state.lastTestId || null,
                symptom_data: window.cvsScores,
                total_score: totalScore,
                has_cvs: hasCvs ? 1 : 0
            };
            
            fetch("{{ route('cvs_test.save') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            }).then(() => {
                onTestFinish();
            });
        };
'''
content = content.replace("    document.addEventListener('DOMContentLoaded', () => {", cvs_js + "\n    document.addEventListener('DOMContentLoaded', () => {")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
