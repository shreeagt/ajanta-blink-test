@extends('layouts.auth-master')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #005eb8;
            --primary-gradient: linear-gradient(135deg, #005eb8 0%, #004282 100%);
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 20px;
        }

        .login-card {
            background: white;
            padding: 50px 40px;
            border-radius: 40px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.08);
            text-align: center;
            border: 1px solid #f1f5f9;
            width: 100%;
            max-width: 440px;
        }

        .branding-badge {
            display: inline-block;
            padding: 10px 24px;
            background: #eff6ff;
            border-radius: 50px;
            margin-bottom: 30px;
            border: 1px solid #dbeafe;
        }

        .branding-badge span {
            font-size: 13px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .login-title {
            font-size: 28px;
            font-weight: 900;
            color: #1e293b;
            margin-bottom: 8px;
            letter-spacing: -1px;
        }

        .login-subtitle {
            font-size: 14px;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 40px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 10px;
            margin-left: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 18px;
        }

        .form-control {
            height: 60px;
            width: 100%;
            border-radius: 20px;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            padding: 0 20px 0 55px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 5px rgba(0, 94, 184, 0.1);
            outline: none;
        }

        .btn-login {
            height: 64px;
            background: var(--primary-gradient);
            border: none;
            border-radius: 20px;
            color: white;
            font-weight: 800;
            font-size: 18px;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 15px 35px rgba(0, 94, 184, 0.25);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 45px rgba(0, 94, 184, 0.35);
        }

        .footer-note {
            margin-top: 35px;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
            line-height: 1.6;
        }
    </style>

    <div class="login-card">
        <div class="branding-badge">
            <span>Ajanta Pharma</span>
        </div>

        <h2 class="login-title">Admin Access</h2>
        <p class="login-subtitle">Diagnostic Analytics Dashboard</p>

        <form method="post" action="{{route('verify.admin')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            @include('layouts.partials.messages')

            <div class="form-group">
                <label>Admin ID</label>
                <div class="input-wrapper">
                    <i class="fas fa-user-shield"></i>
                    <input type="text" class="form-control" name="email" value="{{ old('email') }}"
                        placeholder="Enter Admin ID" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label>Security Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button class="btn-login" type="submit">
                Access Dashboard <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <p class="footer-note">
            Unauthorized access is strictly prohibited. <br>
            All administrative actions are encrypted and logged.
        </p>
    </div>
@endsection