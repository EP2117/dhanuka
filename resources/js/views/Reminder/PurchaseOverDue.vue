<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/reminder'">Reminder</a></li>
                <li class="breadcrumb-item active" aria-current="page">Purchase Over Due</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Purchase Over Due</h4>
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

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="invoice_no">Invoice No.</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="search.invoice_no">
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

                    <!--                    <div class="form-group col-md-4 col-lg-3">-->
                    <!--                        <label for="warehouse_id">Warehouse</label>-->
                    <!--                        <select id="warehouse_id" class="form-control"-->
                    <!--                                name="warehouse_id" v-model="search.warehouse_id" style="width:100%" required-->
                    <!--                        >-->
                    <!--                            <option value="">Select One</option>-->
                    <!--                            <option v-for="warehouse in warehouses" :value="warehouse.id"  >{{warehouse.warehouse_name}}</option>-->
                    <!--                        </select>-->
                    <!--                    </div>-->

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label >Supplier</label>
                        <select id="supplier_id" class="form-control mm-txt"
                                name="supplier_Id" v-model="search.supplier_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="sup in suppliers" :value="sup.id"  >{{sup.name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label >State</label>
                        <select id="state_id" class="form-control mm-txt"
                                 v-model="search.state_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="s in states" :value="s.id"  >{{s.state_name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label >Township</label>
                        <select id="township_id" class="form-control mm-txt"
                                 v-model="search.township_id" style="width:100%" required>
                            <option value="">Select One</option>
                            <option v-for="t in townships" :value="t.id"  >{{t.township_name}}</option>
                        </select>
                    </div>


<!--                    <div class="form-group col-md-4 col-lg-3 mm-txt">-->
<!--                        <label for="brand_id">Brand</label>-->
<!--                        <select id="brand_id" class="form-control mm-txt"-->
<!--                                name="brand_id" v-model="search.brand_id" style="width:100%" required-->
<!--                        >-->
<!--                            <option value="">Select One</option>-->
<!--                            <option v-for="brand in brands" :value="brand.id"  >{{brand.brand_name}}</option>-->
<!--                        </select>-->
<!--                    </div>-->

                    <div class="form-group col-md-3 col-lg-2">
                        <label for="currency_id">Currency</label>
                        <select class="form-control"
                                name="currency_id" id="currency_id" style="min-width:100px;" v-model="search.currency_id"
                        >
                            <option v-for="c in currency" :value="c.id" :data-sign="c.sign">{{c.name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" >&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getPurchaseOutstanding(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Purchase Over Due List</h6>
            </div>
            <div class="card-body">
                <!-- sort by -->
<!--                <div class="form-group float-left pr-2" v-if="payments.length > 0">-->
<!--                    <label for="sort_by">Sort By</label>-->
<!--                    <select id="sort_by" class="form-control"-->
<!--                            name="sort_by" v-model="search.sort_by" style="width:200px" @change="getDailyPurchases(1)">-->
<!--                        <option value="">Select One</option>-->
<!--                        <option value="invoice_no">Invoice No.</option>-->
<!--                    </select>-->
<!--                </div>-->
<!--                <div v-if="payments.length > 0">-->

<!--                    <div class="form-group float-left">-->
<!--                        <select id="order" class="form-control mt-2"-->
<!--                                name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getDailyPurchases(1)"-->
<!--                        >-->
<!--                            <option value="">Select One</option>-->
<!--                            <option value="ASC">Ascending</option>-->
<!--                            <option value="DESC">Descending</option>-->
<!--                        </select>-->
<!--                    </div>-->
<!--                    <div class="text-right form-group mt-4" >-->
<!--                        <div class="text-right mb-2" v-if="payments.length > 0">-->
<!--                            <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
                <!-- end sort by -->
                <!--<div class="text-right mb-2" v-if="sales.length > 0">
                    <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                </div>-->
                 <div class="text-right form-group mt-4" >
                         <div class="text-right mb-2" v-if="out_count > 0">
                              <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                          </div>
                  </div>
                <div class="table-responsive" v-if="out_count>0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                        <tr>
                            <!--<th class="text-center">No.</th>-->
                            <th class="text-center">Invoice No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Due Date</th>
                            <th class="text-center">Supplier Name</th>
                            <th class="text-center">Supplier Code</th>
                            <th class="text-center">Invoice Amount({{sign}})</th>
                            <th class="text-center">Paid Amount({{sign}})</th>
                            <th class="text-center">Balance Amount({{sign}})</th>
                            <!-- <th class="text-center"> Discount</th> -->
                        </tr>
                        </thead>
                        <tbody id="result" >
                            <template v-for="(po,k) in purchase_over_due">
                                <template v-for="(c,key) in po.out_list">
                                    <tr v-if="c.type=='paid'">
                                       <!-- <td class="text-center"></td> -->
                                <td class="text-center">{{c.invoice_no}}</td>
                                <td class="text-center">{{c.invoice_date != null ? dateFormat(c.invoice_date) : ''}}</td>
                                <td class="text-center">{{c.due_date != null ? dateFormat(c.due_date) : ''}}</td>
                                <!--                            <td class="text-center">{{c.vochur_no}}</td>-->
                                <td class="text-center">{{c.supplier.name}}</td>
                                <td class="text-center" style="right: 4px ">{{c.supplier.supplier_code}}</td>
                                <td class="text-center">{{numberWithCommas(c.total_amount)}} </td>
                                <td class="text-center">{{numberWithCommas(c.t_paid_amount)}} </td>
                                <td class="text-center">{{numberWithCommas(c.t_balance_amount)}} </td>
                                    </tr>
                                </template>
                                <tr class="">
                                    <td colspan="5" class="text-right mm-txt"><b>Total</b></td>
                                    <td class="text-center">{{numberWithCommas(po.total_inv_amt)}}</td>
                                    <td class="text-center">{{numberWithCommas(po.total_paid_amt)}}</td>
                                    <td class="text-center">{{numberWithCommas(po.total_bal_amt)}}</td>
                                </tr>
                                    <!-- </template> -->
                                
                            </template>
                            <tr>
                                <td></td>
                            </tr>
                             <tr class="">
                                    <td colspan="5" class="text-right mm-txt"><strong>Total Net</strong></td>
                                    <td class="text-center">{{numberWithCommas(net_inv_amt)}}</td>
                                    <td class="text-center">{{numberWithCommas(net_paid_amt)}}</td>
                                    <td class="text-center">{{numberWithCommas(net_bal_amt)}}</td>
                                </tr>
                        </tbody>
                    </table>
                </div>
                 <div v-else>
                    <h5 class="text-center m-5">No Record found!</h5>
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
                invoice_no: "",
                supplier_id: "",
                sort_by: '',
                branch_id: '',
                state_id:'',
                township_id:'',
                currency_id: 1,
            },
            purchase_over_due: [],
            suppliers:[],
            brands: [],
            out_count:0,
            warehouses:[],
            states:[],
            townships:[],
            user_year: '',
            user_role: '',
            branches: [],
            site_path: '',
            storage_path: '',
            net_bal_amt:'',
            net_inv_amt:'',
            net_paid_amt:'',
            user_role_id: '',
            currency: [],
            sign: 'MMK',
            isMMK: true,
        };
    },
    created() {
        this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
        // this.getPurchaseOutstanding();

        this.user_role_id = document.querySelector("meta[name='user-role-id']").getAttribute('content');
            
        if(this.user_role != "admin" && this.user_role != "system" && this.user_role_id != 19 && this.user_role_id != 15)
        {
            var url =  window.location.origin;
            window.location.replace(url);
        }
    },

    mounted() {
        $("#loading").hide();
        let app = this;
        app.initSuppliers();
        app.initBranches();
        app.initBrands();

        app.initCurrency();
        $("#currency_id").select2();
        $("#currency_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.search.currency_id = data.id;
            app.sign = e.target.options[e.target.options.selectedIndex].dataset.sign;
        });

        // app.initWarehouses();
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
                //app.search.from_date = moment().format('YYYY-MM-DD');
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
                //app.search.to_date = moment().format('YYYY-MM-DD');
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

        $("#supplier_id").on("select2:select", function(e) {
            var data = e.params.data
            app.search.supplier_id = data.id;
        });

        $("#warehouse_id").on("select2:select", function(e) {

            var data = e.params.data;
            app.search.warehouse_id = data.id;
        });
        // $("#brand_id").select2();
        // $("#brand_id").on("select2:select", function(e) {
        //     var data = e.params.data;
        //     app.search.brand_id = data.id;
        // });
        $("#branch_id").select2();
        $("#branch_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.search.branch_id = data.id;
        });

        $("#state_id").select2();
        $("#state_id").on("select2:select", function(e) {
            app.townships=[];
            var data = e.params.data;
            app.search.state_id = data.id;
            axios.get("/township_by_state/"+ data.id).then(({ data }) => (app.townships = data.data));

        });
        $("#township_id").select2();
        $("#township_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.search.township_id = data.id;

        });
        app.initBrands();
        app.initStates();
        app.initTownships();

    },
    methods: {
        initSuppliers() {
            axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
            $("#supplier_id").select2();
        },

        initCurrency() {
            axios.get("/all_currency").then(({ data }) => (this.currency = data.data));
            $("#currency_id").select2();

        },

        initBranches() {
            //axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
            axios.get("/branches").then(({ data }) => (this.branches = data.data));
            $("#branch_id").select2();
        },
        initStates() {
                axios.get("/state").then(({ data }) => (this.states = data.data));
                $("#state_id").select2();
        },
        initTownships() {
            if(this.search.state_id != "") {
                axios.get("/township_by_state/" + this.search.state_id).then(({ data }) => (this.townships = data.data));
                $("#township_id").select2();
            }
        },

        initWarehouses() {
            axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
            $("#warehouse_id").select2();
        },


        initBrands() {
            axios.get("/report_brands").then(({ data }) => (this.brands = data.data));
            $("#brand_id").select2();
        },
        getPurchaseOutstanding(page = 1) {

            // if(this.search.from_date == "") {
            //     swal("Warning!", "From Date must be added!", "warning")
            //     return false;
            // }
            $("#loading").show();
            let app = this;
            var search =
                "&from_date=" +
                app.search.from_date +
                "&to_date=" +
                app.search.to_date +
                "&invoice_no=" +
                app.search.invoice_no +
                "&currency_id=" +
                app.search.currency_id +
                "&sign=" +
                app.sign+
                "&branch_id=" +
                app.search.branch_id +
                "&supplier_id=" +
                app.search.supplier_id +
                "&brand_id=" +
                app.search.branch_id +
                "&state_id=" +
                app.search.state_id +
                "&township_id=" +
                app.search.township_id; 
            /***axios.get("/daily_sale_product_report?" + search).then(({ data }) => (app.sales = data.data))
             .then(function() {
                    console.log(app.sales);
                    $("#loading").hide();
                });***/
            axios.get("/reminder/purchase_over_due?" + search)
                .then(function(response) {
                    console.log(response);
                    app.purchase_over_due=response.data.purchase_over_due;
                    app.out_count=response.data.purchase_over_due.length;
                    app.net_bal_amt=response.data.net_balance_amt;
                    app.net_inv_amt=response.data.net_inv_amt;
                    app.net_paid_amt=response.data.net_paid_amt;
                    // app.net_bal_amt=response.data.purchase_over_due;
                    // console.log(response.data.html);
                    // $("#result").html(response.data.html);
                    // if(response.data.html != "") {
                    //     app.payments.push('1');
                    // } else { app.payments = []; }
                    $('#loading').hide();
                })
                .catch(error => {
                    console.log(error);
                });
        },

        exportExcel() {

            let app = this;
            if(this.search.from_date == "") {
                swal("Warning!", "From Date must be added!", "warning")
                return false;
            }

            var search =
                "&from_date=" +
                app.search.from_date +
                "&to_date=" +
                app.search.to_date +
                "&invoice_no=" +
                app.search.invoice_no +
                "&currency_id=" +
                app.search.currency_id +
                "&sign=" +
                app.sign+
                "&warehouse_id=" +
                app.search.warehouse_id +
                "&supplier_id=" +
                app.search.supplier_id +
                "&product_name=" +
                app.search.product_name +
                "&order=" +
                app.search.order +
                "&branch_id=" +
                app.search.branch_id +
                "&sort_by=" +
                app.search.sort_by;

            /*axios.get("/daily_sales_export?" + search)
            .then(function(response) {
              console.log(response);
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => loading.hide());*/

            var baseurl = window.location.origin;
            //window.open(baseurl+'/daily_sale_product_export?'+search);
            window.open(this.site_path+'/reminder/purchase_over_due_export?'+search);
        },

        dateFormat(d) {
            //return moment(d).format('YYYY-MM-DD');
            return moment(d).format('DD-MM-YYYY');
        },

        // getSellingUom(product,uom_id) {
        //     var key = product.selling_uoms.findIndex(x => x.pivot.uom_id == uom_id);
        //     if(key == -1) {
        //         return product.uom.uom_name;
        //     } else {
        //         return product.selling_uoms[key].uom_name;
        //     }
        // },
        numberWithCommas(x) {
            if(x != null) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            } else {
                return x;
            }
        },
    },

    // computed: {
    //     priceTotal: function(){
    //
    //         let sum = 0;
    //         let app = this;
    //         app.totalcount = 0;
    //         app.purchases.forEach(function(sale) {
    //
    //             if(app.search.product_name != '' || app.search.brand_id != '') {
    //                 sale.products.forEach(function(product) {
    //
    //                     if(app.user_role == 'Country Head') {
    //
    //                         if(((app.search.product_name != '' && product.product_name.toLowerCase().includes(app.search.product_name.toLowerCase()) && app.search.brand_id != '' && app.search.brand_id == product.brand_id) || (app.search.product_name == '' && app.search.brand_id != '' && app.search.brand_id == product.brand_id) || (app.search.brand_id == '' && app.search.product_name != '' && product.product_name.toLowerCase().includes(app.search.product_name.toLowerCase()))) && app.brands.findIndex(x => x.id == product.brand_id) > -1){
    //                             if(product.pivot.is_foc == '0') {
    //                                 sum += (parseFloat(product.pivot.total_amount));
    //                             }
    //                         }
    //                     } else {
    //                         if((app.search.product_name != '' && product.product_name.toLowerCase().includes(app.search.product_name.toLowerCase()) && app.search.brand_id != '' && app.search.brand_id == product.brand_id) || (app.search.product_name == '' && app.search.brand_id != '' && app.search.brand_id == product.brand_id) || (app.search.brand_id == '' && app.search.product_name != '' && product.product_name.toLowerCase().includes(app.search.product_name.toLowerCase()))) {
    //                             if(product.pivot.is_foc == '0') {
    //                                 sum += (parseFloat(product.pivot.total_amount));
    //                             }
    //                         }
    //                     }
    //
    //                 });
    //             } else {
    //
    //                 sale.products.forEach(function(product) {
    //                     if(app.user_role == 'Country Head') {
    //                         if(product.pivot.is_foc == '0' && app.brands.findIndex(x => x.id == product.brand_id) > -1) {
    //                             sum += (parseFloat(product.pivot.total_amount));
    //                         }
    //                     } else {
    //                         if(product.pivot.is_foc == '0') {
    //                             sum += (parseFloat(product.pivot.total_amount));
    //                         }
    //                     }
    //
    //                 });
    //             }
    //
    //         });
    //
    //
    //         return this.numberWithCommas(sum);
    //     },
    // },


}
</script>
