<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item active" aria-current="page">Collection</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Collection</h4>
            <router-link :to="'/collection/new'" class="d-sm-inline-block btn btn-primary shadow-sm inventory">
                <i class="fas fa-plus"></i> Add New Collection
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
                        <label for="collection_no">Collection No.</label>
                        <input type="text" class="form-control" id="collection_no" name="collection_no" v-model="search.collection_no">
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
                        <label >State</label>
                        <select id="state_id" class="form-control mm-txt"
                                 v-model="search.state_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="s in states" :value="s.id"  >{{s.state_name}}</option>
                        </select>
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

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getCollections(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Collection List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="collections.length > 0">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Collection No.</th>
                                <th class="text-center">Collection Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Collect Amount</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Collection No.</th>
                                <th class="text-center">Collection Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Collect Amount</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="collection,index in collections">
                                <td class="textalign">{{index+1}}</td>
                                <td class="textalign">{{collection.collection_no}}</td>
                                <td class="textalign">{{collection.collection_date}}</td>
                                <td v-if="collection.branch != null">{{collection.branch.branch_name}}</td>
                                <td v-else></td>
                                <td class="mm-txt">{{collection.customer.cus_name}}</td>
                                <td class="text-right">{{numberWithCommas(collection.total_paid_amount)}}</td>

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
                                <td class="text-center">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <router-link tag="span" :to="'/collection/edit/' + collection.id" >
                                                    <a href="#" title="Edit/View" class="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>&nbsp;
                                                </router-link>
                                            </a>
                                            <a class="dropdown-item">
                                                <a title="Delete" class="text-danger" @click="removeCollection(collection.id)" v-if="user_role == 'admin' || user_role == 'system'">
                                                    <i class="fas fa-trash"></i>
                                                </a>&nbsp;
                                            </a>
                                        </div>
                                    </div>

                                </td>
                                <!-- Kamlesh End-->
                            </tr>
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
                    collection_no: "",
                    customer_id: "",
                    branch_id: "",
                    state_id:'',
                },
                customers: [],
                collections: [],
                user_role: '',
                user_year: "",
                states:[],
                branches: [],
                site_path: '',
                storage_path: '',
            };
        },
        created(){
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            
            /*if(this.user_role == "office_order_user")*/
            if(this.user_role != "admin" && this.user_role != "system")
            {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            this.getCollections();    
        },
        mounted() {
            let app = this;
            app.initCustomers();
            app.initBranches();
            app.initStates();
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
            $("#state_id").select2();
            $("#state_id").on("select2:select", function(e) {
                app.townships=[];
                var data = e.params.data;
                app.search.state_id = data.id;
                axios.get("/township_by_state/"+ data.id).then(({ data }) => (app.townships = data.data));
            });
        },
        methods: {
            initStates() {
                    axios.get("/state").then(({ data }) => (this.states = data.data));
                    $("#state_id").select2();
            },
            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },
            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            },
            getCollections(page = 1) {
                $("#loading").show();
                let app = this;
                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&collection_no=" +
                    app.search.collection_no +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&customer_id=" +
                    app.search.customer_id+
                     "&state_id=" +
                    app.search.state_id;
                    axios.get("/collection?" + search).then(({ data }) => (app.collections = data.data))
                    .then(function() {
                        $("#loading").hide();
                    });
            },

            removeCollection(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/collection/" + id).then(function() {
                            swal("Success! Collection has been deleted!", {
                                icon: "success"
                            });
                            app.getCollections();
                        });
                           
                    } else {
                      //
                    }
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
                return localTimezone.format('YYYY-MM-DD HH:mm:ss');
            },

        },

        
    }
</script>