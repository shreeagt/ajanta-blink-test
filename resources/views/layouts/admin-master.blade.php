<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Ajanta Blink</title>
    
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">

    <style>
        :root {
            --primary: #005eb8;
            --primary-gradient: linear-gradient(135deg, #005eb8 0%, #004282 100%);
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --surface: #ffffff;
            --radius-lg: 40px;
            --radius-md: 24px;
            --shadow-sm: 0 4px 12px rgba(0,0,0,0.02);
            --shadow-md: 0 20px 50px rgba(0,0,0,0.04);
            --shadow-lg: 0 30px 80px rgba(0,0,0,0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            background-attachment: fixed;
        }

        .admin-shell {
            width: 100%;
            max-width: 1280px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            min-height: 100vh;
            box-shadow: 0 0 100px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            position: relative;
            border-left: 1px solid rgba(255,255,255,0.5);
            border-right: 1px solid rgba(255,255,255,0.5);
        }

        /* --- Header --- */
        .admin-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            padding: 20px 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-links {
            display: flex;
            gap: 12px;
            background: #f1f5f9;
            padding: 8px;
            border-radius: 50px;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .nav-link-item {
            padding: 12px 28px;
            color: var(--text-muted);
            font-weight: 800;
            font-size: 14px;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link-item:hover {
            color: var(--primary);
            text-decoration: none;
            background: rgba(255, 255, 255, 0.5);
        }

        .nav-link-item.active {
            background: var(--surface);
            color: var(--primary);
            box-shadow: 0 10px 20px rgba(0,94,184,0.1);
            transform: scale(1.02);
        }

        .logout-btn {
            width: 48px;
            height: 48px;
            background: #fef2f2;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 1px solid #fee2e2;
        }

        .logout-btn:hover {
            background: #ef4444;
            color: white;
            text-decoration: none;
            transform: rotate(90deg);
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);
        }

        /* --- Hero --- */
        .admin-hero {
            padding: 60px 50px 40px;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.5) 0%, rgba(255, 255, 255, 0) 100%);
        }

        .admin-hero h1 {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: -1.5px;
            color: var(--text-dark);
            margin: 0;
            background: linear-gradient(to right, #0f172a, #334155);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .admin-hero p {
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 12px;
            font-size: 16px;
            max-width: 600px;
            line-height: 1.6;
        }

        /* --- Stats --- */
        .stats-grid {
            padding: 0 50px 50px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: var(--radius-md);
            padding: 35px;
            border: 1px solid rgba(255,255,255,0.8);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
            background: white;
            border-color: var(--primary);
        }

        .stat-value {
            font-size: 48px;
            font-weight: 900;
            color: var(--text-dark);
            line-height: 1;
            margin-bottom: 8px;
            letter-spacing: -2px;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .stat-icon-box {
            width: 70px;
            height: 70px;
            background: #eff6ff;
            color: var(--primary);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 10px 20px rgba(0,94,184,0.05);
        }

        /* --- Content Section --- */
        .admin-content {
            padding: 0 50px 80px;
        }

        .card-container {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .card-header-custom {
            padding: 40px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
        }

        .card-header-custom h2 {
            font-size: 24px;
            font-weight: 900;
            margin: 0;
            letter-spacing: -1px;
            color: #1e293b;
        }

        /* --- Table Styling --- */
        .table-responsive-custom {
            padding: 30px;
        }

        .custom-table {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .custom-table th {
            background: transparent !important;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 15px 30px !important;
            border: none !important;
        }

        .custom-table tbody tr {
            background: #fff;
            transition: all 0.3s ease;
        }

        .custom-table tbody td {
            padding: 25px 30px !important;
            vertical-align: middle !important;
            border-top: 1px solid #f1f5f9 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 15px;
        }

        .custom-table tbody td:first-child {
            border-left: 1px solid #f1f5f9 !important;
            border-radius: 20px 0 0 20px;
        }

        .custom-table tbody td:last-child {
            border-right: 1px solid #f1f5f9 !important;
            border-radius: 0 20px 20px 0;
        }

        .custom-table tbody tr:hover {
            transform: scale(1.005);
            z-index: 10;
            position: relative;
        }

        .custom-table tbody tr:hover td {
            background: #f8fafc;
            border-color: #e2e8f0 !important;
            box-shadow: 0 10px 20px rgba(0,0,0,0.02);
        }

        /* --- Buttons & Inputs --- */
        .btn-custom {
            border-radius: 18px;
            padding: 14px 28px;
            font-weight: 800;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            cursor: pointer;
        }

        .btn-primary-custom {
            background: var(--primary-gradient);
            color: white !important;
            box-shadow: 0 12px 25px rgba(0,94,184,0.2);
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0,94,184,0.3);
            text-decoration: none;
        }

        .dataTables_filter input {
            border: 2.5px solid #f1f5f9 !important;
            border-radius: 18px !important;
            padding: 14px 25px !important;
            outline: none !important;
            font-size: 15px !important;
            background: #f8fafc !important;
            width: 350px !important;
            transition: all 0.3s ease !important;
        }

        .dataTables_filter input:focus {
            border-color: var(--primary) !important;
            background: white !important;
            box-shadow: 0 10px 30px rgba(0,94,184,0.08) !important;
        }

        @media (max-width: 768px) {
            .admin-header { padding: 15px 20px; }
            .admin-hero, .stats-grid, .admin-content { padding-left: 20px; padding-right: 20px; }
            .nav-link-item { padding: 8px 16px; font-size: 12px; }
        }
    </style>
    @yield('extra_css')
</head>
<body>

    <div class="admin-shell">
        <header class="admin-header">
            <img src="{{ asset('assets/images/company_logo_horizontal.png') }}" style="height: 35px;" alt="Ajanta Logo">
            
            <nav class="nav-links">
                <a href="{{ route('admin.blink.dashboard') }}" class="nav-link-item {{ Request::routeIs('admin.blink.dashboard') ? 'active' : '' }}">Analytics</a>
                <a href="{{ route('admin.manpower.master') }}" class="nav-link-item {{ Request::routeIs('admin.manpower.master') ? 'active' : '' }}">Manpower</a>
            </nav>
            
            <a href="{{ route('admin.logout.perform') }}" class="logout-btn" title="Logout">
                <i class="fas fa-power-off"></i>
            </a>
        </header>

        <main>
            @yield('content')
        </main>
        
        <footer style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 13px; font-weight: 600;">
            &copy; {{ date('Y') }} Ajanta Pharma Ltd. &bull; Dry Eye Awareness Diagnostic Portal
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    @yield('scripts')
</body>
</html>
