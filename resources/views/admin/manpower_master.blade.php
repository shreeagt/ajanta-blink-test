@extends('layouts.admin-master')

@section('title', 'Manpower Master')

@section('content')
<div class="admin-hero d-flex justify-content-between align-items-center">
    <div>
        <h1>Field Force Management</h1>
        <p>Administer representative credentials and personalized screening URLs for the Dry Eye campaign.</p>
    </div>
    <a href="{{ route('admin.import.manpower') }}" class="btn-custom btn-primary-custom" style="padding: 12px 25px; border-radius: 15px;">
        <i class="fas fa-file-import mr-2"></i> Sync from CSV
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-value">{{ $soEmployees->count() + $dmEmployees->count() }}</div>
            <div class="stat-label">Total Representatives</div>
        </div>
        <div class="stat-icon-box"><i class="fas fa-user-friends"></i></div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="color: #3b82f6;">{{ $soEmployees->count() }}</div>
            <div class="stat-label">Sales Officers (SO)</div>
        </div>
        <div class="stat-icon-box" style="background: #eff6ff; color: #3b82f6;"><i class="fas fa-user-tie"></i></div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="color: #8b5cf6;">{{ $dmEmployees->count() }}</div>
            <div class="stat-label">District Managers (DM)</div>
        </div>
        <div class="stat-icon-box" style="background: #f5f3ff; color: #8b5cf6;"><i class="fas fa-user-shield"></i></div>
    </div>
</div>

<div class="admin-content">
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 20px; padding: 20px; font-weight: 700;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="card-container p-4">
        <!-- Tabs Navigation -->
        <ul class="nav nav-pills mb-4" id="manpowerTabs" role="tablist" style="background: #f8fafc; padding: 10px; border-radius: 16px; border: 1px solid #f1f5f9;">
            <li class="nav-item" role="presentation" style="flex: 1;">
                <button class="nav-link active w-100" id="so-tab" data-toggle="pill" data-target="#so-pane" type="button" role="tab" 
                    style="border-radius: 12px; font-weight: 800; font-size: 14px; padding: 12px;">
                    <i class="fas fa-users mr-2"></i> Sales Officers (SO)
                </button>
            </li>
            <li class="nav-item" role="presentation" style="flex: 1; margin-left: 10px;">
                <button class="nav-link w-100" id="dm-tab" data-toggle="pill" data-target="#dm-pane" type="button" role="tab" 
                    style="border-radius: 12px; font-weight: 800; font-size: 14px; padding: 12px;">
                    <i class="fas fa-user-shield mr-2"></i> District Managers (DM)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="manpowerTabsContent">
            <!-- SO Tab Pane -->
            <div class="tab-pane fade show active" id="so-pane" role="tabpanel" aria-labelledby="so-tab">
                <div class="table-responsive-custom">
                    <table class="table custom-table mb-0 w-100" id="soTable">
                        <thead>
                            <tr>
                                <th>Unit Code</th>
                                <th>SO Name</th>
                                <th>Reporting DM</th>
                                <th>HQ / State</th>
                                <th>Login URL</th>
                                <th class="d-none">Code</th>
                                <th class="d-none">Name</th>
                                <th class="d-none">DM</th>
                                <th class="d-none">RSM</th>
                                <th class="d-none">HQ</th>
                                <th class="d-none">State</th>
                                <th class="d-none">URL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($soEmployees as $emp)
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
                                    <div style="font-weight: 700; color: var(--text-dark); font-size: 13px;">{{ $emp->dm_name ?? '-' }}</div>
                                    <div style="font-weight: 600; color: var(--text-muted); font-size: 11px;">RSM: {{ $emp->rsm_name ?? '-' }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-muted); font-size: 13px;">{{ $emp->hq }}</div>
                                    <div style="font-weight: 600; color: var(--text-muted); font-size: 11px; margin-top: 3px;">{{ $emp->state }}</div>
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
                                <td class="d-none">{{ $emp->dm_name }}</td>
                                <td class="d-none">{{ $emp->rsm_name }}</td>
                                <td class="d-none">{{ $emp->hq }}</td>
                                <td class="d-none">{{ $emp->state }}</td>
                                <td class="d-none">{{ url('/') }}/{{ $emp->emp_code }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DM Tab Pane -->
            <div class="tab-pane fade" id="dm-pane" role="tabpanel" aria-labelledby="dm-tab">
                <div class="table-responsive-custom">
                    <table class="table custom-table mb-0 w-100" id="dmTable">
                        <thead>
                            <tr>
                                <th>DM Code</th>
                                <th>DM Name</th>
                                <th>Reporting RSM</th>
                                <th>State</th>
                                <th>Login URL</th>
                                <th class="d-none">Code</th>
                                <th class="d-none">Name</th>
                                <th class="d-none">RSM</th>
                                <th class="d-none">State</th>
                                <th class="d-none">URL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dmEmployees as $emp)
                            <tr>
                                <td>
                                    <span style="background: #f5f3ff; color: #8b5cf6; font-weight: 900; font-size: 11px; padding: 6px 16px; border-radius: 10px; border: 1px solid #ddd6fe;">
                                        {{ $emp->emp_code }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 800; color: var(--text-dark); font-size: 15px;">{{ $emp->name }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-dark); font-size: 13px;">{{ $emp->rsm_name ?? '-' }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-muted); font-size: 13px;">{{ $emp->state }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap: 10px;">
                                        <input type="text" class="form-control" value="{{ url('/') }}/{{ $emp->emp_code }}" readonly 
                                            style="font-size: 11px; font-weight: 700; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9; height: 38px; padding: 0 15px; color: #64748b; flex: 1;">
                                        <button onclick="copyToClipboard('{{ url('/') }}/{{ $emp->emp_code }}', this)" 
                                            class="btn-custom" style="background: white; border: 1.5px solid #8b5cf6; color: #8b5cf6; height: 38px; padding: 0 15px; font-size: 12px; white-space: nowrap;">
                                            <i class="far fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </td>
                                <td class="d-none">{{ $emp->emp_code }}</td>
                                <td class="d-none">{{ $emp->name }}</td>
                                <td class="d-none">{{ $emp->rsm_name }}</td>
                                <td class="d-none">{{ $emp->state }}</td>
                                <td class="d-none">{{ url('/') }}/{{ $emp->emp_code }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .nav-pills .nav-link { color: #64748b; transition: all 0.3s ease; border: 1px solid transparent; }
    .nav-pills .nav-link.active { background: white !important; color: var(--primary) !important; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
    #dm-tab.active { color: #8b5cf6 !important; }
</style>
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
                btn.style.color = btn.style.borderColor; // Revert to original color
                btn.style.borderColor = btn.style.borderColor;
            }, 2000);
        });
    }

    $(document).ready(function() {
        // Initialize SO Table
        $('#soTable').DataTable({
            dom: '<"row align-items-center mb-4"<"col-md-6"B><"col-md-6 d-flex justify-content-md-end"f>>rt<"row align-items-center mt-4"<"col-md-6"i><"col-md-6"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-cloud-download-alt mr-2"></i> Export SO List',
                    className: 'btn-custom btn-primary-custom',
                    title: 'Blink_Test_SO_Dataset',
                    exportOptions: { columns: [5, 6, 7, 8, 9, 10, 11] }
                }
            ],
            pageLength: 50,
            language: { search: "", searchPlaceholder: "Filter SOs...", info: "Showing _START_ to _END_ of _TOTAL_ SOs" }
        });

        // Initialize DM Table
        $('#dmTable').DataTable({
            dom: '<"row align-items-center mb-4"<"col-md-6"B><"col-md-6 d-flex justify-content-md-end"f>>rt<"row align-items-center mt-4"<"col-md-6"i><"col-md-6"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-cloud-download-alt mr-2"></i> Export DM List',
                    className: 'btn-custom btn-primary-custom',
                    style: 'background: #8b5cf6; border-color: #8b5cf6;',
                    title: 'Blink_Test_DM_Dataset',
                    exportOptions: { columns: [5, 6, 7, 8, 9] }
                }
            ],
            pageLength: 50,
            language: { search: "", searchPlaceholder: "Filter DMs...", info: "Showing _START_ to _END_ of _TOTAL_ DMs" }
        });
    });
</script>
@endsection
