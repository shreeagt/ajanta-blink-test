import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add CVS Button CSS to the main style block
cvs_css = """
        .cvs-opt-btn {
            flex: 1; padding: 12px 5px; border-radius: 12px; border: 1.5px solid #e2e8f0;
            background: white; color: #64748b; font-size: 13px; font-weight: 700;
            cursor: pointer; transition: 0.3s; text-align: center;
        }
        .cvs-opt-btn.active {
            background: var(--primary); color: white; border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,94,184,0.2);
        }
    </style>
"""
content = content.replace('</style>', cvs_css)

# 2. Fix the JS errors (getElementById and duplicates)
# Remove the broken block
content = re.sub(r'window\.openLanguageModal = function\(.*?\n\s+window\.closeLanguageModal = function\(.*?\n\s+', '', content, flags=re.DOTALL)

# Ensure openLanguageModal is correct
correct_lang_js = """
    window.openLanguageModal = function() {
        document.getElementById('language-modal').style.display = 'flex';
        document.querySelectorAll('.lang-card').forEach(c => c.classList.remove('active'));
        const currentLang = state.lang || 'en';
        const activeCard = document.getElementById(`lang-${currentLang}`);
        if(activeCard) activeCard.classList.add('active');
    };
"""
# Replace any broken versions with this
content = re.sub(r'window\.openLanguageModal = function\(.*?};', correct_lang_js, content, flags=re.DOTALL)

# 3. Add data-t to Thank You screen
content = content.replace('Assessment Complete</h2>', 'Assessment Complete</h2>'.replace('Assessment Complete', '<span data-t=\"assessment_complete_title\">Assessment Complete</span>'))
content = content.replace('Your eye health screening has been securely recorded.</p>', '<p style=\"font-size: 16px; color: #64748b; font-weight: 600;\" data-t=\"assessment_complete_desc\">Your eye health screening has been securely recorded.</p>')
content = content.replace('Download Eye Care Certificate', '<span data-t=\"download_cert\">Download Eye Care Certificate</span>')
content = content.replace('Back to Home', '<span data-t=\"back_home\">Back to Home</span>')
content = content.replace('Representative ID</p>', '<p style=\"font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase;\" data-t=\"rep_id\">Representative ID</p>')

# 4. Add data-t to Result Dashboards
content = content.replace('Blink Analysis Report</span>', '<span style=\"font-size: 12px; font-weight: 900; color: var(--primary); text-transform: uppercase; letter-spacing: 2px; background: #eff6ff; padding: 8px 20px; border-radius: 50px;\" data-t=\"blink_report_title\">Blink Analysis Report</span>')
content = content.replace('YOUR SCORE</p>', '<p style=\"font-size: 14px; color: #64748b; font-weight: 700; margin-bottom: 10px;\" data-t=\"your_score\">YOUR SCORE</p>')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
