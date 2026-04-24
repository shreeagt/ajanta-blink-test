<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dry Eye Blink Test</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        :root {
            --primary: #005eb8;       
            --primary-dark: #004282;
            --accent: #4caf50;        
            --primary-gradient: linear-gradient(135deg, #005eb8 0%, #4caf50 100%);
            --bg-body: #eff6ff; 
            --surface: #ffffff;
            --text-main: #0f172a;
            --text-sub: #64748b;
            --error: #ef4444;
            --radius: 20px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #ffffff;
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }
        body::before {
            content: ''; position: fixed; top: -10%; left: -10%; width: 40%; height: 40%;
            background: radial-gradient(circle, rgba(0, 94, 184, 0.08) 0%, transparent 70%);
            z-index: -1; animation: orbit 20s infinite linear;
        }
        body::after {
            content: ''; position: fixed; bottom: -10%; right: -10%; width: 50%; height: 50%;
            background: radial-gradient(circle, rgba(76, 175, 80, 0.08) 0%, transparent 70%);
            z-index: -1; animation: orbit 25s infinite linear reverse;
        }
        @keyframes orbit {
            from { transform: rotate(0deg) translateX(50px) rotate(0deg); }
            to { transform: rotate(360deg) translateX(50px) rotate(-360deg); }
        }

        .app-shell {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            position: relative;
            height: 100vh;
            background: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 80px rgba(0,0,0,0.1);
            overflow: hidden;
            border-left: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
        }

        .screen {
            display: none;
            padding: 24px;
            flex-direction: column;
            animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            flex: 1;
            overflow-y: auto;
            padding-bottom: 100px;
        }
        .screen.active { display: flex; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- Header --- */
        .header {
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255,255,255,0.5);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-logo { font-size: 22px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 8px; }
        .header-logo i { color: #f59e0b; }

        /* --- Login --- */
        .login-card { margin-top: 40px; }
        .input-group { margin-bottom: 16px; }
        .input-group label { display: block; font-size: 13px; font-weight: 700; color: var(--text-sub); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;}
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            transition: 0.2s;
        }
        .input-wrapper:focus-within { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(0,94,184,0.1); }
        .input-prefix {
            padding: 0 16px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
        }
        .input-wrapper input {
            flex: 1; border: none; padding: 14px 18px 14px 8px; font-size: 15px; font-weight: 500; outline: none; background: transparent;
        }
        .input-group input:not(.prefixed), .input-group select {
            width: 100%; padding: 14px 18px; border: 2px solid #e2e8f0; border-radius: 14px;
            font-size: 15px; font-weight: 500; outline: none; transition: 0.2s; background: #fff;
        }
        .input-group input:not(.prefixed):focus, .input-group select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(0,94,184,0.1); }
        .input-group.has-error input:not(.prefixed),
        .input-group.has-error select,
        .input-group.has-error .input-wrapper { border: 2px solid #ef4444 !important; border-radius: 14px; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
        .field-error-msg { color: #ef4444; font-size: 12px; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 5px; }
        .upload-error-msg {
            color: #ef4444; font-size: 13px; font-weight: 600; margin: 8px 0 12px;
            background: #fef2f2; padding: 12px 16px; border-radius: 12px; gap: 8px;
            border: 1.5px solid #fecaca; display: none; align-items: center;
        }

        .btn {
            width: 100%; padding: 16px; border: none; border-radius: 16px;
            font-size: 16px; font-weight: 700; cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-primary { background: var(--primary-gradient); color: white; box-shadow: 0 8px 20px rgba(0,94,184,0.25);}
        .btn-primary:active { transform: scale(0.96); }

        /* --- Dashboard --- */
        .stat-card {
            background: var(--primary-gradient); border-radius: 28px; padding: 28px;
            color: white; margin-bottom: 24px; box-shadow: 0 15px 45px rgba(0,87,184,0.3);
            position: relative; overflow: hidden; display: flex; flex-direction: column; gap: 24px;
            transition: 0.3s; width: 100%; flex-shrink: 0; box-sizing: border-box;
        }
        .stat-card::before {
            content: ''; position: absolute; top: -100px; right: -100px; width: 250px; height: 250px;
            background: rgba(255,255,255,0.1); border-radius: 50%;
        }
        .stat-main { display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 2; }
        .stat-label { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; opacity: 0.85; margin-bottom: 4px; }
        .stat-val { font-size: 40px; font-weight: 800; letter-spacing: -1px; line-height: 1; }
        
        .stat-medal {
            width: 54px; height: 54px; background: rgba(255,255,255,0.2); 
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.3);
            animation: pulse-glow 2s infinite ease-in-out;
        }
        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); box-shadow: 0 8px 20px rgba(0,0,0,0.15); border-color: rgba(255,255,255,0.3); }
            50% { transform: scale(1.05); box-shadow: 0 0 25px rgba(255,255,255,0.4); border-color: rgba(255,255,255,0.6); }
        }

        .stat-grid-mini { 
            display: grid; grid-template-columns: 1fr 1fr; gap: 0; 
            position: relative; z-index: 2; padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.2); 
        }
        .mini-stat { text-align: center; }
        .mini-stat:first-child { border-right: 1px solid rgba(255,255,255,0.2); }
        .mini-stat h5 { font-size: 26px; font-weight: 800; line-height: 1; margin-bottom: 6px; }
        .mini-stat p { font-size: 10px; font-weight: 800; text-transform: uppercase; opacity: 0.8; letter-spacing: 1.2px; }
        
        .gullak-hub { text-align: center; margin-bottom: 30px; flex-shrink: 0; }
        .date-badge {
            display: flex; align-items: center; gap: 8px; background: #f1f5f9;
            padding: 8px 16px; border-radius: 50px; font-size: 13px; font-weight: 700;
            color: var(--primary); cursor: pointer; border: 1px solid #e2e8f0;
            width: fit-content; margin-bottom: 20px; transition: 0.2s;
        }
        .date-badge:active { transform: scale(0.95); background: #e2e8f0; }

        /* Custom Calendar Modal */
        .calendar-modal {
            position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 4000;
            display: none; align-items: center; justify-content: center; padding: 24px;
        }
        .calendar-card {
            background: white; border-radius: 24px; width: 100%; max-width: 320px; padding: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3); animation: slideUp 0.3s ease;
        }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; margin-top: 15px; }
        .cal-day {
            aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; border-radius: 10px; cursor: pointer; color: #64748b;
        }
        .cal-day.active { background: var(--primary) !important; color: #fff !important; font-weight: 800; border-radius: 50% !important; box-shadow: 0 4px 10px rgba(0,94,184,0.3); }
        .cal-day.today { color: var(--primary); font-weight: 800; border: 2px solid var(--primary); border-radius: 50%; }
        .cal-day:hover { background: #f8fafc; color: var(--primary); }
        .cal-day.disabled { opacity: 0.15; pointer-events: none; }
        .cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }

        /* --- Entry Form --- */
        .count-picker { display: flex; gap: 10px; margin-bottom: 24px; }
        .count-btn {
            flex: 1; height: 50px; border-radius: 12px; background: #fff;
            border: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: var(--text-sub); cursor: pointer; transition: 0.2s;
        }
        .count-btn.active { border-color: var(--primary); background: #eff6ff; color: var(--primary); transform: scale(1.05); }

        .upload-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 24px; }
        .upload-box {
            position: relative; border: 2px dashed #cbd5e1; border-radius: 14px;
            aspect-ratio: 1/1; display: flex; flex-direction: column; align-items: center;
            justify-content: center; color: #94a3b8; cursor: pointer; transition: 0.2s;
            overflow: hidden; background: #fff;
        }
        .upload-box:hover { border-color: var(--primary); color: var(--primary); background: #f8fafc; }
        .upload-box img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 2; }
        .upload-box.has-img i, .upload-box.has-img span { display: none; }
        .upload-box input { position: absolute; opacity: 0; inset: 0; cursor: pointer; }

        /* --- Gullak Animation --- */
        .anim-screen { justify-content: center; align-items: center; text-align: center; background: #ffffff; }
        .gullak-container { position: relative; width: 280px; height: 280px; margin-bottom: 50px; perspective: 1000px; display: flex; align-items: center; justify-content: center; }
        .gullak-img { width: 100%; height: 100%; object-fit: contain; position: relative; z-index: 5; mix-blend-mode: multiply; transition: 0.3s; }
        
        .gullak-glow-ring {
            position: absolute; width: 160px; height: 160px; background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
            border-radius: 50%; opacity: 0; filter: blur(30px); z-index: 1; transition: 0.2s;
        }

        .gullak-shake { animation: jitter 0.1s linear infinite; }
        .gullak-pulse { animation: softPulse 0.3s ease-out; }
        
        @keyframes softPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }

        @keyframes jitter {
            0% { transform: translate(0,0) rotate(0); }
            10% { transform: translate(-3px,-2px) rotate(-1deg); }
            20% { transform: translate(3px,2px) rotate(1deg); }
            30% { transform: translate(-3px,2px) rotate(-1deg); }
            40% { transform: translate(3px,-2px) rotate(1deg); }
            50% { transform: translate(-3px,-2px) rotate(-1deg); }
            60% { transform: translate(3px,2px) rotate(1deg); }
            70% { transform: translate(-3px,2px) rotate(-1deg); }
            80% { transform: translate(3px,-2px) rotate(1deg); }
            90% { transform: translate(-3px,2px) rotate(-1deg); }
            100% { transform: translate(0,0) rotate(0); }
        }

        .flying-photo {
            position: fixed; width: 55px; height: 75px; background: white;
            border: 2px solid #fff; border-radius: 6px; z-index: 1000;
            box-shadow: 0 15px 35px rgba(0,0,0,0.25); display: flex; flex-direction: column; gap: 4px; padding: 6px;
            animation: modernFly 1.2s cubic-bezier(0.5, -0.5, 0.4, 1.5) forwards;
            overflow: hidden;
        }
        .flying-photo img { width: 100%; height: 60%; object-fit: cover; border-radius: 2px; }
        .flying-photo::after { content: ''; width: 80%; height: 4px; background: #f1f5f9; border-radius: 2px; }

        @keyframes modernFly {
            0% { opacity: 0; transform: translate(0, 300px) scale(0.2) rotate(-45deg); }
            30% { opacity: 1; transform: translate(0, 0px) scale(1.2) rotate(15deg); }
            60% { opacity: 1; transform: translate(0, -60px) scale(0.8) rotate(-10deg); }
            100% { opacity: 0; transform: translate(0, -180px) scale(0.1) rotate(360deg); }
        }

        .saving-bar {
            width: 140px; height: 6px; background: #e2e8f0; border-radius: 10px; margin: 20px auto 0; overflow: hidden;
            position: relative;
        }
        .saving-progress {
            position: absolute; height: 100%; background: var(--primary-gradient); width: 0%;
            transition: 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .achievement-badge {
            position: absolute; top: -30px; left: 50%; transform: translateX(-50%) scale(0);
            background: #fcd34d; color: #92400e; padding: 6px 18px; border-radius: 50px;
            font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
            box-shadow: 0 10px 20px rgba(251,191,36,0.3); z-index: 10;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .achievement-badge.show { transform: translateX(-50%) scale(1); top: 10px; }

        .sparkle {
            position: fixed; pointer-events: none; background: #fbbf24; border-radius: 50%;
            animation: sparkle-float 4s infinite linear; opacity: 0; z-index: 0;
        }
        @keyframes sparkle-float {
            0% { transform: translateY(0) scale(0); opacity: 0; }
            50% { opacity: 0.6; }
            100% { transform: translateY(-100px) scale(1.5); opacity: 0; }
        }

        .history-list { display: flex; flex-direction: column; gap: 12px; }
        .history-item {
            padding: 18px; border-radius: 18px; background: #fff; border: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .history-info h4 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .history-info p { font-size: 12px; color: var(--text-sub); font-weight: 600; }
        .history-count { background: #eff6ff; color: var(--primary); padding: 8px 14px; border-radius: 12px; font-weight: 800; font-size: 15px; }

        .bottom-nav-container {
            position: absolute; bottom: 30px; left: 0; width: 100%; display: flex; justify-content: center; z-index: 2000;
            pointer-events: none;
        }
        .bottom-nav {
            background: #1e293b; border-radius: 40px; padding: 10px;
            display: flex; gap: 10px; align-items: center; pointer-events: auto;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
        }
        .nav-btn {
            padding: 10px 20px; color: #94a3b8; font-size: 14px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; gap: 8px; border-radius: 30px; transition: 0.2s;
        }
        .nav-btn.active { background: rgba(255,255,255,0.1); color: #fff; }
        
        .nav-add-fab {
            width: 48px; height: 48px; background: var(--primary-gradient); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: white;
            font-size: 18px; cursor: pointer; transition: 0.2s; box-shadow: 0 5px 15px rgba(0,94,184,0.3);
        }
        .nav-add-fab:active { transform: scale(0.9); }

        /* --- Detail Screen --- */
        .detail-header {
            background: var(--primary-gradient); padding: 50px 24px 120px; border-radius: 0 0 40px 40px;
            color: white; margin-top: -24px; margin-left: -24px; margin-right: -24px;
            position: relative; overflow: hidden;
        }
        .detail-profile-card {
            background: #fff; border-radius: 24px; padding: 24px; margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06); position: relative; z-index: 5;
            border: 1px solid #f1f5f9; margin-top: -70px; display: flex; flex-direction: column;
        }
        .detail-row {
            display: flex; align-items: flex-start; gap: 16px; padding: 12px 0; 
            border-bottom: 1px solid #f8fafc;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-row i { color: var(--primary); font-size: 16px; margin-top: 4px; }
        .detail-row strong { font-size: 11px; text-transform: uppercase; color: var(--text-sub); letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
        .detail-row p { font-size: 15px; font-weight: 700; color: var(--text-main); }

        .impact-pill {
            background: #fef3c7; color: #92400e; padding: 8px 16px; border-radius: 50px;
            font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;
            border: 1px solid #fde68a; align-self: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .action-bar { display: flex; gap: 12px; margin-bottom: 30px; }
        .action-btn {
            flex: 1; padding: 12px; border-radius: 14px; background: #f1f5f9;
            color: var(--text-main); font-size: 13px; font-weight: 700;
            display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s;
        }
        .action-btn:active { transform: scale(0.95); background: #e2e8f0; }

        .gallery-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .gallery-item {
            width: 100%; aspect-ratio: 4/3; border-radius: 18px; overflow: hidden; background: #f8fafc;
            border: 2px solid #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.06); transition: 0.3s;
            position: relative;
        }
        .gallery-item:active { transform: scale(0.95); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; }

        .lightbox {
            position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 3000;
            display: none; flex-direction: column; align-items: center; justify-content: center; padding: 40px;
        }
        .lightbox-img { max-width: 100%; max-height: 70vh; border-radius: 12px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .lightbox-close { position: absolute; top: 40px; right: 20px; color: white; font-size: 32px; cursor: pointer; }
        .lightbox-nav { display: flex; gap: 40px; margin-top: 30px; color: white; font-size: 24px; }
        .lightbox-nav i { cursor: pointer; opacity: 0.7; transition: 0.2s; }
        .lightbox-nav i:hover { opacity: 1; }

        .toast {
            position: fixed; top: 24px; left: 50%; transform: translateX(-50%);
            background: #1e293b; color: white; padding: 14px 28px; border-radius: 50px;
            font-size: 14px; font-weight: 700; box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            z-index: 2000; opacity: 0; pointer-events: none; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes blinkRipple {
            0% { transform: translate(-50%, -50%) scale(1); opacity: 0.8; }
            100% { transform: translate(-50%, -50%) scale(2.5); opacity: 0; }
        }
        .ripple-effect {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 100px; height: 100px; background: rgba(255,255,255,0.4);
            border-radius: 50%; pointer-events: none; z-index: 10;
            display: none;
        }
        .ripple-active { display: block; animation: blinkRipple 0.4s ease-out; }
    </style>
    <!-- MediaPipe Face Mesh for Blink Detection -->
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js" crossorigin="anonymous"></script>
</head>
<body>

<div id="toast" class="toast"></div>

<div class="app-shell">
    <div class="header" id="app-header" style="display:none;">
        <div class="header-logo"><i class="fas fa-eye"></i> Blink Test</div>
        <div onclick="logout()" style="color: var(--text-sub); cursor: pointer; font-size: 18px;"><i class="fas fa-sign-out-alt"></i></div>
    </div>

    <!-- Login -->
    <style>
        #scr-login.active {
            display: flex !important;
            flex-direction: column;
            justify-content: center;
            min-height: 100vh;
            padding-bottom: 50px;
        }
    </style>
    <div id="scr-login" class="screen active" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
        
        <!-- Top Branding -->
        <div style="text-align: center; margin-bottom: 40px; margin-top: 40px;">
            <div style="display: inline-block; padding: 10px 24px; background: white; border-radius: 50px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #f1f5f9;">
                <span style="font-size: 14px; font-weight: 900; color: var(--primary); letter-spacing: 2px; text-transform: uppercase;">Ajanta Pharma Ltd.</span>
            </div>
            
            <div style="position: relative; width: 100px; height: 100px; margin: 0 auto 20px;">
                <div style="position: absolute; inset: 0; background: var(--primary-gradient); border-radius: 30px; transform: rotate(15deg); opacity: 0.1; animation: pulse 2s infinite;"></div>
                <div style="position: absolute; inset: 0; background: var(--primary); border-radius: 30px; display: flex; align-items: center; justify-content: center; color: white; font-size: 40px; box-shadow: 0 15px 35px rgba(0,94,184,0.3); z-index: 2;">
                    <i class="fas fa-eye-slash"></i>
                </div>
            </div>
            
            <h1 style="font-size: 32px; font-weight: 900; color: #1e293b; letter-spacing: -1px;">Dry Eye Awareness</h1>
            <p style="color: #64748b; font-weight: 700; font-size: 15px; margin-top: 5px;">Field Force Diagnostics Portal</p>
        </div>

        <!-- Login Card -->
        <div style="background: white; padding: 40px 30px; border-radius: 40px; box-shadow: 0 30px 70px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
            <div style="margin-bottom: 25px; text-align: center;">
                <span style="font-size: 12px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Sign in to start screening</span>
            </div>
            
            <div class="input-group" style="margin-bottom: 20px;">
                <div style="position: relative;">
                    <i class="far fa-user" style="position: absolute; left: 20px; top: 18px; color: var(--primary); font-size: 18px;"></i>
                    <input type="text" id="login-id" placeholder="Employee Code" style="padding-left: 55px; height: 60px; border-radius: 20px; font-size: 16px; font-weight: 600; border-color: #f1f5f9; background: #f8fafc;">
                </div>
            </div>
            
            <div class="input-group" style="margin-bottom: 30px;">
                <div style="position: relative;">
                    <i class="fas fa-key" style="position: absolute; left: 20px; top: 18px; color: #f59e0b; font-size: 18px;"></i>
                    <input type="password" id="login-pass" placeholder="Password" style="padding-left: 55px; height: 60px; border-radius: 20px; font-size: 16px; font-weight: 600; border-color: #f1f5f9; background: #f8fafc;">
                </div>
            </div>
            
            <button class="btn btn-primary" onclick="doLogin()" id="btn-login" style="height: 64px; border-radius: 20px; font-size: 18px; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0 15px 35px rgba(0,94,184,0.2);">
                Enter Dashboard <i class="fas fa-sign-in-alt"></i>
            </button>
            
            <div style="margin-top: 25px; text-align: center;">
                <p style="font-size: 12px; color: #94a3b8; font-weight: 600; line-height: 1.6;">Use your company-assigned credentials to access the diagnostic tools.</p>
            </div>
        </div>
        
        <style>
            @keyframes pulse {
                0% { transform: rotate(15deg) scale(1); opacity: 0.1; }
                50% { transform: rotate(15deg) scale(1.2); opacity: 0.2; }
                100% { transform: rotate(15deg) scale(1); opacity: 0.1; }
            }
        </style>
    </div>


    <!-- Blink Test Disclaimer -->
    <div id="scr-disclaimer" class="screen anim-screen" style="padding-top: 0; background: #f8fafc;">
        <div style="background: var(--primary-gradient); margin: 0 -24px 30px; padding: 40px 24px 50px; border-radius: 0 0 45px 45px; color: white; text-align: center; position: relative; box-shadow: 0 10px 30px rgba(0,94,184,0.15);">
            <div style="width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2 style="font-size: 28px; font-weight: 900; letter-spacing: -0.5px; margin-bottom: 8px;">Screening Guide</h2>
            <p style="font-size: 15px; font-weight: 600; opacity: 0.9;">Quick 15-second blink analysis</p>
            
            <div id="so-facilitator" style="display:none; margin-top: 20px; font-size: 13px; font-weight: 800; color: white; background: rgba(0,0,0,0.2); padding: 10px 20px; border-radius: 50px; display: inline-flex; align-items: center; gap: 8px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                <i class="fas fa-id-badge"></i> <span id="so-name-display"></span>
            </div>
        </div>

        <div style="padding: 0 5px;">
            <div style="background: white; padding: 24px; border-radius: 32px; box-shadow: 0 15px 40px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; margin-bottom: 20px;">
                <div style="display:flex; gap:18px; margin-bottom:24px; align-items: flex-start;">
                    <div style="width:32px; height:32px; background:#eff6ff; border-radius:10px; display:flex; align-items:center; justify-content:center; color:var(--primary); font-weight:900; flex-shrink:0; font-size: 13px;">1</div>
                    <div style="text-align: left;">
                        <div style="font-size:15px; font-weight:800; color:#1e293b; margin-bottom:4px;">Position Yourself</div>
                        <div style="font-size:13px; color:#64748b; font-weight:600;">Stare at the center target on screen.</div>
                    </div>
                </div>
                <div style="display:flex; gap:18px; margin-bottom:24px; align-items: flex-start;">
                    <div style="width:32px; height:32px; background:#fff7ed; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#f59e0b; font-weight:900; flex-shrink:0; font-size: 13px;">2</div>
                    <div style="text-align: left;">
                        <div style="font-size:15px; font-weight:800; color:#1e293b; margin-bottom:4px;">Natural Blinking</div>
                        <div style="font-size:13px; color:#64748b; font-weight:600;">Blink naturally as you normally would.</div>
                    </div>
                </div>
                <div style="display:flex; gap:18px; align-items: flex-start;">
                    <div style="width:32px; height:32px; background:#f0fdf4; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#10b981; font-weight:900; flex-shrink:0; font-size: 13px;">3</div>
                    <div style="text-align: left;">
                        <div style="font-size:15px; font-weight:800; color:#1e293b; margin-bottom:4px;">AI Analysis</div>
                        <div style="font-size:13px; color:#64748b; font-weight:600;">Review your instant medical-grade report.</div>
                    </div>
                </div>
            </div>

            <div style="background: #fff; padding: 18px 24px; border-radius: 24px; border: 1px solid #f1f5f9; margin-bottom: 25px; text-align: left; display: flex; align-items: center; gap: 15px;">
                <div style="width: 44px; height: 44px; background: #f0f9ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 20px;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div style="flex: 1;">
                    <span style="font-size: 14px; font-weight: 800; color: #1e293b; display: block; margin-bottom: 2px;">Privacy First</span>
                    <p style="font-size: 11px; color: #64748b; font-weight: 600; line-height: 1.4; margin: 0;">Video is processed locally. No data is uploaded or saved.</p>
                </div>
            </div>

            <button class="btn btn-primary" onclick="startBlinkTest()" style="font-size: 18px; padding: 20px; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,94,184,0.3); width: 100%; display: flex; justify-content: center; align-items: center; gap: 12px; font-weight: 900; background: var(--primary-gradient); border: none;">
                Accept & Proceed <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Blink Test Main Screen -->
    <div id="scr-blink-test" class="screen anim-screen" style="flex-direction: column; background: #0f172a; color: white;">
        <div style="text-align: center; margin-top: 30px; margin-bottom: 40px;">
            <h2 style="font-size: 22px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 5px;">Analyzing Blinks...</h2>
            <p style="font-size: 14px; color: #94a3b8; font-weight: 600;">Please stare at the center</p>
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
                    <p style="font-size: 12px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Live Count</p>
                    <div style="font-size: 32px; font-weight: 900; color: #38bdf8;" id="live-blink-count">0</div>
                </div>
                <div style="width: 1px; height: 40px; background: rgba(255,255,255,0.1);"></div>
                <div style="text-align: left;">
                    <p style="font-size: 12px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Status</p>
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
    <div id="scr-test-result" class="screen anim-screen" style="background: #f1f5f9; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; width: 100%; padding: 30px 24px; border-radius: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.06); border: 1px solid #fff; position: relative;">
            <div style="text-align: center; margin-bottom: 25px;">
                <span style="font-size: 12px; font-weight: 900; color: var(--primary); text-transform: uppercase; letter-spacing: 2px; background: #eff6ff; padding: 8px 20px; border-radius: 50px;">Blink Analysis Report</span>
            </div>
            
            <div style="text-align: center; margin-bottom: 25px;">
                <p style="font-size: 14px; color: #64748b; font-weight: 700; margin-bottom: 10px;">YOUR SCORE</p>
                <div style="display: flex; align-items: baseline; justify-content: center; gap: 8px;">
                    <h2 style="font-size: 80px; font-weight: 900; color: #0f172a; line-height: 1;" id="scaled-blink-count">0</h2>
                    <span style="font-size: 20px; font-weight: 800; color: #94a3b8;">/ min</span>
                </div>
            </div>

            <div style="margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 900; color: #94a3b8; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px;">
                    <span>Health Scale</span>
                    <span id="report-date">24 APR 2026</span>
                </div>
                <div style="position: relative; height: 16px; background: #f1f5f9; border-radius: 20px; display: flex; overflow: hidden; border: 1px solid #e2e8f0;">
                    <div style="flex: 6; background: #10b981;"></div>
                    <div style="flex: 4; background: #34d399;"></div>
                    <div style="flex: 3; background: #38bdf8;"></div>
                    <div style="flex: 3; background: #fbbf24;"></div>
                    <div style="flex: 2; background: #f97316;"></div>
                    <div style="flex: 2; background: #ef4444;"></div>
                    
                    <div id="result-indicator" style="position: absolute; top: -6px; left: 0%; width: 28px; height: 28px; background: white; border-radius: 50%; border: 6px solid var(--primary); box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: all 1s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
                </div>
            </div>

            <div style="text-align: center; margin-bottom: 30px;">
                <div id="result-tier-badge" style="display: inline-block; padding: 8px 25px; border-radius: 50px; font-size: 16px; font-weight: 900; text-transform: uppercase; margin-bottom: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">Optimal</div>
                <h3 id="result-status" style="font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 12px; line-height: 1.2;">Highly stable tear film</h3>
                <p id="result-analysis" style="font-size: 15px; color: #64748b; font-weight: 600; line-height: 1.6; margin: 0;">Your blinking pattern indicates excellent moisture retention and tear film stability.</p>
            </div>

            <div id="report-so-footer" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; background: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Verified By Representative</div>
                    <div style="font-size: 14px; font-weight: 800; color: #1e293b;" id="report-so-name">AJANTA SO</div>
                </div>
            </div>
            
            <!-- Action Section (Unified inside Card) -->
            <div style="margin-top: 30px; padding-top: 25px; border-top: 2px dashed #f1f5f9; display: flex; flex-direction: column; gap: 15px;">
                <!-- WhatsApp Sharing -->
                <div style="background: #f0fdf4; padding: 18px; border-radius: 20px; border: 1px solid #dcfce7; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <i class="fab fa-whatsapp" style="color: #25d366; font-size: 20px;"></i>
                        <span style="font-size: 14px; font-weight: 800; color: #166534;">Share with Patient</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <input type="tel" id="whatsapp-num" placeholder="WhatsApp Number" style="flex: 1; padding: 10px 14px; border-radius: 12px; border: 1px solid #bbf7d0; font-size: 14px; font-weight: 600;">
                        <button onclick="shareToWhatsApp()" style="background: #25d366; color: white; border: none; padding: 0 18px; border-radius: 12px; font-weight: 800; font-size: 14px;">
                            Share
                        </button>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <button class="btn" onclick="downloadPDF()" style="background: white; color: var(--primary); border: 2px solid var(--primary); font-size: 15px; padding: 14px; border-radius: 16px; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <i class="fas fa-file-pdf"></i> Generate Premium Report
                    </button>
                    <button class="btn btn-primary" onclick="onTestFinish()" style="font-size: 16px; padding: 16px; border-radius: 16px; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        Finish & Close <i class="fas fa-check"></i>
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
                    <p style="font-size: 14px; font-weight: 700; color: #64748b; margin: 5px 0 0;">Dry Eye Awareness Campaign</p>
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
    </div>

    <!-- Thank You Screen (for Patients) -->
    <div id="scr-thank-you" class="screen" style="padding-top: 80px; text-align: center;">
        <div style="font-size: 100px; color: #10b981; margin-bottom: 30px;">
            <i class="fas fa-heart"></i>
        </div>
        <h2 style="font-size: 36px; font-weight: 900; color: #1e293b; margin-bottom: 15px;">Thank You!</h2>
        <p style="font-size: 18px; color: var(--text-sub); font-weight: 600; line-height: 1.6; margin-bottom: 40px;">
            Your screening has been recorded successfully. Please consult with the representative for more details.
        </p>
        <div style="padding: 20px; background: #f8fafc; border-radius: 20px; border: 1px solid #e2e8f0;">
            <p style="font-size: 14px; font-weight: 700; color: #64748b;">Attributed to SO Code:</p>
            <p style="font-size: 20px; font-weight: 900; color: var(--primary);" id="thank-you-so-code"></p>
        </div>
    </div>

    <!-- Dashboard -->
    <div id="scr-dashboard" class="screen" style="padding-top: 0; background: #f1f5f9;">
        <div style="background: var(--primary-gradient); margin: 0 -24px 30px; padding: 40px 24px 70px; border-radius: 0 0 50px 50px; color: white; text-align: center; position: relative; box-shadow: 0 10px 30px rgba(0,94,184,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="text-align: left;">
                    <div style="font-size: 12px; font-weight: 700; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px;">Hello, Representative</div>
                    <h2 style="font-size: 24px; font-weight: 900; letter-spacing: -0.5px;" id="dash-so-name">Dashboard</h2>
                </div>
            </div>
            
            <div style="position: absolute; bottom: -40px; left: 50%; transform: translateX(-50%); background: white; padding: 20px; border-radius: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); display: flex; gap: 15px; width: 92%; border: 1px solid #fff;">
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 26px; font-weight: 900; color: var(--primary); line-height: 1.2;" id="stat-today">0</div>
                    <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Today</div>
                </div>
                <div style="width: 1px; background: #f1f5f9; height: 40px; align-self: center;"></div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 26px; font-weight: 900; color: #10b981; line-height: 1.2;" id="stat-month">0</div>
                    <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Month</div>
                </div>
                <div style="width: 1px; background: #f1f5f9; height: 40px; align-self: center;"></div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 26px; font-weight: 900; color: #f59e0b; line-height: 1.2;" id="stat-visits">0</div>
                    <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Total</div>
                </div>
            </div>
        </div>

        <div id="dash-goal-card" style="margin: 50px 0 30px; background: white; padding: 20px; border-radius: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 15px; border: 1px solid #f1f5f9;">
            <div style="width: 50px; height: 50px; background: #fffbeb; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 24px;">
                <i class="fas fa-fire"></i>
            </div>
            <div style="flex: 1;">
                <h4 style="font-size: 14px; font-weight: 800; color: #1e293b;">Daily Progress</h4>
                <p style="font-size: 12px; color: #64748b; font-weight: 600;" id="dash-motivation">Help 10 patients today to reach your goal!</p>
            </div>
            <div style="font-size: 14px; font-weight: 900; color: var(--primary);" id="dash-percent">0%</div>
        </div>

        <div style="margin-top: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 16px; font-weight: 800; color:#1e293b; display:flex; align-items:center; gap:10px;">
                    <div style="width: 32px; height: 32px; background: #eff6ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-list-ul" style="font-size: 14px;"></i>
                    </div>
                    Recent Screenings
                </h3>
                <button onclick="shareMyLink()" style="background: white; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 12px; font-size: 12px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                    <i class="fas fa-share-alt"></i> Share Link
                </button>
            </div>
            <div class="history-list" id="history-list">
                <!-- Items -->
            </div>
            <div id="pagination-controls" style="display:flex; justify-content:center; align-items:center; gap:20px; padding:20px 0;">
                <button class="nav-btn" style="width:auto; background:white; color:var(--primary); display:none; padding:10px 20px; border-radius:15px; font-weight:800; border:1px solid #e2e8f0;" id="btn-prev" onclick="changePage(-1)">
                    <i class="fas fa-chevron-left"></i> Prev
                </button>
                <span id="page-num" style="font-weight:800; font-size:14px; color:#64748b; background:white; padding:8px 16px; border-radius:12px; border:1px solid #e2e8f0;">Page 1</span>
                <button class="nav-btn" style="width:auto; background:white; color:var(--primary); display:none; padding:10px 20px; border-radius:15px; font-weight:800; border:1px solid #e2e8f0;" id="btn-next" onclick="changePage(1)">
                    Next <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        <div style="height: 100px;"></div>
    </div>

    <div class="bottom-nav-container" id="bottom-nav" style="display:none;">
        <div class="bottom-nav" style="padding: 10px 20px;">
            <div class="nav-btn active" id="nav-dash" onclick="navigate('scr-dashboard')">
                <i class="fas fa-chart-pie"></i> Stats
            </div>
            <div class="nav-btn" onclick="navigate('scr-disclaimer')" id="main-add-btn" style="background: var(--primary-gradient); color: white; border-radius: 30px; margin-left: 10px; padding: 10px 24px;">
                <i class="fas fa-eye"></i> Dry Eye Test
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div id="logout-modal" class="calendar-modal" onclick="closeLogout(event)">
        <div class="calendar-card" style="max-width:280px; text-align:center;">
             <div style="font-size:40px; color:var(--error); margin-bottom:15px;"><i class="fas fa-door-open"></i></div>
             <h3 style="font-weight:800; margin-bottom:8px;">Ready to leave?</h3>
             <p style="font-size:13px; color:var(--text-sub); font-weight:600; margin-bottom:25px;">You will need to log in again to access the dashboard.</p>
             <div style="display:flex; gap:10px;">
                 <button class="btn" style="background:#f1f5f9; color:var(--text-main); flex:1;" onclick="document.getElementById('logout-modal').style.display='none'">Stay</button>
                 <button class="btn btn-primary" style="background:var(--error); flex:1;" onclick="confirmLogout()">Log Out</button>
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
        historyPage: 0
    };

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
        document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
        document.getElementById(screenId).classList.add('active');
        
        const header = document.getElementById('app-header');
        const footer = document.getElementById('bottom-nav');
        
        const hideChrome = ['scr-login', 'scr-thank-you', 'scr-blink-test', 'scr-disclaimer', 'scr-test-result'];
        
        if (hideChrome.includes(screenId)) {
            if (header) header.style.display = 'none';
            if (footer) footer.style.display = 'none';
        } else {
            if (header) header.style.display = 'flex';
            if (footer) footer.style.display = 'flex';
            if(screenId === 'scr-dashboard') loadDashboard();
        }
        
        const navDash = document.getElementById('nav-dash');
        if(navDash) navDash.classList.toggle('active', screenId === 'scr-dashboard');
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
            if (today === 0) motivationEl.innerText = "Start your first screening to ignite your daily goal!";
            else if (today < goal) motivationEl.innerText = `Great start! Only ${goal - today} more to reach your daily target.`;
            else motivationEl.innerText = "Goal achieved! You're making a real impact on eye health today.";

            cache.totalItems = data.total || 0;

            const list = document.getElementById('history-list');
            list.innerHTML = '';

            if (!data.history || data.history.length === 0) {
                list.innerHTML = `
                    <div style="text-align: center; padding: 50px 24px; background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border-radius: 32px; border: 2px dashed #cbd5e1; margin-top:10px; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(0,94,184,0.05); border-radius: 50%;"></div>
                        <i class="fas fa-eye" style="font-size: 50px; color: var(--primary); opacity: 0.2; margin-bottom: 20px; display: block;"></i>
                        <h3 style="font-size: 18px; font-weight: 900; color: #1e293b; margin-bottom: 8px;">No Screenings Yet</h3>
                        <p style="font-size: 13px; color: #64748b; font-weight: 600; line-height: 1.5; margin-bottom: 25px;">Start your first AI blink test to see patient insights here.</p>
                        <button onclick="navigate('scr-disclaimer')" style="background: var(--primary-gradient); color: white; border: none; padding: 12px 24px; border-radius: 50px; font-weight: 800; font-size: 13px; box-shadow: 0 10px 20px rgba(0,94,184,0.2); display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-plus"></i> Start New Test
                        </button>
                    </div>
                `;
                return;
            }

            data.history.forEach((item) => {
                const row = document.createElement('div');
                row.className = 'history-item';
                row.innerHTML = `
                    <div class="history-info">
                        <h4>Blink Test Screening</h4>
                        <p>${new Date(item.created_at).toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'})}</p>
                    </div>
                    <div class="history-count" style="background:#eff6ff; color:var(--primary); padding:6px 12px; border-radius:12px; font-weight:800;">${item.blink_count} Blinks</div>
                `;
                list.appendChild(row);
            });

            document.getElementById('page-num').innerText = `Page ${cache.currentPage + 1}`;
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

    function startBlinkTest() {
        navigate('scr-blink-test');
        blinkCount = 0; testTimer = 15; eyeClosed = false;
        document.getElementById('live-blink-count').innerText = "0";
        document.getElementById('test-timer').innerText = "15";
        document.getElementById('test-status-msg').innerText = "Initializing Camera...";
        
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
        
        // Scale 15s count to 1m for tier calculation
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
        
        let result = { tier: '', status: '', analysis: '', color: '', bg: '' };
        
        if (oneMinuteCount <= 6) {
            result = { tier: 'Optimal', status: 'Highly stable tear film', color: '#10b981', bg: '#f0fdf4', analysis: 'Your eyes are exceptionally well-lubricated. The oily (lipid) layer of your tear film is very thick, preventing your tears from evaporating even when staring.' };
        } else if (oneMinuteCount <= 10) {
            result = { tier: 'Excellent', status: 'Very healthy moisture retention', color: '#10b981', bg: '#f0fdf4', analysis: 'You have great tear stability. You likely do not suffer from symptoms even in dry environments like air-conditioned rooms.' };
        } else if (oneMinuteCount <= 13) {
            result = { tier: 'Healthy Average', status: 'Normal tear film function', color: '#38bdf8', bg: '#f0f9ff', analysis: 'This is the ideal range for most healthy adults. Your eyes refresh themselves at a natural pace without feeling irritation.' };
        } else if (oneMinuteCount <= 16) {
            result = { tier: 'Mild/Borderline', status: 'Possible early moisture evaporation', color: '#f59e0b', bg: '#fffbeb', analysis: 'You may be starting to experience moisture loss. This is often triggered by modern life, such as prolonged screen use or contact lens wear.' };
        } else if (oneMinuteCount <= 18) {
            result = { tier: 'Moderate', status: 'Signs of lipid layer disruption', color: '#f97316', bg: '#fff7ed', analysis: 'Your blinking has increased because your tears are evaporating faster than they should. You may feel occasional soreness or irritation.' };
        } else if (oneMinuteCount <= 20) {
            result = { tier: 'High Chance', status: 'Strong signs of screen-dry eye', color: '#ef4444', bg: '#fef2f2', analysis: 'There is a strong likelihood that you have dry eyes. Your eyes are forcing you to blink frequently to clear away the sensation of having a "foreign body" or grit in your eye.' };
        } else {
            result = { tier: 'Severe/Chronic', status: 'Highly unstable tear film', color: '#991b1b', bg: '#fef2f2', analysis: 'You have a highly unstable tear film. Your eyes feel constant discomfort, requiring rapid-fire blinking to keep the surface covered. You should consult a healthcare professional for a formal diagnosis.' };
        }
        
        tierBadge.innerText = result.tier;
        tierBadge.style.color = result.color;
        tierBadge.style.background = result.bg;
        statusEl.innerText = result.status;
        statusEl.style.color = result.color;
        analysisEl.innerText = result.analysis;
        
        // Move Indicator
        const indicator = document.getElementById('result-indicator');
        if(indicator) {
            let percent = 0;
            if(oneMinuteCount <= 3) percent = 5;
            else if(oneMinuteCount >= 20) percent = 95;
            else percent = ((oneMinuteCount - 3) / (20 - 3)) * 90 + 5;
            indicator.style.left = percent + '%';
        }
        
        navigate('scr-test-result');
        
        // Save results
        fetch("{{ route('blink_test.save') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ emp_code: state.empCode, blink_count: blinkCount })
        });
    }

    function populatePDFTemplate() {
        const element = document.getElementById('pdf-template');
        const count = parseInt(document.getElementById('scaled-blink-count').innerText);
        
        // Populate Template
        document.getElementById('pdf-blink-count').innerText = count;
        document.getElementById('pdf-date').innerText = document.getElementById('report-date').innerText;
        document.getElementById('pdf-so-name').innerText = document.getElementById('report-so-name').innerText;
        document.getElementById('pdf-report-id').innerText = 'BT-' + new Date().getTime().toString().substr(-6);
        
        const tier = document.getElementById('result-tier-badge');
        const pdfTier = document.getElementById('pdf-tier-badge');
        pdfTier.innerText = tier.innerText;
        pdfTier.style.color = tier.style.color;
        pdfTier.style.background = tier.style.background;
        
        document.getElementById('pdf-status').innerText = document.getElementById('result-status').innerText;
        document.getElementById('pdf-status').style.color = tier.style.color;
        document.getElementById('pdf-analysis').innerText = document.getElementById('result-analysis').innerText;
        
        // Calculate Indicator Position (approximate map 3-20 to 0-100%)
        let percent = 0;
        if(count <= 3) percent = 5;
        else if(count >= 20) percent = 95;
        else percent = ((count - 3) / (20 - 3)) * 90 + 5;
        
        document.getElementById('pdf-indicator').style.left = percent + '%';
        return element;
    }

    function downloadPDF() {
        const element = populatePDFTemplate();
        element.style.display = 'block'; // Temporarily show for capture
        
        const opt = {
            margin:       0,
            filename:     `Ajanta_Blink_Report_${new Date().getTime()}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: false },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        html2pdf().set(opt).from(element).save().then(() => {
            element.style.display = 'none';
        });
    }

    async function shareToWhatsApp() {
        const num = document.getElementById('whatsapp-num').value.trim();
        const count = document.getElementById('scaled-blink-count').innerText;
        const tier = document.getElementById('result-tier-badge').innerText;
        const status = document.getElementById('result-status').innerText;
        
        const msg = `🩺 *Ajanta Dry Eye Screening Complete*\n\nHello! Attached is your personalized *Blink Analysis Report*.\n\n📊 *Your Results:*\n• Blink Score: ${count} / min\n• Tier: ${tier}\n• Status: ${status}\n\n👤 *Facilitated by:* ${state.empName || state.empCode}\n\nStay focused on your eye health. Follow the recommendations in the attached report for better ocular comfort.`;

        // Try Native Share (allows file attachment on mobile)
        if (navigator.share && navigator.canShare) {
            try {
                const element = populatePDFTemplate();
                element.style.display = 'block';
                
                const opt = {
                    margin: 0,
                    filename: 'Blink_Report.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                const pdfBlob = await html2pdf().set(opt).from(element).output('blob');
                element.style.display = 'none';

                const file = new File([pdfBlob], `Dry_Eye_Report_${new Date().getTime()}.pdf`, { type: 'application/pdf' });

                if (navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        files: [file],
                        title: 'Ajanta Dry Eye Report',
                        text: msg
                    });
                    return;
                }
            } catch (err) {
                console.error("Share failed", err);
            }
        }

        // Fallback to WhatsApp Web Link (Text only)
        if (!num) return showToast("Please enter a WhatsApp number for link sharing");
        const url = `https://wa.me/${num}?text=${encodeURIComponent(msg)}`;
        window.open(url, '_blank');
    }

    async function shareMyLink() {
        const link = window.location.origin + '/' + state.empCode;
        const msg = `*Dry Eye Blink Test Screening*\n\nHi! You can take your 1-minute dry eye screening test directly on your phone using my personalized link below:\n\n${link}\n\nStay healthy! 👁️`;
        
        if (navigator.share) {
            try {
                await navigator.share({
                    title: 'Dry Eye Blink Test',
                    text: msg,
                    url: link
                });
            } catch (err) {
                console.error("Share failed", err);
            }
        } else {
            // Fallback: Copy to clipboard
            navigator.clipboard.writeText(link).then(() => {
                showToast("Link copied to clipboard!");
            });
        }
    }

    function onTestFinish() {
        // Reset dashboard cache to force latest records on top
        cache.currentPage = 0;
        
        if (state.isPatientMode) {
            document.getElementById('thank-you-so-code').innerText = state.empCode;
            navigate('scr-thank-you');
        } else {
            navigate('scr-dashboard');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Personalization display
        if (state.empName) {
            const display = document.getElementById('so-name-display');
            const container = document.getElementById('so-facilitator');
            if (display) display.innerText = state.empName;
            if (container) container.style.display = 'inline-block';
        }

        if (state.isPatientMode) {
            navigate('scr-disclaimer');
        } else if(state.isLoggedIn) {
            navigate('scr-dashboard');
        } else {
            navigate('scr-login');
        }
    });
</script>

</body>
</html>
