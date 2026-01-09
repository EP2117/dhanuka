@if (Request::path() == 'master' &&
        (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == '13' ||
            Auth::user()->role->id == '18' ||
            Auth::user()->role->id == '9' ||
            Auth::user()->role->id == '11' ||
            Auth::user()->role->id == '17'))

    <!-- Kaamlesh start -->
    <router-link tag="li" to="/master" class="nav-item">
        <li class="pcoded-hasmenu nav-item">
            <a class="nav-link  collapsed" href="#" data-toggle="collapse" data-target="#collapseMenu"
                aria-expanded="true" aria-controls="collapseTwo">
                <i class="fab fa-medium module_logo_sm text-lable icon-box"></i>
                <span class="text-lable pcoded-mtext">Master</span>
            </a>
        </li>
    </router-link>

    <!-- Kamlesh end -->
    @if (Auth::user()->role->role_name != 'marketing' &&
            Auth::user()->role->id != 13 &&
            Auth::user()->role->id != 18 &&
            Auth::user()->role->id != 11)
        <hr class="sidebar-divider">

        <router-link tag="li" to="/brand" class="nav-item">
            <a class="nav-link">
                <i class="fas fa-database"></i>
                <span>Brand</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <router-link tag="li" to="/category" class="nav-item">
            <a class="nav-link">
                <i class="fas fa-database"></i>
                <span>Category</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <router-link tag="li" to="/uom" class="nav-item">
            <a class="nav-link">
                <i class="fas fa-database"></i>
                <span>UOM</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <router-link tag="li" to="/product" class="nav-item">
            <a class="nav-link">
                <i class="fas fa-database"></i>
                <span>Product</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->id == 18)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/product" class="nav-item">
            <a class="nav-link">
                <i class="fas fa-database"></i>
                <span>Product</span>
            </a>
        </router-link>
    @endif
    <hr class="sidebar-divider">

    <router-link tag="li" to="/customer" class="nav-item">
        <a class="nav-link">
            <i class="fas fa-users"></i>
            <span>Customer</span>
        </a>
    </router-link>
    @if (Auth::user()->role->role_name != 'marketing' && Auth::user()->role->id != 11)
        @if (Auth::user()->role->id != 13 && Auth::user()->role->id != 18)
            <hr class="sidebar-divider">

            <router-link tag="li" to="/supplier" class="nav-item">
                <a class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Supplier</span>
                </a>
            </router-link>
        @endif
        @if (Auth::user()->role->id == 1)
            <hr class="sidebar-divider">
            <router-link tag="li" to="/supplier_ob" class="nav-item">
                <a class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Supplier Opening Balance</span>
                </a>
            </router-link>
            <hr class="sidebar-divider">
            <router-link tag="li" to="/customer_ob" class="nav-item">
                <a class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Customer Opening Balance</span>
                </a>
            </router-link>
        @endif


        <!--<hr class="sidebar-divider">-->
        @if (Auth::user()->role->role_name == 'admin' || (Auth::user()->role->id = 18 || (Auth::user()->role->id = 17)))
            <hr class="sidebar-divider">
            <router-link tag="li" to="/sale-man" class="nav-item">
                <a class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Sale Man</span>
                </a>
            </router-link>
        @endif

        @if (Auth::user()->role->role_name == 'system')
            <!-- Divider -->
            @if (Auth::user()->email != 'demo@mail.com')
                <hr class="sidebar-divider">

                <router-link tag="li" to="/import" class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseImport"
                        aria-expanded="true" aria-controls="collapseImport">
                        <i class="fas fa-file-import"></i>
                        <span>Import</span>
                    </a>
                    <div id="collapseImport" class="collapse" aria-labelledby="headingImport"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <router-link tag="span" to="/import/uom">
                                <a class="collapse-item">UOM</a>
                            </router-link>
                            <router-link tag="span" to="/import/brand">
                                <a class="collapse-item">Brand</a>
                            </router-link>
                            <router-link tag="span" to="/import/category">
                                <a class="collapse-item">Category</a>
                            </router-link>
                            <router-link tag="span" to="/import/product">
                                <a class="collapse-item">Product</a>
                            </router-link>
                            <router-link tag="span" to="/import/product_qty">
                                <a class="collapse-item">Product Min & % QTY</a>
                            </router-link>
                            <router-link tag="span" to="/import/warehouse">
                                <a class="collapse-item">Warehouse</a>
                            </router-link>
                            <router-link tag="span" to="/import/state">
                                <a class="collapse-item">State</a>
                            </router-link>
                            <router-link tag="span" to="/import/township">
                                <a class="collapse-item">Township</a>
                            </router-link>
                            <router-link tag="span" to="/import/customer_type">
                                <a class="collapse-item">Customer Type</a>
                            </router-link>
                            <router-link tag="span" to="/import/customer">
                                <a class="collapse-item">Customer</a>
                            </router-link>
                        </div>
                    </div>
                </router-link>
            @endif


            @if (Auth::user()->role->id == 1)
                <hr class="sidebar-divider">

                <router-link tag="li" to="/branch" class="nav-item">
                    <a class="nav-link">
                        <i class="far fa-building"></i>
                        <span>Branch</span>
                    </a>
                </router-link>

                <hr class="sidebar-divider">

                <router-link tag="li" to="/warehouse" class="nav-item">
                    <a class="nav-link">
                        <i class="fas fa-warehouse"></i>
                        <span>Warehouse</span>
                    </a>
                </router-link>
            @endif
            @if (Auth::user()->email != 'demo@mail.com')
                <hr class="sidebar-divider">

                <router-link tag="li" to="/users" class="nav-item">
                    <a class="nav-link">
                        <i class="fas fa-user-cog"></i>
                        <span>User Setting</span>
                    </a>
                </router-link>
            @endif
        @endif

    @endif

@endif
