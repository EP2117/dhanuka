@if (
    (Request::path() == 'van' || Request::path() == 'purchase_office') &&
        (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 15 ||
            Auth::user()->role->id == 17))
    @if (Request::path() == 'purchase_office')
        <li class="text-center">
            <i class="fas fa-building module_logo_sm"></i>
            <h6 class="text-white">Purchase</h6>
        </li>
    @else
    @endif


    <hr class="sidebar-divider">

    <!-- Van Sale == 2; Office Sale == 1; -->
    <router-link tag="li" to="/purchase/<?php echo $role = Request::path() == 'van' ? '2' : '1'; ?>/" class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-chart-line"></i>
            <span>Purchase Invoice</span>
        </a>
    </router-link>
    <hr class="sidebar-divider">
    <router-link tag="li" to="/purchase_credit_note" class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-coins"></i>
            <span> Debit Note</span>
        </a>
    </router-link>

    <!--if(Request::path() != 'van' && Auth::user()->role->id != 6 && Auth::user()->role->id != 7) -->
    <!--for only system and admin role -->
    <hr class="sidebar-divider">
    <router-link tag="li" to="/purchase_collection" class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-coins"></i>
            <span>Credit Payment</span>
        </a>
    </router-link>
@endif
