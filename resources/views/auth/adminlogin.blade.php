@extends('layouts.auth-master')

@section('content')
<style>
    :root {
        --primary: #005eb8;
        --primary-gradient: linear-gradient(135deg, #005eb8 0%, #4caf50 100%);
    }
    body {
        background: var(--primary-gradient);
        background-attachment: fixed;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif;
    }
    .login-wrapper {
        width: 100%;
        max-width: 440px;
        padding: 20px;
    }
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        padding: 45px 35px;
        border-radius: 40px;
        box-shadow: 0 40px 100px rgba(0,0,0,0.15);
        text-align: center;
        border: 1px solid rgba(255,255,255,0.5);
    }
    .brand-logo {
        height: 45px;
        margin-bottom: 30px;
    }
    .login-title {
        font-size: 24px;
        font-weight: 900;
        color: #1e293b;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }
    .login-subtitle {
        font-size: 13px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 35px;
    }
    .form-group label {
        display: block;
        text-align: left;
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        margin-bottom: 8px;
        margin-left: 5px;
    }
    .form-control {
        height: 56px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        padding: 0 20px;
        font-weight: 600;
        font-size: 15px;
        transition: 0.3s;
    }
    .form-control:focus {
        background: white;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(0,94,184,0.1);
    }
    .btn-login {
        height: 60px;
        background: var(--primary-gradient);
        border: none;
        border-radius: 18px;
        color: white;
        font-weight: 900;
        font-size: 17px;
        width: 100%;
        margin-top: 15px;
        box-shadow: 0 15px 30px rgba(0,94,184,0.25);
        transition: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px rgba(0,94,184,0.3);
        opacity: 0.95;
    }
    .footer-note {
        margin-top: 30px;
        font-size: 12px;
        color: #94a3b8;
        font-weight: 600;
        line-height: 1.6;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <img src="{{ asset('assets/images/company_logo_horizontal.png') }}" class="brand-logo" alt="Ajanta Pharma">
        
        <h2 class="login-title">Dry Eye Analytics</h2>
        <p class="login-subtitle">Central Administration Portal</p>

        <form method="post" action="{{route('verify.admin')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            
            @include('layouts.partials.messages')

            <div class="form-group mb-4">
                <label>Admin Identifier</label>
                <input type="text" class="form-control" name="emp_no" value="{{ old('emp_no') }}" placeholder="Enter Employee ID" required autofocus>
            </div>

            <div class="form-group mb-4">
                <label>Security Password</label>
                <input type="password" class="form-control" name="password" placeholder="••••••••" required>
            </div>

            <button class="btn-login" type="submit">
                Secure Login <i class="fas fa-shield-alt"></i>
            </button>
        </form>

        <p class="footer-note">
            Authorized Personnel Only. <br>
            All access attempts are logged and monitored.
        </p>
    </div>
</div>
@endsection
@endsection