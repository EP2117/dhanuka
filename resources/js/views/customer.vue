<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">Customer</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Customer</h4>
            <router-link to="/customer/new" class="d-sm-inline-block btn btn-primary shadow-sm text-right">
                <i class="fas fa-plus"></i> Add New Customer
            </router-link>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="cus_code">Customer Code</label>
                        <input type="text" class="form-control" id="cus_code" name="cus_code" v-model="search.cus_code">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="cus_name">Customer Name</label>
                        <input type="text" class="form-control" id="cus_name" name="cus_name" v-model="search.cus_name">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="cus_type">Customer Type</label>
                        <select id="cus_type" class="form-control mm-txt"
                            name="cus_type" v-model="search.cus_type" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="ctype in cus_types" :value="ctype.id"  >{{ctype.customer_type_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="state_id">State</label>
                        <select id="state_id" class="form-control"
                            name="state_id" v-model="search.state_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="state in states" :value="state.id"  >{{state.state_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="township_id">Township</label>
                        <select id="township_id" class="form-control mm-txt"
                            name="township_id" v-model="search.township_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="tsp in townships" :value="tsp.id"  >{{tsp.township_name}}</option>
                        </select>
                    </div>

                    <!--<div class="form-group col-md-4 col-lg-3">
                        <label for="country_id">Country</label>
                        <select id="country_id" class="form-control"
                            name="country_id" v-model="search.country_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="country in countries" :value="country.id"  >{{country.country_name}}</option>
                        </select>
                    </div>-->

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="status">Status</label>
                        <select id="status" class="form-control"
                            name="status" v-model="search.status"
                        >
                            <option value="">Select One</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getCustomers(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Customer List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="customer_count > 0">
                    <!-- sort by -->
                    <div class="form-group float-left pr-2">
                        <label for="sort_by">Sort By</label>
                        <select id="sort_by" class="form-control"
                            name="sort_by" v-model="search.sort_by" style="width:200px" @change="getCustomers(1)">
                            <option value="">Select One</option>
                            <option value="code">Code</option>
                            <option value="name">Name</option>
                            <option value="cus_type">Customer Type</option>
                            <option value="phone">Phone</option>
                            <option value="address">Address</option>
                        </select>
                    </div>
                    <div>
                        <!--<div class="float-left ml-3 pl-3">&nbsp;&nbsp;</div>
                        <div class="form-group ml-3 pl-3">
                            <label for="order">&nbsp;</label>
                            <select id="order" class="form-control"
                                name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getCustomers(1)"
                            >
                                <option value="">Select One</option>
                                <option value="ASC">Ascending</option>
                                <option value="DESC">Descending</option>
                            </select>
                        </div>-->

                        <div class="form-group float-left">
                            <select id="order" class="form-control mt-2"
                                name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getCustomers(1)"
                            >
                                <option value="">Select One</option>
                                <option value="ASC">Ascending</option>
                                <option value="DESC">Descending</option>
                            </select>
                        </div>
                        <div class="text-right form-group mt-4">
                            <label style="height:10px;">&nbsp;</label>
                            <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                        </div>
                    </div>
                    <!-- end sort by -->
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Code</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Customer Type</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Township, State, Country</th>
                                <th class="text-center">Photo</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Code</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Customer Type</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Township, State, Country</th>
                                <th class="text-center">Photo</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="(cus,index) in customers.data">
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td>{{cus.cus_code}}</td>
                                <td class="mm-txt">{{cus.cus_name}}</td>
                                <td class="mm-txt textalign">{{cus.customer_type_name}}</td> <!--Kamlesh -->
                                <td>{{cus.cus_phone}}</td>
                                <td class="mm-txt">{{cus.township_name + ', ' + cus.state_name + ', ' + cus.country_name }}</td>

                                <td v-if="cus.photo!= '' && cus.photo!= null">
                                    <template v-for="p,i in cus.photo.split(',')">
                                        <a :href="storage_path+'/image/customer/'+p" target="_blank">Image{{i+1}}</a><span v-if="i != cus.photo.split(',').length-1">, </span>
                                    </template>
                                </td>
                                <td v-else></td>

                                <td v-if="cus.is_active == 1">
                                    <span class="badge badge-success">Active</span>
                                </td>
                                <td v-else>
                                    <span class="badge badge-danger">Inactive</span>
                                </td>

                                <!-- <td class="text-center" style="width:110px;">
                                    <router-link tag="span" :to="'/customer/edit/' + cus.id" >
                                        <a href="#" title="Edit/View" class="">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    </router-link>
                                    <a class="badge badge-primary text-white"  @click="changeStatus(cus.id,'inactive')" v-if="cus.is_active == 1">Inactive</a>
                                    <a class="badge badge-primary text-white" @click="changeStatus(cus.id,'active')" v-else>Active</a>
                                </td> -->

                                <!--Kamlesh Start-->
                                <td class="text-center">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                            <router-link tag="span" :to="'/customer/edit/' + cus.id" >
                                                <a href="#" title="Edit/View" class="">
                                                    <i class="fas fa-edit"></i>
                                                </a>&nbsp;
                                            </router-link>
                                            </a>
                                            <a class="dropdown-item">
                                                <a class="badge badge-primary text-white"  @click="changeStatus(cus.id,'inactive')" v-if="cus.is_active == 1">Inactive</a>
                                                <a class="badge badge-primary text-white" @click="changeStatus(cus.id,'active')" v-else>Active</a>
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
                    <h5 class="text-center m-5">No customer found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">

              <!-- pagination start -->
              <div class="row" style="overflow:auto">
                <div class="col-12">
                  <template v-if="customer_count > 0">
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
                        <template v-slot:first-text><span class="text-success" v-on:click="getCustomers(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getCustomers(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getCustomers(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getCustomers(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getCustomers(page)">{{ page }}</span>
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
                    cus_code: "",
                    cus_name: "",
                    township_id: "",
                    state_id: "",
                    country_id: "",
                    cus_type: "",
                    sort_by: '',
                    order: '',
                    status: '',
                },
                pagination: {
                    total: "",
                    next: "",
                    prev: "",
                    last_page: "",
                    current_page: "",
                    next_page_url:""
                },
                townships: [],
                cus_types: [],
                cus:[],
                states:[],
                countries: [],
                rows: "",
                perPage: "30",
                currentPage: 1,
                customer_count: 0,
                customers: [],
                user_role: '',
                site_path: '',
                storage_path: '',
            };
        },

        created() {

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            this.getCustomers();
        },

        mounted() {
            //$("#loading").hide();
            let app = this;
            app.initCountries();
            app.initStates();
            app.initTownships();
            app.initTypes();

            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.customer_id = data.id;
            });

            $("#township_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.township_id = data.id;
            });

            $("#cus_type").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.cus_type = data.id;
            });

            $("#state_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.state_id = data.id;

                app.townships = [];
                axios.get("/township_by_state/"+ data.id).then(({ data }) => (app.townships = data.data));
            });
        },

        methods: {

            initTypes() {
              axios.get("/customer_type").then(({ data }) => (this.cus_types = data.data));
              //console.log(this.cus_types);
              $("#cus_type").select2();
            },
            initTownships() {
                if(this.search.state_id != "") {
                  axios.get("/township_by_state/" + this.search.state_id).then(({ data }) => (this.townships = data.data));
                  $("#township_id").select2();
                }
            },
            initStates() {
              axios.get("/state").then(({ data }) => (this.states = data.data));
              $("#state_id").select2();
            },

            initCountries() {
              axios.get("/country").then(({ data }) => (this.countries = data.data));
              $("#country_id").select2();
            },

            changeStatus(id,status) {
                let app = this;
                var active = status;
                var cus_id = id;

                swal({
                    title: "Are you sure to "+active+"?",
                    text: "",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                }).then(willDelete => {
                    if (willDelete) {
                        $("#loading").show();
                        axios
                        .get("/customer_status/" + cus_id + "/" + active)
                        .then(function(response) {
                            $("#loading").hide();
                            app.getCustomers(app.currentPage);
                            swal("Success! Customer has been updated as " + active+".", {
                            icon: "success"
                          });
                        });

                    } else {

                    }
                });
            },

            getCustomers(page = 1) {
                $("#loading").show();
                let app = this;

                var search =
                    "&cus_code=" +
                    app.search.cus_code +
                    "&cus_name=" +
                    app.search.cus_name +
                    "&cus_type=" +
                    app.search.cus_type +
                    "&state_id=" +
                    app.search.state_id +
                    "&township_id=" +
                    app.search.township_id +
                    "&country_id=" +
                    app.search.country_id +
                    "&status=" +
                    app.search.status +
                    "&order=" +
                    app.search.order +
                    "&sort_by=" +
                    app.search.sort_by;

                axios.get("/customer?page=" + page + search).then(function(response) {
                    $("#loading").hide();
                    let data = response.data.data;
                    app.customers = data;
                    app.customer_count = app.customers.data.length;
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

                /*axios.get("/customer").then(({ data }) => (app.customers = data.data))
                .then(function() {
                    console.log(app.customers);
                    $("#loading").hide();
                });*/
            },

            exportExcel() {

                let app = this;

                var search =
                    "&cus_code=" +
                    app.search.cus_code +
                    "&cus_name=" +
                    app.search.cus_name +
                    "&cus_type=" +
                    app.search.cus_type +
                    "&state_id=" +
                    app.search.state_id +
                    "&township_id=" +
                    app.search.township_id +
                    "&country_id=" +
                    app.search.country_id +
                    "&status=" +
                    app.search.status +
                    "&order=" +
                    app.search.order +
                    "&sort_by=" +
                    app.search.sort_by;


                var baseurl = window.location.origin;
                //window.open(baseurl+'/customer_export?'+search);
                window.open(this.site_path+'/customer_export?'+search);
            },

            deleteCustomer(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                }).then(willDelete => {
                if (willDelete) {
                  axios.delete("/customer/" + id).then(function() {
                    app.getCustomers(app.currentPage);
                  });
                  swal("Success! Customer has been deleted!", {
                    icon: "success"
                  });
                } else {
                  //
                }
                });
            }
        }
    }
</script>
