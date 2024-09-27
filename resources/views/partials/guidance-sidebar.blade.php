<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
                <a href="{{route ('guidance.dashboard')}}" class="logo d-flex align-items-center">
                    <img src="../../../../assets/img/logo-2.png" alt="navbar brand" class="navbar-brand" height="40" />
                    <span class="ms-2" style="font-size: 18px; font-weight: bold; color: white;">BullyProof</span>
                </a>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="gg-menu-right"></i>
                    </button>
                    <button class="btn btn-toggle sidenav-toggler">
                        <i class="gg-menu-left"></i>
                    </button>
                </div>
                <button class="topbar-toggler more">
                    <i class="gg-more-vertical-alt"></i>
                </button>
            </div>
            <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
                <ul class="nav nav-secondary">
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Menu</h4>
                    </li>
                    <li class="nav-item {{ Route::currentRouteName() === 'guidance.dashboard' ? 'active' : '' }}">
                        <a href="{{ route('guidance.dashboard') }}" class="collapsed">
                            <i class="fas fa-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Report Management</h4>
                    </li>
                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['guidance.reports.incident-reports', 'guidance.reports.view']) ? 'active' : '' }}">
                        <a href="{{ route('guidance.reports.incident-reports') }}">
                            <i class="fas fa-file-alt"></i>
                            <p>Incidents Report</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarLayouts">
                            <i class="fas fa-users"></i>
                            <p>List of Bullies</p>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Appointment</h4>
                    </li>
                    <li class="nav-item {{ Route::currentRouteName() === 'guidance.users.users' ? 'active' : '' }}">
                        <a href="{{ route('guidance.users.users') }}">
                            <i class="fas fa-calendar"></i>
                            <p>Counselling Appointment</p>
                        </a>
                    </li>
                    {{-- <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Scheduling</h4>
                    </li>
                    <li class="nav-item ">
                        <a href="{{ route('guidance.users.users') }}">
                            <i class="fas fa-user"></i>
                            <p>Appointment</p>
                        </a>
                    </li> --}}
                </ul>
            </div>
        </div>
    </div>
    <!-- End Sidebar -->
