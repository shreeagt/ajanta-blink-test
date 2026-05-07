import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Fix app-shell and global centering
content = re.sub(
    r'\.app-shell\s*\{[^}]*\}',
    '.app-shell { width: 100%; max-width: 500px; margin: 0 auto; background: white; min-height: 100vh; position: relative; display: flex; flex-direction: column; box-shadow: 0 0 50px rgba(0,0,0,0.05); }',
    content
)

# 2. Fix screen behavior
content = re.sub(
    r'\.screen\s*\{[^}]*\}',
    '.screen { display: none; padding: 20px; width: 100%; flex: 1; flex-direction: column; }',
    content
)

# 3. Completely overhaul dashboard to be stable
dashboard_replacement = """
    <!-- Dashboard -->
    <div id="scr-dashboard" class="screen" style="background: #f8fafc; padding: 0;">
        <div style="background: var(--primary-gradient); padding: 40px 24px 80px; border-radius: 0 0 40px 40px; color: white; position: relative; box-shadow: 0 10px 30px rgba(0,94,184,0.15);">
            <div style="text-align: left; margin-bottom: 20px;">
                <div style="font-size: 11px; font-weight: 700; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px;" data-t="hello_rep">Hello, Representative</div>
                <h2 style="font-size: 24px; font-weight: 900; letter-spacing: -0.5px; margin: 5px 0 0 0;" id="dash-so-name">Dashboard</h2>
            </div>
            
            <div style="position: absolute; bottom: -35px; left: 20px; right: 20px; background: white; padding: 22px; border-radius: 24px; box-shadow: 0 15px 45px rgba(0,0,0,0.1); display: flex; gap: 10px; border: 1px solid #fff; z-index: 5;">
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 24px; font-weight: 900; color: var(--primary); line-height: 1.2;" id="stat-today">0</div>
                    <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;" data-t="stat_today">Today</div>
                </div>
                <div style="width: 1px; background: #f1f5f9; height: 35px; align-self: center;"></div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 24px; font-weight: 900; color: #10b981; line-height: 1.2;" id="stat-month">0</div>
                    <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;" data-t="stat_month">Month</div>
                </div>
                <div style="width: 1px; background: #f1f5f9; height: 35px; align-self: center;"></div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 24px; font-weight: 900; color: #f59e0b; line-height: 1.2;" id="stat-visits">0</div>
                    <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;" data-t="stat_total">Total</div>
                </div>
            </div>
        </div>

        <div style="padding: 20px; margin-top: 40px;">
            <div id="dash-goal-card" style="margin-bottom: 25px; background: white; padding: 20px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px; border: 1px solid #f1f5f9;">
                <div style="width: 48px; height: 48px; background: #fffbeb; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 22px;">
                    <i class="fas fa-fire"></i>
                </div>
                <div style="flex: 1;">
                    <h4 style="font-size: 14px; font-weight: 800; color: #1e293b; margin: 0;" data-t="daily_progress">Daily Progress</h4>
                    <p style="font-size: 11px; color: #64748b; font-weight: 600; margin: 2px 0 0 0;" id="dash-motivation">Help 10 patients today to reach your goal!</p>
                </div>
                <div style="font-size: 15px; font-weight: 900; color: var(--primary);" id="dash-percent">0%</div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding: 0 5px;">
                <h3 style="font-size: 16px; font-weight: 800; color:#1e293b; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-history" style="color: var(--primary); font-size: 14px;"></i>
                    Recent Screenings
                </h3>
                <button onclick="shareMyLink()" style="background: white; border: 1.5px solid #eff6ff; padding: 8px 15px; border-radius: 12px; font-size: 11px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <i class="fas fa-share-alt"></i> <span data-t="share_link">Share Link</span>
                </button>
            </div>
            
            <div class="history-list" id="history-list">
                <!-- Items -->
            </div>
            
            <div id="pagination-controls" style="display:flex; justify-content:center; align-items:center; gap:15px; padding:25px 0;">
                <button class="nav-btn" style="width:auto; background:white; color:var(--primary); display:none; padding:10px 18px; border-radius:12px; font-weight:800; border:1px solid #e2e8f0; font-size:12px;" id="btn-prev" onclick="changePage(-1)">
                    <i class="fas fa-chevron-left"></i> <span data-t="prev">Prev</span>
                </button>
                <span id="page-num" style="font-weight:800; font-size:13px; color:#64748b; background:white; padding:8px 16px; border-radius:10px; border:1px solid #e2e8f0;">Page 1</span>
                <button class="nav-btn" style="width:auto; background:white; color:var(--primary); display:none; padding:10px 18px; border-radius:12px; font-weight:800; border:1px solid #e2e8f0; font-size:12px;" id="btn-next" onclick="changePage(1)">
                    <span data-t="next">Next</span> <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        <div style="height: 120px;"></div>
    </div>
"""

# Find the dashboard block and replace it
pattern = r'<!-- Dashboard -->\s*<div id="scr-dashboard".*?</div>\s*<!-- Language Selection Modal -->'
# Note: I need to be careful with the regex to match the dashboard properly
# Let's search for the dashboard div and everything until the next major comment or script
content = re.sub(r'<div id="scr-dashboard" class="screen".*?</div>\s*<div class="bottom-nav-container"', dashboard_replacement + '\n    <div class="bottom-nav-container"', content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
