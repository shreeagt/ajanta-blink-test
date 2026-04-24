<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manpower</title>
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <style>
        :root {
            --primary: #005eb8;
            --primary-gradient: linear-gradient(135deg, #005eb8 0%, #4caf50 100%);
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: var(--text-dark);
            margin: 0;
            padding: 0;
        }
        .app-envelope {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            min-height: 100vh;
            box-shadow: 0 0 50px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }
        .app-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav-links a {
            color: var(--text-muted);
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            margin-right: 20px;
            transition: 0.2s;
        }
        .nav-links a:hover, .nav-links a.active { color: var(--primary); }
        .logout-btn { color: #94a3b8; font-size: 18px; transition: 0.2s; }
        .logout-btn:hover { color: #ef4444; }

        .dashboard-hero {
            padding: 40px 30px 20px;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        }
        .dashboard-hero h1 { font-size: 28px; font-weight: 900; letter-spacing: -0.5px; margin: 0; }
        .dashboard-hero p { color: var(--text-muted); font-weight: 600; margin-top: 5px; }

        .summary-container {
            padding: 0 30px 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .stat-info h3 { font-size: 32px; font-weight: 800; margin: 0; color: var(--text-dark); }
        .stat-info p { font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin: 0; }
        .stat-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: #f1f5f9; color: var(--primary); }

        .table-section { padding: 0 30px 50px; }
        .table-card {
            background: white; border-radius: 24px; border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); overflow: hidden;
        }
        .table-header { padding: 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .table-header h2 { font-size: 18px; font-weight: 800; margin: 0; }

        .custom-table th {
            background: #f8fafc; color: var(--text-muted); font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px; padding: 15px 25px; border: none;
        }
        .custom-table td { padding: 18px 25px; vertical-align: middle; border-top: 1px solid #f8fafc; }
        
        .dataTables_wrapper .dataTables_filter { margin-bottom: 20px; }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 18px; outline: none; font-size: 14px; background: #f8fafc;
        }
        .dt-buttons .btn { border-radius: 12px; font-weight: 800; font-size: 13px; padding: 10px 20px; }
        
        .page-item.active .page-link { background: var(--primary-gradient) !important; border: none !important; box-shadow: 0 4px 12px rgba(0,94,184,0.3); }
        .page-link { border: none !important; color: var(--text-muted); font-weight: 700; border-radius: 10px !important; margin: 0 4px; padding: 10px 16px; }
    </style>
</head>
<body>

  <div class="app-envelope">
    <div class="app-header">
        <img src="{{ asset('assets/images/company_logo_horizontal.png') }}" style="height: 35px;">
        <div class="nav-links">
            <a href="{{ route('admin.blink.dashboard') }}">Analytics</a>
            <a href="{{ route('admin.manpower.master') }}" class="active">Manpower</a>
        </div>
        <a href="{{ route('admin.logout.perform') }}" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
    </div>

    <div class="dashboard-hero">
        <h1>Field Force Management</h1>
        <p>Administer representative credentials and personalized screening URLs for the Dry Eye campaign.</p>
    </div>

    <div class="summary-container">
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Representatives</p>
                <h3>{{ $employees->count() }}</h3>
            </div>
            <div class="stat-icon"><i class="fas fa-user-friends"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Deployment Status</p>
                <h3 style="color: #10b981;">Active</h3>
            </div>
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>

    <div class="table-section">
        @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        @endif

        <div class="table-card">
            <div class="table-header">
                <h2>Field Force Credentials</h2>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 w-100" id="manpowerTable">
                        <thead>
                            <tr>
                                <th>Unit Code</th>
                                <th>Representative Name</th>
                                <th>Headquarter</th>
                                <th>Campaign URL</th>
                            <th class="d-none">Code</th>
                            <th class="d-none">Name</th>
                            <th class="d-none">HQ</th>
                            <th class="d-none">URL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                        <tr>
                            <td><span class="badge" style="background: #f1f5f9; color: var(--primary); font-weight: 800; font-size: 11px; padding: 5px 12px; border-radius: 8px;">{{ $emp->emp_code }}</span></td>
                            <td><div class="font-weight-bold text-dark" style="font-size: 15px;">{{ $emp->name }}</div></td>
                            <td><div class="text-muted font-weight-bold" style="font-size: 13px;">{{ $emp->hq }}</div></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control form-control-sm mr-2" value="{{ url('/') }}/{{ $emp->emp_code }}" readonly style="font-size: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; height: 30px; padding: 0 10px; color: #64748b;">
                                    <button onclick="copyToClipboard('{{ url('/') }}/{{ $emp->emp_code }}', this)" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-weight: 800; padding: 4px 10px; font-size: 10px; white-space: nowrap; height: 30px; display: flex; align-items: center; justify-content: center;">
                                        <i class="far fa-copy mr-1" style="font-size: 11px;"></i> Copy
                                    </button>
                                </div>
                            </td>
                            <td class="d-none">{{ $emp->emp_code }}</td>
                            <td class="d-none">{{ $emp->name }}</td>
                            <td class="d-none">{{ $emp->hq }}</td>
                            <td class="d-none">{{ url('/') }}/{{ $emp->emp_code }}</td>
                        </tr>
                        @endforeach

                        @if($employees->count() == 0)
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-3x mb-3" style="opacity: 0.2;"></i>
                                <h5 style="font-weight: 800;">No representative data found.</h5>
                                <p style="font-size: 14px;">Sync official manpower data to begin.</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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

    <script>
        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
                btn.classList.replace('btn-outline-primary', 'btn-success');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.replace('btn-success', 'btn-outline-primary');
                }, 2000);
            });
        }

        $(document).ready(function() {
            $('#manpowerTable').DataTable({
                dom: '<"row align-items-center mb-4"<"col-md-6"B><"col-md-6 d-flex justify-content-md-end"f>>rt<"row align-items-center mt-4"<"col-md-6"i><"col-md-6"p>>',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-cloud-download-alt mr-2"></i> Export Unit List',
                        className: 'btn btn-success shadow-sm',
                        title: 'Dry_Eye_Manpower_Dataset',
                        exportOptions: {
                            columns: [4, 5, 6, 7]
                        }
                    }
                ],
                pageLength: 50,
                language: {
                    search: "",
                    searchPlaceholder: "Filter manpower...",
                    info: "Showing _START_ to _END_ of _TOTAL_ representatives"
                }
            });
        });
    </script>
  </div>
</body>
</html>
