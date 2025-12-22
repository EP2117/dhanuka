<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Order Approval</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Sale Order Approval</h4>            
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="from_date">From Date</label>
                        <input type="text" class="form-control datetimepicker" id="from_date" name="from_date"
                        v-model="search.from_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="to_date">To Date</label>
                        <input type="text" class="form-control datetimepicker" id="to_date" name="to_date"
                        v-model="search.to_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="order_no">Order No.</label>
                        <input type="text" class="form-control" id="order_no" name="order_no" v-model="search.order_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="customer_id">Customer</label>
                        <select id="customer_id" class="form-control mm-txt"
                            name="customer_id" v-model="search.customer_id" style="width:100%" 
                        >
                            <option value="">Select One</option>
                            <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                        </select>
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

                     <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="order_status">Approval Status</label>
                        <select id="order_status" class="form-control mm-txt"
                            name="order_status" v-model="search.order_status" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option vale="Draft">Draft</option>
                            <option vale="Pending">Pending</option>
                            <option vale="Done">Done</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getOrders(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Order List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="order_count > 0">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Order No.</th>
                                <th class="text-center">Order Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Approval Status</th>
                                <th class="text-center">Approved Order</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Order No.</th>
                                <th class="text-center">Order Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Approval Status</th>
                                <th class="text-center">Approved Order</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <template v-for="order,index in orders.data">
                            <tr>
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td>{{order.order_no}}</td>
                                <td>{{order.order_date}}</td>
                                <td v-if="order.branch != null">{{order.branch.branch_name}}</td>
                                <td v-else></td>
                                <td class="mm-txt">{{order.customer.cus_name}}</td>
                                <td>{{order.total_amount}}</td>
                                <td v-if="order.order_status == 'Draft'" class="text-danger text-center">{{order.order_status}}</td>
                                <td v-else-if="order.order_status == 'Pending'" class="text-warning text-center">{{order.order_status}}</td>
                                <td v-else class="text-success text-center">{{order.order_status}}</td>
                                <td>
                                    <select :id="'approval'+order.id" class="form-control mm-txt approval_select"
                                        :name="'approval'+order.id" style="width:100%"
                                        @change="showApproval($event,order.id)"
                                    >
                                        <option value="">Select</option>
                                        <option v-for="approval in order.approvals" :value="approval.id"  >{{approval.approval_no}}</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <a @click="showOrder(order.id)" title="View/Approve" class="text-primary">
                                        <i class="fas fa-tasks"></i>
                                    </a>
                                                                      
                                </td>
                            </tr>
                            <tr style="border:none;display:none;" :id="order.id" class="order_view">
                                <td colspan="9" style="border:none;">
                                    <table class="table table-bordered" id="" style="background:#f7fafa;">
                                        <thead class="thead-grey">
                                            <tr>
                                                <th scope="col" class="bg_blue">Product</th>
                                                <th scope="col" class="bg_blue">Order QTY</th>
                                                <th scope="col" class="bg_blue">Pre Accepted QTY</th>
                                                <th scope="col" class="bg_blue">Accept QTY</th>
                                                <th scope="col" class="bg_blue">Selling Unit</th>
                                                <th scope="col" class="bg_blue">Relation</th>
                                                <th scope="col" class="bg_blue">FOC</th>
                                                <th scope="col" class="bg_blue">Unit Price</th>
                                                <th scope="col" class="bg_blue">Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="prod,index in order.products">
                                                <td>{{prod.product_name}}</td>
                                                <td>{{prod.pivot.product_quantity}}</td>
                                                <td>{{prod.pivot.approved_quantity}}</td>

                                                <td v-if="prod.pivot.approved_quantity == null">
                                                    <input type="text" class="num_txt form-control approve_product"
                                                     :data-pid ="prod.id"
                                                     :data-pivotid = "prod.pivot.id"
                                                     :data-uom = "prod.pivot.uom_id"
                                                     :data-isfoc = "prod.pivot.is_foc"
                                                     :data-pricevariant = "prod.pivot.price_variant"
                                                     :data-price = "prod.pivot.price"
                                                     :data-uomqty="getRelationQty(prod,prod.selling_uoms,prod.pivot.uom_id)"
                                                     :data-productuom="prod.uom.uom_name"
                                                     :id="'approve_qty'+order.id+index" :name="'approve_qty'+order.id+index" :value="prod.pivot.product_quantity" style="width:100px;"
                                                     @blur="checkQty(prod,order.id,order.order_status,index)">
                                                </td>
                                                <td v-else>
                                                    <input type="text" class="num_txt form-control approve_product" 
                                                     :data-pid ="prod.id"
                                                     :data-pivotid = "prod.pivot.id"
                                                     :data-uom = "prod.pivot.uom_id"
                                                     :data-isfoc = "prod.pivot.is_foc"
                                                     :data-pricevariant = "prod.pivot.price_variant"
                                                     :data-price = "prod.pivot.price"
                                                     :data-uomqty="getRelationQty(prod,prod.selling_uoms,prod.pivot.uom_id)"
                                                     :data-productuom="prod.uom.uom_name"
                                                     :id="'approve_qty'+order.id+index" :name="'approve_qty'+order.id+index" :value="prod.pivot.product_quantity - prod.pivot.approved_quantity" style="width:100px;" @blur="checkQty(prod,order.id,order.order_status,index)">
                                                </td>

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
                                                    <input type="checkbox" disabled />
                                                </td>
                                                <td v-else>
                                                    <input type="checkbox" checked disabled />
                                                </td>

                                                <td>
                                                    <input type="text" class="form-control" :id="'unit_price'+order.id+index" :name="'unit_price'+order.id+index" :value="prod.pivot.price" style="width:100px;" readonly>
                                                </td>

                                                <td v-if="prod.pivot.approved_quantity == null || order.order_status == 'Done'">
                                                    <input type="text" class="form-control total_amount" :id="'total_amount'+order.id+index" :name="'total_amount'+order.id+index" :value="prod.pivot.total_amount" style="width:100px;"  readonly>
                                                </td>
                                                <td v-else>
                                                     <input type="text" class="form-control total_amount" :id="'total_amount'+order.id+index" :name="'total_amount'+order.id+index" :value="getTotalAmount(prod)" style="width:100px;"  readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="8" class="text-right">Sub Total</td>
                                                <td v-if="order.order_status == 'Done'">
                                                    <input type="text" class="form-control" :id="'sub_total'+order.id" :name="'sub_total'+order.id" :value = "order.total_amount" style="width:100px;"  readonly>
                                                </td>
                                                <td v-else>
                                                    <input type="text" class="form-control" :id="'sub_total'+order.id" :name="'sub_total'+order.id" :value = "getSubTotal(order.products)" style="width:100px;"  readonly>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="text-right" v-if="order.order_status != 'Done' && user_role != 'office_user'">
                                        <button
                                          class="form-control btn btn-success font-weight-bold " style="width:120px;"
                                          @click="approve(order)"
                                        ><i class="fas fa-check"></i> &nbsp;&nbsp;Approve </button>
                                    </div>
                                </td>
                            </tr>
                            <tr style="border:none;display:none;" :id="'approval_vw'+order.id" class="approval_view">
                                <td colspan="9" :id="'approval_tbl'+order.id" style="border:none;">
                                    <table class="table table-bordered" id="" style="background:#f7fafa;">
                                        <thead class="thead-grey">
                                            <tr>
                                                <th scope="col" class="bg_blue">Approval No.</th>
                                                <th scope="col" class="bg_blue">Approval Date</th>
                                                <th scope="col" class="bg_blue">Product</th>
                                                <th scope="col" class="bg_blue">Accepted QTY</th>
                                                <th scope="col" class="bg_blue">Selling Unit</th>
                                                <th scope="col" class="bg_blue">Relation</th>
                                                <th scope="col" class="bg_blue">FOC</th>
                                                <th scope="col" class="bg_blue">Unit Price</th>
                                                <th scope="col" class="bg_blue">Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="app_prod,index in approval_products">
                                                <td>{{approval_no}}</td>
                                                <td>{{approval_date}}</td>
                                                <td>{{app_prod.product_name}}</td>
                                                <td>{{app_prod.pivot.approval_qty}}</td>
                                                <td v-if="app_prod.uom_id == app_prod.pivot.uom_id">
                                                    {{app_prod.uom.uom_name}}
                                                </td>
                                                <td v-else>
                                                    {{getUOMname(app_prod.selling_uoms,app_prod.pivot.uom_id)}}
                                                </td>

                                                <td v-if="app_prod.uom_id == app_prod.pivot.uom_id"></td>
                                                <td v-else>
                                                {{getRelation(app_prod,app_prod.selling_uoms,app_prod.pivot.uom_id)}}
                                                </td>

                                                <td v-if="app_prod.pivot.is_foc == 0">
                                                    <input type="checkbox" disabled />
                                                </td>
                                                <td v-else>
                                                    <input type="checkbox" checked disabled />
                                                </td>

                                                <td>
                                                    {{app_prod.pivot.price}}
                                                </td>
                                                <td>
                                                    {{app_prod.pivot.total_amount}}
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
                  <template v-if="order_count > 0">
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
                    from_date: "",
                    to_date: "",
                    order_no: "",
                    customer_id: "",
                    order_status: "",
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
                orders: [],
                customers: [],
                products: [],
                user_role: '',
                approval_no: '',
                approval_date: '',
                approval_products: [],
                user_year: "",
                rows: "",
                perPage: "15",
                currentPage: 1,
                order_count: 0,
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

            app.initProducts()
            app.initCustomers();
            app.initBranches();

            $("#from_date")
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
                if(app.user_year < y) { 
                  if(app.search.from_date == app.user_year+"-12-31" || app.search.from_date == '') {
                    app.search.from_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.from_date = formatedValue;
            });

            $("#to_date")
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
                if(app.user_year < y) { 
                  if(app.search.to_date == app.user_year+"-12-31" || app.search.to_date == '') {
                    app.search.to_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.to_date = formatedValue;
            });

            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.customer_id = data.id;
            });
            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.branch_id = data.id;
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

            initProducts() {
              axios.get("/productsByUserWarehouse").then(({ data }) => (this.products = data.data));
              $(".txt_product").select2();
            },

            getOrders(page = 1) {
                $("#loading").show();
                let app = this;

                var empty = true;
                $(":text, select").each(function() {
                   if($(this).val() !== "") {
                        empty = false;
                   }
                    
                });

                if(!empty) {
                    var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&order_no=" +
                    app.search.order_no +
                    "&order_status=" +
                    app.search.order_status +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&customer_id=" +
                    app.search.customer_id;

                    /*axios.get("/order?" + search).then(({ data }) => (app.orders = data.data))
                    .then(function() {
                        $("#loading").hide();
                    });*/

                    axios.get("/order?page=" + page + search).then(function(response) {
                        $("#loading").hide();
                        let data = response.data.data;
                        app.orders = data;
                        app.order_count = app.orders.data.length;
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

                } else {
                    $("#loading").hide();
                    swal("Warning!", "Please fill From Date!", "warning");
                    return false;
                }
            },

            showOrder(order_id)
            {
                $('.approval_view').hide();
                $('#approval'+order_id).val('');
                $('.approval_select').val('');
                if($('#'+order_id).is(":hidden")) {
                    var vw = 'off';
                } else {
                    var vw = 'on';
                }
                $('.order_view').hide();
                if(vw == 'off') {
                    $('#'+order_id).show();

                    $("input.approve_product:visible:first").focus();
                } else {
                    $('#'+order_id).hide();

                }
            },

            showApproval(event,order_id)
            {   $("#loading").show(); 
                var approval_id = event.target.value;
                
                if(approval_id != "") {
                    let app = this;
                    axios
                        .get("/order_approval/" + approval_id)
                        .then(function(response) {
                            app.approval_products = response.data.approval.products;
                            app.approval_no = response.data.approval.approval_no;
                            app.approval_date = app.localTime(response.data.approval.created_at);
                            console.log(app.approval_products);
                            $("#loading").hide(); 
                            //show approval view
                            $('.order_view').hide();
                            $('.approval_view').hide();
                            $("#approval_vw"+order_id).show();
                        });
                } else {
                    $("#loading").hide(); 
                    $('.order_view').hide();        
                    $('.approval_view').hide();
                }
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

            checkQty(prod,order,order_status,index)
            {
                
                if($('#approve_qty'+order+index).val() != "") {

                    //check that apprval quantity must not greater than order quantity
                    if(prod.pivot.approved_quantity == null) {
                        var app_qty = 0;
                    } else {
                        var app_qty = prod.pivot.approved_quantity;
                    }
                    var order_qty = parseInt(prod.pivot.product_quantity) - parseInt(app_qty);
                    if($('#approve_qty'+order+index).val() > order_qty) {
                         swal("Warning!", "Accept quantity is more than order quantity!", "warning");

                        $('#approve_qty'+order+index).focus(); $('#approve_qty'+order+index).val('');

                        return false;
                    }
                    //end 

                    var product_qty = 0;
                    var p_uom_val = '';
                    var p_uom_name = '';
                    var p_qty = '';
                    //calculate same products quantity in product list
                    $(".approve_product:visible").each(function() {
                        if(prod.id == $(this).attr('data-pid')) {
                            p_uom_val  = $(this).attr('data-uomqty');
                            p_uom_name = $(this).attr('data-productuom');
                            //p_qty =  $('#approve_qty'+order+index).val();
                            p_qty = $(this).val();
                            if(typeof p_uom_val !== "undefined" && typeof p_uom_name != "undefined" != "" && p_qty != "") {

                                product_qty = parseInt(product_qty) + (parseInt(p_qty) * parseInt(p_uom_val));
                            }
                        }
                    });

                    var uom_name = $('#approve_qty'+order+index).attr('data-productuom');

                    var key = this.products.findIndex(x => x.product_id == prod.id);
                    if(key != -1) {
                        var available_qty = parseInt(this.products[key].in_count) - parseInt(this.products[key].out_count);
                        if(product_qty > available_qty) {

                            swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");

                            $('#approve_qty'+order+index).focus(); $('#approve_qty'+order+index).val('');
                        }
                    } else {
                        swal("Warning!", "Not enough quantity! Availabel quantity is 0 "+uom_name+".", "warning");

                        $('#approve_qty'+order+index).focus(); $('#approve_qty'+order+index).val('');
                    }
                    console.log(product_qty);
                    console.log(available_qty);

                    //calculate product's total amount
                    if(prod.pivot.is_foc == 0) {
                        var prod_total_amt = 0;
                        if($('#approve_qty'+order+index).val() != '') {
                         prod_total_amt = parseInt($("#approve_qty"+order+index).val()) * parseInt($("#approve_qty"+order+index).attr('data-uomqty')) * parseFloat($("#unit_price"+order+index).val());
                        }

                        if(order_status != "Done") {
                            $('#total_amount'+order+index).val(Math.round(prod_total_amt));
                        }
                    }

                    //calculate sub total
                    var sub_total = 0;
                    var total_val = 0;
                    $(".total_amount:visible").each(function() {
                        if($('#approve_qty'+order+index).val() != '') {
                            if($(this).val() == ''){
                                total_val = 0;
                            } else {
                                total_val = $(this).val();
                            }
                            sub_total = parseInt(sub_total) + parseInt(total_val);
                        }
                    });

                    if(order_status != "Done") {
                        $("#sub_total"+order).val(sub_total);
                    }

                } else {
                    
                    if(order_status != "Done") {
                        $('#total_amount'+order+index).val(prod_total_amt);
                    }
                    //calculate sub total
                    var sub_total = 0;
                    var total_val = 0;
                    $(".total_amount:visible").each(function() {
                        if($('#approve_qty'+order+index).val() != '') {
                            if($(this).val() == ''){
                                total_val = 0;
                            } else {
                                total_val = $(this).val();
                            }
                            sub_total = parseInt(sub_total) + parseInt(total_val);
                        }
                    });

                   if(order_status != "Done") {
                        $("#sub_total"+order).val(sub_total);
                    }
                }
            },

            approve(order)
            {
                let app = this;
                $("#loading").show(); 

                var product_arr = [];
                var product_pivot_arr = [];
                var uom_arr = [];
                var is_foc_arr = [];
                var price_variant_arr = [];
                var approval_qty_arr = [];
                var total_amount_arr = [];
                var unit_price_arr = [];
                var approved_qty = 0;

                $(".approve_product:visible").each(function() {
                    if($(this).val() != "" && $(this).val() != 0) {
                        approved_qty = parseInt(approved_qty) + parseInt($(this).val());
                        product_arr.push($(this).attr('data-pid'));
                        product_pivot_arr.push($(this).attr('data-pivotid'));
                        uom_arr.push($(this).attr('data-uom'));
                        is_foc_arr.push($(this).attr('data-isfoc'));
                        price_variant_arr.push($(this).attr('data-pricevariant'));
                        unit_price_arr.push($(this).attr('data-price'));
                        approval_qty_arr.push($(this).val());


                        var p_uom_val  = $(this).attr('data-uomqty');
                        var p_qty =  $(this).val();
                        var unit_price = $(this).attr('data-price');

                        var total = parseInt(p_qty) * parseInt(p_uom_val) * parseFloat(unit_price);
                        total_amount_arr.push(Math.round(total));
                       
                    }
                });

                if(approved_qty == 0) {
                    swal("Warning!", "Please enter accept quantity!", "warning");
                    $("input.approve_product:visible:first").focus();
                    return false;
                }

                var data = {
                    order_id: order.id,
                    branch_id: order.branch_id,
                    order_no: order.order_no,
                    total_amount: $("#sub_total"+order.id).val(),
                    product_arr: product_arr,
                    product_pivot_arr: product_pivot_arr,
                    uom_arr: uom_arr,
                    unit_price_arr: unit_price_arr,
                    is_foc_arr: is_foc_arr,
                    price_variant_arr: price_variant_arr,
                    approval_qty_arr: approval_qty_arr,
                    total_amount_arr: total_amount_arr
                };

                axios.post('/order_approval', data)
                .then(response => {  
                console.log(response.data);      
               
                app.initProducts();
                app.getOrders(app.currentPage); 
                $("#loading").hide();  

                $('.order_view').hide();
                
                swal({
                    title: "Success!",
                    text: 'Approved Successfully!.',
                    icon: "success",
                    button: true
                });
                $("#loading").hide();
               });
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