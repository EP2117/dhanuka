<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Stock Ledger</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Stock Ledger</h4>
           <!-- <router-link to="/inventory/transfer/new" class="d-sm-inline-block btn btn-primary shadow-sm inventory">
                <i class="fas fa-plus"></i> Add New Transfer
            </router-link>-->
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

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="branch_id">Branch</label>
                        <select id="branch_id" class="form-control mm-txt"
                            name="branch_id" v-model="search.branch_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="branch in branches" :value="branch.id"  :key="branch.id">{{branch.branch_name}}</option>
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

                     <div class="form-group col-md-4 col-lg-3">
                        <label for="product_name">Product Names</label>
                        <select  multiple id="product_name" class="form-control mm-txt"
                                name="product_name[]" style="width:100%" 
                        >
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="brand_id">Brand</label>
                        <select id="brand_id" class="form-control mm-txt"
                            name="brand_id" v-model="search.brand_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="brand in brands" :value="brand.id"  >{{brand.brand_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getStockLedger(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Product List</h6>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                <!-- Kamlesh Start -->
                <!-- sort by -->
               <!-- <div class="form-group float-left pr-2">
                    <label for="sort_by">Sort By</label>
                    <select id="sort_by" class="form-control"
                        name="sort_by" v-model="search.sort_by" style="width:200px" @change="getStockLedger(1)"
                    >
                        <option value="">Select One</option>
                        <option value="name">Product Name</option>
                        <option value="code">Product Code</option>
                        <option value="brand">Brand</option>
                    </select>
                </div>-->
                <div>
                <!--<div class="form-group float-left">
                    <select id="order" class="form-control mt-2"
                        name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getStockLedger(1)"
                    >
                        <option value="">Select One</option>
                        <option value="ASC">Ascending</option>
                        <option value="DESC">Descending</option>
                    </select>
                </div>-->
               
                <div class="text-right form-group mt-4" >
                    <div class="mb-2" style="display:inline-block" v-if="products.length>0">
                        <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                    </div>
                    <!--<div class="mb-2 pl-2" style="display:inline-block;">
                        <button class="btn btn-primary btn-icon btn-sm" @click="exportPdf()"><i class="fas fa-file-pdf"></i> &nbsp;Export to PDF</button>
                    </div>-->
                </div>
               
                </div>
               
 <!-- Kamlesh End -->

                    <!--<table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">-->
                        <table class="" border="1" cellspacing="10" cellpadding="10" style="color:#000000" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <!--<th class="text-center">No.</th>-->
                                <th >Date</th>
                                <th>Brand</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Invoice No.</th>
                                <th>Description</th>
                                <th>Opening</th>
                                <th>In</th>
                                <th>Stock <br />Receive</th>
                                <th>Stock <br />Transfer</th>
                                <th>Direct Sale</th>
                                <th>Sale Return</th>
                                <th>Adjustment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(p ,index) in products">
                                <template v-if="index==0 || (products.length > 1 && (products[index-1].product_id != p.product_id && products[index-1].transition_date != p.transition_date) || (products[index-1].product_id == p.product_id && products[index-1].transition_date != p.transition_date) ||  (products[index-1].product_id != p.product_id && products[index-1].transition_date == p.transition_date))">

                                <tr >
                                    <td>{{dateFormat(p.transition_date)}}</td>
                                    <td>{{p.brand_name}}</td>
                                    <td>{{p.product_code}}</td>
                                    <td>{{p.product_name}}</td>
                                    <td></td>

                                    <td></td>

                                    <td v-if="index==0">{{p.product_opening = getOpening(p.product_id)}}</td>

                                    <td v-else>{{getOpeningByDate(p.product_id, p.transition_date)}} </td>

                                    <!-- <td v-if="products[index-1].product_id != p.product_id && products[index-1].transition_date != p.transition_date">{{getClosing(p.product_id,products[index-1].transition_date)}}</td>

                                    <td v-if="products[index-1].product_id == p.product_id && products[index-1].transition_date != p.transition_date">{{getClosing(p.product_id,products[index-1].transition_date)}}</td>

                                    <td v-if="products[index-1].product_id != p.product_id && products[index-1].transition_date == p.transition_date">{{getClosing(p.product_id,products[index-1].transition_date)}}</td>-->

                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <!--<td>{{dateFormat(p.transition_date)}}</td>--> 
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td v-if="p.transition_return_id == null">{{p.invoice_no}}</td>
                                    <td v-else>{{p.return_invoice_no}}</td>

                                    <td v-if="p.transition_sale_id != null"> 
                                        <!--Sale Invoice--> {{p.sale_customer_name}} - {{p.invoice_no}}
                                    </td>
                                    <td v-else-if="p.transition_purchase_id != null">
                                        <!-- Purchase Invoice --> {{p.purchase_reference_no}} - {{p.invoice_no}}
                                     </td>
                                    <td v-else-if="p.transition_entry_id != null"> 
                                        <!--Main Warehouse Entry --> {{p.entry_reference_no}} - {{p.invoice_no}}
                                    </td>
                                    <td v-else-if="p.transition_adjustment_id != null"> 
                                        <!--Inventory Adjustment--> {{p.adjustment_reference_no}} - {{p.invoice_no}}
                                    </td>
                                    <td v-else-if="p.transition_transfer_id != null && p.transition_type == 'out'"> Transfer</td>
                                    <td v-else-if="p.transition_transfer_id != null && p.transition_type == 'in'"> Receive from Tranfer</td>
                                    <td v-else-if="p.transition_return_id != null"> 
                                        <!--Sale Return-->{{p.return_customer_name}} - {{p.return_invoice_no}}
                                    </td>

                                    <td></td>

                                    <td>{{p.transition_entry_id != null || p.transition_purchase_id != null ? p.product_quantity : ''}}</td>
                                    <td>{{p.transition_transfer_id != null && p.transition_type == 'in' ? p.product_quantity : ''}}</td>
                                    <td>{{p.transition_transfer_id != null && p.transition_type == 'out' ? p.product_quantity : ''}}</td>
                                    <td>{{p.transition_sale_id != null && p.transition_return_id == null ? p.product_quantity : ''}}</td>
                                    <td>{{p.transition_return_id != null ? p.product_quantity : ''}}</td>
                                    <td>{{p.transition_adjustment_id != null ? p.product_quantity : ''}}</td>
                                </tr>
                                </template>
                                <template v-else>
                                <tr>
                                    <!--<td>{{dateFormat(p.transition_date)}}</td>-->
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td v-if="p.transition_return_id == null">{{p.invoice_no}}</td>
                                    <td v-else>{{p.return_invoice_no}}</td>

                                    <td v-if="p.transition_sale_id != null"> 
                                        <!--Sale Invoice--> {{p.sale_customer_name}} - {{p.invoice_no}}
                                    </td>
                                    <td v-else-if="p.transition_purchase_id != null">
                                        <!-- Purchase Invoice --> {{p.purchase_reference_no}} - {{p.invoice_no}}
                                     </td>
                                    <td v-else-if="p.transition_entry_id != null"> 
                                        <!--Main Warehouse Entry --> {{p.entry_reference_no}} - {{p.invoice_no}}
                                    </td>
                                    <td v-else-if="p.transition_adjustment_id != null"> 
                                        <!--Inventory Adjustment--> {{p.adjustment_reference_no}} - {{p.invoice_no}}
                                    </td>
                                    <td v-else-if="p.transition_transfer_id != null && p.transition_type == 'out'"> Transfer</td>
                                    <td v-else-if="p.transition_transfer_id != null && p.transition_type == 'in'"> Receive from Tranfer</td>
                                    <td v-else-if="p.transition_return_id != null"> 
                                        <!--Sale Return-->{{p.return_customer_name}} - {{p.return_invoice_no}}
                                    </td>

                                    <td></td>

                                    <td>{{p.transition_entry_id != null || p.transition_purchase_id != null ? p.product_quantity : ''}}</td>
                                    <td>{{p.transition_transfer_id != null && p.transition_type == 'in' ? p.product_quantity : ''}}</td>
                                    <td>{{p.transition_transfer_id != null && p.transition_type == 'out' ? p.product_quantity : ''}}</td>
                                    <td>{{p.transition_sale_id != null && p.transition_return_id == null ? p.product_quantity : ''}}</td>
                                    <td>{{p.transition_return_id != null ? p.product_quantity : ''}}</td>
                                    <td>{{p.transition_adjustment_id != null ? p.product_quantity : ''}}</td>
                                </tr>
                                </template>
                                <template v-if="products.length==1 || products.length - 1 == index">
                                    <tr>
                                        <td style="text-align:right;" colspan="6"><b>Closing Balances</b></td>
                                        <td>{{getClosing(p.product_id,p.transition_date) }} </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </template>
                                <template v-else-if="(products[index+1].product_id != p.product_id && products[index+1].transition_date != p.transition_date) || (products[index+1].product_id == p.product_id && products[index+1].transition_date != p.transition_date) ||  (products[index+1].product_id != p.product_id && products[index+1].transition_date == p.transition_date)">
                                    <tr>
                                        <td style="text-align:right" colspan="6"><b>Closing Balances</b></td>
                                        <td>{{getClosing(p.product_id,p.transition_date) }} </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </template>
                                
                            </template>
                        </tbody>
                    </table>
                </div>
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
                    warehouse_id: "",
                    product_name: [],
                    brand_id: "",
                    branch_id: "",
                    sort_by: "",
                    order:"",
                },
                products: [],
                op_products: [],
                order_products: [],
                brands: [],
                def_branch_id:'',
                warehouses:[],
                user_year: '',
                user_role: '',
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
        },

        mounted() {
            $("#loading").hide();
            let app = this;

            app.initBrands();
            app.initBranches();
            app.initWarehouses();

            $("#product_name").select2({
            allowClear: true,
            placeholder: "Select One",
            minimumInputLength: 3,
            ajax: {
                url: app.site_path+'/search_products',
            data: function (params) {
                var query = {
                term: params.term,           
                }

                return query;
            },
            processResults: function (data) {

                return {
                results: $.map(data, function (obj) {
                    return { 
                        'id': obj.id, 
                        'text': obj.product_name,
                        'datatsp': obj.category_id,
                    };
                })
                };
            }
            
            }
        });
        $("#product_name").on("select2:select", function(e) {
            var data = e.params.data;
            
            //app.search.product_name = data.id;
        });
         $("#product_name").on("select2:unselecting", function(e) {
                //app.search.product_name = '';
            });

            // console.log(this.search.branch_id);
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
                app.search.from_date = moment().format('YYYY-MM-DD');
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
                app.search.to_date = moment().format('YYYY-MM-DD');
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
            // $(document).ready(function(e){
            //     var branch_id=$('#branch_id').val();
            //     if(branch_id) {
            //         axios.get("warehouses_bybranch/"+ branch_id).then(
            //             // ({ data }) => (app.warehouses = data.data));
            //             (data)=>{
            //                 this.warehouses=data.data.data;
            //                 const id=this.warehouses.map(a=>a.id);
            //                 console.log('id is '+id[0]);
            //                 app.search.warehouse_id=id[0];
            //                 // console.log(app.search.warehouse_id);
            //                 $('#warehouse_id').val(id[0]).trigger('change');
            //             }
            //         );
            //     } else {
            //         alert('a');
            //         axios.get("warehouses_bybranch/null").then(({ data }) => (app.warehouses = data.data));
            //     }
            // });
            // $(document).on("select2:select",'#branch_id', function(e) {
            //     var data=e.params.data;
            //     this.search.branch_id=data.id;
            //     app.warehouses = [];
                // if(data.id != "") {
                //     axios.get("warehouses_bybranch/"+ data.id).then(({ data }) => (app.warehouses = data.data));
                // } else {
                //     axios.get("warehouses_bybranch/null").then(({ data }) => (app.warehouses = data.data));
                // }
            // });

            $("#warehouse_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.warehouse_id = data.id;
            });

            $("#brand_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.brand_id = data.id;
            });
        },

        methods: {


            initBranches() {
            let app = this;
              axios.get("/branches_byuser").then(
                  (data)=>{
                    app.branches = data.data.data;
                     const branch=app.branches.find(a=>a.branch_name=='Main Branch');
                      app.search.branch_id=branch.id;
                      app.def_branch_id=branch.id;
                      $('branch_id').val(branch.id).trigger('change');
                      $("#branch_id").select2();
                  });
            },
            initWarehouses() {
                let app = this;
                // console.log(this.search.branch_id);
              axios.get("/warehouses").then(
                //   ({ data }) => (this.warehouses = data.data)
                (data)=>{
                    app.warehouses=data.data.data;
                     const id=app.warehouses.find(a=>a.branch.branch_name=='Main Branch');
                     app.search.warehouse_id=id.id;
                      $('#warehouse_id').val(id.id).trigger('change');
                      $("#warehouse_id").select2();
                }
                  );
            //   $("#warehouse_id").select2();
            },
            initBrands() {
              axios.get("/report_brands").then(({ data }) => (this.brands = data.data));
              $("#brand_id").select2();
            },

            getSO(product_id) {
                let app = this;
                //var d=moment("28-02-1999","YYYY-MM-DD");
                //d.isSame("28-02-1999");
                var qty = 0;
                /*$.each(app.order_products, function(index, value) {
                    if(product_id == value.product_id) {
                        qty = value.order_qty;
                    }
                });*/
                var key = app.order_products.findIndex(x => x.product_id == product_id);
                if(key != -1) {
                    qty = app.order_products[key].order_qty;
                }

                return qty;
            },

            getStockLedger(page = 1) {

                if(this.search.from_date == "" || this.search.to_date == "" || this.search.branch_id == "") {
                    swal("Warning!", "From Date, To Date and Branch must be added!", "warning")
                    return false;
                } else {
                    var fromDate = this.search.from_date, toDate = this.search.to_date, from, to, duration;
                  
                    from = moment(fromDate, 'YYYY-MM-DD'); // format in which you have the date
                    to = moment(toDate, 'YYYY-MM-DD');     // format in which you have the date
                  
                    /* using diff */
                    duration = to.diff(from, 'days');
                    
                }
                if(duration > 31) {
                    swal("Warning!", "Maximum 31 days! ", "warning")
                    return false;
                } else {
                console.log($("#product_name").val());
                //return false;

                $("#loading").show();
                let app = this;
                app.search.product_name = $("#product_name").val();
                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&warehouse_id=" +
                    app.search.warehouse_id +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&brand_id=" +
                    app.search.brand_id +
                    "&product_name=" +
                    app.search.product_name+
                    "&order=" +
                    app.search.order +
                    "&sort_by=" +
                    app.search.sort_by; //Kamlesh

                    console.log(search);


                axios.get("/report/stock_ledger?" + search)
                .then(function(response) {
                    app.products = response.data.data;
                    app.op_products = response.data.op_data;
                    console.log(app.op_products);
                    $("#loading").hide();
                });
                }
            },

            getOpening(id) {
                let app = this;
                var count = 0;
                var in_count = 0;
                var out_count = 0;

                var key = app.op_products.findIndex(x => x.product_id == id);
                if(key != -1) {
                    in_count = in_count + parseFloat(app.op_products[key].in_qty);
                    out_count = out_count + parseFloat(app.op_products[key].out_qty);
                }else{
                    return 0;
                }
                /***** for (var key in app.op_products) {
                    if(app.op_products[key].product_id == id) {
                        in_count = in_count +parseFloat(app.op_products[key].in_qty);
                        out_count = out_count + parseFloat(app.op_products[key].out_qty);
                    }
                }*****/

                count = in_count - out_count;
                // alert(count);

                return count;

            },

            getClosing(id,t_date) {

                let app = this;
                var op = app.getOpening(id);
                var duplicateIds = [id];
                var products = app.products.filter(obj => duplicateIds.includes(obj.product_id));

                var closing = 0;
                var entry = 0; var sale_return =0;  var purchase = 0;  var receive = 0;
                var sale = 0; var adjustment =0; var transfer=0;

                products.forEach((p)=> {
                   var p_date = app.dateFormat(p.transition_date);
                   t_date = app.dateFormat(t_date);
                   if(p.transition_entry_id != null && p_date <= t_date) {
                        entry += parseInt(p.product_quantity);
                   }
                   else if(p.transition_return_id != null && p_date <= t_date) {
                        sale_return += parseInt(p.product_quantity);
                   }
                   else if(p.transition_purchase_id != null && p_date <= t_date) {
                        purchase += parseInt(p.product_quantity);
                   }
                   else if(p.transition_transfer_id != null && p.transition_type == 'out' && p_date <= t_date) {
                        transfer += parseInt(p.product_quantity);
                   }
                   else if(p.transition_transfer_id != null && p.transition_type == 'in' && p_date <= t_date) {
                        receive += parseInt(p.product_quantity);
                   }
                   else if(p.transition_sale_id != null && p.transition_return_id == null&& p_date <= t_date) {
                        sale += parseInt(p.product_quantity);
                   }
                   else if(p.transition_adjustment_id != null && p_date <= t_date) {
                        adjustment += parseInt(p.product_quantity);
                   } else {}
                });

                //console.log (op + ', E ' + entry + ', P ' + purchase+ ', R ' + sale_return+ ', Rcv ' + receive+ ', Adj ' + adjustment+ ', S ' + sale+ ', T ' + transfer)

                closing = (parseInt(op) + entry + purchase + sale_return + receive + adjustment)-(sale + transfer);

                return closing;
            },

            getOpeningByDate(id,t_date) {

                let app = this;
                var op = app.getOpening(id);
                var duplicateIds = [id];
                var products = app.products.filter(obj => duplicateIds.includes(obj.product_id));

                var closing = 0;
                var entry = 0; var sale_return =0;  var purchase = 0;  var receive = 0;
                var sale = 0; var adjustment =0; var transfer=0;

                products.forEach((p)=> {
                   var p_date = app.dateFormat(p.transition_date);
                   t_date = app.dateFormat(t_date);
                   if(p.transition_entry_id != null && p_date < t_date) {
                        entry += parseInt(p.product_quantity);
                   }
                   else if(p.transition_return_id != null && p_date < t_date) {
                        sale_return += parseInt(p.product_quantity);
                   }
                   else if(p.transition_purchase_id != null && p_date < t_date) {
                        purchase += parseInt(p.product_quantity);
                   }
                   else if(p.transition_transfer_id != null && p.transition_type == 'out' && p_date < t_date) {
                        transfer += parseInt(p.product_quantity);
                   }
                   else if(p.transition_transfer_id != null && p.transition_type == 'in' && p_date < t_date) {
                        receive += parseInt(p.product_quantity);
                   }
                   else if(p.transition_sale_id != null && p.transition_return_id == null && p_date < t_date) {
                        sale += parseInt(p.product_quantity);
                   }
                   else if(p.transition_adjustment_id != null && p_date < t_date) {
                        adjustment += parseInt(p.product_quantity);
                   } else {}
                });

                //console.log (op + ', E ' + entry + ', P ' + purchase+ ', R ' + sale_return+ ', Rcv ' + receive+ ', Adj ' + adjustment+ ', S ' + sale+ ', T ' + transfer)

                closing = (parseInt(op) + entry + purchase + sale_return + receive + adjustment)-(sale + transfer);

                return closing;
            },

            exportExcel() {

                if(this.search.from_date == "" || this.search.to_date == "" || this.search.branch_id == "") {
                    swal("Warning!", "From Date, To Date and Branch must be added!", "warning")
                    return false;
                } else {
                    var fromDate = this.search.from_date, toDate = this.search.to_date, from, to, duration;
                  
                    from = moment(fromDate, 'YYYY-MM-DD'); // format in which you have the date
                    to = moment(toDate, 'YYYY-MM-DD');     // format in which you have the date
                  
                    /* using diff */
                    duration = to.diff(from, 'days');
                    
                }
                if(duration > 31) {
                    swal("Warning!", "Maximum 31 days! ", "warning")
                    return false;
                } else {
                console.log($("#product_name").val());
                let app = this;
                app.search.product_name = $("#product_name").val();
                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&warehouse_id=" +
                    app.search.warehouse_id +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&brand_id=" +
                    app.search.brand_id +
                    "&product_name=" +
                    app.search.product_name+
                    "&order=" +
                    app.search.order +
                    "&sort_by=" +
                    app.search.sort_by;

                    console.log(search);

                var baseurl = window.location.origin;
                //window.open(baseurl+'/inventory_export?'+search);
                window.open(this.site_path+'/report/stock_ledger_export?'+search);
                }
            },

            //Kamlesh Start
         exportPdf() {   

                let app = this;
                if(this.search.from_date == "") {                  
                    swal("Warning!", "From Date must be added!", "warning")
                    return false;
                } 
                // else {
                //     $("#loading").show();
                // }

                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&warehouse_id=" +
                    app.search.warehouse_id +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&brand_id=" +
                    app.search.brand_id +
                    "&product_name=" +
                    app.search.product_name+
                    "&order=" +
                    app.search.order +
                    "&sort_by=" +
                    app.search.sort_by;
                    

                axios.get("/inventory_export_pdf?" + search, {responseType: 'blob'}).then(response => {
                    $('#loading').hide();
                    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
                    window.open(url);

                  })
                  .catch(error => {
                    console.log(error);
                  });

            },
            // Kamlesh End

            dateFormat(d) {
                return moment(d).format('YYYY-MM-DD');
            },

            getSellingUom(product,uom_id) {
                var key = product.selling_uoms.findIndex(x => x.pivot.uom_id == uom_id);
                if(key == -1) {
                    return product.uom.uom_name;
                } else {
                    return product.selling_uoms[key].uom_name;
                }
            },

            numberWithCommas(x) {
                if(x != null) {
                  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                } else {
                  return x;
                }
            },
        },


    }
</script>
