<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Edit Comparison Report</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Sale Edit Comparison Report</h4>            
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="app_from_date">Approval From Date</label>
                        <input type="text" class="form-control datetimepicker" id="app_from_date" name="app_from_date"
                        v-model="search.app_from_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="app_to_date">Approval To Date</label>
                        <input type="text" class="form-control datetimepicker" id="app_to_date" name="app_to_date"
                        v-model="search.app_to_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="approval_no">Approval No.</label>
                        <input type="text" class="form-control" id="approval_no" name="approval_no" v-model="search.approval_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="customer_id">Customer</label>
                        <select id="customer_id" class="form-control mm-txt"
                            name="customer_id" v-model="search.customer_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                        </select>
                    </div> 

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="inv_from_date">Invoice From Date</label>
                        <input type="text" class="form-control datetimepicker" id="inv_from_date" name="inv_from_date"
                        v-model="search.inv_from_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="inv_to_date">Invoice To Date</label>
                        <input type="text" class="form-control datetimepicker" id="inv_to_date" name="inv_to_date"
                        v-model="search.inv_to_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="invoice_no">Invoice No.</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="search.invoice_no">
                    </div>  
                    
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="reference_no">Reference No.</label>
                        <input type="text" class="form-control" id="reference_no" name="reference_no" v-model="search.reference_no">
                    </div>  

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="branch_id">Branch</label>
                        <select id="branch_id" class="form-control mm-txt"
                            name="branch_id" v-model="search.branch_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="warehouse_id">Warehouse</label>
                        <select id="warehouse_id" class="form-control"
                            name="warehouse_id" v-model="search.warehouse_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="warehouse in warehouses" :value="warehouse.id"  >{{warehouse.warehouse_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getRevises(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sale List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="sale_count > 0">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Approval No.</th>
                                <th class="text-center">Approval Date</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Invoice No.</th>
                                <th class="text-center">Reference No.</th>
                                <th class="text-center">Invoice Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Edit User</th>
                                <th class="text-center">Edit Count</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Approval No.</th>
                                <th class="text-center">Approval Date</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Invoice No.</th>
                                <th class="text-center">Reference No.</th>
                                <th class="text-center">Invoice Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Edit User</th>
                                <th class="text-center">Edit Count</th>
                                <th class="text-center"></th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <template v-for="sale,index in sales.data">
                            <tr>
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td>{{sale.approval.approval_no}}</td>
                                <td>{{localTime(sale.approval.created_at)}}</td>
                                <td class="mm-txt">{{sale.customer.cus_name}}</td>
                                <td>{{sale.invoice_no}}</td>
                                <td>{{sale.reference_no}}</td>
                                <td>{{sale.invoice_date}}</td>
                                <td v-if="sale.branch != null">{{sale.branch.branch_name}}</td>
                                <td v-else></td>
                                <td v-if="sale.update_user != null">{{sale.update_user.name}}</td>
                                <td v-else></td>
                                <td>{{sale.edit_count}}</td>
                                <td class="text-center">
                                    <a  class="text-white btn btn-sm btn-primary btn-icon" @click="viewDetail(sale.id)">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            <tr style="border:none;display:none" :id="sale.id" class="approval_view">
                                <td colspan="11" style="border:none;">
                                    <table class="table table-bordered" id="" style="background:#f7fafa;">
                                        <thead class="thead-grey">
                                            <tr>
                                                <td colspan='7' class="text-center bg-light"><strong>Approval</strong></td>
                                            </tr>
                                            <tr>
                                                <th scope="col" class="bg_blue">Product Name</th>
                                                <th scope="col" class="bg_blue">QTY</th>
                                                <th scope="col" class="bg_blue">Selling Unit</th>
                                                <th scope="col" class="bg_blue">Relation</th>
                                                <th scope="col" class="bg_blue">FOC</th>
                                                <th scope="col" class="bg_blue">Unit Price</th>
                                                <th scope="col" class="bg_blue">Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="prod,index in sale.approval.products">
                                                <td>{{prod.product_name}}</td>
                                                <td>{{prod.pivot.approval_qty}}</td>

                                                <td v-if="prod.uom_id == prod.pivot.uom_id">
                                                    {{prod.uom.uom_name}}
                                                </td>
                                                <td v-else>
                                                    {{getUOMname(prod.selling_uoms,prod.pivot.uom_id)}}
                                                </td>

                                                <td v-if="prod.uom_id == prod.pivot.uom_id"></td>
                                                <td v-else>
                                                {{getRelation(prod,prod.selling_uoms,prod.pivot.uom_id)}}
                                                </td>

                                                <td v-if="prod.pivot.is_foc == 0">
                                                </td>
                                                <td v-else>
                                                    FOC
                                                </td>

                                                <td class="text-right">
                                                    {{prod.pivot.price}}
                                                </td>

                                                <td class="text-right">
                                                    {{prod.pivot.total_amount}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="text-right">Sub Total</td>
                                                
                                                <td class="text-right">
                                                    {{getSubTotal(sale.approval.products)}}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr style="border:none;display:none" :id="'revise_vw'+sale.id" class="revise_view">
                                <td colspan="11" :id="'revise_tbl'+sale.id" style="border:none;">
                                    <table class="table table-bordered" id="" style="background:#f7fafa;">
                                        <thead class="thead-grey">
                                            <tr>
                                                <td colspan='8' class="text-center bg-light"><strong>Revise</strong></td>
                                            </tr>
                                            <tr>
                                                <th scope="col" class="bg_blue">Product Name</th>
                                                <th scope="col" class="bg_blue">QTY</th>
                                                <th scope="col" class="bg_blue">Selling Unit</th>
                                                <th scope="col" class="bg_blue">Relation</th>
                                                <th scope="col" class="bg_blue">FOC</th>
                                                <th scope="col" class="bg_blue">Unit Price</th>
                                                <th scope="col" class="bg_blue">Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="prod,index in sale.products">
                                                <td>{{prod.product_name}}</td>
                                                <td>{{prod.pivot.product_quantity}}</td>

                                                <td v-if="prod.uom_id == prod.pivot.uom_id">
                                                    {{prod.uom.uom_name}}
                                                </td>
                                                <td v-else>
                                                    {{getUOMname(prod.selling_uoms,prod.pivot.uom_id)}}
                                                </td>

                                                <td v-if="prod.uom_id == prod.pivot.uom_id"></td>
                                                <td v-else>
                                                {{getRelation(prod,prod.selling_uoms,prod.pivot.uom_id)}}
                                                </td>

                                                <td v-if="prod.pivot.is_foc == 0">
                                                </td>
                                                <td v-else>
                                                    FOC
                                                </td>

                                                <td class="text-right">
                                                    {{prod.pivot.price}}
                                                </td>

                                                <td class="text-right">
                                                    {{prod.pivot.total_amount}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="text-right">Sub Total</td>
                                                
                                                <td class="text-right">
                                                    {{getSubTotal(sale.products)}}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No record found!</h5>
                </div>

            </div>

            <div class="card-footer text-center">
          
              <!-- pagination start -->
              <div class="row" style="overflow:auto">
                <div class="col-12">
                  <template v-if="sale_count > 0">
                    <div class="overflow-auto text-center" style="display:inline-block">
                      <!-- Use text in props -->
                      <b-pagination
                        v-model="currentPage"
                        :total-rows="rows"
                        :per-page="perPage"
                        first-text="First"
                        prev-text="Prev"
                        next-text="Next"
                        last-text="Last"
                      >
                        <template v-slot:first-text><span class="text-success" v-on:click="getOrders(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getOrders(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getOrders(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getOrders(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getOrders(page)">{{ page }}</span>
                        </template>
                      </b-pagination>
                    </div>
                  </template>
                </div>
              </div>
              <!-- pagination end -->
            </div>

        </div>
        <!-- table end -->
        <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div>
    </div>

</template>

<script>
    export default {

        data() {
            return {
                search: {
                    app_from_date: "",
                    app_to_date: "",
                    approval_no: "",
                    customer_id: "",
                    inv_from_date: "",
                    inv_to_date: "",
                    invoice_no: "",
                    reference_no: "",
                    warehouse_id: "",
                    branch_id: "",
                },
                pagination: {
                    total: "",
                    next: "",
                    prev: "",
                    last_page: "",
                    current_page: "",
                    next_page_url:""
                },
                sales: [],
                customers: [],
                approvals: [],
                user_role: '',
                approval_products: [],
                sale_products: [],
                user_year: "",
                rows: "",
                perPage: "15",
                currentPage: 1,
                sale_count: 0,
                warehouses:[],
                branches: [],
                site_path: '',
                storage_path: '',
            };
        },

        created() {
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            if(this.user_role != "office_user" && this.user_role != "admin" && this.user_role != "system") {
                var url =  window.location.origin;
                window.location.replace(url);
            }   
            //this.getOrders(); 

        },

        mounted() {
            console.log('Component mounted.');
            $("#loading").hide(); 
            let app = this;

            //app.initWarehouses();
            app.initBranches();

            //app.initProducts();
            app.initCustomers();

            $("#app_from_date")
                .datetimepicker({
            icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                },
                format:"YYYY-MM-DD",
                minDate: app.user_year+"-01-01",
                maxDate: app.user_year+"-12-31",
            })
            .on("dp.show", function(e) {
                var y = new Date().getFullYear();
                app.search.app_from_date = moment().format('YYYY-MM-DD');
                if(app.user_year < y) { 
                  if(app.search.app_from_date == app.user_year+"-12-31" || app.search.app_from_date == '') {
                    app.search.app_from_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.app_from_date = formatedValue;
            });

            $("#app_to_date")
                .datetimepicker({
            icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                },
                format:"YYYY-MM-DD",
                minDate: app.user_year+"-01-01",
                maxDate: app.user_year+"-12-31",
            })
            .on("dp.show", function(e) {
               app.search.app_to_date = moment().format('YYYY-MM-DD');
                var y = new Date().getFullYear();
                if(app.user_year < y) { 
                  if(app.search.app_to_date == app.user_year+"-12-31" || app.search.app_to_date == '') {
                    app.search.app_to_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.app_to_date = formatedValue;
            });

            $("#inv_from_date")
                .datetimepicker({
            icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                },
                format:"YYYY-MM-DD",
                minDate: app.user_year+"-01-01",
                maxDate: app.user_year+"-12-31",
            })
            .on("dp.show", function(e) {
                app.search.inv_from_date = moment().format('YYYY-MM-DD');
                var y = new Date().getFullYear();
                if(app.user_year < y) { 
                  if(app.search.inv_from_date == app.user_year+"-12-31" || app.search.inv_from_date == '') {
                    app.search.inv_from_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.inv_from_date = formatedValue;
            });

            $("#inv_to_date")
                .datetimepicker({
            icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                },
                format:"YYYY-MM-DD",
                minDate: app.user_year+"-01-01",
                maxDate: app.user_year+"-12-31",
            })
            .on("dp.show", function(e) {
                app.search.inv_to_date = moment().format('YYYY-MM-DD');
                var y = new Date().getFullYear();
                if(app.user_year < y) { 
                  if(app.search.inv_to_date == app.user_year+"-12-31" || app.search.inv_to_date == '') {
                    app.search.inv_to_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.inv_to_date = formatedValue;
            });

            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.customer_id = data.id;
            });
            $("#order_status").select2();
            $("#order_status").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.order_status = data.id;
            });

            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.branch_id = data.id;
                app.warehouses = []; 
                if(data.id != "") {
                    axios.get("warehouses_bybranch/"+ data.id).then(({ data }) => (app.warehouses = data.data));
                } else {
                    axios.get("warehouses_bybranch/null").then(({ data }) => (app.warehouses = data.data));
                }
            });

            $("#warehouse_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.warehouse_id = data.id;
            });
        },

        methods: {

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },

            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            },

            initWarehouses() {
              axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
              $("#warehouse_id").select2();
            },

            initProducts() {
              axios.get("/productsByUserWarehouse").then(({ data }) => (this.products = data.data));
              $(".txt_product").select2();
            },

            getRevises(page = 1) {
                $("#loading").show();
                let app = this;

                var empty = true;
                $(":text, select").each(function() {
                   if($(this).val() !== "") {
                        empty = false;
                   }
                    
                });

                /** if(!empty) { **/
                    var search =
                    "&app_from_date=" +
                    app.search.app_from_date +
                    "&app_to_date=" +
                    app.search.app_to_date +
                     "&inv_from_date=" +
                    app.search.inv_from_date +
                    "&inv_to_date=" +
                    app.search.inv_to_date +
                    "&invoice_no=" +
                    app.search.invoice_no +
                    "&approval_no=" +
                    app.search.approval_no +
                    "&reference_no=" +
                    app.search.reference_no +
                    "&warehouse_id=" +
                    app.search.warehouse_id +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&customer_id=" +
                    app.search.customer_id;

                    /*axios.get("/order?" + search).then(({ data }) => (app.orders = data.data))
                    .then(function() {
                        $("#loading").hide();
                    });*/

                    axios.get("/sale_revise?page=" + page + search).then(function(response) {
                        $("#loading").hide();
                        let data = response.data.data;
                        app.sales = data;
                        app.sale_count = app.sales.data.length;
                        app.pagination.last_page = data.last_page;
                        app.pagination.next = data.next_page_url;
                        app.pagination.prev = data.prev_page_url;
                        app.pagination.total = data.total;
                        app.pagination.current_page = data.current_page;
                        app.pagination.next_page_url = data.next_page_url;


                        app.rows = data.total;
                        app.currentPage = data.current_page;
                        console.log(app.currentPage);
                    });

               /*** } else {
                    $("#loading").hide();
                    swal("Warning!", "Please fill at least one key to search!", "warning");
                    return false;
                } ***/
            },

           /** localTime($date)
            {
                var gmtDateTime = moment.utc($date, "YYYY-MM-DD")
                var local = gmtDateTime.local().format('YYYY-MM-DD');
                return local;
            }, **/

            getUOMname(uoms,uom_id)
            {
                var uom_name = '';
                $.each(uoms, function( key, suom ) {
                    if(uom_id == suom.pivot.uom_id) {
                        uom_name = suom.uom_name;
                    }
                });

                return uom_name;
            },

            getRelation(product,uoms,uom_id)
            {
                var relation_val = '';

                $.each(uoms, function( key, suom ) {
                    if(uom_id == suom.pivot.uom_id) {
                        relation_val = "1 "+suom.uom_name+" = "+ suom.pivot.relation +" "+ product.uom.uom_name;
                    }
                });

                return relation_val;
            },

            viewDetail(id)
            {
                $('.approval_view').hide();
                $('.revise_view').hide();
                $('#'+id).show();
                $('#revise_vw'+id).show();
            },

            getTotalAmount(prod)
            {
                var relation = '';

                $.each(prod.selling_uoms, function( key, suom ) {
                    if(prod.pivot.uom_id == suom.pivot.uom_id) {
                        relation = suom.pivot.relation;
                    }
                });

                var total = (prod.pivot.product_quantity - prod.pivot.approved_quantity) * relation * prod.pivot.price;

                return total;
            },

            getRelationQty(product,uoms,uom_id)
            {
                var relation_val = '1';

                $.each(uoms, function( key, suom ) {
                    if(uom_id == suom.pivot.uom_id) {
                        relation_val = suom.pivot.relation;
                    }
                });

                return relation_val;
            },

            getSubTotal(products)
            {
                var sub_total = 0;
                var relation = '';
                $.each(products, function( key, prod ) {
                    if(prod.pivot.approved_quantity == null) {
                        sub_total = sub_total + prod.pivot.total_amount;
                    } else {

                          $.each(prod.selling_uoms, function( key, suom ) {
                                if(prod.pivot.uom_id == suom.pivot.uom_id) {
                                    relation = suom.pivot.relation;
                                }
                            });

                            sub_total = sub_total + (prod.pivot.product_quantity - prod.pivot.approved_quantity) * relation * prod.pivot.price;
                    }
                });

                return Math.round(sub_total);
            },

            dateFormat(d) {
                return moment(d).format('YYYY-MM-DD');
            },

            numberWithCommas(x) {
                if(x != null) {
                  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                } else {
                  return x;
                }
            },

            localTime(utcTime) 
            {
                var utcDate = moment.utc(utcTime+'Z');
                // Apply a time zone
                var localTimezone = utcDate.tz('Asia/Rangoon');
               // return localTimezone.format('YYYY-MM-DD HH:mm:ss');
                return localTimezone.format('YYYY-MM-DD');
            },
        },

        
    }
</script>