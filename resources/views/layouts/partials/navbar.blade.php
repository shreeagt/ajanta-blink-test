<style>
  a.active {
    background-color: #DDF2FA;
    color: #102a83;
  }

  .log {
    position: absolute;
    /* bottom: 150px; */
    bottom: 30px;
  }

  .log a {
    border: 2px solid #2c4392;
    width: 250px;
  }


  .logo img {
    max-width: 100px;
  }

  .table {
    overflow-x: auto
  }

  @media only screen and (min-width: 992px) {
    /* .log {
      display: none !important;
    } */
  }
</style>
<div class="dashboard_content_wrapper">
  <div class="dashboard dashboard_wrapper pr30 pr0-md">



    @auth
    <div class="dashboard__sidebar">
      <div class="dashboard_header_logo mb-4 d-none d-lg-block text-center">
        <a href="/" class="logo">
            <img src="{{asset('assets/images/campaign_logo.png')}}" alt="logo">
        </a>
    </div>
      <div class="dashboard_sidebar_list">
        @if(Auth::user()->role_id == 1)
        <div class="sidebar_list_item">

             <a href="{{ route('admin.jal.dashboard') }}" class="{{ (request()->routeIs('admin.jal.dashboard') || request()->routeIs('admin.doctors') || request()->routeIs('doctor.campaign') || request()->routeIs('doctro.campaign.details')) ? 'active' : '' }} items-center">
            <i class="fa fa-user-md mr15"></i>Dashboard
          </a>
          {{-- <a href="{{ route('doctor.campaign') }}" class="{{ (request()->routeIs('admin.doctors') || request()->routeIs('doctor.campaign') || request()->routeIs('doctro.campaign.details')) ? 'active' : '' }} items-center">
            <i class="fa fa-user-md mr15"></i>Camp
          </a>
          <a href="{{ route('campaign.view') }}" class="{{ (request()->routeIs('admin.doctors') || request()->routeIs('doctor.campaign') || request()->routeIs('doctro.campaign.details')) ? 'active' : '' }} items-center">
            <i class="fas fa-first-aid mr15"></i>
            Camp Type
          </a>
          <a href="{{ route('doctor.report') }}" class="{{ (request()->routeIs('admin.doctors') || request()->routeIs('doctor.campaign') || request()->routeIs('doctro.campaign.details')) ? 'active' : '' }} items-center">
            <i class="fas fa-first-aid mr15"></i>
            Report
          </a>
          <a href="/admin/team" class="{{ (request()->routeIs('admin.doctors') || request()->routeIs('doctor.campaign') || request()->routeIs('doctro.campaign.details')) ? 'active' : '' }} items-center">
            <i class="fas fa-first-aid mr15"></i>
            Team
          </a> --}}
        </div>
        <div class="sidebar_list_item log" >
          <h3>{{Auth::user()->name}}</h3>
          <a href="{{ route('admin.logout.perform') }}" class="">
            <span class="flaticon-exit mr15"></span>Logout
          </a>
        </div>
        @elseif(Auth::user()->role_id == 2 && Auth::user()->role_id != 1)
       
      <div class="sidebar_list_item">
    <a href="{{ route('supervisor.doctors') }}" 
       class="items-center {{ request()->is('supervisor/doctors/status/all')  ? 'active' : '' }}">
        <i class="fa fa-user-md mr15"></i>All
    </a>
</div>

{{-- <div class="sidebar_list_item">
    <a href="{{ route('supervisor.doctors.status', ['status' => 'under_review']) }}" 
       class="items-center {{ request()->is('supervisor/doctors/status/under_review')  ? 'active' : '' }}">
        <i class="fa fa-user-md mr15"></i>Under Review
    </a>
</div>

<div class="sidebar_list_item">
    <a href="{{ route('supervisor.doctors.status', ['status' => 'active']) }}" 
       class="items-center {{ request()->is('supervisor/doctors/status/active') ? 'active' : '' }}">
        <i class="fa fa-user-md mr15"></i>Active
    </a>
</div>

<div class="sidebar_list_item">
    <a href="{{ route('supervisor.doctors.status', ['status' => 'completed']) }}" 
       class="items-center {{ request()->is('supervisor/doctors/status/completed') ? 'active' : '' }}">
        <i class=" fa fa-user-md mr15"></i>Completed
    </a>
</div> --}}
<div class="sidebar_list_item">
    <a href="{{ route('supervisor.own-certificates') }}" 
       class="items-center {{ request()->is('supervisor/my-team') ? 'active' : '' }}">
        <i class="fa fa-certificate mr15"></i>My Certificates
    </a>
</div>

<div class="sidebar_list_item">
    <a href="{{ route('supervisor.my-team') }}" 
       class="items-center {{ request()->is('supervisor/my-team') ? 'active' : '' }}">
        <i class="fa fa-users mr15"></i>My Team
    </a>
</div>


        <!-- <div class="sidebar_list_item">
          <a href="{{ route('doctor.campaign.listing') }}" class="{{ request()->routeIs('doctor.campaign.listing') ? 'active' : '' }} items-center">
            <i class="fa fa-list mr15"></i>Campaign List
          </a>
        </div> -->
        <div class="sidebar_list_item log" >
          <h3>{{Auth::user()->name}}</h3>
          <a href="{{ route('supervisor.logout') }}" class="">
            <span class="flaticon-exit mr15"></span>Logout
          </a>
        </div>
        @elseif(Auth::user()->role_id == 10)
        <div class="sidebar_list_item">
          <a href="/post-manager/camp-list" class="{{ request('/post-manager/camp-list') ? 'active' : '' }}  items-center">
            <i class="fas fa-first-aid mr15"></i>
            Camp
          </a>
        </div>
        <div class="sidebar_list_item">
          <a href="/post-manager/active-camp" class="{{ request()->routeIs('manager.active.camp') ? 'active' : '' }}  items-center">
            <i class="fas fa-first-aid mr15"></i>
            Active Camp
          </a>
        </div>
        <div class="sidebar_list_item">
          <a href="{{route('manager.logout')}}" class="{{ request()->routeIs('manager.active.camp') ? 'active' : '' }}  items-center">
            <i class="fas fa-first-aid mr15"></i>
            Logout
          </a>
        </div>
        @else
        <div class="sidebar_list_item">
          <a href="{{ route('so.doctors') }}" class="{{ request()->routeIs('so.campaign.list') ? 'active' : '' }}  items-center">
            <i class="fas fa-first-aid mr15"></i>
            All
          </a>
        </div>  
        {{-- <div class="sidebar_list_item">
          <a href="{{ route('underReviewCamp.camp') }}" class="{{ request()->routeIs('underReviewCamp.camp') ? 'active' : '' }}  items-center">
            <i class="fas fa-thumbs-up mr15"></i>
            Under Review
          </a>
        </div>
        <div class="sidebar_list_item">
          <a href="{{ route('active.camp') }}" class="{{ request()->routeIs('live.camp') ? 'active' : '' }}  items-center">
              <i class="fa-solid fa-wave-pulse mr15"></i>
        Active Camp
          </a>
        </div>
        <div class="sidebar_list_item">
          <a href="{{ route('completed.camp') }}" class="{{ request()->routeIs('completed.camp') ? 'active' : '' }}  items-center">
            <i class="fa-duotone fa-solid fa-circle-check mr15"></i>
            Completed Camp
          </a>
        </div> --}}

      
        <!-- <div class="sidebar_list_item">
          <a href="{{ route('closed.camp') }}" class="{{ request()->routeIs('closed.camp') ? 'active' : '' }}  items-center">
            <i class="fa-sharp fa-thin fa-door-closed mr15"></i>
            Closed Camp
          </a>
        </div>
        <div class="sidebar_list_item">
          <a href="{{ route('rejected.camp') }}" class="{{ request()->routeIs('rejected.camp') ? 'active' : '' }}  items-center">
            <i class="fas fa-ban mr15"></i>
            Rejected Camp
          </a>
        </div> -->
        <div class="sidebar_list_item log" >
          <h3>{{Auth::user()->name}}</h3>
          <a href="{{ route('so.logout') }}" class="">
            <span class="flaticon-exit mr15"></span>Logout
          </a>
        </div>
        @endif
      </div>
    </div>
    @endauth
  </div>
</div>
