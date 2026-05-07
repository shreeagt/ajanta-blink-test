@extends('layouts.admin-master')

@section('title', 'Manpower Master')

@section('content')
<div class="admin-hero">
    <h1>Field Force Management</h1>
    <p>Administer representative credentials and personalized screening URLs for the Dry Eye campaign.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-value">{{ $employees->count() }}</div>
            <div class="stat-label">Total Representatives</div>
        </div>
        <div class="stat-icon-box"><i class="fas fa-user-friends"></i></div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="color: #10b981;">Active</div>
            <div class="stat-label">Deployment Status</div>
        </div>
        <div class="stat-icon-box" style="background: #f0fdf4; color: #10b981;"><i class="fas fa-check-circle"></i></div>
    </div>
</div>

<div class="admin-content">
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 20px; padding: 20px; font-weight: 700;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="card-container">
        <div class="card-header-custom">
            <h2>Field Force Credentials</h2>
        </div>
        
        <div class="table-responsive-custom">
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
                        <td>
                            <span style="background: #eff6ff; color: var(--primary); font-weight: 900; font-size: 11px; padding: 6px 16px; border-radius: 10px; border: 1px solid #dbeafe;">
                                {{ $emp->emp_code }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 800; color: var(--text-dark); font-size: 15px;">{{ $emp->name }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-muted); font-size: 13px;">{{ $emp->hq }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <input type="text" class="form-control" value="{{ url('/') }}/{{ $emp->emp_code }}" readonly 
                                    style="font-size: 11px; font-weight: 700; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9; height: 38px; padding: 0 15px; color: #64748b; flex: 1;">
                                <button onclick="copyToClipboard('{{ url('/') }}/{{ $emp->emp_code }}', this)" 
                                    class="btn-custom" style="background: white; border: 1.5px solid var(--primary); color: var(--primary); height: 38px; padding: 0 15px; font-size: 12px; white-space: nowrap;">
                                    <i class="far fa-copy"></i> Copy
                                </button>
                            </div>
                        </td>
                        <td class="d-none">{{ $emp->emp_code }}</td>
                        <td class="d-none">{{ $emp->name }}</td>
                        <td class="d-none">{{ $emp->hq }}</td>
                        <td class="d-none">{{ url('/') }}/{{ $emp->emp_code }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copied';
            btn.style.background = '#10b981';
            btn.style.color = 'white';
            btn.style.borderColor = '#10b981';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = 'white';
                btn.style.color = 'var(--primary)';
                btn.style.borderColor = 'var(--primary)';
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
                    className: 'btn-custom btn-primary-custom',
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
@endsection
