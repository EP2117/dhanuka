<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Return</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Sale Return</h4>
            <router-link :to="'/sale_return/new'" class="d-sm-inline-block btn btn-primary shadow-sm inventory">
                <i class="fas fa-plus"></i> Add Sale Return
            </router-link>
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
                        <label for="return_no">Return No.</label>
                        <input type="text" class="form-control" id="return_no" name="return_no" v-model="search.return_no">
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
                        <label for="sale_man_id">Sale Man</label>
                        <select id="sale_man_id" class="form-control mm-txt"
                            name="sale_man_id" v-model="search.sale_man_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="sale_man in sale_men" :value="sale_man.id"  >{{sale_man.sale_man}}</option>
                        </select>
                    </div>
                    <!--<div class="form-group col-md-4 col-lg-3 mm-txt"  v-if=" user_role == 'admin'">
                        <label for="office_sale_man_id">Office Sale Man</label>
                        <select id="office_sale_man_id" class="form-control mm-txt"
                            name="office_sale_man_id" v-model="search.office_sale_man_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="office_sale_man in office_sale_mans" :value="office_sale_man.id"  >{{office_sale_man.name}}</option>
                        </select>
                    </div>-->

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getReturns(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sale Return List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="return_count > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Return No.</th>
                                <th class="text-center">Return Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Sale Man</th>
                                <th class="text-center">Warehouse</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Return Amount</th>
                                <th class="text-center">Balance</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Return No.</th>
                                <th class="text-center">Return Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Sale Man</th>
                                <th class="text-center">Warehouse</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Return Amount</th>
                                <th class="text-center">Balance</th>
                                <th class="text-center"></th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <template v-for="sreturn,index in returns.data">
                            <tr>
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td class="textalign">{{sreturn.return_no}}</td>
                                <td class="textalign">{{sreturn.return_date}}</td>
                                <td v-if="sreturn.branch != null">{{sreturn.branch.branch_name}}</td>
                                <td v-else></td>
                                <td class="mm-txt">{{sreturn.customer.cus_name}}</td>
                                <td class="mm-txt" v-if="sreturn.office_sale_man_id != null">{{sreturn.office_sale_man.sale_man}}</td>
                                <td v-else></td>
                                <td class="mm-txt">{{sreturn.warehouse.warehouse_name}}</td>
                                <td class="text-right">{{sreturn.total_amount}}</td>
                                <td class="text-right">{{sreturn.payment_amount}}</td>
                                <td class="text-right">{{sreturn.balance_amount}}</td>

                                <td class="text-center">

                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <router-link tag="span" :to="'/sale_return/edit/' + sreturn.id">
                                                    <a href="#" title="Edit/View" class="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>&nbsp;
                                                </router-link>        
                                            </a>

                                            <a title="Print" class=" dropdown-item text-primary" @click="generatePDF(sreturn.id)">
                                                <i class="fas fa-print"></i>
                                            </a>

                                            <a class="dropdown-item" v-if="sreturn.return_method == 'with invoice' && ((sreturn.payment_amount == sreturn.total_payment_amount && sreturn.sale.extra_return_amount == 0) || (sreturn.return_status == 'extra' && sreturn.payment_amount == sreturn.total_payment_amount))">
                                                <a title="Delete" class="text-danger" @click="removeReturn(sreturn.id)">
                                                    <i class="fas fa-trash"></i>
                                                </a>           
                                            </a>

                                            <a class="dropdown-item" v-if="sreturn.return_method == 'without invoice' && sreturn.payment_amount == sreturn.total_payment_amount">
                                                <a title="Delete" class="text-danger" @click="removeReturn(sreturn.id)">
                                                    <i class="fas fa-trash"></i>
                                                </a>           
                                            </a>
                                        </div>
                                    </div>
                                                                      
                                </td>
                            </tr>
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
                  <template v-if="return_count > 0">
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
                        <template v-slot:first-text><span class="text-success" v-on:click="getReturns(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getReturns(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getReturns(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getReturns(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getReturns(page)">{{ page }}</span>
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
                    return_no: "",
                    customer_id: "",
                    sale_man_id: "",
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
                return_count: 0,
                returns: [],
                customers: [],
                sale_type: '',
                sale_mans: [],
                office_sale_mans: [],
                user_role: '',
                user_year: '',
                branches: [],
                states:[],
                site_path: '',
                storage_path: '',
                sale_men: [],
            };
        },

        created() {
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            console.log(this.site_path.split('/'));
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
            this.getReturns(); 
            //var ls = localStorage.getItem('storedData') != null ?  localStorage.getItem('storedData') : 'no ls'
            //console.log(localStorage.length);
        },

        mounted() {
            let app = this;
            //app.initSaleMan();
            app.initSaleMen();
           // app.initOfficeSaleMan();
            app.initCustomers();
            app.initBranches();
            app.initStates();
            
            // app.initCreatedUser();
            //app.calcLStorageSize();

            $("#sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.sale_man_id = data.id;
            });
            $("#office_sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.office_sale_man_id = data.id;
            });

            $("#office_sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.office_sale_man_id = data.id;
            });

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

            initSaleMen() {
              axios.get("/sale_men").then(({ data }) => (this.sale_men = data.data));              
              $("#sale_man_id").select2();
            },
             initStates() {
                    axios.get("/state").then(({ data }) => (this.states = data.data));
                    $("#state_id").select2();
            },
            initSaleMan() {
              axios.get("/sale_man").then(({ data }) => (this.sale_mans = data.data));
              //$("#sale_man_id").select2();
            },

            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            },

            initOfficeSaleMan() {
              axios.get("/office_sale_man").then(({ data }) => (this.office_sale_mans = data.data));
              $("#office_sale_man_id").select2();
            },

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
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

            getReturns(page = 1) {
                $("#loading").show();
                let app = this;

                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&return_no=" +
                    app.search.return_no +
                    "&customer_id=" +
                    app.search.customer_id +
                    "&sale_man_id=" +
                    app.search.sale_man_id; 
                axios.get("/sale_return?page=" + page + search).then(function(response) {
                    $("#loading").hide();
                    let data = response.data.data;
                    app.returns = data;
                    app.return_count = app.returns.data.length;
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

            removeReturn(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/sale_return/" + id).then(function() {
                            app.getReturns();
                        });
                        swal("Success! Sale Return has been deleted!", {
                            icon: "success"
                        });   
                    } else {
                      //
                    }
                });
            },

            dateFormat(d) {
                return moment(d).format('YYYY-MM-DD');
            },

            generatePDF(return_id)
            {
                var baseurl = window.location.origin;
                window.open(this.site_path+'/generate_sale_return/'+return_id);
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
            }
        },
    }
</script>