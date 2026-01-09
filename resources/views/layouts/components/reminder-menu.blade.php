@if (Request::path() == 'reminder')
    <li class="text-center">
        <i class="far fa-chart-bar module_logo_sm"></i>
        <h6 class="text-white">Remainder</h6>
    </li>
    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 10 ||
            Auth::user()->role->id == 11 ||
            Auth::user()->role->id == 14 ||
            Auth::user()->role->id == 17 ||
            Auth::user()->role->id == 18 ||
            Auth::user()->role->id == 4)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/reminder/sale_order_pending" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale Order Pending</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 12 ||
            Auth::user()->role->id == 14 ||
            Auth::user()->role->id == 17 ||
            Auth::user()->role->id == 4)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/reminder/delivery_pending" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Delivery Pending</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 10 ||
            Auth::user()->role->id == 11 ||
            Auth::user()->role->id == 13 ||
            Auth::user()->role->id == 14 ||
            Auth::user()->role->id == 18 ||
            Auth::user()->role->id == 14 ||
            Auth::user()->role->id == 17 ||
            Auth::user()->role->id == 4)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/reminder/sale_over_due" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale OverDue</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 15)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/reminder/purchase_over_due" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Purchase Over Due</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 15 ||
            Auth::user()->role->id == 16)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/reminder/min_max" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Min-Max Report</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 15 ||
            Auth::user()->role->id == 16)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/reminder/reorder_level" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Reorder Level Report</span>
            </a>
        </router-link>
    @endif


@endif
