@if (Request::path() == 'marketings' &&
        (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 11 ||
            Auth::user()->role->id == 13))
    <li class="text-center">
        <i class="far fa-chart-bar module_logo_sm"></i>
        <h6 class="text-white">Marketing</h6>
    </li>
    <hr class="sidebar-divider">
    <router-link tag="li" to="/marketing" class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-chart-bar"></i>
            <span>Marketing Lists</span>
        </a>
    </router-link>
@endif


@if (Request::path() == 'marketings' &&
        (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 11 ||
            Auth::user()->role->id == 13 ||
            Auth::user()->role->id == 14 ||
            Auth::user()->role->id == 18 ||
            Auth::user()->role->id == 19))
    <hr class="sidebar-divider">
    <router-link tag="li" to="/marketing-rpt" class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-scroll"></i>
            <span>Marketing Report</span>
        </a>
    </router-link>
@endif
