<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delivery</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Delivery</h4>
            <!-- <router-link :to="'/sale/'+sale_type+'/new'" class="d-sm-inline-block btn btn-primary shadow-sm inventory" v-if="user_role == 'system' || user_role == 'office_user' || user_role == 'van_user'">
                <i class="fas fa-plus"></i> Add New Invoice
            </router-link> -->
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
                        <label for="invoice_no">Invoice No.</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="search.invoice_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="customer_id">Customer</label>
                        <select id="customer_id" class="form-control mm-txt"
                            name="customer_id" v-model="search.customer_id" style="width:100%" required
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

                    <!--<div class="form-group col-md-4 col-lg-3">
                        <label for="ref_no">Reference No.</label>
                        <input type="text" class="form-control" id="ref_no" name="ref_no" v-model="search.ref_no">
                    </div>-->

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="delivery_approve">Delivery Approve</label>
                        <select id="delivery_approve" class="form-control mm-txt"
                            name="delivery_approve" v-model="search.delivery_approve" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option value="true">Dagon</option>
                            <option value="false">Others</option>
                        </select>
                        <!--<input type="checkbox" class="form-control" id="delivery_approve" name="delivery_approve" v-model="search.delivery_approve" style="width:20px;height:20px;">-->
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getSales(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Delivery List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="sale_count > 0">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Invoice No.</th>
                                <th class="text-center">Reference No.</th>
                                <th class="text-center">Invoice Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Delivery Status</th>
                                <th class="text-center">Delivery Order</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Invoice No.</th>
                                <th class="text-center">Reference No.</th>
                                <th class="text-center">Invoice Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Delivery Status</th>
                                <th class="text-center">Delivery Order</th>
                                <th class="text-center"></th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <template v-for="sale,index in sales.data">
                            <tr>
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td>{{sale.invoice_no}}</td>
                                <td>{{sale.reference_no}}</td>
                                <td>{{sale.invoice_date}}</td>
                                <td v-if="sale.branch != null">{{sale.branch.branch_name}}</td>
                                <td v-else></td>
                                <td class="mm-txt">{{sale.customer.cus_name}}</td>
                                <td>{{sale.total_amount}}</td>
                                <td v-if="sale.delivery_status == 'Draft'" class="text-danger text-center">{{sale.delivery_status}}</td>
                                <td v-else-if="sale.delivery_status == 'Pending'" class="text-warning text-center">{{sale.delivery_status}}</td>
                                <td v-else class="text-success text-center">{{sale.delivery_status}}</td>
                                <td>
                                    <select :id="'delivery'+sale.id" class="form-control mm-txt delivery_select"
                                        :name="'delivery'+sale.id" style="width:100%"
                                        @change="showDelivery($event,sale.id)"
                                    >
                                        <option value="">Select</option>
                                        <option v-for="delivery in sale.deliveries" :value="delivery.id"  >{{delivery.delivery_no}}</option>
                                    </select>
                                </td>
                                <!--<td class="text-center">
                                    <a @click="showSale(sale.id,sale.delivery_status,sale.delivery_approve)" title="View/Deliver" class="text-primary">
                                        <i class="fas fa-tasks"></i>
                                    </a>&nbsp;

                                    <a title="Delete All Delivery" class="text-danger" @click="removeDelivery(sale.id)" v-if="(user_role == 'system' || user_role == 'admin') && sale.delivery_status != 'Draft'">
                                        <i class="fas fa-trash"></i>
                                    </a>&nbsp;                                                                     
                                </td> -->

                                <!--Kamlesh Start-->
                                <td class="text-center">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <a @click="showSale(sale.id,sale.delivery_status,sale.delivery_approve)" title="View/Deliver" class="text-primary">
                                                <i class="fas fa-tasks"></i>
                                            </a>&nbsp;
                                            </a>
                                            <a class="dropdown-item">
                                                <a title="Delete All Delivery" class="text-danger" @click="removeDelivery(sale.id)" v-if="(user_role == 'system' || user_role == 'admin') && sale.delivery_status != 'Draft'">
                                                    <i class="fas fa-trash"></i>
                                                </a>&nbsp;
                                            </a>
                                        </div>
                                    </div>

                                </td>
                                <!-- Kamlesh End-->

                            </tr>

                            <!-- sale details -->
                            <tr style="border:none;display:none;" :id="sale.id" class="sale_view">
                                <td colspan="10" style="border:none;">
                                    <table class="table table-bordered sale_tbl" id="" style="background:#f7fafa;">
                                        <thead class="thead-grey">
                                            <tr>
                                                <th colspan="7">
                                                    <div class="float-left p-2">
                                                    <label for="delivery_date">Delivery Date</label>
                                                    </div>
                                                    <div class="col-md-4 col-lg-3 float-left p-2">
                                                        <input type="text" class="form-control datetimepicker delivery_date" :id="'delivery_date'+sale.id" :name="'delivery_date'+sale.id" >
                                                    </div>
                                                    <div style="clear:both"></div>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th scope="col" class="bg_blue">Product</th>
                                                <!--<th scope="col" class="bg_blue">Delivery Date</th>-->
                                                <th scope="col" class="bg_blue">Invoice QTY</th>
                                                <th scope="col" class="bg_blue">Pre Delivered QTY</th>
                                                <th scope="col" class="bg_blue">Delivery QTY</th>
                                                <th scope="col" class="bg_blue">Selling Unit</th>
                                                <th scope="col" class="bg_blue">Relation</th>
                                                <th scope="col" class="bg_blue">FOC</th>
                                                <!--<th scope="col" class="bg_blue">Unit Price</th>
                                                <th scope="col" class="bg_blue">Total Amount</th>-->
                                            </tr>
                                            
                                        </thead>
                                        <tbody>
                                            <tr v-for="prod,index in sale.products">
                                                <td>{{prod.product_name}}</td>
                                                <!-- <td>
                                                    <input type="text" class="form-control datetimepicker delivery_date" :id="'delivery_date'+sale.id+index" :name="'delivery_qty'+sale.id+index" >
                                                </td> -->
                                                <td>{{prod.pivot.product_quantity}}</td>
                                                <td>{{prod.pivot.delivered_quantity}}</td>

                                                <td v-if="prod.pivot.delivered_quantity == null">
                                                    <input type="text" class="num_txt form-control delivery_product"
                                                     :data-pid ="prod.id"
                                                     :data-pivotid = "prod.pivot.id"
                                                     :data-uom = "prod.pivot.uom_id"
                                                     :data-isfoc = "prod.pivot.is_foc"
                                                     :data-pricevariant = "prod.pivot.price_variant"
                                                     :data-price = "prod.pivot.price"
                                                     :data-uomqty="getRelationQty(prod,prod.selling_uoms,prod.pivot.uom_id)"
                                                     :data-productuom="prod.uom.uom_name"
                                                     :id="'delivery_qty'+sale.id+index" :name="'delivery_qty'+sale.id+index" :value="prod.pivot.product_quantity" style="width:100px;"
                                                     @blur="checkQty(prod,sale.id,sale.delivery_status,index)" :readonly="isReadonly">
                                                </td>
                                                <td v-else>
                                                    <input type="text" class="num_txt form-control delivery_product" 
                                                     :data-pid ="prod.id"
                                                     :data-pivotid = "prod.pivot.id"
                                                     :data-uom = "prod.pivot.uom_id"
                                                     :data-isfoc = "prod.pivot.is_foc"
                                                     :data-pricevariant = "prod.pivot.price_variant"
                                                     :data-price = "prod.pivot.price"
                                                     :data-uomqty="getRelationQty(prod,prod.selling_uoms,prod.pivot.uom_id)"
                                                     :data-productuom="prod.uom.uom_name"
                                                     :id="'delivery_qty'+sale.id+index" :name="'delivery_qty'+sale.id+index" :value="prod.pivot.product_quantity - prod.pivot.delivered_quantity" style="width:100px;" @blur="checkQty(prod,sale.id,sale.delivery_status,index)" :readonly="isReadonly">
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
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="text-right" v-if="sale.delivery_status != 'Done' && (user_role == 'system' || (user_role == 'office_user' && sale.delivery_approve == 0) || (user_role == 'delivery' && sale.delivery_approve == 1))">
                                        <button
                                          class="form-control btn btn-success font-weight-bold " style="width:120px;"
                                          @click="deliver(sale)"
                                        ><i class="fas fa-check"></i> &nbsp;&nbsp;Deliver </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- end sale details -->
                            <!-- start delivery details -->
                            <tr style="border:none;display:none;" :id="'delivery_vw'+sale.id" class="delivery_view">
                                <td colspan="10" :id="'delivery_tbl'+sale.id" style="border:none;">
                                    <table class="table table-bordered" id="" style="background:#f7fafa;">
                                        <thead class="thead-grey">
                                            <tr>
                                                <th colspan="6">
                                                    <div class="float-left p-2">
                                                    <label for="delivery_date">Delivery Date</label>
                                                    </div>
                                                    <div class="col-md-4 col-lg-3 float-left p-2">
                                                        <input type="text" class="form-control delivery_date_vw" :id="'delivery_date_vw'+sale.id" :name="'delivery_date_vw'+sale.id" readonly>
                                                    </div>
                                                    <div style="clear:both"></div>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th scope="col" class="bg_blue">Delivery No.</th>
                                                <!--<th scope="col" class="bg_blue">Delivery Date</th>-->
                                                <th scope="col" class="bg_blue">Product</th>
                                                <th scope="col" class="bg_blue">Delivery QTY</th>
                                                <th scope="col" class="bg_blue">Selling Unit</th>
                                                <th scope="col" class="bg_blue">Relation</th>
                                                <th scope="col" class="bg_blue">FOC</th>
                                                <!--<th scope="col" class="bg_blue">Unit Price</th>
                                                <th scope="col" class="bg_blue">Total Amount</th>-->
                                            </tr>
                                            
                                        </thead>
                                        <tbody>
                                            <tr v-for="deli_prod,index in delivery_products">
                                                <td>{{delivery_no}}</td>
                                                <td>{{deli_prod.product_name}}</td>
                                                <td>{{deli_prod.pivot.delivery_qty}}</td>
                                                <td v-if="deli_prod.uom_id == deli_prod.pivot.uom_id">
                                                    {{deli_prod.uom.uom_name}}
                                                </td>
                                                <td v-else>
                                                    {{getUOMname(deli_prod.selling_uoms,deli_prod.pivot.uom_id)}}
                                                </td>

                                                <td v-if="deli_prod.uom_id == deli_prod.pivot.uom_id"></td>
                                                <td v-else>
                                                {{getRelation(deli_prod,deli_prod.selling_uoms,deli_prod.pivot.uom_id)}}
                                                </td>

                                                <td v-if="deli_prod.pivot.is_foc == 0">
                                                    <input type="checkbox" disabled />
                                                </td>
                                                <td v-else>
                                                    <input type="checkbox" checked disabled />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <!-- end delivery details -->
                            </template>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No record found!</h5>
                </div>

                <!-- detail view -->
                <!-- detail view end -->

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
                        <template v-slot:first-text><span class="text-success" v-on:click="getSales(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getSales(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getSales(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getSales(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getSales(page)">{{ page }}</span>
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
                    invoice_no: "",
                    customer_id: "",
                    ref_no: "",
                    sale_man_id: "",
                    office_sale_man_id: "",
                    delivery_approve: "",
                    invoice_type: "",
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
                rows: "",
                perPage: "15",
                currentPage: 1,
                sale_count: 0,
                sales: [],
                customers: [],
                sale_type: '',
                sale_mans: [],
                office_sale_mans: [],
                user_role: '',
                user_year: '',

                products: [],
                delivery_products: [],
                delivery_no: '',
                isReadonly: false,
                branches: [],
                site_path: '',
                storage_path: '',
            };
        },

        created() {
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            
            if(this.user_role == "office_order_user") {
                //var url =  window.location.origin;
                //window.location.replace(url);
            }
            var j;
            /*for(var i=0; i<200; i++) {
                j= i+1;
                localStorage.setItem('storedData'+j,'hello there1 hello there1 hello there1 hello there1 hello there1 hello there1');
            } */
            //localStorage.setItem('storedData2','hello there1 hello there1 hello there1');

           // localStorage.clear(); 
           // this.sale_type = this.$route.params.sale_type;
            this.getSales(); 
            //var ls = localStorage.getItem('storedData') != null ?  localStorage.getItem('storedData') : 'no ls'
            //console.log(localStorage.length);
        },

        mounted() {
            let app = this;
            //app.initSaleMan();
            //app.initOfficeSaleMan();
            app.initCustomers();
            //app.calcLStorageSize();

            app.initBranches();
            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.branch_id = data.id;
            });

            $("#sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.sale_man_id = data.id;
            });

            $("#office_sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.office_sale_man_id = data.id;
            });

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

        },

        methods: {

            initSaleMan() {
              axios.get("/sale_man").then(({ data }) => (this.sale_mans = data.data));
              $("#sale_man_id").select2();
            },

            initOfficeSaleMan() {
              axios.get("/office_sale_man").then(({ data }) => (this.office_sale_mans = data.data));
              $("#office_sale_man_id").select2();
            },

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },

            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            },

            calcLStorageSize() {
                var lsTotal = 0;
                var  xLen, x;
                var  k = 0;
                for (x in localStorage) {
                    if (!localStorage.hasOwnProperty(x)) {
                        continue;
                    }
                    k++;
                    xLen = ((localStorage[x].length + x.length) * 2);
                    lsTotal += xLen;
                    console.log(x.substr(0, 50) + " = " + (xLen / 1024).toFixed(2) + " KB")
                };
                console.log(k);
                console.log("Total = " + (lsTotal / 1024).toFixed(2) + " KB");
            },

            getSales(page = 1) {
                $("#loading").show();
                let app = this;

                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&invoice_no=" +
                    app.search.invoice_no +
                    "&customer_id=" +
                    app.search.customer_id +
                    "&ref_no=" +
                    app.search.ref_no +
                    "&office_sale_man_id=" +
                    app.search.office_sale_man_id +
                    "&delivery_approve=" +
                    app.search.delivery_approve +
                    "&invoice_type=" +
                    app.search.invoice_type +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&sale_man_id=" +
                    app.search.sale_man_id; 

                axios.get("/sale/type/1?page=" + page + search).then(function(response) {
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

                /*axios.get("/sale/type/" +app.sale_type + "?" + search ).then(({ data }) => (app.sales = data.data))
                .then(function() {
                    $("#loading").hide();
                });*/
            },

            dateFormat(d) {
                return moment(d).format('YYYY-MM-DD');
            },

            showSale(sale_id,status,approve)
            {
                let app = this;
                $('.delivery_view').hide();
               $('#delivery'+sale_id).val('');
               $('.delivery_select').val('');
                if($('#'+sale_id).is(":hidden")) {
                    var vw = 'off';
                } else {
                    var vw = 'on';
                }
                $('.sale_view').hide();
                if(vw == 'off') {
                    $('#'+sale_id).show();

                    $("input.delivery_product:visible:first").focus();
                } else {
                    $('#'+sale_id).hide();

                }

                if(status != 'Done' && (app.user_role == 'system' || (app.user_role == 'office_user' && approve == 0) || (app.user_role == 'delivery' && approve == 1)))
                {
                    app.isReadonly = false;
                } else {
                    app.isReadonly = true;
                }

                $(".delivery_date").datepicker({ 
                    format: 'yyyy-mm-dd',
                    startDate: app.user_year+'-01-01',
                    endDate: app.user_year+'-12-31',
                });

                $('.delivery_date').datepicker('setDate', 'today');

               /** $(".delivery_date")
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
                    widgetPositioning:{
                        horizontal: 'auto',
                        vertical: 'auto'
                    }
                }); **/
            },

            getUomName(product,uom_id) {
                var key = product.selling_uoms.findIndex(x => x.pivot.uom_id == uom_id);
                if(key == -1) {
                    return product.uom.uom_name;
                } else {
                    return product.selling_uoms[key].uom_name;
                }
            },

            getUomRelation(product,uom_id) {
                var key = product.selling_uoms.findIndex(x => x.pivot.uom_id == uom_id);
                return product.selling_uoms[key].pivot.relation;
            },

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
                return localTimezone.format('YYYY-MM-DD HH:mm:ss');
            },

            checkQty(prod,sale_id,delivery_status,index)
            {
                 
                if($('#deliver_qty'+sale_id+index).val() != "") {

                    //check that delivery quantity must not greater than sale quantity
                    if(prod.pivot.delivered_quantity == null) {
                        var deli_qty = 0;
                    } else {
                        var deli_qty = prod.pivot.delivered_quantity;
                    }
                    var sale_qty = parseInt(prod.pivot.product_quantity) - parseInt(deli_qty);
                    if($('#delivery_qty'+sale_id+index).val() > sale_qty) {
                         swal("Warning!", "Delivery quantity is more than invoice quantity!", "warning");

                        $('#delivery_qty'+sale_id+index).focus(); $('#delivery_qty'+sale_id+index).val('');

                        return false;
                    }
                }
            },

            showDelivery(event,sale_id)
            {   $("#loading").show(); 
                var delivery_id = event.target.value;
                if(delivery_id != "") {
                    let app = this;
                    axios
                        .get("/sale_delivery/" + delivery_id)
                        .then(function(response) {
                            app.delivery_products = response.data.delivery.products;
                            app.delivery_no = response.data.delivery.delivery_no;
                            var delivery_date = response.data.delivery.delivery_date;
                            $("#delivery_date_vw"+sale_id).val(delivery_date);
                            $("#loading").hide(); 
                            //show approval view
                            $('.sale_view').hide();
                            $('.delivery_view').hide();
                            $("#delivery_vw"+sale_id).show();
                        });
                } else {
                    $("#loading").hide(); 
                    $('.sale_view').hide();        
                    $('.delivery_view').hide();
                }
            },

            deliver(sale)
            {
                let app = this;
                $('#loading').show();

                var product_arr = [];
                var product_pivot_arr = [];
                var uom_arr = [];
                var is_foc_arr = [];
                var price_variant_arr = [];
                var delivery_qty_arr = [];
                var total_amount_arr = [];
                var unit_price_arr = [];
                var delivery_qty = 0;
                var delivery_date = $("#delivery_date"+sale.id).val();

                $(".delivery_product:visible").each(function() {
                    if($(this).val() != "" && $(this).val() != 0) {
                        delivery_qty = parseInt(delivery_qty) + parseInt($(this).val());
                        product_arr.push($(this).attr('data-pid'));
                        product_pivot_arr.push($(this).attr('data-pivotid'));
                        uom_arr.push($(this).attr('data-uom'));
                        is_foc_arr.push($(this).attr('data-isfoc'));
                        price_variant_arr.push($(this).attr('data-pricevariant'));
                        unit_price_arr.push($(this).attr('data-price'));
                        delivery_qty_arr.push($(this).val());


                       /***** var p_uom_val  = $(this).attr('data-uomqty');
                        var p_qty =  $(this).val();
                        var unit_price = $(this).attr('data-price');

                        var total = parseInt(p_qty) * parseInt(p_uom_val) * parseFloat(unit_price);
                        total_amount_arr.push(Math.round(total)); *****/
                       
                    }
                });

                if(delivery_qty == 0) {
                    swal("Warning!", "Please enter delivery quantity!", "warning");
                    $("input.delivery_product:visible:first").focus();
                    return false;
                }

                var data = {
                    sale_id: sale.id,
                    invoice_no: sale.invoice_no,
                    delivery_date: delivery_date,
                    product_arr: product_arr,
                    product_pivot_arr: product_pivot_arr,
                    uom_arr: uom_arr,
                    unit_price_arr: unit_price_arr,
                    is_foc_arr: is_foc_arr,
                    price_variant_arr: price_variant_arr,
                    delivery_qty_arr: delivery_qty_arr,
                    //total_amount_arr: total_amount_arr
                };

                axios.post('/sale_delivery', data)
                .then(response => {  
                console.log(response.data);      
               
                //app.initProducts();
                app.getSales(app.currentPage);  
                $('#loading').hide();

                $('.sale_view').hide();
                
                swal({
                    title: "Success!",
                    text: 'Delivered Successfully!.',
                    icon: "success",
                    button: true
                });
                $("#loading").hide();
               });
            },

            removeDelivery(id) {
                let app = this;
                $('#loading').show();
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/sale_delivery/" + id).then(function() {
                            app.getSales();
                            $('#loading').hide();
                        });
                        swal("Success! Delivery has been deleted!", {
                            icon: "success"
                        });   
                    } else {
                      //
                    }
                });
            },

        },
    }
</script>