<div class="col-xl-3 col-md-6">
    <div class="card stat-card h-100 border-0 shadow-sm">
        <div class="card-body p-4">
            <p class="text-muted text-uppercase fw-semibold mb-1">Total Doctors</p>
            <h3 class="fw-bold text-dark">{{ $campaign['total'] }}</h3>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-6">
    <div class="card stat-card h-100 border-0 shadow-sm">
        <div class="card-body p-4">
            <p class="text-muted text-uppercase fw-semibold mb-1">Active Doctors</p>
            <h3 class="fw-bold text-dark">{{ $campaign['active'] }}</h3>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-6">
    <div class="card stat-card h-100 border-0 shadow-sm">
        <div class="card-body p-4">
            <p class="text-muted text-uppercase fw-semibold mb-1">Under Review</p>
            <h3 class="fw-bold text-dark">{{ $campaign['pending'] }}</h3>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-6">
    <div class="card stat-card h-100 border-0 shadow-sm">
        <div class="card-body p-4">
            <p class="text-muted text-uppercase fw-semibold mb-1">Completed</p>
            <h3 class="fw-bold text-dark">{{ $campaign['completed'] }}</h3>
        </div>
    </div>
</div>
