@if (Request::path() == 'hr' &&
        (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'HR Admin' ||
            Auth::user()->role->role_name == 'HR User' ||
            Auth::user()->hr_access == 'admin' ||
            Auth::user()->hr_access == 'user'))
    <li class="text-center">
        <i class="far fa-chart-bar module_logo_sm"></i>
        <h6 class="text-white">HR</h6>
    </li>
    <hr class="sidebar-divider">
    <router-link tag="li" to="/employee" class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-users"></i>
            <span>Employee</span>
        </a>
    </router-link>

    <hr class="sidebar-divider">
    <router-link tag="li" to="/attendance" class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-clipboard-list"></i>
            <span>Attendance</span>
        </a>
    </router-link>

    <hr class="sidebar-divider">
    <router-link tag="li" to="/leave" class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-clipboard-list"></i>
            <span>Leave</span>
        </a>
    </router-link>
@endif
