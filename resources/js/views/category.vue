<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">Category</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Category</h4>
            <router-link to="/category/new" class="d-sm-inline-block btn btn-primary shadow-sm text-right">
                <i class="fas fa-plus"></i> Add New Category
            </router-link>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="category_name">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" v-model="search.category_name">
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
                          @click="getCategories(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Category List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="category_count > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Category Name</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Category Name</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="cat,index in categories.data">
                                <td class="text-center">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td class="mm-txt text-center">{{cat.category_name}}</td>
                                <td class="mm-txt text-center" v-if="cat.brand != null">{{cat.brand.brand_name}}</td>
                                <td v-else></td>

                                <td v-if="cat.is_active == 1" class="text-center">
                                    <span class="badge badge-success">Active</span>
                                </td>
                                <td v-else class="text-center">
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
                                            <router-link tag="span" :to="'/category/edit/' + cat.id" >
                                                <a href="#" title="Edit/View" class="">
                                                    <i class="fas fa-edit"></i>
                                                </a>&nbsp;
                                            </router-link>
                                            </a>
                                            <a class="dropdown-item">
                                                <a class="badge badge-primary text-white"  @click="changeStatus(cat.id,'inactive')" v-if="cat.is_active == 1">Inactive</a>
                                                <a class="badge badge-primary text-white" @click="changeStatus(cat.id,'active')" v-else>Active</a>  
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
                    <h5 class="text-center m-5">No category found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">
          
              <!-- pagination start -->
              <div class="row" style="overflow:auto">
                <div class="col-12">
                  <template v-if="category_count > 0">
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
                        <template v-slot:first-text><span class="text-success" v-on:click="getCategories(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getCategories(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getCategories(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getCategories(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getCategories(page)">{{ page }}</span>
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
                    category_name: "",
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
                category_count: 0,
                categories: [],
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

            /*if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }*/

            this.getCategories();    
        },

        mounted() {
            //$("#loading").hide();
            let app = this;
        },

        methods: {

            changeStatus(id,status) {
                let app = this;
                var active = status;
                var category_id = id;

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
                        .get("/category_status/" + category_id + "/" + active)
                        .then(function(response) {
                            $("#loading").hide();
                            app.getCategories(app.currentPage);
                            swal("Success! Category has been updated as " + active+".", {
                            icon: "success"
                          });
                        });
                        
                    } else {
                       
                    }
                });
            },

            getCategories(page = 1) {
                $("#loading").show();
                let app = this;

                var search =
                    "&category_name=" +
                    app.search.category_name +
                    "&status=" +
                    app.search.status;

                axios.get("/category?page=" + page + search).then(function(response) {
                    $("#loading").hide();
                    let data = response.data.data;
                    app.categories = data;
                    app.category_count = app.categories.data.length;
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