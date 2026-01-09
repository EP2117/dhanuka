@if (Request::path() == 'report')
    <li class="text-center">
        <i class="far fa-chart-bar module_logo_sm"></i>
        <h6 class="text-white">Report</h6>
    </li>
    <hr class="sidebar-divider">
    <router-link tag="li" to="" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#sale_report_collapse">
            <i class="fas fa-chart-bar"></i>
            <span>Sale Reports</span>
        </a>
        <div id="sale_report_collapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if (Auth::user()->role->role_name == 'system' ||
                        Auth::user()->role->role_name == 'admin' ||
                        Auth::user()->role->id == 19 ||
                        Auth::user()->role->id == 10 ||
                        Auth::user()->role->id == 11 ||
                        Auth::user()->role->id == 14 ||
                        Auth::user()->role->id == 17 ||
                        Auth::user()->role->id == 18 ||
                        Auth::user()->role->id == 4 ||
                        Auth::user()->role->id == 22)
                    @if (Auth::user()->role->id != 22)
                        <router-link tag="span" to="/report/sale_order">
                            <a class="collapse-item">
                                <small>Sale Order Report</small>
                            </a>
                        </router-link>
                        <router-link tag="span" to="/report/so-product-rpt">
                            <a class="collapse-item">
                                <small>Sale Order Product Wise Report</small>
                            </a>
                        </router-link>
                    @endif
                @endif

                @if (Auth::user()->role->role_name == 'system' ||
                        Auth::user()->role->role_name == 'admin' ||
                        Auth::user()->role->id == 19 ||
                        Auth::user()->role->id == 10 ||
                        Auth::user()->role->id == 11 ||
                        Auth::user()->role->id == 13 ||
                        Auth::user()->role->id == 14 ||
                        Auth::user()->role->id == 17 ||
                        Auth::user()->role->id == 18 ||
                        Auth::user()->role->id == 4 ||
                        Auth::user()->role->id == 22)
                    {{-- <hr class="sidebar-divider"> --}}
                    <router-link tag="span" to="/report/daily-sale-rpt">
                        <a class="collapse-item">
                            <small>Daily Sale Report</small>
                        </a>
                    </router-link>
                    <router-link tag="span" to="/report/daily-sale-product-rpt">
                        <a class="collapse-item">
                            <small>Daily Sale Product Wise Report</small>
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
                        Auth::user()->role->id == 17 ||
                        Auth::user()->role->id == 18 ||
                        Auth::user()->role->id == 4)
                    <router-link tag="span" to="/report/credit_collection">
                        <a class="collapse-item">
                            <small>Credit Collection Report</small>
                        </a>
                    </router-link>
                @endif

                @if (Auth::user()->role->role_name == 'system' ||
                        Auth::user()->role->role_name == 'admin' ||
                        Auth::user()->role->id == 19 ||
                        Auth::user()->role->id == 10 ||
                        Auth::user()->role->id == 11 ||
                        Auth::user()->role->id == 14 ||
                        Auth::user()->role->id == 17 ||
                        Auth::user()->role->id == 13 ||
                        Auth::user()->role->id == 18 ||
                        Auth::user()->role->id == 4 ||
                        Auth::user()->role->id == 22)
                    <router-link tag="span" to="/report/sale_outstanding">
                        <a class="collapse-item">
                            <small>Sale Outstanding Report</small>
                        </a>
                    </router-link>
                @endif
            </div>
        </div>
    </router-link>

    <hr class="sidebar-divider">
    <router-link tag="li" to="" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#purchase_report_collapse">
            <i class="fas fa-chart-bar"></i>
            <span>Purchase Reports</span>
        </a>
        <div id="purchase_report_collapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if (Auth::user()->role->role_name == 'system' ||
                        Auth::user()->role->role_name == 'admin' ||
                        Auth::user()->role->id == 15 ||
                        Auth::user()->role->id == 19)
                    <router-link tag="span" to="/report/daily_purchase">
                        <a class="collapse-item">
                            <small>Daily Purchase Report</small>
                        </a>
                    </router-link>
                    <router-link tag="span" to="/report/daily_purchase_product_report">
                        <a class="collapse-item">
                            <small>Daily Purchase Product Wise Report</small>
                        </a>
                    </router-link>
                    <router-link tag="span" to="/report/credit_payment_report">
                        <a class="collapse-item">
                            <small>Credit Payment Report</small>
                        </a>
                    </router-link>
                    <router-link tag="span" to="/report/purchase_outstanding">
                        <a class="collapse-item">
                            <small>Purchase Outstanding Report</small>
                        </a>
                    </router-link>
                @endif
            </div>
        </div>
    </router-link>

    <hr class="sidebar-divider">
    <router-link tag="li" to="" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#inventory_report_collapse">
            <i class="fas fa-chart-bar"></i>
            <span>Inventory Reports</span>
        </a>
        <div id="inventory_report_collapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if (Auth::user()->role->role_name == 'system' ||
                        Auth::user()->role->role_name == 'admin' ||
                        Auth::user()->role->id == 15 ||
                        Auth::user()->role->id == 16 ||
                        Auth::user()->role->id == 19 ||
                        Auth::user()->role->id == 18 ||
                        Auth::user()->role->id == 14 ||
                        Auth::user()->role->id == 4 ||
                        Auth::user()->role->id == 22)
                    <router-link tag="span" to="/report/inventory-rpt">
                        <a class="collapse-item">
                            <small>Inventory Report</small>
                        </a>
                    </router-link>
                    <router-link tag="span" to="/report/valuation">
                        <a class="collapse-item">
                            <small>Inventory Valuation Report</small>
                        </a>
                    </router-link>
                    <router-link tag="span" to="/report/product-profit-rpt">
                        <a class="collapse-item">
                            <small>Product Wise Profit Report</small>
                        </a>
                    </router-link>
                @endif
            </div>
        </div>
    </router-link>

    @if (Auth::user()->role->id == 22)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/daily_purchase_product_report" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Daily Purchase Product Wise Report</span>
            </a>
        </router-link>
    @endif

    {{-- @if (Auth::user()->role->role_name == 'system' || Auth::user()->role->role_name == 'admin' || Auth::user()->role->id == 15 || Auth::user()->role->id == 19)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/daily_purchase" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Daily Purchase Report</span>
            </a>
        </router-link>
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/daily_purchase_product_report" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Daily Purchase Product Wise Report</span>
            </a>
        </router-link>
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/credit_payment_report" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Credit Payment Report</span>
            </a>
        </router-link>
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/purchase_outstanding" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Purchase Outstanding Report</span>
            </a>
        </router-link>
    @endif --}}

    {{-- @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 15 ||
            Auth::user()->role->id == 16 ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 18 ||
            Auth::user()->role->id == 14 ||
            Auth::user()->role->id == 4 ||
            Auth::user()->role->id == 22)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/inventory-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Inventory Report</span>
            </a>
        </router-link>
        @if (Auth::user()->role->id != 14 && Auth::user()->role->id != 4 && Auth::user()->role->id != 22)
            <hr class="sidebar-divider">
            <router-link tag="li" to="/report/valuation" class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-bar"></i>
                    <span>Inventory Valuation Report</span>
                </a>
            </router-link>

            <hr class="sidebar-divider">
            <router-link tag="li" to="/report/product-profit-rpt" class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-bar"></i>
                    <span>Product Wise Profit Report</span>
                </a>
            </router-link>
        @endif
    @endif --}}

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 19)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/profit_and_loss" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Profit & Loss Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/balance_sheet" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Balance Sheet</span>
            </a>
        </router-link>
    @endif
    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 13 ||
            Auth::user()->role->id == 14 ||
            Auth::user()->role->id == 17 ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 18 ||
            Auth::user()->role->id == 4 ||
            Auth::user()->role->id == 22)
        @if (Auth::user()->role->id != 22)
            <hr class="sidebar-divider">
            <router-link tag="li" to="/report/credit_note_report" class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-bar"></i>
                    <span>Credit Note Report</span>
                </a>
            </router-link>
        @endif
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/credit_note_product_wise" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span> Credit Note Product Wise Report</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 15 ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 22)
        @if (Auth::user()->role->id != 22)
            <hr class="sidebar-divider">
            <router-link tag="li" to="/report/purchase_credit_note_report" class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-bar"></i>
                    <span>Debit Note Report</span>
                </a>
            </router-link>
        @endif
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/purchase_credit_note_product_wise" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Debit Note Product Wise Report</span>
            </a>
        </router-link>
    @endif

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
        <router-link tag="li" to="/report/sale_foc" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale Foc Report</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 16 ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 22)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/transfer" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Transfer Report</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 12 ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 22)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/receive" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Receive Report</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->role_name == 'system' || Auth::user()->role->role_name == 'admin')
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/stock_adjustment" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Stock Adjustment Report</span>
            </a>
        </router-link>
    @endif
    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 18 ||
            Auth::user()->role->id == 14 ||
            Auth::user()->role->id == 17 ||
            Auth::user()->role->id == 4)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/sale_product_customer" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale Product Customer Report</span>
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
            Auth::user()->role->id == 17 ||
            Auth::user()->role->id == 18 ||
            Auth::user()->role->id == 4)
        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/sale_man_collection" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale Man Collection Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link tag="li" to="/report/cogs" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>COGS Report</span>
            </a>
        </router-link>
    @endif

    @if (Auth::user()->role->role_name == 'system' ||
            Auth::user()->role->role_name == 'admin' ||
            Auth::user()->role->id == 10 ||
            Auth::user()->role->id == 11 ||
            Auth::user()->role->id == 13 ||
            Auth::user()->role->id == 14 ||
            Auth::user()->role->id == 17 ||
            Auth::user()->role->id == 18 ||
            Auth::user()->role->id == 19 ||
            Auth::user()->role->id == 4)
        <router-link tag="li" to="/report" class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse"
                data-target="#top_10_report_collapse" aria-expanded="true" aria-controls="top_10_report_collapse">
                <i class="fas fa-file-import"></i>
                <span>Top 10 Report</span>
            </a>
            <div id="top_10_report_collapse" class="collapse" aria-labelledby="headingImport"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <router-link tag="span" to="/report/top_customer">
                        <a class="collapse-item">Top Customer</a>
                    </router-link>
                    <router-link tag="span" to="/report/top_Product">
                        <a class="collapse-item">Top Product</a>
                    </router-link>
                    <router-link tag="span" to="/report/top_category">
                        <a class="collapse-item">Top Category</a>
                    </router-link>
                    <router-link tag="span" to="/report/top_township">
                        <a class="collapse-item">Top Township</a>
                    </router-link>
                    <router-link tag="span" to="/report/top_state">
                        <a class="collapse-item">Top State</a>
                    </router-link>

                    <router-link tag="span" to="/report/top_salemen">
                        <a class="collapse-item">Top Sale Men</a>
                    </router-link>
                    <router-link tag="span" to="/report/top_Brand">
                        <a class="collapse-item">Top Brand</a>
                    </router-link>
                </div>
            </div>
        </router-link>
    @endif
@endif
