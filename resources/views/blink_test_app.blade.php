<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Blink Test Screening</title>

    <style>
        :root {
            --primary: #005eb8;
            --primary-light: #eff6ff;
            --primary-gradient: linear-gradient(135deg, #005eb8 0%, #003d7a 100%);
            --secondary: #f59e0b;
            --secondary-light: #fffbeb;
            --success: #10b981;
            --success-light: #ecfdf5;
            --error: #ef4444;
            --bg-main: #f8fafc;
            --text-main: #0f172a;
            --text-sub: #64748b;
            --glass: rgba(255, 255, 255, 0.8);
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; outline: none; }
        body, html { 
            margin: 0; 
            padding: 0; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #0f172a; 
            color: var(--text-main); 
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .app-shell { 
            width: 100%; 
            max-width: 500px; 
            min-height: 100vh;
            background: #f8fafc; 
            position: relative; 
            display: flex;
            flex-direction: column;
            margin: 0 auto;
            box-shadow: 0 0 100px rgba(0,0,0,0.5); 
            z-index: 1;
        }
        @media (min-width: 501px) {
            .app-shell { min-height: 95vh; border-radius: 40px; margin: 20px auto; }
        }

        .screen { 
            display: none; 
            width: 100%; 
            flex-direction: column; 
            background: #f8fafc;
            animation: fadeIn 0.4s ease-out;
            overflow-y: auto;
            min-height: 100vh;
        }
        .screen.active { display: flex !important; }
        /* Blink test screen fills fully in dark */
        #scr-blink-test { min-height: 100vh; background: var(--primary-gradient); }
        
        .screen::-webkit-scrollbar { width: 0; background: transparent; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse-soft { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        
        /* Animations */
        .anim-screen { animation: screenFade 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes screenFade { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        .btn { border: none; border-radius: 18px; padding: 18px 24px; font-weight: 800; font-size: 16px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; }
        .btn-primary { background: var(--primary-gradient); color: white; box-shadow: 0 10px 25px rgba(0,94,184,0.25); }
        .btn:active { transform: scale(0.98); opacity: 0.9; }
        
        .toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); background: #1e293b; color: white; padding: 12px 24px; border-radius: 50px; font-weight: 700; font-size: 14px; z-index: 9999; opacity: 0; transition: 0.3s; pointer-events: none; white-space: nowrap; }
    
        .cvs-opt-btn {
            flex: 1; padding: 12px 5px; border-radius: 12px; border: 1.5px solid #e2e8f0;
            background: white; color: #64748b; font-size: 13px; font-weight: 700;
            cursor: pointer; transition: 0.3s; text-align: center;
        }
        .cvs-opt-btn.active {
            background: var(--primary); color: white; border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,94,184,0.2);
        }

        .header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 24px; background: #005eb8;
            border-bottom: none;
            position: sticky; top: 0; z-index: 1001;
            width: 100%;
            color: white;
        }
        .header-logo {
            font-size: 18px; font-weight: 900; color: var(--primary);
            display: flex; align-items: center; gap: 8px; letter-spacing: -0.5px;
        }
        .header-actions { display: flex; gap: 12px; align-items: center; }
        .header-icon-btn {
            width: 40px; height: 40px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; cursor: pointer; transition: 0.3s;
            background: var(--primary-light); color: var(--primary);
            border: 1px solid rgba(0,94,184,0.1);
        }
        .header-icon-btn:hover { background: #eff6ff; transform: translateY(-2px); }
        .header-icon-btn.logout { color: #94a3b8; }
        .header-icon-btn.logout:hover { color: var(--error); background: #fef2f2; border-color: #fee2e2; }

        .history-list { display: flex; flex-direction: column; gap: 12px; width: 100%; }
        .history-item {
            background: white; border-radius: 20px; padding: 18px;
            display: flex; justify-content: space-between; align-items: center;
            border: 1px solid #f1f5f9; box-shadow: var(--shadow-sm);
            transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
        }
        .history-item:active { transform: scale(0.98); background: #f8fafc; box-shadow: none; }
        .history-info h4 { font-size: 15px; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.3px; }
        .history-info p { font-size: 11px; font-weight: 700; color: var(--text-sub); margin: 4px 0 0 0; }
        .history-badge { background: #f0f9ff; color: #005eb8; padding: 6px 12px; border-radius: 12px; font-weight: 900; font-size: 12px; border: 1px solid #e0f2fe; }
        
        .stat-card {
            background: white; 
            padding: 16px 8px; 
            border-radius: 24px; 
            text-align: center; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.8);
            transition: 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }

        .bottom-nav-container {
            position: fixed;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 40px);
            max-width: 460px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 12px 10px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.4);
            z-index: 9000;
        }
        .bottom-nav {
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: 100%;
        }
        .nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 0;
            border-radius: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #94a3b8;
            gap: 4px;
            cursor: pointer;
        }
        .nav-item i { font-size: 20px; }
        .nav-item span { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .fab {
            background: var(--primary-gradient);
            color: white;
            border-radius: 50px;
            box-shadow: 0 8px 20px rgba(0,94,184,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 800;
            gap: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
            flex: none; /* Override nav-item flex: 1 */
            flex-direction: row; /* Override nav-item column */
        }
        .fab i { font-size: 18px; margin: 0; }
        .fab span { font-size: 14px; color: white; text-transform: none; margin: 0; }
        .fab:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0,94,184,0.4);
        }
        
        .stat-card {
            background: white;
            border-radius: 24px;
            padding: 15px 10px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
            transition: 0.3s;
        }

        .lightbox {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }
        .lightbox-close {
            position: absolute;
            top: 20px; right: 20px;
            color: white; font-size: 30px; cursor: pointer;
            z-index: 10001;
        }
    </style>



    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="app-shell">
    <div id="splash-screen" style="position: fixed; inset: 0; background: var(--primary-gradient); z-index: 10000; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white;">
        <div style="width: 100px; height: 100px; background: white; border-radius: 30px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 45px; margin-bottom: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); animation: pulse-soft 2s infinite;">
            <i class="fas fa-eye-slash"></i>
        </div>
        <h1 style="font-size: 24px; font-weight: 900; letter-spacing: 1px; margin: 0;">AJANTA BLINK</h1>
        <div style="margin-top: 30px; width: 40px; height: 4px; background: rgba(255,255,255,0.3); border-radius: 10px; overflow: hidden;">
            <div style="width: 100%; height: 100%; background: white; animation: loading-bar 1.5s infinite ease-in-out;"></div>
        </div>
        <style>@keyframes loading-bar { from { transform: translateX(-100%); } to { transform: translateX(100%); } }</style>
    </div>

    <div id="toast" class="toast"></div>

    <div class="header" id="app-header" style="display:none; border-bottom: none; background: transparent; color: white; flex-shrink: 0; position: absolute; top: 0; left: 0; width: 100%; z-index: 1000;">
        <div class="header-logo" style="color: white; font-weight: 800; font-size: 18px; padding-left: 5px;"><i class="fas fa-eye"></i> Ajanta Blink</div>
        <div class="header-actions">
            <div onclick="openLanguageModal()" class="header-icon-btn" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: white;">
                <i class="fas fa-globe"></i>
            </div>
            <div onclick="logout()" class="header-icon-btn logout" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: white;">
                <i class="fas fa-sign-out-alt"></i>
            </div>
        </div>
    </div>


    <!-- Login -->
    <style>
        #scr-login.active {
            display: flex !important;
            flex-direction: column;
            justify-content: center;
            min-height: 100%;
        }
        #scr-login .input-group input {
            width: 100%;
            padding-left: 55px;
            height: 60px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 600;
            border: 2px solid #f1f5f9;
            transition: 0.3s;
            outline: none;
            color: #1e293b;
        }
        #scr-login .input-group input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 94, 184, 0.1);
        }
    
        
        
    
        
    </style>



    <div id="scr-login" class="screen" style="background: linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%); justify-content: center; align-items: center; padding: 30px;">
        <!-- Top Branding -->
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="width: 100px; height: 100px; margin: 0 auto 20px; background: white; border-radius: 30px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 45px; box-shadow: var(--shadow-xl);">
                <i class="fas fa-eye-slash"></i>
            </div>
            <h1 style="font-size: 32px; font-weight: 900; color: var(--text-main); letter-spacing: -1px; margin: 0;">Blink Test Awareness</h1>
            <p style="color: var(--text-sub); font-weight: 700; font-size: 15px; margin-top: 8px;">Field Force Diagnostics Portal</p>
        </div>

        <!-- Login Card -->
        <div style="background: white; padding: 40px 30px; border-radius: 40px; box-shadow: var(--shadow-xl); border: 1px solid #fff; width: 100%;">
            <div style="margin-bottom: 30px; text-align: center;">
                <span style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px;">Sign in to start screening</span>
            </div>
            
            <div class="input-group" style="margin-bottom: 20px;">
                <div style="position: relative;">
                    <i class="far fa-user" style="position: absolute; left: 22px; top: 22px; color: var(--primary); font-size: 18px; z-index: 10;"></i>
                    <input type="text" id="login-id" placeholder="Employee Code" style="width: 100%; height: 64px; padding-left: 60px; border-radius: 20px; border: 2px solid #f1f5f9; background: #f8fafc; font-weight: 700; font-size: 16px; transition: 0.3s;">
                </div>
            </div>
            
            <div class="input-group" style="margin-bottom: 30px;">
                <div style="position: relative;">
                    <i class="fas fa-key" style="position: absolute; left: 22px; top: 22px; color: var(--secondary); font-size: 18px; z-index: 10;"></i>
                    <input type="password" id="login-pass" placeholder="Password" style="width: 100%; height: 64px; padding-left: 60px; border-radius: 20px; border: 2px solid #f1f5f9; background: #f8fafc; font-weight: 700; font-size: 16px; transition: 0.3s;">
                </div>
            </div>
            
            <button class="btn btn-primary" onclick="doLogin()" id="btn-login" style="height: 68px; border-radius: 22px; font-size: 18px; font-weight: 900; box-shadow: 0 15px 35px rgba(0,94,184,0.25);">
                Enter Dashboard <i class="fas fa-arrow-right"></i>
            </button>
            
            <div style="margin-top: 30px; text-align: center;">
                <div style="display: inline-block; padding: 10px 20px; background: #f8fafc; border-radius: 50px; border: 1px solid #f1f5f9;">
                    <p style="font-size: 12px; color: #94a3b8; font-weight: 700; margin: 0;">Ajanta Pharma Ltd. Internal Portal</p>
                </div>
            </div>
        </div>
    </div>
    <!-- End scr-login -->
    
    <style>
            @keyframes pulse {
                0% { transform: rotate(15deg) scale(1); opacity: 0.1; }
                50% { transform: rotate(15deg) scale(1.2); opacity: 0.2; }
                100% { transform: rotate(15deg) scale(1); opacity: 0.1; }
            }
    </style>

    <!-- Blink Test Disclaimer -->

    <div id="scr-disclaimer" class="screen anim-screen" style="padding: 0; background: #f8fafc; flex-direction: column;">
        <div style="background: var(--primary-gradient); width: 100%; padding: 45px 24px 60px; border-radius: 0 0 50px 50px; color: white; text-align: center; position: relative; box-shadow: var(--shadow-lg);">
            <div style="width: 76px; height: 76px; background: rgba(255,255,255,0.15); border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px; backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2 style="font-size: 30px; font-weight: 900; letter-spacing: -1px; margin-bottom: 8px;" data-t="screening_guide">Screening Guide</h2>
            <p style="font-size: 15px; color: rgba(255,255,255,0.8); font-weight: 600;" data-t="blink_analysis_desc">Dual-Stage Eye Health Assessment</p>
            
            <div id="so-facilitator" style="display:none; margin-top: 25px; font-size: 13px; font-weight: 800; color: white; background: rgba(0,0,0,0.15); padding: 12px 24px; border-radius: 50px; display: inline-flex; align-items: center; gap: 8px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                <i class="fas fa-user-check" style="font-size: 14px;"></i> <span id="so-name-display"></span>
            </div>
        </div>

        <div style="padding: 24px; max-width: 500px; margin: 0 auto; width: 100%;">
            <div style="background: white; padding: 35px 24px; border-radius: 35px; box-shadow: var(--shadow-md); border: 1px solid #f1f5f9; margin-bottom: 30px;">
                <!-- Steps -->
                <div style="display:flex; gap:18px; margin-bottom:30px; align-items: flex-start;">
                    <div style="width:40px; height:40px; background:var(--primary-light); border-radius:14px; display:flex; align-items:center; justify-content:center; color:var(--primary); font-weight:900; flex-shrink:0; font-size: 16px;">1</div>
                    <div style="text-align: left;">
                        <div style="font-size:17px; font-weight:900; color:var(--text-main); margin-bottom:4px;" data-t="step1_title">AI Blink Analysis</div>
                        <div style="font-size:13px; color:var(--text-sub); font-weight:600; line-height:1.5;" data-t="step1_desc">A 15-second AI scan to detect your natural blink rate and eye lubrication.</div>
                    </div>
                </div>
                <div style="display:flex; gap:18px; margin-bottom:30px; align-items: flex-start;">
                    <div style="width:40px; height:40px; background:var(--secondary-light); border-radius:14px; display:flex; align-items:center; justify-content:center; color:var(--secondary); font-weight:900; flex-shrink:0; font-size: 16px;">2</div>
                    <div style="text-align: left;">
                        <div style="font-size:17px; font-weight:900; color:var(--text-main); margin-bottom:4px;" data-t="step2_title">CVS Symptom Check</div>
                        <div style="font-size:13px; color:var(--text-sub); font-weight:600; line-height:1.5;" data-t="step2_desc">Quick assessment to identify Computer Vision Syndrome and digital strain.</div>
                    </div>
                </div>
                <div style="display:flex; gap:18px; align-items: flex-start;">
                    <div style="width:40px; height:40px; background:var(--success-light); border-radius:14px; display:flex; align-items:center; justify-content:center; color:var(--success); font-weight:900; flex-shrink:0; font-size: 16px;">3</div>
                    <div style="text-align: left;">
                        <div style="font-size:17px; font-weight:900; color:var(--text-main); margin-bottom:4px;" data-t="step3_title">Combined Report</div>
                        <div style="font-size:13px; color:var(--text-sub); font-weight:600; line-height:1.5;" data-t="step3_desc">Get a comprehensive medical-grade report with personalized eye care tips.</div>
                    </div>
                </div>
            </div>

            <div style="background: white; padding: 22px; border-radius: 28px; border: 1px solid #f1f5f9; margin-bottom: 30px; text-align: left; display: flex; align-items: center; gap: 15px; box-shadow: var(--shadow-sm);">
                <div style="width: 48px; height: 48px; background: #f0f9ff; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 22px;">
                    <i class="fas fa-lock"></i>
                </div>
                <div style="flex: 1;">
                    <span style="font-size: 14px; font-weight: 800; color: var(--text-main); display: block; margin-bottom: 2px;" data-t="privacy_title">Privacy Guaranteed</span>
                    <p style="font-size: 11px; color: var(--text-sub); font-weight: 700; line-height: 1.4; margin: 0;" data-t="privacy_desc">Video is processed locally. No biometric data is ever stored or shared.</p>
                </div>
            </div>

            <button class="btn btn-primary" onclick="startBlinkTest()" style="height: 68px; font-size: 18px; border-radius: 24px; box-shadow: var(--shadow-lg); font-weight: 900;">
                <span data-t="accept_proceed">Start Assessment</span> <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Blink Test Main Screen -->
    <div id="scr-blink-test" class="screen anim-screen" style="flex-direction: column; color: white;">
        <div style="text-align: center; margin-top: 30px; margin-bottom: 40px;">
            <h2 style="font-size: 22px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 5px;" data-t="analyzing_blinks">Analyzing Blinks...</h2>
            <p style="font-size: 14px; color: #94a3b8; font-weight: 600;" data-t="stare_center">Please stare at the center</p>
        </div>
        
        <div style="position: relative; width: 320px; height: 320px; margin: 0 auto;">
            <!-- Pulsing Rings -->
            <div class="test-pulse-ring" style="animation-delay: 0s;"></div>
            <div class="test-pulse-ring" style="animation-delay: 0.5s;"></div>
            
            <div style="position: relative; width: 100%; height: 100%; z-index: 5;">
                <video id="input_video" style="display: none;" playsinline></video>
                <img src="{{ asset('assets/images/stare_creative.png') }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; box-shadow: 0 0 50px rgba(0,94,184,0.5); border: 4px solid rgba(255,255,255,0.2);">
                <div id="blink-ripple" class="ripple-effect"></div>
                
                <!-- Timer Overlay -->
                <div style="position: absolute; bottom: -20px; left: 50%; transform: translateX(-50%); background: var(--primary-gradient); padding: 10px 25px; border-radius: 50px; font-size: 24px; font-weight: 900; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 3px solid #fff;">
                    <span id="test-timer">15</span><span style="font-size: 14px; margin-left: 2px;">s</span>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 80px; text-align: center;">
            <div style="display: inline-flex; align-items: center; gap: 15px; background: rgba(255,255,255,0.05); padding: 15px 30px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="text-align: left;">
                    <p style="font-size: 12px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;" data-t="live_count">Live Count</p>
                    <div style="font-size: 32px; font-weight: 900; color: #38bdf8;" id="live-blink-count">0</div>
                </div>
                <div style="width: 1px; height: 40px; background: rgba(255,255,255,0.1);"></div>
                <div style="text-align: left;">
                    <p style="font-size: 12px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;" data-t="live_status">Status</p>
                    <div style="font-size: 14px; font-weight: 800; color: #10b981;" id="test-status-msg">Camera Active</div>
                </div>
            </div>
        </div>

        <style>
            .test-pulse-ring {
                position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                border: 2px solid var(--primary); border-radius: 50%;
                animation: testPulse 2s cubic-bezier(0, 0.45, 0.55, 1) infinite;
                opacity: 0;
            }
            @keyframes testPulse {
                0% { transform: scale(1); opacity: 0.8; }
                100% { transform: scale(1.4); opacity: 0; }
            }
        
        
        
    
        
    </style>


    </div>

    <!-- Result Screen -->
    <div id="scr-test-result" class="screen anim-screen" style="background: #f8fafc; padding: 100px 20px 100px;">
        <div style="background: white; width: 100%; padding: 35px 24px; border-radius: 40px; box-shadow: var(--shadow-xl); border: 1px solid #fff; position: relative;">
            <div style="text-align: center; margin-bottom: 30px;">
                <span style="font-size: 11px; font-weight: 900; color: var(--primary); text-transform: uppercase; letter-spacing: 2px; background: var(--primary-light); padding: 8px 24px; border-radius: 50px;" data-t="blink_report_title">Blink Analysis Report</span>
            </div>
            
            <div style="text-align: center; margin-bottom: 30px;">
                <p style="font-size: 13px; color: var(--text-sub); font-weight: 800; margin-bottom: 5px;" data-t="your_score">YOUR SCORE</p>
                <div style="display: flex; align-items: baseline; justify-content: center; gap: 8px;">
                    <h2 style="font-size: 80px; font-weight: 900; color: var(--text-main); line-height: 1;" id="scaled-blink-count">0</h2>
                    <span style="font-size: 20px; font-weight: 800; color: var(--text-sub);">/ min</span>
                </div>
            </div>

            <div style="margin-bottom: 35px;">
                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 900; color: var(--text-sub); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px;">
                    <span>Stability Scale</span>
                    <span id="report-date"></span>
                </div>
                <div style="position: relative; height: 14px; background: #f1f5f9; border-radius: 20px; display: flex; overflow: hidden; border: 1px solid #e2e8f0;">
                    <div style="flex: 6; background: #10b981;"></div>
                    <div style="flex: 4; background: #34d399;"></div>
                    <div style="flex: 3; background: #38bdf8;"></div>
                    <div style="flex: 3; background: #fbbf24;"></div>
                    <div style="flex: 2; background: #f97316;"></div>
                    <div style="flex: 2; background: #ef4444;"></div>
                    <div id="result-indicator" style="position: absolute; top: -7px; left: 0%; width: 28px; height: 28px; background: white; border-radius: 50%; border: 6px solid var(--primary); box-shadow: var(--shadow-md); transition: all 1s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
                </div>
            </div>

            <div style="text-align: center; margin-bottom: 35px;">
                <div id="result-tier-badge" style="display: inline-block; padding: 10px 30px; border-radius: 50px; font-size: 16px; font-weight: 900; text-transform: uppercase; margin-bottom: 15px; box-shadow: var(--shadow-md); background: var(--primary); color: white;">Optimal</div>
                <h3 id="result-status" style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0 0 10px 0; line-height: 1.2;">Highly stable tear film</h3>
                <p id="result-analysis" style="font-size: 15px; color: var(--text-sub); font-weight: 600; line-height: 1.6; margin: 0;">Your blinking pattern indicates excellent moisture retention.</p>
            </div>

            <!-- WhatsApp Sharing -->
            <div style="background: #f0fdf4; padding: 22px; border-radius: 28px; border: 1px solid #dcfce7; text-align: left; margin-top: 20px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                    <div style="width: 40px; height: 40px; background: #25d366; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <span style="font-size: 15px; font-weight: 900; color: #166534;">Share with Patient</span>
                </div>
                <div style="display: flex; gap: 10px;">
                    <input type="tel" id="whatsapp-num" placeholder="WhatsApp Number" style="flex: 1; height: 54px; padding: 0 18px; border-radius: 16px; border: 1.5px solid #bbf7d0; font-size: 15px; font-weight: 700;">
                    <button onclick="shareToWhatsApp()" style="background: #25d366; color: white; border: none; padding: 0 24px; border-radius: 16px; font-weight: 900; font-size: 15px; box-shadow: 0 8px 15px rgba(37,211,102,0.2);">
                        Share
                    </button>
                </div>
            </div>
            
            <div style="margin-top: 30px; display: flex; flex-direction: column; gap: 12px;">
                <button class="btn btn-primary" onclick="window.startCvsScreening()" style="height: 64px; border-radius: 22px; font-size: 16px; font-weight: 900; background: #f59e0b; box-shadow: 0 10px 25px rgba(245,158,11,0.2);">
                    Next: CVS Screening <i class="fas fa-arrow-right"></i>
                </button>
                <button class="btn btn-primary" onclick="window.onTestFinish()" style="height: 64px; border-radius: 22px; font-size: 16px; font-weight: 900;">
                    <span data-t="finish_close">Finish & Close</span> <i class="fas fa-check-circle"></i>
                </button>
            </div>
        </div>
    </div>

        <!-- Hidden PDF Template for Premium Export -->
        <div id="pdf-template" style="display: none; width: 210mm; padding: 20mm; background: white; font-family: 'Plus Jakarta Sans', sans-serif; color: #0f172a;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 4px solid var(--primary); padding-bottom: 20px; margin-bottom: 30px;">
                <div>
                    <h1 style="color: var(--primary); font-size: 32px; font-weight: 900; margin: 0;">AJANTA</h1>
                    <p style="font-size: 14px; font-weight: 700; color: #64748b; margin: 5px 0 0;">Blink Test Awareness Campaign</p>
                </div>
                <div style="text-align: right;">
                    <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0;">BLINK ANALYSIS REPORT</h2>
                    <p style="font-size: 12px; color: #94a3b8; font-weight: 600; margin: 5px 0 0;">Date: <span id="pdf-date"></span></p>
                </div>
            </div>

            <!-- Patient/SO Info -->
            <div style="display: flex; gap: 40px; margin-bottom: 40px; background: #f8fafc; padding: 20px; border-radius: 15px;">
                <div style="flex: 1;">
                    <p style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px;">Screening Location</p>
                    <p style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0;">Local Field Screening Hub</p>
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px;">Representative</p>
                    <p style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0;" id="pdf-so-name">-</p>
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px;">Report ID</p>
                    <p style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0;" id="pdf-report-id">-</p>
                </div>
            </div>

            <!-- Result Section -->
            <div style="display: flex; gap: 30px; align-items: flex-start; margin-bottom: 50px;">
                <div style="width: 200px; height: 200px; background: #eff6ff; border-radius: 30px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px solid #bfdbfe;">
                    <p style="font-size: 12px; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 5px;">Blink Count</p>
                    <div style="font-size: 72px; font-weight: 900; color: #0f172a;" id="pdf-blink-count">0</div>
                    <p style="font-size: 12px; font-weight: 700; color: #64748b;">(per minute)</p>
                </div>
                <div style="flex: 1; padding-top: 20px;">
                    <div id="pdf-tier-badge" style="display: inline-block; padding: 10px 25px; border-radius: 50px; font-size: 24px; font-weight: 900; text-transform: uppercase; margin-bottom: 15px;">-</div>
                    <h3 id="pdf-status" style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 15px;">-</h3>
                    <p id="pdf-analysis" style="font-size: 15px; color: #475569; font-weight: 600; line-height: 1.6;"></p>
                </div>
            </div>

            
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

            <!-- Visual Scale (Health Bar) -->
            <div style="margin-bottom: 60px;">
                <h4 style="font-size: 14px; font-weight: 800; color: #1e293b; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px;">Blink Rate Comparison Scale</h4>
                <div style="position: relative; height: 40px; background: #f1f5f9; border-radius: 20px; display: flex; overflow: hidden; border: 1px solid #e2e8f0;">
                    <div style="flex: 6; background: #10b981; display: flex; align-items: center; justify-content: center; font-size: 9px; color: white; font-weight: 800; border-right: 1px solid rgba(255,255,255,0.3);">OPTIMAL</div>
                    <div style="flex: 4; background: #34d399; display: flex; align-items: center; justify-content: center; font-size: 9px; color: white; font-weight: 800; border-right: 1px solid rgba(255,255,255,0.3);">EXCELLENT</div>
                    <div style="flex: 3; background: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 9px; color: white; font-weight: 800; border-right: 1px solid rgba(255,255,255,0.3);">HEALTHY</div>
                    <div style="flex: 3; background: #fbbf24; display: flex; align-items: center; justify-content: center; font-size: 9px; color: white; font-weight: 800; border-right: 1px solid rgba(255,255,255,0.3);">MILD</div>
                    <div style="flex: 2; background: #f97316; display: flex; align-items: center; justify-content: center; font-size: 9px; color: white; font-weight: 800; border-right: 1px solid rgba(255,255,255,0.3);">MODERATE</div>
                    <div style="flex: 2; background: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 9px; color: white; font-weight: 800;">HIGH</div>
                </div>
                <div style="position: relative; margin-top: 10px;">
                    <!-- Indicator Triangle -->
                    <div id="pdf-indicator" style="position: absolute; top: -55px; left: 50%; transform: translateX(-50%); text-align: center;">
                        <div style="width: 2px; height: 50px; background: #0f172a; margin: 0 auto;"></div>
                        <div style="font-size: 11px; font-weight: 900; color: #0f172a; background: white; padding: 2px 8px; border: 2px solid #0f172a; border-radius: 5px; margin-top: 2px;">YOUR SCORE</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 10px; font-weight: 700; color: #94a3b8; padding: 0 10px; margin-top: 5px;">
                        <span>3</span><span>7</span><span>11</span><span>14</span><span>17</span><span>19</span><span>20+</span>
                    </div>
                </div>
            </div>

            <!-- Footer / Disclaimer -->
            <div style="border-top: 1px solid #e2e8f0; padding-top: 30px; display: flex; justify-content: space-between; align-items: flex-end;">
                <div style="max-width: 400px;">
                    <p style="font-size: 10px; font-weight: 700; color: #1e293b; margin-bottom: 5px;">Disclaimer:</p>
                    <p style="font-size: 9px; color: #94a3b8; line-height: 1.5; margin: 0;">This report is generated by an AI-assisted blink analysis tool and is intended for preliminary screening purposes only. It is not a clinical diagnosis. Please consult a qualified ophthalmologist for a complete ocular health evaluation.</p>
                </div>
                <div style="text-align: right;">
                    <p style="font-size: 12px; font-weight: 800; color: var(--primary);">AJANTA PHARMA LTD.</p>
                    <p style="font-size: 10px; color: #94a3b8; font-weight: 600;">Committed to Eye Health Worldwide</p>
                </div>
            </div>
        </div>

    <!-- Thank You Screen (for Patients) -->


    <!-- New Premium Thank You Screen -->
    <div id="scr-thank-you" class="screen anim-screen" style="background: #f8fafc; padding: 80px 20px 20px;">
        <div style="background: white; width: 100%; padding: 40px 24px; border-radius: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.06); border: 1px solid #fff; margin: 0 auto; max-width: 500px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 80px; height: 80px; background: #f0fdf4; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 40px; margin: 0 auto 20px; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.1);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 style="font-size: 32px; font-weight: 900; color: #0f172a; margin-bottom: 10px;"><span data-t="assessment_complete_title">Assessment Complete</span></h2>
                <p style="font-size: 16px; color: #64748b; font-weight: 600;" data-t="assessment_complete_desc">Your eye health screening has been securely recorded.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 35px;">
                <div style="background: #eff6ff; padding: 20px; border-radius: 24px; border: 1px solid #dbeafe; text-align: center;">
                    <p style="font-size: 11px; font-weight: 800; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Blink Rate</p>
                    <div style="font-size: 28px; font-weight: 900; color: #1e3a8a;" id="ty-blink-score">0</div>
                    <p style="font-size: 12px; font-weight: 700; color: #60a5fa;" id="ty-blink-status">Normal</p>
                </div>
                <div style="background: #fffbeb; padding: 20px; border-radius: 24px; border: 1px solid #fef3c7; text-align: center;">
                    <p style="font-size: 11px; font-weight: 800; color: #d97706; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">CVS Score</p>
                    <div style="font-size: 28px; font-weight: 900; color: #92400e;" id="ty-cvs-score">0</div>
                    <p style="font-size: 12px; font-weight: 700; color: #f59e0b;" id="ty-cvs-status">Healthy</p>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                <button class="btn" onclick="downloadCombinedPDF()" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; height: 64px; border-radius: 20px; font-weight: 800; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0 10px 25px rgba(217, 119, 6, 0.25); border: none;">
                    <i class="fas fa-award"></i> <span data-t="download_cert">Download Eye Care Certificate</span>
                </button>
                <button class="btn" onclick="window.location.href='/'" style="background: white; color: #64748b; height: 54px; border-radius: 18px; font-weight: 700; font-size: 14px; border: 1px solid #e2e8f0; margin-top: 10px;">
                    <span data-t="back_home">Back to Home</span>
                </button>
            </div>

            <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid #f1f5f9; text-align: center;">
                <p style="font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase;" data-t="rep_id">Representative ID</p>
                <p style="font-size: 15px; font-weight: 800; color: #1e293b;" id="thank-you-so-code">---</p>
            </div>
        </div>
    </div>

    <!-- CVS Screening Screen -->
    <div id="scr-cvs-screening" class="screen" style="background: #f8fafc; padding: 0;">
        <div style="background: var(--primary-gradient); width: 100%; padding: 100px 24px 50px; border-radius: 0 0 50px 50px; color: white; text-align: center; position: relative; box-shadow: var(--shadow-lg);">
            <div style="width: 70px; height: 70px; background: rgba(255,255,255,0.15); border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 15px; backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-laptop-medical"></i>
            </div>
            <h2 style="font-size: 28px; font-weight: 900; letter-spacing: -1px; margin-bottom: 5px;" data-t="symptom_assessment">Symptom Check</h2>
            <p style="font-size: 14px; color: rgba(255,255,255,0.8); font-weight: 600;" data-t="cvs_subtitle">CVS Severity Assessment</p>
        </div>

        <div style="padding: 24px; max-width: 500px; margin: 0 auto; width: 100%;">
            <div id="cvs-questions-container" style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px;">
                <!-- Symptoms injected here -->
            </div>

            <div style="background: white; padding: 25px; border-radius: 35px; box-shadow: var(--shadow-md); border: 1px solid #f1f5f9; text-align: center; margin-bottom: 30px;">
                <p style="font-size: 12px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;" data-t="current_cvs_score">Running Score</p>
                <div id="cvs-running-score" style="font-size: 45px; font-weight: 900; color: var(--primary); line-height: 1;">0</div>
            </div>

            <button class="btn btn-primary" onclick="window.submitCvsScreening()" style="height: 68px; border-radius: 24px; font-size: 18px; font-weight: 900; box-shadow: var(--shadow-lg);">
                <span data-t="complete_assessment">Complete & Analyze</span> <i class="fas fa-check-double"></i>
            </button>
        </div>
        <div style="height: 50px;"></div>
    </div>


    <!-- CVS Result Screen -->
    <div id="scr-cvs-result" class="screen anim-screen" style="background: #f8fafc; padding: 100px 20px 100px;">
        <div style="background: white; width: 100%; padding: 35px 24px; border-radius: 40px; box-shadow: var(--shadow-xl); border: 1px solid #fff; position: relative;">
            <div style="text-align: center; margin-bottom: 30px;">
                <span style="font-size: 11px; font-weight: 900; color: var(--secondary); text-transform: uppercase; letter-spacing: 2px; background: var(--secondary-light); padding: 8px 24px; border-radius: 50px;">CVS Assessment Result</span>
            </div>
            
            <div style="text-align: center; margin-bottom: 30px;">
                <p style="font-size: 13px; color: var(--text-sub); font-weight: 800; margin-bottom: 5px;" data-t="freq">TOTAL CVS SCORE</p>
                <div style="display: flex; align-items: baseline; justify-content: center; gap: 8px;">
                    <h2 style="font-size: 80px; font-weight: 900; color: var(--text-main); line-height: 1;" id="cvs-final-score">0</h2>
                    <span style="font-size: 20px; font-weight: 800; color: var(--text-sub);">/ 32</span>
                </div>
            </div>

            <div style="margin-bottom: 35px;">
                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 900; color: var(--text-sub); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px;">
                    <span>Severity Scale</span>
                    <span id="cvs-report-date"></span>
                </div>
                <div style="position: relative; height: 14px; background: #f1f5f9; border-radius: 20px; display: flex; overflow: hidden; border: 1px solid #e2e8f0;">
                    <div style="flex: 6; background: #10b981;"></div>
                    <div style="flex: 12; background: #fbbf24;"></div>
                    <div style="flex: 14; background: #ef4444;"></div>
                    <div id="cvs-result-indicator" style="position: absolute; top: -7px; left: 0%; width: 28px; height: 28px; background: white; border-radius: 50%; border: 6px solid var(--secondary); box-shadow: var(--shadow-md); transition: all 1s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
                </div>
            </div>

            <div style="text-align: center; margin-bottom: 35px;">
                <div id="cvs-tier-badge" style="display: inline-block; padding: 10px 30px; border-radius: 50px; font-size: 16px; font-weight: 900; text-transform: uppercase; margin-bottom: 15px; box-shadow: var(--shadow-md); background: var(--secondary); color: white;">Moderate</div>
                <h3 id="cvs-status-title" style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0 0 10px 0; line-height: 1.2;">Significant Eye Strain</h3>
                <p id="cvs-analysis-text" style="font-size: 15px; color: var(--text-sub); font-weight: 600; line-height: 1.6; margin: 0;">Your score indicates moderate digital eye strain symptoms.</p>
            </div>

            <div style="margin-top: 30px; padding-top: 25px; border-top: 2px dashed #f1f5f9; display: flex; flex-direction: column; gap: 12px;">
                <button class="btn btn-primary" onclick="window.onTestFinish()" style="height: 64px; border-radius: 22px; font-size: 16px; font-weight: 900; box-shadow: var(--shadow-lg);">
                    <span data-t="finish_close">Finish & Close</span> <i class="fas fa-check-circle"></i>
                </button>
            </div>
        </div>
    </div>

    

    <!-- Dashboard -->
    
    <!-- Dashboard -->
    <div id="scr-dashboard" class="screen" style="padding: 0;">
        <div style="background: var(--primary-gradient); padding: 55px 24px 80px; border-radius: 0 0 45px 45px; color: white; position: relative; box-shadow: 0 15px 40px rgba(0,94,184,0.15);">
            <div style="text-align: left; margin-bottom: 25px; margin-top: 10px;">
                <div style="font-size: 12px; font-weight: 800; opacity: 0.8; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px;" data-t="hello_rep">Hello, Representative</div>
                <h2 style="font-size: 26px; font-weight: 900; letter-spacing: -1px; margin: 0; line-height: 1.1;" id="dash-so-name">Dashboard</h2>
            </div>
            
            <div style="position: absolute; bottom: -50px; left: 20px; right: 20px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; z-index: 10;">
                <div class="stat-card">
                    <div style="font-size: 20px; font-weight: 900; color: var(--primary); line-height: 1;" id="stat-today">0</div>
                    <div style="font-size: 9px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; margin-top: 6px; letter-spacing: 0.5px; line-height: 1.2;" data-t="stat_today">Today</div>
                </div>
                <div class="stat-card">
                    <div style="font-size: 20px; font-weight: 900; color: var(--success); line-height: 1;" id="stat-month">0</div>
                    <div style="font-size: 9px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; margin-top: 6px; letter-spacing: 0.5px; line-height: 1.2;" data-t="stat_month">Month</div>
                </div>
                <div class="stat-card">
                    <div style="font-size: 20px; font-weight: 900; color: var(--secondary); line-height: 1;" id="stat-visits">0</div>
                    <div style="font-size: 9px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; margin-top: 6px; letter-spacing: 0.5px; line-height: 1.2;" data-t="stat_total">Total</div>
                </div>
            </div>
        </div>

        <div style="padding: 24px; margin-top: 55px;">
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


    <!-- ═══ Test Detail Screen ═══ -->
    <div id="scr-detail" class="screen anim-screen" style="background: #f8fafc; padding: 0;">
        <!-- Back header -->
        <div style="background: var(--primary-gradient); padding: 100px 24px 30px; color: white; position: relative;">
            <button onclick="navigate('scr-dashboard')" style="position:absolute; top:80px; left:20px; background: rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2); border-radius:12px; color:white; width:38px; height:38px; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div style="text-align:center; padding-top:10px;">
                <div style="font-size:11px; font-weight:800; opacity:0.7; text-transform:uppercase; letter-spacing:2px; margin-bottom:8px;">Screening Record</div>
                <div style="font-size:26px; font-weight:900; letter-spacing:-1px;" id="det-sid">SID-000000</div>
                <div style="margin-top:12px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                    <div id="det-badge" style="background:rgba(255,255,255,0.15); padding:6px 16px; border-radius:50px; font-size:11px; font-weight:900; border:1px solid rgba(255,255,255,0.25);"></div>
                    <div id="det-date" style="background:rgba(255,255,255,0.1); padding:6px 16px; border-radius:50px; font-size:11px; font-weight:700; opacity:0.8;"></div>
                </div>
            </div>
        </div>

        <div style="padding: 24px; padding-bottom: 40px;">
            <!-- Blink Score Card -->
            <div style="background: white; border-radius: 32px; padding: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: 1px solid #f1f5f9; margin-bottom: 16px;">
                <div style="text-align:center; margin-bottom:20px;">
                    <div style="font-size:12px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:6px;">Blink Rate</div>
                    <div style="display:flex; align-items:baseline; justify-content:center; gap:6px;">
                        <div style="font-size:72px; font-weight:900; color:#0f172a; line-height:1;" id="det-blink">0</div>
                        <span style="font-size:16px; font-weight:800; color:#94a3b8;">/ min</span>
                    </div>
                </div>
                <div style="position:relative; height:12px; background:#f1f5f9; border-radius:20px; display:flex; overflow:hidden; border:1px solid #e2e8f0; margin-bottom:20px;">
                    <div style="flex:6; background:#10b981;"></div>
                    <div style="flex:4; background:#34d399;"></div>
                    <div style="flex:3; background:#38bdf8;"></div>
                    <div style="flex:3; background:#fbbf24;"></div>
                    <div style="flex:2; background:#f97316;"></div>
                    <div style="flex:2; background:#ef4444;"></div>
                    <div id="det-indicator" style="position:absolute; top:-8px; left:5%; width:28px; height:28px; background:white; border-radius:50%; border:5px solid var(--primary); box-shadow:0 4px 12px rgba(0,94,184,0.3); transition:left 1s cubic-bezier(0.34,1.56,0.64,1);"></div>
                </div>
                <div style="text-align:center;">
                    <div id="det-tier" style="display:inline-block; padding:8px 24px; border-radius:50px; font-size:14px; font-weight:900; text-transform:uppercase; background:var(--primary); color:white; margin-bottom:10px;"></div>
                    <div id="det-status" style="font-size:17px; font-weight:800; color:#0f172a; margin-bottom:6px;"></div>
                    <div id="det-analysis" style="font-size:13px; color:#64748b; font-weight:600; line-height:1.5;"></div>
                </div>
            </div>

            <!-- CVS Card -->
            <div id="det-cvs-card" style="background: white; border-radius: 32px; padding: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: 1px solid #f1f5f9; margin-bottom: 16px;">
                <div style="font-size:12px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:16px;">CVS Assessment</div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div style="background:#fffbeb; border-radius:20px; padding:18px; border:1px solid #fef3c7; text-align:center;">
                        <div style="font-size:11px; font-weight:800; color:#d97706; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">CVS Score</div>
                        <div style="font-size:36px; font-weight:900; color:#92400e;" id="det-cvs-score">0</div>
                        <div style="font-size:11px; color:#d97706; font-weight:700;">out of 32</div>
                    </div>
                    <div style="background:#fffbeb; border-radius:20px; padding:18px; border:1px solid #fef3c7; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <div style="font-size:11px; font-weight:800; color:#d97706; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Diagnosis</div>
                        <div id="det-cvs-tier" style="padding:6px 14px; border-radius:50px; font-size:12px; font-weight:900; background:#f59e0b; color:white;"></div>
                    </div>
                </div>
            </div>
            <div id="det-no-cvs" style="display:none; background:white; border-radius:32px; padding:28px; text-align:center; border:2px dashed #e2e8f0; margin-bottom:16px;">
                <i class="fas fa-clipboard" style="font-size:32px; color:#cbd5e1; margin-bottom:10px;"></i>
                <div style="font-size:14px; font-weight:700; color:#94a3b8;">CVS screening not yet completed</div>
                <button onclick="window.startCvsScreening()" style="margin-top:16px; background:var(--primary-gradient); color:white; border:none; padding:12px 24px; border-radius:16px; font-weight:800; font-size:13px; cursor:pointer;">
                    <i class="fas fa-plus"></i> Complete CVS Screening
                </button>
            </div>

            <!-- PDF Download -->
            <button onclick="downloadSODetailPDF()" style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%); color:white; width:100%; border:none; height:64px; border-radius:22px; font-size:16px; font-weight:900; display:flex; align-items:center; justify-content:center; gap:12px; box-shadow:0 10px 25px rgba(245,158,11,0.25);">
                <i class="fas fa-file-pdf"></i> Download Eye Health Certificate
            </button>
            <div style="height:30px;"></div>
        </div>
    </div>


    <div id="bottom-nav" class="bottom-nav-container">
        <div class="bottom-nav">
            <div class="nav-item" id="nav-dash" onclick="navigate('scr-dashboard')">
                <i class="fas fa-th-large"></i> <span data-t="home_nav">Home</span>
            </div>
            <div class="nav-item fab" onclick="window.location.href='/' + (state.empCode || '')" id="main-add-btn">
                <i class="fas fa-plus-circle"></i> <span data-t="dry_eye_nav">Blink Test</span>
            </div>
        </div>
    </div>

    
    <!-- Language Selection Modal -->
    <div id="language-modal" class="calendar-modal" style="display:none;" onclick="if(event.target.id==='language-modal') closeLanguageModal()">
        <div class="calendar-card" style="max-width:400px; padding: 30px 20px;">
             <div style="text-align:center; margin-bottom:25px;">
                <div style="width:60px; height:60px; background:#eff6ff; border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:24px; margin:0 auto 15px;">
                    <i class="fas fa-globe"></i>
                </div>
                <h3 style="font-weight:900; color:#1e293b; margin:0; font-size:20px;">Select Language</h3>
                <p style="font-size:13px; color:var(--text-sub); font-weight:600; margin-top:5px;">Choose your preferred tongue</p>
             </div>
             
             
             <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                <div onclick="selectLanguage('en')" class="lang-card" id="lang-en">
                    <span style="font-size:14px; font-weight:800;">English</span>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('hi')" class="lang-card" id="lang-hi">
                    <span style="font-size:14px; font-weight:800;">हिंदी</span>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('or')" class="lang-card" id="lang-or">
                    <span style="font-size:14px; font-weight:800;">ଓଡ଼ିଆ</span>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('mr')" class="lang-card" id="lang-mr">
                    <span style="font-size:14px; font-weight:800;">मराठी</span>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('gu')" class="lang-card" id="lang-gu">
                    <span style="font-size:14px; font-weight:800;">ગુજરાતી</span>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('ta')" class="lang-card" id="lang-ta">
                    <span style="font-size:14px; font-weight:800;">தமிழ்</span>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('te')" class="lang-card" id="lang-te">
                    <span style="font-size:14px; font-weight:800;">తెలుగు</span>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('kn')" class="lang-card" id="lang-kn">
                    <span style="font-size:14px; font-weight:800;">ಕನ್ನಡ</span>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('ml')" class="lang-card" id="lang-ml">
                    <span style="font-size:14px; font-weight:800;">മലയാളം</span>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('bn')" class="lang-card" id="lang-bn">
                    <span style="font-size:14px; font-weight:800;">বাংলা</span>
                    <i class="fas fa-check-circle check-icon"></i>
                </div>
                <div onclick="selectLanguage('as')" class="lang-card" id="lang-as">
                    <span style="font-size:14px; font-weight:800;">অসমীয়া</span>
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



    <!-- Logout Modal -->
    <div id="logout-modal" class="calendar-modal" onclick="closeLogout(event)">
        <div class="calendar-card" style="max-width:320px; text-align:center; padding: 35px 24px;">
             <div style="width: 70px; height: 70px; background: #fef2f2; border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size:32px; color:var(--error); margin: 0 auto 20px;">
                 <i class="fas fa-sign-out-alt"></i>
             </div>
             <h3 style="font-weight:900; color:#1e293b; margin:0 0 10px 0; font-size: 22px;">Ready to leave?</h3>
             <p style="font-size:14px; color:#64748b; font-weight:600; margin:0 0 30px 0; line-height: 1.5;">You will need to log in again to access the dashboard and your screening history.</p>
             <div style="display:flex; gap:12px;">
                 <button class="btn" style="background:#f1f5f9; color:#475569; flex:1; padding: 16px; border-radius: 18px; font-weight: 800; border: none; font-size: 15px; cursor: pointer; transition: 0.2s;" onclick="document.getElementById('logout-modal').style.display='none'" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Cancel</button>
                 <button class="btn btn-primary" style="background:#ef4444; color: white; flex:1; padding: 16px; border-radius: 18px; font-weight: 800; border: none; font-size: 15px; box-shadow: 0 10px 20px rgba(239, 68, 68, 0.25); cursor: pointer; transition: 0.2s;" onclick="confirmLogout()" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 15px 25px rgba(239,68,68,0.35)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 10px 20px rgba(239,68,68,0.25)'">Log Out</button>
             </div>
        </div>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <div class="lightbox-close" onclick="closeLightbox()">&times;</div>
    </div>

<script>
    let state = {
        empCode: @json($emp_code ?? null) || sessionStorage.getItem('empCode'),
        empName: @json($emp_name ?? null) || sessionStorage.getItem('empName'),
        isLoggedIn: sessionStorage.getItem('isLoggedIn') === 'true',
        isPatientMode: @json(isset($emp_code)),
        historyPage: 0,
        lang: sessionStorage.getItem('lang') || null
    };

    function shareMyLink() {
        const url = window.location.origin + '/' + (state.empCode || '');
        if (navigator.share) {
            navigator.share({ title: 'Ajanta Blink Test', url: url });
        } else {
            const tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            showToast("Link copied to clipboard");
        }
    }

    // Global translation data
    const rawTranslations = @json($all_translations ?? []);
    const rawSymptoms = @json($all_symptoms ?? []);
    const translations = {};
    const blinkAnalysisSet = {};
    const resSets = {};

    // Initialize translations immediately
    Object.keys(rawTranslations).forEach(lang => {
        const pages = rawTranslations[lang];
        translations[lang] = pages.ui || {};
        resSets[lang] = pages.cvs_result || {};
        
        if (pages.analysis) {
            blinkAnalysisSet[lang] = {};
            const analysis = pages.analysis;
            ['optimal', 'excellent', 'healthy', 'mild', 'moderate', 'high', 'severe'].forEach(tier => {
                blinkAnalysisSet[lang][tier] = {
                    tier: analysis[`${tier}_tier`] || tier.toUpperCase(),
                    status: analysis[`${tier}_status`] || '',
                    analysis: analysis[`${tier}_analysis`] || ''
                };
            });
        }
    });

    // Initialize cvsSymptoms
    window.cvsSymptoms = rawSymptoms[state.lang || 'en'] || rawSymptoms['en'] || [];

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.innerText = msg;
        t.style.opacity = '1';
        t.style.transform = 'translateX(-50%) translateY(10px)';
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transform = 'translateX(-50%) translateY(0)';
        }, 3000);
    }

    function navigate(screenId) {
        console.log("Navigating to:", screenId);
        document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
        const target = document.getElementById(screenId);
        if (target) {
            target.classList.add('active');
            console.log("Screen activated:", screenId);
        } else {
            console.error("Screen NOT FOUND:", screenId);
        }
        
        const navItems = document.querySelectorAll('.nav-item');
        if (navItems && navItems.length) {
            navItems.forEach(btn => btn.classList.toggle('active', btn.id === 'nav-dash' && screenId === 'scr-dashboard'));
        }
        
        // Reset scroll position on shell
        const shell = document.querySelector('.app-shell');
        if (shell) shell.scrollTop = 0;
        
        const header = document.getElementById('app-header');
        const footer = document.getElementById('bottom-nav');
        
        const hideChrome = ['scr-login', 'scr-blink-test'];
        
        if (hideChrome.includes(screenId)) {
            if (header) header.style.display = 'none';
            if (footer) footer.style.display = 'none';
        } else {
            if (header) {
                header.style.display = 'flex';
                // Adjust header theme based on screen
                if (['scr-dashboard', 'scr-disclaimer'].includes(screenId)) {
                    header.style.background = 'transparent';
                    header.style.boxShadow = 'none';
                } else {
                    header.style.background = '#005eb8';
                    header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                }
            }
            const hideFooterOnly = ['scr-thank-you', 'scr-test-result', 'scr-cvs-result', 'scr-cvs-screening', 'scr-disclaimer', 'scr-detail'];
            if (hideFooterOnly.includes(screenId)) {
                if (footer) footer.style.display = 'none';
            } else {
                if (footer) footer.style.display = 'flex';
            }
            // Transparent header on detail screen too
            if (['scr-dashboard', 'scr-disclaimer', 'scr-detail'].includes(screenId)) {
                if (header) { header.style.background = 'transparent'; header.style.boxShadow = 'none'; }
            }
            if(screenId === 'scr-dashboard') loadDashboard();
        }
        
        if (navItems.length) {
            navItems.forEach(btn => btn.classList.toggle('active', btn.id === 'nav-dash' && screenId === 'scr-dashboard'));
        }
    }

    function doLogin() {
        const id = document.getElementById('login-id').value.trim();
        const pass = document.getElementById('login-pass').value;
        if(!id || !pass) { showToast('Please enter credentials'); return; }

        fetch("{{ route('blink.login') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ emp_code: id, password: pass })
        }).then(res => res.json()).then(data => {
            if(data.success) {
                sessionStorage.setItem('isLoggedIn', 'true');
                sessionStorage.setItem('empCode', data.employee.emp_code);
                sessionStorage.setItem('empName', data.employee.name);
                state.empCode = data.employee.emp_code;
                state.empName = data.employee.name;
                state.isLoggedIn = true;
                navigate('scr-dashboard');
            } else {
                showToast('Invalid Code or Password');
            }
        });
    }

    function logout() {
        document.getElementById('logout-modal').style.display = 'flex';
    }

    function confirmLogout() {
        sessionStorage.clear();
        state.isLoggedIn = false;
        state.empCode = null;
        state.empName = null;
        navigate('scr-login');
        document.getElementById('logout-modal').style.display = 'none';
    }

    function closeLogout(e) {
        if(e.target.id === 'logout-modal') e.target.style.display = 'none';
    }

    let cache = {
        currentPage: 0,
        totalItems: 0
    };

    function loadDashboard() {
        // Re-sync state from storage to be safe
        if (!state.empCode) state.empCode = sessionStorage.getItem('empCode');
        if (!state.empName) state.empName = sessionStorage.getItem('empName');
        
        if(!state.empCode) {
            console.warn("No SO ID found for dashboard sync");
            return;
        }

        const offset = cache.currentPage * 10;
        const url = `{{ route('prescription.dashboard') }}?so_id=${state.empCode}&offset=${offset}`;
        console.log("Syncing Dashboard:", url);
        
        if(state.empName) document.getElementById('dash-so-name').innerText = state.empName;

        fetch(url)
        .then(res => {
            if(!res.ok) throw new Error(`Server Error: ${res.status}`);
            return res.json();
        })
        .then(data => {
            console.log("Dashboard Data Received:", data);
            const oldToday = parseInt(document.getElementById('stat-today').innerText) || 0;
            const oldMonth = parseInt(document.getElementById('stat-month').innerText) || 0;
            const oldTotal = parseInt(document.getElementById('stat-visits').innerText) || 0;
            
            animateValue('stat-today', oldToday, data.today || 0, 1000);
            animateValue('stat-month', oldMonth, data.month || 0, 1000);
            animateValue('stat-visits', oldTotal, data.total || 0, 1000);
            
            // Goal Logic
            const goal = 10;
            const today = data.today || 0;
            const percent = Math.min(Math.round((today / goal) * 100), 100);
            document.getElementById('dash-percent').innerText = percent + '%';
            
            const motivationEl = document.getElementById('dash-motivation');
            if (today === 0) motivationEl.innerText = t("start_motivation");
            else if (today < goal) motivationEl.innerText = t("progress_motivation", { count: goal - today });
            else motivationEl.innerText = t("goal_motivation");

            cache.totalItems = data.total || 0;

            const list = document.getElementById('history-list');
            list.innerHTML = '';

            if (!data.history || data.history.length === 0) {
                list.innerHTML = `
                    <div style="text-align: center; padding: 50px 24px; background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border-radius: 32px; border: 2px dashed #cbd5e1; margin-top:10px; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(0,94,184,0.05); border-radius: 50%;"></div>
                        <i class="fas fa-eye" style="font-size: 50px; color: var(--primary); opacity: 0.2; margin-bottom: 20px; display: block;"></i>
                        <h3 style="font-size: 18px; font-weight: 900; color: #1e293b; margin-bottom: 8px;" data-t="no_screenings_title">${t("no_screenings_title")}</h3>
                        <p style="font-size: 13px; color: #64748b; font-weight: 600; line-height: 1.5; margin-bottom: 25px;" data-t="no_screenings_desc">${t("no_screenings_desc")}</p>
                        <button onclick="navigate('scr-disclaimer')" style="background: var(--primary-gradient); color: white; border: none; padding: 12px 24px; border-radius: 50px; font-weight: 800; font-size: 13px; box-shadow: 0 10px 20px rgba(0,94,184,0.2); display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-plus"></i> <span data-t="start_new_test">${t("start_new_test")}</span>
                        </button>
                    </div>
                `;
                return;
            }

            data.history.forEach((item) => {
                const row = document.createElement('div');
                row.className = 'history-item';
                row.style.cursor = 'pointer';
                row.onclick = () => openTestDetail(item.id);
                
                // Show CVS button if test is missing symptom score
                const showCvsAction = !item.cvs_score;

                
                row.innerHTML = `
                    <div style="display:flex; align-items:center; gap:16px; flex: 1;">
                        <div style="width:44px; height:44px; background:var(--primary-light); border-radius:12px; display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:18px; flex-shrink: 0;">
                            <i class="fas fa-file-medical-alt"></i>
                        </div>
                        <div class="history-info" style="overflow: hidden;">
                            <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" data-t="blink_screening_label">${t("blink_screening_label")}</h4>
                            <p style="margin: 2px 0 0; font-size: 12px; color: var(--text-sub); font-weight: 600;">${new Date(item.created_at).toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'})}</p>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        ${showCvsAction ? `
                            <button onclick="window.startCvsForTest('${item.id}')" style="background: #fff7ed; color: #f97316; border: 1px solid #ffedd5; padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 800; display:flex; align-items:center; gap:4px;">
                                <i class="fas fa-clipboard-check"></i> CVS
                            </button>
                        ` : `
                            <div style="background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 800;">
                                <i class="fas fa-check"></i>
                            </div>
                        `}
                        <div class="history-badge" style="background:${item.blink_count < 10 ? 'var(--error)' : 'var(--success-light)'}; color:${item.blink_count < 10 ? 'white' : 'var(--success)'}; border:none; padding: 6px 12px; border-radius: 10px; font-weight: 800; font-size: 13px; display: flex; align-items: baseline; gap: 3px; flex-shrink: 0;">
                            ${item.blink_count} <span style="font-size:9px; opacity:0.8;">Bpm</span>
                        </div>
                    </div>
                `;
                list.appendChild(row);
            });

            document.getElementById('page-num').innerText = t("page_label", { count: cache.currentPage + 1 });
            document.getElementById('btn-prev').style.display = cache.currentPage > 0 ? 'block' : 'none';
            document.getElementById('btn-next').style.display = (offset + 10) < cache.totalItems ? 'block' : 'none';
        })
        .catch(err => {
            console.error("Dashboard Load Error:", err);
            showToast(`Sync Failed: ${err.message}`);
        });
    }

    function changePage(delta) {
        cache.currentPage += delta;
        loadDashboard();
    }

    function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        if(!obj) return;
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) window.requestAnimationFrame(step);
        };
        window.requestAnimationFrame(step);
    }

    let blinkCount = 0;
    let testTimer = 15;
    let testInterval;
    let camera;
    let faceMesh;
    let blinkThreshold = 0.23;
    let eyeClosed = false;

    function getEAR(landmarks, indices) {
        const dist = (pA, pB) => Math.hypot(pA.x - pB.x, pA.y - pB.y);
        return (dist(landmarks[indices[1]], landmarks[indices[5]]) + dist(landmarks[indices[2]], landmarks[indices[4]])) / (2.0 * dist(landmarks[indices[0]], landmarks[indices[3]]));
    }

    function onResults(results) {
        if (!results.multiFaceLandmarks || results.multiFaceLandmarks.length === 0) return;
        const landmarks = results.multiFaceLandmarks[0];
        const leftEAR = getEAR(landmarks, [362, 385, 387, 263, 373, 380]);
        const rightEAR = getEAR(landmarks, [33, 160, 158, 133, 153, 144]);
        const avgEAR = (leftEAR + rightEAR) / 2.0;
        
        if (avgEAR < blinkThreshold) {
            eyeClosed = true;
        } else if (eyeClosed) {
            blinkCount++;
            document.getElementById('live-blink-count').innerText = blinkCount;
            eyeClosed = false;
            
            // Physical Feedback: Vibration
            if (navigator.vibrate) navigator.vibrate(50);
            
            // Visual Feedback: Ripple
            const ripple = document.getElementById('blink-ripple');
            ripple.classList.remove('ripple-active');
            void ripple.offsetWidth; // Trigger reflow
            ripple.classList.add('ripple-active');
        }
    }

    window.startCvsForTest = function(testId) {
        state.lastBlinkTestId = testId;
        window.startCvsScreening();
    };

    function startBlinkTest() {
        navigate('scr-blink-test');
        blinkCount = 0; testTimer = 15; eyeClosed = false;
        document.getElementById('live-blink-count').innerText = "0";
        document.getElementById('test-timer').innerText = "15";
        document.getElementById('test-status-msg').innerText = t("init_camera");
        
        if(!faceMesh) {
            faceMesh = new FaceMesh({locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`});
            faceMesh.setOptions({ maxNumFaces: 1, refineLandmarks: true, minDetectionConfidence: 0.5, minTrackingConfidence: 0.5 });
            faceMesh.onResults(onResults);
        }
        if(!camera) {
            camera = new Camera(document.getElementById('input_video'), {
                onFrame: async () => { await faceMesh.send({image: document.getElementById('input_video')}); },
                width: 640, height: 480
            });
        }
        camera.start().then(() => {
            document.getElementById('test-status-msg').innerText = "Test Started. Focus on the center.";
            document.getElementById('test-status-msg').style.color = "#10b981";
            testInterval = setInterval(() => {
                testTimer--;
                document.getElementById('test-timer').innerText = testTimer;
                if(testTimer <= 0) endBlinkTest();
            }, 1000);
        }).catch(err => {
            document.getElementById('test-status-msg').innerText = "Camera error: " + err.message;
            document.getElementById('test-status-msg').style.color = "var(--error)";
        });
    }

    
    function endBlinkTest() {
        clearInterval(testInterval);
        if(camera) camera.stop();
        
        const oneMinuteCount = blinkCount * 4;
        const finalCountEl = document.getElementById('final-blink-count');
        if(finalCountEl) finalCountEl.innerText = blinkCount;
        
        const scaledCountEl = document.getElementById('scaled-blink-count');
        if(scaledCountEl) scaledCountEl.innerText = oneMinuteCount;
        
        const reportDateEl = document.getElementById('report-date');
        if(reportDateEl) reportDateEl.innerText = new Date().toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'});
        const reportSoEl = document.getElementById('report-so-name');
        if(reportSoEl && state.empName) reportSoEl.innerText = state.empName;
        
        const tierBadge = document.getElementById('result-tier-badge');
        const statusEl = document.getElementById('result-status');
        const analysisEl = document.getElementById('result-analysis');
        
        const lang = state.lang || 'en';
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
        const msg = `🩺 *Ajanta Eye Health Screening Complete*\n\nBlink Rate: ${state.blinkCount}\nCVS Score: ${state.cvsScore}\n\nFacilitated by: ${state.empName || state.empCode}`;
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

    let detailTestData = null;

    function openTestDetail(testId) {
        navigate('scr-detail');
        // Reset indicator
        document.getElementById('det-indicator').style.left = '5%';

        fetch(`/blink-test/${testId}/detail?so_id=${state.empCode || ''}`)
            .then(r => r.json())
            .then(d => {
                if (!d.success) { showToast('Could not load test details'); return; }
                const t = d.test;
                detailTestData = t;
                const scaledCount = t.blink_count;
                const sid = 'SID-' + String(t.id).padStart(6, '0');
                const date = new Date(t.created_at).toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'});

                let tier, status, analysis, tierColor;
                if (scaledCount <= 6)       { tier='Optimal'; status='Highly stable tear film'; analysis='Excellent eye lubrication. Your blink rate is very healthy.'; tierColor='#10b981'; }
                else if (scaledCount <= 10) { tier='Excellent'; status='Good blink rate'; analysis='Healthy moisture retention. Eyes are well lubricated.'; tierColor='#34d399'; }
                else if (scaledCount <= 13) { tier='Healthy'; status='Normal blink pattern'; analysis='Adequate lubrication. Maintain eye hygiene habits.'; tierColor='#38bdf8'; }
                else if (scaledCount <= 16) { tier='Mild Dry Eye'; status='Reduced blink rate'; analysis='Consider using lubricating eye drops. Take screen breaks.'; tierColor='#f59e0b'; }
                else if (scaledCount <= 18) { tier='Moderate'; status='Significant reduction'; analysis='Recommend consulting an ophthalmologist soon.'; tierColor='#f97316'; }
                else if (scaledCount <= 20) { tier='High Risk'; status='High dry eye risk'; analysis='Urgent ophthalmologist visit recommended.'; tierColor='#ef4444'; }
                else                        { tier='Severe'; status='Severe dry eye'; analysis='Immediate medical attention is strongly advised.'; tierColor='#991b1b'; }

                const pct = Math.min(95, Math.max(5, ((scaledCount-3)/(20-3))*90+5));

                document.getElementById('det-sid').textContent = sid;
                document.getElementById('det-badge').textContent = tier.toUpperCase();
                document.getElementById('det-date').textContent = date;
                document.getElementById('det-blink').textContent = scaledCount;
                document.getElementById('det-tier').textContent = tier;
                document.getElementById('det-tier').style.background = tierColor;
                document.getElementById('det-status').textContent = status;
                document.getElementById('det-analysis').textContent = analysis;
                setTimeout(() => { document.getElementById('det-indicator').style.left = pct + '%'; }, 200);

                if (t.cvs) {
                    document.getElementById('det-cvs-card').style.display = 'block';
                    document.getElementById('det-no-cvs').style.display = 'none';
                    const cvsScore = t.cvs.total_score;
                    let cvsTier = cvsScore < 6 ? 'CVS Negative' : cvsScore <= 12 ? 'Mild CVS' : cvsScore <= 20 ? 'Moderate CVS' : 'Severe CVS';
                    document.getElementById('det-cvs-score').textContent = cvsScore;
                    document.getElementById('det-cvs-tier').textContent = cvsTier;
                } else {
                    document.getElementById('det-cvs-card').style.display = 'none';
                    document.getElementById('det-no-cvs').style.display = 'block';
                    // Store testId for CVS flow
                    state.lastBlinkTestId = testId;
                }
            })
            .catch(() => showToast('Could not load test details'));
    }

    function downloadSODetailPDF() {
        const el = document.getElementById('scr-detail');
        const filename = detailTestData
            ? `Ajanta_Eye_Health_${detailTestData.emp_code}_${detailTestData.id}.pdf`
            : 'Ajanta_Eye_Health_Report.pdf';
        const opt = {
            margin: 10,
            filename: filename,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(el).save();
    }
    function t(key, params = {}) {
        const trans = (translations && state.lang && translations[state.lang]) || (translations && translations['hi']) || (translations && translations['en']) || {};
        let text = trans[key] || key;
        Object.keys(params).forEach(k => {
            text = text.replace(`{${k}}`, params[k]);
        });
        return text;
    }

    function updateTranslations() {
        console.log("Updating translations for:", state.lang);
        const trans = (translations && state.lang && translations[state.lang]) || (translations && translations['hi']) || (translations && translations['en']);
        if(!trans) {
            console.warn("No translations found for", state.lang, "or fallbacks.");
            return;
        }
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
        window.cvsSymptoms = rawSymptoms[lang] || rawSymptoms['en'] || [];
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
        } else {
            if (state.empCode) {
                fetchDashboard();
            }
            const activeScreen = document.querySelector('.screen.active');
            if (activeScreen && activeScreen.id === 'scr-cvs-screening') {
                renderCvsQuestions();
            }
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
            item.style.padding = '25px'; item.style.background = '#fff'; item.style.borderRadius = '28px';
            item.style.boxShadow = 'var(--shadow-sm)'; item.style.border = '1px solid #f1f5f9';
            item.innerHTML = `
                <div style="font-size: 16px; font-weight: 900; color: var(--text-main); margin-bottom: 15px; line-height: 1.4;">${index + 1}. ${s.label}</div>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Frequency</div>
                        <div style="display: flex; gap: 8px;">
                            <button onclick="setCvsValue('${s.id}', 'freq', 0, this)" class="cvs-opt-btn" data-t="never">Never</button>
                            <button onclick="setCvsValue('${s.id}', 'freq', 1, this)" class="cvs-opt-btn" data-t="occas">Occasionally</button>
                            <button onclick="setCvsValue('${s.id}', 'freq', 2, this)" class="cvs-opt-btn" data-t="often">Often</button>
                        </div>
                    </div>
                    <div id="intens-${s.id}" style="display: none; flex-direction: column;">
                        <div style="font-size: 11px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Intensity</div>
                        <div style="display: flex; gap: 8px;">
                            <button onclick="setCvsValue('${s.id}', 'intens', 1, this)" class="cvs-opt-btn" data-t="moderate">Moderate</button>
                            <button onclick="setCvsValue('${s.id}', 'intens', 2, this)" class="cvs-opt-btn" data-t="intense">Intense</button>
                        </div>
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
        const lang = state.lang || 'en';
        const currentResSet = resSets[lang] || resSets['hi'] || resSets['en'];
        
        let tier, title, desc, percent;
        if (score < 6) {
            tier = currentResSet.neg; title = currentResSet.negTitle; desc = currentResSet.negDesc; percent = (score/6)*15;
        } else if (score <= 12) {
            tier = currentResSet.mild; title = currentResSet.mildTitle; desc = currentResSet.mildDesc; percent = 20 + ((score-6)/6)*25;
        } else if (score <= 20) {
            tier = currentResSet.mod; title = currentResSet.modTitle; desc = currentResSet.modDesc; percent = 50 + ((score-12)/8)*25;
        } else {
            tier = currentResSet.sev; title = currentResSet.sevTitle; desc = currentResSet.sevDesc; percent = 80 + ((score-20)/12)*20;
        }

        state.cvsTier = tier;
        document.getElementById('cvs-final-score').innerText = score;
        document.getElementById('cvs-tier-badge').innerText = tier;
        document.getElementById('cvs-status-title').innerText = title;
        document.getElementById('cvs-analysis-text').innerText = desc;
        document.getElementById('cvs-result-indicator').style.left = Math.min(95, percent) + '%';
        
        fetch(`{{ route('cvs_test.save') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ emp_code: state.empCode, blink_test_id: state.lastBlinkTestId || null, symptom_data: window.cvsScores, total_score: score, has_cvs: score >= 6 })
        });
        navigate('scr-cvs-result');
    }

    // High-Reliability Splash Dismissal
    window.dismissSplash = function() {
        try {
            const splash = document.getElementById('splash-screen');
            if(splash) {
                splash.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                splash.style.opacity = '0';
                splash.style.transform = 'scale(1.1)';
                splash.style.pointerEvents = 'none';
                setTimeout(() => {
                    if(splash.parentNode) splash.parentNode.removeChild(splash);
                }, 500);
            }
        } catch (e) { console.error('Splash dismissal error:', e); }
    };

    document.addEventListener('DOMContentLoaded', () => {
        try {
            // Immediate update
            updateTranslations();
            
            if (state.empName) {
                const display = document.getElementById('so-name-display');
                if (display) display.innerText = state.empName;
                const container = document.getElementById('so-facilitator');
                if (container) container.style.display = 'inline-block';
            }

            let target = state.isPatientMode ? 'scr-disclaimer' : (state.isLoggedIn ? 'scr-dashboard' : 'scr-login');
            if (!target) target = 'scr-login';
            navigate(target);
            
            if (!state.lang) {
                setTimeout(openLanguageModal, 500);
            }

            // Failsafe dismissal
            setTimeout(window.dismissSplash, 800);
        } catch (e) {
            console.error('Initialization error:', e);
            window.dismissSplash(); // Dismiss anyway
        }
    });

    // Secondary failsafe on window load
    window.addEventListener('load', () => {
        setTimeout(window.dismissSplash, 1000);
    });

    window.closeLightbox = function() {
        document.getElementById('lightbox').style.display = 'none';
    };
</script>

</div> <!-- End App Shell -->
</body>
</html>
