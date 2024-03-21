<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/remainder'">Remainder</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delivery Pending</li>
            </ol>
        </nav>
        <!-- Page Heading -->
     

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                     <div class="form-group col-md-4 col-lg-3">
                        <label for="invoice_no"> Invoice No</label>
                        <input type="text" class="form-control" id="sale_invoice"  v-model="search.invoice_no">
                    </div>
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
                        <label for="product_name"> Product Name</label>
                        <input type="text" class="form-control" id="product_name"  v-model="search.product_name">
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="product_code"> Product Code</label>
                        <input type="text" class="form-control" id="product_code"  v-model="search.product_code">
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
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getDeliveryPending(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sale Order Pending</h6>
            </div>
            <div class="card-body">
            <div class="text-right mb-2" v-if="delivery_pending.length>0" >
                <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-print"></i> &nbsp;Export Excel</button>
            </div>
              <div class="table-responsive" v-if="delivery_pending.length > 0">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Sale Order Invoice </th>
                                <th class="text-center">Sale Order Date</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Product Code</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Invoice Qty</th>
                                <th class="text-center">Delivery Qty</th>
                                <th class="text-center">Balance</th>
                                <!-- <th class="text-center"> Total </th>  -->
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Sale Order</th>
                                <th class="text-center">Sale Order Date</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Product Code</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Invoice Qty</th>
                                <th class="text-center">Delivery Qty</th>
                                <th class="text-center">Balance</th>
                                <!-- <th class="text-center"> Total </th>  -->
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="cn,index in delivery_pending">
                                <td class="textalign">{{index+1}}</td>
                                <td class="textalign">{{cn.invoice_no}}</td>
                                <td class="textalign">{{dateFormat(cn.invoice_date)}}</td>
                                <td class="textalign">{{cn.cus_name}}</td>
                                <td class="textalign">{{cn.product_code}}</td>
                                <td class="textalign">{{cn.product_name}}</td>
                                 <td class="textalign">{{parseInt(cn.si_qty)}}</td>
                                <td class="textalign">{{parseInt(cn.del_qty)}}</td>
                                <td class="textalign">{{cn.si_qty-parseInt(cn.del_qty)}}</td> 
                                <!-- <td class="textalign" style="display:none">{{this.total=cn.amount+this.total}}</td> -->
                                <!-- <td class="textalign">{{cn.products.pivot.total_amount}}</td> -->
                                <!-- <td class="text-right">{{numberWithCommas(collection.total_paid_amount)}}</td> -->

                                <!--<td class="text-center">
                                    <router-link tag="span" :to="'/collection/edit/' + collection.id" >
                                        <a href="#" title="Edit/View" class="">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    </router-link>
                                    <a title="Delete" class="text-danger" @click="removeCollection(collection.id)" v-if="user_role == 'admin' || user_role == 'system'">
                                        <i class="fas fa-trash"></i>
                                    </a>&nbsp;  
                                </td>-->

                                <!--Kamlesh Start-->
                               
                                <!-- Kamlesh End-->
                            </tr>
                            <!-- <tr>
                                 <td colspan="5" style="text-align: right;">
                                   <strong><h4>Total Net </h4>   </strong>
                                 </td>
                                <td class="text-center">
                                    {{sub_total}}
                                </td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No record found!</h5>
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
                    township_id:'',
                    state_id:'', 
                    invoice_no: "",
                    customer_id: "",
                    product_code:'',
                    product_name:'',
                },
                customers: [],
                delivery_pending: [],
                townships:[],
                states:[],
                user_role: '',
                user_year: "",
                branches: [],
                site_path: '',
                storage_path: '',
                sub_total:'',
                user_role_id: '',
            };
        },

        created() {
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            
            this.user_role_id = document.querySelector("meta[name='user-role-id']").getAttribute('content');
            
            if(this.user_role != "admin" && this.user_role != "system" && this.user_role_id != 19 && this.user_role_id != 12 && this.user_role_id != 14 && this.user_role_id != 18)
            {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            // this.getDeliveryPending();    
        },

        mounted() {
            $('#loading').hide();
            let app = this;
            app.initCustomers();
            app.initStates();
            app.initTownships();
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
             $("#cn_from_date")
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
                  if(app.search.cn_from_date == app.user_year+"-12-31" || app.search.cn_from_date == '') {
                    app.search.cn_from_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.cn_from_date = formatedValue;
            });

            $("#cn_to_date")
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
                  if(app.search.cn_to_date == app.user_year+"-12-31" || app.search.cn_to_date == '') {
                    app.search.cn_to_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                app.search.cn_to_date = formatedValue;
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
            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.customer_id = data.id;
            });

            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.branch_id = data.id;
            });
        },

        methods: {
            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
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
                }else{
                      axios.get("/township").then(({ data }) => (this.townships = data.data));
                    $("#township_id").select2();
                }
            },
            getDeliveryPending(page = 1) {
                if(this.search.cn_from_date == "") {                  
                    swal("Warning!", "From Date must be added!", "warning")
                    return false;
                } 
                $("#loading").show();
                let app = this;
                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                     "&invoice_no=" +
                    app.search.invoice_no +
                     "&state_id=" +
                    app.search.state_id +
                    "&township_id=" +
                    app.search.township_id+
                     "&product_code=" +
                    app.search.product_code +
                    "&product_name=" +
                    app.search.product_name+
                    "&customer_id=" +
                    app.search.customer_id;
                    axios.get("/report/get_delivery_pending?" + search).then(data=>{
                        app.delivery_pending=data.data.delivery_pending;
                        // app.sub_total=data.data.sub_total;
                        console.log(data);
                    })
                    .then(function() {
                        $("#loading").hide();
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
                 "&state_id=" +
                app.search.state_id +
                "&township_id=" +
                app.search.township_id+
                 "&product_code=" +
                app.search.product_code +
                "&product_name=" +
                app.search.product_name+
                "&customer_id=" +
                app.search.customer_id;
                var baseurl = window.location.origin;
                window.open(this.site_path+'/report/delivery_pending_export?'+search);
            },
            dateFormat(d) {
                //return moment(d).format('YYYY-MM-DD');
                return moment(d).format('DD-MM-YYYY');
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

        },

        
    }
</script>