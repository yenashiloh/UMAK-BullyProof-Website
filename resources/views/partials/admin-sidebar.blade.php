<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
                <a href="{{route ('admin.dashboard')}}" class="logo d-flex align-items-center">
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
                    <li class="nav-item {{ Route::currentRouteName() === 'admin.dashboard' ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="collapsed">
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
                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['admin.reports.incident-reports', 'admin.reports.view']) ? 'active' : '' }}">
                        <a href="{{ route('admin.reports.incident-reports') }}">
                            <i class="fas fa-file-alt"></i>
                            <p>Incidents Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarLayouts">
                            <i class="fas fa-users"></i>
                            <p>Complainants</p>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::currentRouteName() === 'admin.list.list-perpetrators' ? 'active' : '' }}">
                        <a href="{{ route('admin.list.list-perpetrators') }}">
                            <i class="fas fa-user-friends"></i>
                            <p>Respondents</p>
                        </a>
                    </li>      
                    
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Appointment Management</h4>
                    </li>
                    <li class="nav-item {{ Route::currentRouteName() === 'admin.appointment.appointment' ? 'active' : '' }}">
                        <a href="{{ route('admin.appointment.appointment') }}">
                            <i class="fas fa-calendar"></i>
                            <p>Appointments</p>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::currentRouteName() === 'admin.appointment.summary' ? 'active' : '' }}">
                        <a href="{{ route('admin.appointment.summary') }}">
                            <i class="fas fa-calendar-check"></i>
                            <p>Appointments Summary</p>
                        </a>
                    </li>
                    {{-- <li class="nav-item ">
                        <a href="">
                            <i class="fas fa-envelope"></i>
                            <p>Email Content</p>
                        </a>
                    </li> --}}
                    
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">User Management</h4>
                    </li>
                    <li class="nav-item {{ Route::currentRouteName() === 'admin.users.users' ? 'active' : '' }}">
                        <a href="{{ route('admin.users.users') }}">
                            <i class="fas fa-user"></i>
                            <p>Users</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- End Sidebar -->
