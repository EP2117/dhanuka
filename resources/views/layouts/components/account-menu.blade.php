@if (Request::path() == 'van' || Request::path() == 'account')
    @if (Request::path() == 'account')
        <li class="text-center">
            <i class="fas fa-building module_logo_sm"></i>
            <h6 class="text-white">Account</h6>
        </li>
    @else
    @endif


    <hr class="sidebar-divider">

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 17)
        <!-- Van Sale == 2; Office Sale == 1; -->
        <router-link tag="li" to="/account_head" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-line"></i>
                <span>Account Head</span>
            </a>
        </router-link>
        <!--if(Request::path() != 'van' && Auth::user()->role->id != 6 && Auth::user()->role->id != 7) -->
        <!--for only system and admin role -->
    @endif
    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 17)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/sub_account" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-coins"></i>
                <span>Sub Account</span>
            </a>
        </router-link>
    @endif
    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 17)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/receipt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-receipt"></i>
                <span>Receipt</span>
            </a>
        </router-link>
    @endif
    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 17)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/payment" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-receipt"></i>
                <span>Payment</span>
            </a>
        </router-link>
    @endif
    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 17)
    @endif
    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 17)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/cashbook" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-receipt"></i>
                <span>Cashbook</span>
            </a>
        </router-link>
    @endif
    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 17 ||
            Auth::user()->role->id == 13)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/ledger" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-receipt"></i>
                <span>Ledger</span>
            </a>
        </router-link>
    @endif
@endif
