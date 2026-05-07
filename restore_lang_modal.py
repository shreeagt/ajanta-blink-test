import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove the scr-language screen if it exists
content = re.sub(r'<!-- Language Selection Screen -->.*?</div>\s+</div>\s+</div>', '', content, flags=re.DOTALL)
content = re.sub(r'<div id="scr-language".*?</div>\s+</div>', '', content, flags=re.DOTALL)

# 2. Add Language Modal
language_modal_html = """
    <!-- Language Selection Modal -->
    <div id="language-modal" class="calendar-modal" style="display:none;" onclick="if(event.target.id==='language-modal') closeLanguageModal()">
        <div class="calendar-card" style="max-width:320px; padding: 30px 20px;">
             <div style="text-align:center; margin-bottom:25px;">
                <div style="width:60px; height:60px; background:#eff6ff; border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:24px; margin:0 auto 15px;">
                    <i class="fas fa-globe"></i>
                </div>
                <h3 style="font-weight:900; color:#1e293b; margin:0; font-size:20px;">Select Language</h3>
                <p style="font-size:13px; color:var(--text-sub); font-weight:600; margin-top:5px;">Choose your preferred tongue</p>
             </div>
             
             <div style="display:flex; flex-direction:column; gap:12px;">
                <div onclick="selectLanguage('en')" class="lang-card" id="lang-en">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span style="font-size:18px; font-weight:800;">English</span>
                    </div>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('hi')" class="lang-card" id="lang-hi">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span style="font-size:18px; font-weight:800;">हिंदी</span>
                    </div>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('or')" class="lang-card" id="lang-or">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span style="font-size:18px; font-weight:800;">ଓଡ଼ିଆ</span>
                    </div>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
             </div>
        </div>
    </div>
    
    <style>
        .lang-card {
            padding: 18px 20px; border-radius: 18px; border: 2px solid #f1f5f9; cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; justify-content: space-between; background: #f8fafc;
        }
        .lang-card:hover { border-color: var(--primary); background: #eff6ff; }
        .lang-card.active { border-color: var(--primary); background: #eff6ff; }
        .lang-card .check-icon { color: var(--primary); opacity: 0; transition: 0.3s; }
        .lang-card.active .check-icon { opacity: 1; }
        
        .calendar-modal { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 10000; display: none; align-items: center; justify-content: center; padding: 20px; animation: modalFade 0.3s ease; }
        .calendar-card { background: white; width: 100%; border-radius: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); position: relative; animation: cardSlide 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
        @keyframes modalFade { from { opacity: 0; } to { opacity: 1; } }
        @keyframes cardSlide { from { transform: translateY(30px) scale(0.95); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
    </style>
"""

if 'id="language-modal"' not in content:
    content = content.replace('<!-- Logout Modal -->', language_modal_html + '\n    <!-- Logout Modal -->')

# 3. Add Language Toggle to Header
header_toggle_html = """
        <div style="display:flex; gap:15px; align-items:center;">
            <div onclick="openLanguageModal()" style="color: var(--text-sub); cursor: pointer; font-size: 18px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 12px;"><i class="fas fa-globe"></i></div>
"""

if 'openLanguageModal()' not in content:
    content = content.replace('<div class="header-logo"><i class="fas fa-eye"></i> Blink Test</div>', '<div class="header-logo"><i class="fas fa-eye"></i> Blink Test</div>' + header_toggle_html)

# 4. Update JS Functions
js_mods = r"""
    window.openLanguageModal = function() {
        document.getElementById('language-modal').style.display = 'flex';
        // Mark active
        document.querySelectorAll('.lang-card').forEach(c => c.classList.remove('active'));
        const currentLang = state.lang || 'en';
        const activeCard = document.getElementById(`lang-${currentLang}`);
        if(activeCard) activeCard.classList.add('active');
    };

    window.closeLanguageModal = function() {
        document.getElementById('language-modal').style.display = 'none';
    };

    window.selectLanguage = function(lang) {
        const wasInitial = !state.lang;
        state.lang = lang;
        sessionStorage.setItem('lang', lang);
        if(typeof updateTranslations === 'function') updateTranslations();
        closeLanguageModal();
        
        if(wasInitial) {
            if (state.isPatientMode) {
                navigate('scr-disclaimer');
            } else if(state.isLoggedIn) {
                navigate('scr-dashboard');
            } else {
                navigate('scr-login');
            }
        }
    };
"""

content = re.sub(r'window\.selectLanguage = function\(lang\) \{.*?\};', js_mods, content, flags=re.DOTALL)

# Update DOMContentLoaded
content = content.replace('if (!state.lang) {\n            navigate("scr-language");\n        }', 'if (!state.lang) {\n            openLanguageModal();\n        }')

# Ensure scr-language is removed from hideChrome
content = content.replace(", 'scr-language']", "]")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
