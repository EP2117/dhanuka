<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/master">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">Currency</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Currency</h4>
            <router-link to="/currency/new" class="d-sm-inline-block btn btn-primary shadow-sm text-right">
                <i class="fas fa-plus"></i> Add New Currency
            </router-link>
        </div>
        <!--<div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="brand_name">Brand Name</label>
                        <input type="text" class="form-control" id="brand_name" name="brand_name" v-model="search.brand_name">
                    </div>

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
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getBrands(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>-->
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Currency List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="currency_count > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Currency Name</th>
                                <th class="text-center">Currency Sign</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Currency Name</th>
                                <th class="text-center">Currency Sign</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="c,index in currency.data">
                                <td class="text-center">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td class="mm-txt text-center">{{c.name}}</td>
                                <td class="mm-txt text-center" v-if="c.sign != null">{{c.sign}}</td>
                                <td v-else></td>

                                <td v-if="c.is_active == 1" class="text-center">
                                    <span class="badge badge-success">Active</span>
                                </td>
                                <td v-else class="text-center">
                                    <span class="badge badge-danger">Inactive</span>
                                </td>

                                <!--Kamlesh Start-->
                                <td class="text-center">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            
                                            <a class="dropdown-item">
                                                <a class="badge badge-primary text-white"  @click="changeStatus(c.id,'inactive')" v-if="c.is_active == 1">Inactive</a>
                                                <a class="badge badge-primary text-white" @click="changeStatus(c.id,'active')" v-else>Active</a>  
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
                    <h5 class="text-center m-5">No Currency found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">
          
              <!-- pagination start -->
              <div class="row" style="overflow:auto">
                <div class="col-12">
                  <template v-if="brand_count > 0">
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
                        <template v-slot:first-text><span class="text-success" v-on:click="getCurrency(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getCurrency(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getCurrency(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getCurrency(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getCurrency(page)">{{ page }}</span>
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
         <div id="loading" class="text-center"><img src="/storage/image/loader_2.gif" /></div>
    </div>

</template>

<script>
    export default {

        data() {
            return {
                search: {
                    brand_name: "",
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
                rows: "",
                perPage: "30",
                currentPage: 1,
                currency_count: 0,
                currency: [],
                user_role: '',
            };
        },

        created() {

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            if(this.user_role != "system") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            this.getCurrency();    
        },

        mounted() {
            //$("#loading").hide();
            let app = this;
        },

        methods: {

            changeStatus(id,status) {
                let app = this;
                var active = status;
                var id = id;

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
                        .get("/currency_status/" + id + "/" + active)
                        .then(function(response) {
                            $("#loading").hide();
                            app.getCurrency(app.currentPage);
                            swal("Success! Currency has been updated as " + active+".", {
                            icon: "success"
                          });
                        });
                        
                    } else {
                       
                    }
                });
            },

            getCurrency(page = 1) {
                $("#loading").show();
                let app = this;

                var search =
                    "&brand_name=" +
                    app.search.brand_name +
                    "&status=" +
                    app.search.status;

                axios.get("/currency?page=" + page + search).then(function(response) {
                    $("#loading").hide();
                    let data = response.data.data;
                    app.currency = data;
                    app.currency_count = app.currency.data.length;
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
        }
    }
</script>