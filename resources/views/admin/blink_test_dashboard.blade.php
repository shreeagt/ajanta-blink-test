@extends('layouts.admin-master')

@section('title', 'Blink Test Analytics')

@section('extra_css')
<style>
    .stat-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f0fdf4; color: #10b981; font-size: 11px;
        font-weight: 800; padding: 4px 10px; border-radius: 50px;
        border: 1px solid #bbf7d0;
    }
    .action-btn {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--primary-gradient); color: white;
        border: none; border-radius: 12px; padding: 8px 16px;
        font-size: 12px; font-weight: 800; cursor: pointer;
        transition: all 0.2s ease; text-decoration: none;
    }
    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,94,184,0.25); color: white; text-decoration: none; }
    .so-chip {
        display: inline-flex; align-items: center; gap: 8px;
        background: #eff6ff; border: 1px solid #dbeafe;
        border-radius: 50px; padding: 4px 12px 4px 4px;
    }
    .so-avatar {
        width: 28px; height: 28px; border-radius: 50%;
        background: var(--primary-gradient); color: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 900;
    }
    .cvs-badge {
        font-size: 10px; font-weight: 900; padding: 3px 8px;
        border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;
    }
    /* Modal */
    .detail-modal { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
    .detail-modal.open { display: flex; }
    .detail-card { background: white; border-radius: 40px; max-width: 750px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 40px 100px rgba(0,0,0,0.2); animation: slideUp 0.4s cubic-bezier(0.34,1.56,0.64,1); }
    @keyframes slideUp { from { opacity: 0; transform: translateY(40px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .detail-card::-webkit-scrollbar { display: none; }
    .detail-header { background: var(--primary-gradient); padding: 40px; border-radius: 40px 40px 0 0; color: white; position: relative; }
    .detail-body { padding: 40px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
    .info-box { background: #f8fafc; border-radius: 20px; padding: 20px; border: 1px solid #f1f5f9; }
    .info-label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
    .info-val { font-size: 22px; font-weight: 900; color: #0f172a; }
    .info-sub { font-size: 12px; color: #64748b; font-weight: 600; margin-top: 2px; }
    .close-btn { position: absolute; top: 20px; right: 20px; width: 40px; height: 40px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; font-size: 16px; }
    .close-btn:hover { background: rgba(255,255,255,0.25); }
    .scale-bar { position: relative; height: 14px; background: #f1f5f9; border-radius: 20px; display: flex; overflow: hidden; border: 1px solid #e2e8f0; margin: 15px 0; }
    .scale-indicator { position: absolute; top: -7px; width: 28px; height: 28px; background: white; border-radius: 50%; border: 5px solid var(--primary); box-shadow: 0 4px 12px rgba(0,94,184,0.3); transition: left 1s cubic-bezier(0.34,1.56,0.64,1); }
</style>
@endsection

@section('content')
<div class="admin-hero">
    <h1>Blink Test Analytics</h1>
    <p>Complete overview of all screening sessions — with SO details, blink rate, and CVS data.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-value">{{ $pledgeCount }}</div>
            <div class="stat-label">Total Screenings</div>
        </div>
        <div class="stat-icon-box"><i class="fas fa-eye"></i></div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">{{ $todayCount }}</div>
            <div class="stat-label">Today's Screenings</div>
        </div>
        <div class="stat-icon-box" style="background:#f0fdf4; color:#10b981;"><i class="fas fa-calendar-day"></i></div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">{{ $soCount }}</div>
            <div class="stat-label">Field Force Units</div>
        </div>
        <div class="stat-icon-box" style="background:#fff7ed; color:#f59e0b;"><i class="fas fa-user-md"></i></div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">{{ $posters->where('cvs', '!=', null)->count() }}</div>
            <div class="stat-label">With CVS Data</div>
        </div>
        <div class="stat-icon-box" style="background:#fef2f2; color:#ef4444;"><i class="fas fa-clipboard-check"></i></div>
    </div>
</div>

<div class="admin-content">
    <div class="card-container">
        <div class="card-header-custom" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:15px;">
            <div>
                <h2>All Screening Records</h2>
                <p style="font-size:13px; color:#64748b; font-weight:600; margin:5px 0 0;">Click any row to view full test details &amp; download PDF</p>
            </div>
            <div style="display:flex; gap:15px; align-items:center;">
                <div style="position:relative;">
                    <i class="fas fa-filter" style="position:absolute; left:15px; top:12px; color:var(--primary); font-size:14px;"></i>
                    <select id="dmFilter" class="form-control" style="padding-left:40px; border-radius:12px; font-weight:700; color:#475569; height:42px; border:1px solid #cbd5e1; min-width:200px;">
                        <option value="">All District Managers</option>
                        @if(isset($uniqueDMs))
                            @foreach($uniqueDMs as $dm)
                                <option value="{{ $dm }}">{{ $dm }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <button onclick="exportCSV()" class="btn-custom btn-primary-custom" style="height:42px;">
                    <i class="fas fa-cloud-download-alt"></i> Export CSV
                </button>
            </div>
        </div>

        <div class="table-responsive-custom">
            <table class="table custom-table mb-0 w-100" id="analyticsTable">
                <thead>
                    <tr>
                        <th>Session</th>
                        <th>SO Name & ID</th>
                        <th>DM Name</th>
                        <th>Associated Doctor</th>
                        <th>HQ / State</th>
                        <th>Blink Rate</th>
                        <th>CVS Score</th>
                        <th>Date & Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posters as $item)
                    @php
                        $scaledCount = $item->blink_count;
                        if ($scaledCount <= 6) {
                            $status = 'Optimal'; $color = '#10b981'; $bg = '#f0fdf4';
                        } elseif ($scaledCount <= 10) {
                            $status = 'Excellent'; $color = '#34d399'; $bg = '#ecfdf5';
                        } elseif ($scaledCount <= 13) {
                            $status = 'Healthy'; $color = '#38bdf8'; $bg = '#f0f9ff';
                        } elseif ($scaledCount <= 16) {
                            $status = 'Mild Dry Eye'; $color = '#f59e0b'; $bg = '#fffbeb';
                        } elseif ($scaledCount <= 18) {
                            $status = 'Moderate'; $color = '#f97316'; $bg = '#fff7ed';
                        } elseif ($scaledCount <= 20) {
                            $status = 'High Risk'; $color = '#ef4444'; $bg = '#fef2f2';
                        } else {
                            $status = 'Severe'; $color = '#991b1b'; $bg = '#fef2f2';
                        }
                        $cvs = $item->cvs;
                        $soName = $item->employee ? $item->employee->name : ($item->emp_code ?: 'Unknown');
                        $soHq = $item->employee ? $item->employee->hq : '—';
                        $initials = collect(explode(' ', $soName))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
                    @endphp
                    <tr onclick="openDetail({{ $item->id }})" style="cursor:pointer;">
                        <td>
                            <div style="font-weight: 900; color: #1e293b; font-size: 15px;">SID-{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</div>
                            <span style="background:{{ $bg }}; color:{{ $color }}; font-size:10px; font-weight:900; border-radius:6px; padding:3px 8px; border:1px solid {{ $color }}30; display:inline-block; margin-top:4px;">
                                {{ strtoupper($status) }}
                            </span>
                        </td>
                        <td>
                            <div class="so-chip">
                                <div class="so-avatar">{{ $initials }}</div>
                                <div>
                                    <div style="font-size:13px; font-weight:800; color:#1e293b;">{{ $soName }}</div>
                                    <div style="font-size:11px; font-weight:700; color:var(--primary);">{{ $item->emp_code }}</div>
                                </div>
                            </div>
                        </td>
                        @php
                            $dmName = $item->employee ? $item->employee->dm_name : '—';
                            $rsmName = $item->employee ? $item->employee->rsm_name : '—';
                            $stateName = $item->employee ? $item->employee->state : '—';
                        @endphp
                        <td>
                            <div style="font-weight: 800; color: var(--text-dark); font-size: 13px;">{{ $dmName ?: '—' }}</div>
                            <div style="font-weight: 600; color: #94a3b8; font-size: 11px;">RSM: {{ $rsmName ?: '—' }}</div>
                        </td>
                        <td>
                            @if($item->doctor)
                                <div style="font-weight: 800; color: var(--primary); font-size: 13px;">{{ $item->doctor->name }}</div>
                                <div style="font-weight: 600; color: #94a3b8; font-size: 11px;">{{ $item->doctor->speciality ?: 'Doctor' }}</div>
                            @else
                                <span style="font-size: 11px; font-weight: 700; color: #cbd5e1; background: #f8fafc; padding: 4px 10px; border-radius: 6px; border: 1px solid #e2e8f0;">No Doctor</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:700; color:#64748b; font-size:13px;">{{ $soHq }}</div>
                            <div style="font-weight:600; color:#94a3b8; font-size:11px;">{{ $stateName ?: '—' }}</div>
                        </td>
                        <td>
                            <div style="font-size:20px; font-weight:900; color:#0f172a;">{{ $scaledCount }} <span style="font-size:11px; color:#94a3b8; font-weight:700;">bpm</span></div>
                            <div style="background:#f1f5f9; border-radius:10px; height:6px; width:100px; margin-top:6px; overflow:hidden;">
                                <div style="width:{{ min(100, ($scaledCount/20)*100) }}%; height:100%; background:{{ $color }}; border-radius:10px;"></div>
                            </div>
                        </td>
                        <td>
                            @if($cvs)
                                <div style="font-size:18px; font-weight:900; color:#92400e;">{{ $cvs->total_score }}<span style="font-size:11px; color:#94a3b8; font-weight:700;"> / 32</span></div>
                                <span class="cvs-badge" style="background:{{ $cvs->has_cvs ? '#fff7ed' : '#f0fdf4' }}; color:{{ $cvs->has_cvs ? '#f97316' : '#10b981' }}; border:1px solid {{ $cvs->has_cvs ? '#fed7aa' : '#bbf7d0' }};">
                                    {{ $cvs->has_cvs ? 'CVS Positive' : 'CVS Negative' }}
                                </span>
                            @else
                                <span style="font-size:12px; font-weight:700; color:#cbd5e1;">No CVS Data</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:800; color:#1e293b; font-size:13px;">{{ $item->created_at->format('d M, Y') }}</div>
                            <div style="font-size:12px; color:#94a3b8; font-weight:700;">{{ $item->created_at->format('h:i A') }}</div>
                        </td>
                        <td onclick="event.stopPropagation();">
                            <button onclick="openDetail({{ $item->id }})" class="action-btn">
                                <i class="fas fa-chart-bar"></i> View & PDF
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div class="detail-modal" id="detailModal" onclick="if(event.target===this) closeDetail()">
    <div class="detail-card" id="detailCard">
        <div class="detail-header" id="detail-header">
            <button class="close-btn" onclick="closeDetail()"><i class="fas fa-times"></i></button>
            <div style="font-size:12px; font-weight:800; opacity:0.7; text-transform:uppercase; letter-spacing:2px; margin-bottom:10px;">Screening Record</div>
            <div style="font-size:28px; font-weight:900; letter-spacing:-1px;" id="dh-id">SID-000000</div>
            <div style="margin-top:15px; display:flex; gap:10px; flex-wrap:wrap;">
                <div id="dh-badge" style="background:rgba(255,255,255,0.15); padding:6px 16px; border-radius:50px; font-size:12px; font-weight:800; border:1px solid rgba(255,255,255,0.2);"></div>
                <div id="dh-date" style="background:rgba(255,255,255,0.1); padding:6px 16px; border-radius:50px; font-size:12px; font-weight:700; opacity:0.8;"></div>
            </div>
        </div>
        <div class="detail-body">
            {{-- Doctor & Patient Info --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                {{-- Facilitator Box --}}
                <div style="background:#eff6ff; border-radius:24px; padding:20px; display:flex; align-items:center; gap:15px; border:1px solid #dbeafe;">
                    <div id="dh-avatar" style="width:50px; height:50px; border-radius:14px; background:var(--primary-gradient); color:white; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:900;"></div>
                    <div style="flex:1;">
                        <div style="font-size:10px; font-weight:800; color:#3b82f6; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px;">Sales Officer</div>
                        <div style="font-size:15px; font-weight:900; color:#1e3a8a; line-height:1.2;" id="dh-soname">—</div>
                        <div style="font-size:11px; font-weight:700; color:#3b82f6;" id="dh-socode">—</div>
                    </div>
                </div>
                
                {{-- Doctor Box --}}
                <div style="background:#f5f3ff; border-radius:24px; padding:20px; display:flex; align-items:center; gap:15px; border:1px solid #ddd6fe;">
                    <div style="width:50px; height:50px; border-radius:14px; background:linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color:white; display:flex; align-items:center; justify-content:center; font-size:18px;">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:10px; font-weight:800; color:#8b5cf6; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px;">Associated Doctor</div>
                        <div style="font-size:15px; font-weight:900; color:#4c1d95; line-height:1.2;" id="dh-docname">No Doctor</div>
                        <div style="font-size:11px; font-weight:700; color:#8b5cf6;" id="dh-docspec">—</div>
                    </div>
                </div>
            </div>

            {{-- Hierarchy Info --}}
            <div style="background:#f8fafc; border-radius:24px; padding:15px 25px; margin-bottom:25px; display:flex; justify-content:space-between; border:1px solid #f1f5f9; align-items:center;">
                <div style="display:flex; gap:30px;">
                    <div>
                        <div style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase; margin-bottom:2px;">District Manager</div>
                        <div style="font-size:13px; font-weight:800; color:#1e293b;" id="dh-sodm">—</div>
                    </div>
                    <div>
                        <div style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase; margin-bottom:2px;">Regional Manager</div>
                        <div style="font-size:13px; font-weight:800; color:#1e293b;" id="dh-sorsm">—</div>
                    </div>
                    <div>
                        <div style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase; margin-bottom:2px;">Headquarter</div>
                        <div style="font-size:13px; font-weight:800; color:#1e293b;" id="dh-sohq">—</div>
                    </div>
                </div>
            </div>

            {{-- Blink Results --}}
            <div style="font-size:13px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:15px;">Blink Analysis</div>
            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Blink Rate</div>
                    <div class="info-val" id="d-blink">—</div>
                    <div class="info-sub">blinks / minute</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Result</div>
                    <div class="info-val" id="d-status" style="font-size:16px;">—</div>
                    <div class="info-sub" id="d-analysis"></div>
                </div>
            </div>

            {{-- Scale Bar --}}
            <div style="margin-bottom:30px;">
                <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Stability Scale</div>
                <div class="scale-bar">
                    <div style="flex:6; background:#10b981;"></div>
                    <div style="flex:4; background:#34d399;"></div>
                    <div style="flex:3; background:#38bdf8;"></div>
                    <div style="flex:3; background:#fbbf24;"></div>
                    <div style="flex:2; background:#f97316;"></div>
                    <div style="flex:2; background:#ef4444;"></div>
                    <div class="scale-indicator" id="d-indicator" style="left:5%;"></div>
                </div>
            </div>

            {{-- CVS Results --}}
            <div id="cvs-section">
                <div style="font-size:13px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:15px;">CVS Assessment</div>
                <div class="info-grid">
                    <div class="info-box" style="background:#fffbeb;">
                        <div class="info-label" style="color:#d97706;">CVS Score</div>
                        <div class="info-val" id="d-cvs-score" style="color:#92400e;">—</div>
                        <div class="info-sub">out of 32</div>
                    </div>
                    <div class="info-box" style="background:#fffbeb;">
                        <div class="info-label" style="color:#d97706;">Diagnosis</div>
                        <div class="info-val" id="d-cvs-status" style="font-size:14px; color:#92400e;">—</div>
                    </div>
                </div>
            </div>
            <div id="no-cvs" style="display:none; background:#f8fafc; border-radius:20px; padding:20px; text-align:center; border:1px dashed #cbd5e1; margin-bottom:20px;">
                <i class="fas fa-clipboard" style="font-size:24px; color:#cbd5e1;"></i>
                <p style="font-size:13px; color:#94a3b8; font-weight:700; margin:8px 0 0;">CVS screening not completed</p>
            </div>

            {{-- PDF Download --}}
            <button onclick="downloadDetailPDF()" class="action-btn" style="width:100%; justify-content:center; padding:16px; border-radius:20px; font-size:15px; margin-top:10px;">
                <i class="fas fa-file-pdf"></i> Download Full Report PDF
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
let currentTest = null;

let table;
$(document).ready(function() {
    table = $('#analyticsTable').DataTable({
        dom: '<"row align-items-center mb-4"<"col-md-6"B><"col-md-6 d-flex justify-content-end"f>>rt<"row align-items-center mt-4"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="fas fa-file-excel mr-2"></i> Excel', className: 'btn-custom btn-primary-custom', title: 'Ajanta_BlinkTest_Report' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            search: "",
            searchPlaceholder: "Search records...",
            info: "Showing _START_ to _END_ of _TOTAL_ screenings"
        }
    });

    // Custom filtering for DM Name dropdown
    $('#dmFilter').on('change', function() {
        const selectedDM = $(this).val();
        if (selectedDM) {
            // Filter the 'DM Name' column (index 2) by exact match using regex
            table.column(2).search('^' + $.fn.dataTable.util.escapeRegex(selectedDM) + '$', true, false).draw();
        } else {
            // Clear filter
            table.column(2).search('').draw();
        }
    });
});

function openDetail(id) {
    document.getElementById('detailModal').classList.add('open');
    fetch(`/blink-test/${id}/detail`)
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const t = d.test;
            currentTest = t;
            const scaledCount = t.blink_count;
            const sid = 'SID-' + String(t.id).padStart(6, '0');
            const soName = t.employee ? t.employee.name : t.emp_code;
            const soCode = t.emp_code;
            const soHq = t.employee ? t.employee.hq : '—';
            const soDm = t.employee ? t.employee.dm_name : '—';
            const soRsm = t.employee ? t.employee.rsm_name : '—';
            const initials = soName.split(' ').map(w=>w[0] ? w[0].toUpperCase() : '').slice(0,2).join('');
            const date = new Date(t.created_at);
            const dateStr = date.toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'});

            // Status
            let status, color, analysis;
            if (scaledCount <= 6)       { status='Optimal'; color='#10b981'; analysis='Highly stable tear film. Excellent eye lubrication.'; }
            else if (scaledCount <= 10) { status='Excellent'; color='#34d399'; analysis='Good blink rate. Healthy moisture retention.'; }
            else if (scaledCount <= 13) { status='Healthy'; color='#38bdf8'; analysis='Normal blink pattern. Adequate lubrication.'; }
            else if (scaledCount <= 16) { status='Mild Dry Eye'; color='#f59e0b'; analysis='Slightly reduced blink rate. Consider eye drops.'; }
            else if (scaledCount <= 18) { status='Moderate'; color='#f97316'; analysis='Significant blink reduction. Recommend consultation.'; }
            else if (scaledCount <= 20) { status='High Risk'; color='#ef4444'; analysis='High dry eye risk. Urgent ophthalmologist visit recommended.'; }
            else                         { status='Severe'; color='#991b1b'; analysis='Severe dry eye. Immediate medical attention needed.'; }

            // Indicator position
            const pct = Math.min(95, Math.max(5, ((scaledCount-3)/(20-3))*90+5));

            // Populate
            document.getElementById('dh-id').textContent = sid;
            document.getElementById('dh-badge').textContent = status.toUpperCase();
            document.getElementById('dh-date').textContent = dateStr;
            document.getElementById('dh-avatar').textContent = initials;
            document.getElementById('dh-soname').textContent = soName;
            document.getElementById('dh-socode').textContent = soCode;
            document.getElementById('dh-sohq').textContent = soHq;
            document.getElementById('dh-sodm').textContent = soDm || '—';
            document.getElementById('dh-sorsm').textContent = soRsm || '—';
            
            // Doctor Info
            if (t.doctor) {
                document.getElementById('dh-docname').textContent = t.doctor.name;
                document.getElementById('dh-docspec').textContent = (t.doctor.speciality || 'General') + ' • ' + (t.doctor.city || '—');
            } else {
                document.getElementById('dh-docname').textContent = 'No Doctor';
                document.getElementById('dh-docspec').textContent = '—';
            }
            document.getElementById('d-blink').textContent = scaledCount;
            document.getElementById('d-status').textContent = status;
            document.getElementById('d-status').style.color = color;
            document.getElementById('d-analysis').textContent = analysis;
            setTimeout(() => { document.getElementById('d-indicator').style.left = pct + '%'; }, 100);

            // CVS
            if (t.cvs) {
                document.getElementById('cvs-section').style.display = 'block';
                document.getElementById('no-cvs').style.display = 'none';
                const cvsScore = t.cvs.total_score;
                let cvsStatus = cvsScore < 6 ? 'CVS Negative (Healthy)' : cvsScore <= 12 ? 'Mild CVS' : cvsScore <= 20 ? 'Moderate CVS' : 'Severe CVS';
                document.getElementById('d-cvs-score').textContent = cvsScore;
                document.getElementById('d-cvs-status').textContent = cvsStatus;
            } else {
                document.getElementById('cvs-section').style.display = 'none';
                document.getElementById('no-cvs').style.display = 'block';
            }
        });
}

function closeDetail() {
    document.getElementById('detailModal').classList.remove('open');
    document.getElementById('d-indicator').style.left = '5%';
    currentTest = null;
}

function downloadDetailPDF() {
    if (!currentTest) return;
    const el = document.getElementById('detailCard');
    const opt = {
        margin: 10,
        filename: `Ajanta_BlinkTest_${currentTest.emp_code}_${currentTest.id}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(el).save();
}

function exportCSV() {
    const table = document.getElementById('analyticsTable');
    let csv = [];
    const headers = Array.from(table.querySelectorAll('thead th')).slice(0,-1).map(th => '"'+th.textContent.trim()+'"');
    csv.push(headers.join(','));
    table.querySelectorAll('tbody tr').forEach(row => {
        const cols = Array.from(row.querySelectorAll('td')).slice(0,-1).map(td => '"'+td.textContent.trim().replace(/\n/g,' ').replace(/\s+/g,' ')+'"');
        csv.push(cols.join(','));
    });
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'Ajanta_BlinkTest_Report.csv';
    a.click();
}
</script>
@endsection
