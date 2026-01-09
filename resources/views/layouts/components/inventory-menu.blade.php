<!-- Nav Item - Pages Collapse Menu -->
@if (Request::path() == 'inventory' &&
        (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 16))
    <li class="text-center">
        <i class="fas fa-boxes module_logo_sm"></i>
        <h6 class="text-white">Inventory</h6>
    </li>
    @if (Auth::user()->role->id != 16)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/inventory/main-warehouse" class="nav-item">
            <a class="nav-link">
                <i class="fas fa-warehouse"></i>
                <span>Main Warehouse</span>
            </a>
        </router-link>
    @endif
    <hr class="sidebar-divider">


    <router-link tag="li" to="/inventory/transfer" class="nav-item">
        <a class="nav-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Internal Transfer</span>
        </a>
    </router-link>

    <hr class="sidebar-divider">

    <router-link tag="li" to="/inventory/receive" class="nav-item">
        <a class="nav-link">
            <i class="fas fa-sign-in-alt"></i>
            <span>Internal Receive</span>
        </a>
    </router-link>
    <hr class="sidebar-divider">

    <router-link tag="li" to="/inventory/adjustment" class="nav-item">
        <a class="nav-link">
            <i class="fas fa-sign-in-alt"></i>
            <span>Inventory Adjustment</span>
        </a>
    </router-link>
@endif
