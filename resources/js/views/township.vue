<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">Township</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Township</h4>
            <router-link to="/township/new" class="d-sm-inline-block btn btn-primary shadow-sm text-right">
                <i class="fas fa-plus"></i> Add New Township
            </router-link>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
               <div class="row">
                    
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
                        <input type="text" id="township_name" class="form-control mm-txt"
                                name="township_name" v-model="search.township_name"
                        />
                    </div>
                    <!--<div class="form-group col-md-4 col-lg-3">
                        <label for="status">Status</label>
                        <select id="status" class="form-control"
                                name="status" v-model="search.status"
                        >
                            <option value="">Select One</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>-->

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="initTownships(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div> 
                </div>
            </div>
        </div>
<!--         table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Township List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="township_count > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                         <tr>
                            <th class="text-center">No.</th>
                            <th class="">Name</th>
                            <th class="">State</th>
                            <th >Status</th>
                            <th >  </th> <!--Kamlesh -->
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="">Name</th>
                            <th class="">State</th>
                            <th >Status</th>
                            <th >  </th> <!--Kamlesh -->
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr v-for="(t,index) in townships">
<!--                            <td></td>-->
                            <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                            <td class="mm-txt">{{t.township_name}}</td>
                            <td class="mm-txt" v-if="t.state != null">{{t.state.state_name}}</td>
                            <td v-else></td>
                            <td v-if="t.is_active == 1">
                                <span class="badge badge-success">Active</span>
                            </td>
                            <td v-else>
                                <span class="badge badge-danger">Inactive</span>
                            </td>

                            <!--Kamlesh Start-->
                            <td class="text-center">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item" v-if="user_role=='system'">
                                            <router-link tag="span" :to="'/township/edit/' + t.id" >
                                                <a href="#" title="Edit/View" class="">
                                                    <i class="fas fa-edit"></i>
                                                </a>&nbsp;
                                            </router-link>
                                        </a>
                                        <a class="dropdown-item">
                                            <a class="badge badge-primary text-white"  @click="changeStatus(t.id,'inactive')" v-if="t.is_active == 1">Inactive</a>
                                            <a class="badge badge-primary text-white" @click="changeStatus(t.id,'active')" v-else>Active</a>
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
                    <h5 class="text-center m-5">No Supplier found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">
                <!-- pagination start -->
                <div class="row" style="overflow:auto">
                    <div class="col-12">
                        <template v-if="township_count > 0">
                            <div class="overflow-auto text-center" style="display:inline-block">
                                <!-- Use text in props -->
                                <b-pagination
                                    v-model="currentPage"
                                    :total-rows="rows"
                                    :per-page="perPage"
                                    first-text="First"
                                    prev-text="Prev"
                                    next-text="Next"
                                    last-text="Last">
                                    <template v-slot:first-text><span class="text-success" v-on:click="initTownships(1)">First</span></template>
                                    <template v-slot:prev-text><span class="text-danger" v-on:click="initTownships(currentPage)">Prev</span></template>
                                    <template v-slot:next-text><span class="text-warning" v-on:click="initTownships(currentPage)">Next</span></template>
                                    <template v-slot:last-text><span class="text-info" v-on:click="initTownships(pagination.last_page)">Last</span></template>
                                    <template v-slot:ellipsis-text>
                                    </template>
                                    <template v-slot:page="{ page, active }">
                                        <span v-if="active"><b>{{ page }}</b></span>
                                        <span v-else v-on:click="initTownships(page)">{{ page }}</span>
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
                township_name: "",
                state_id: "",
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
            states:[],
            rows: "",
            perPage: 30,
            currentPage: 1,
            township_count: 0,
            user_role: '',
            site_path: '',
            storage_path: '',
        };
    },

    created() {
        // console.log(this.perPage);
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

        if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
            var url =  window.location.origin;
            window.location.replace(url);
        }

        //this.getTownships();
    },

    mounted() {
        //$("#loading").hide();
        let app = this;
        app.initStates();
        app.initTownships();
        // app.initTypes();

        $("#customer_id").on("select2:select", function(e) {

            var data = e.params.data;
            app.search.customer_id = data.id;
        });

        $("#township_id").on("select2:select", function(e) {

            var data = e.params.data;
            app.search.township_id = data.id;
        });

        // $("#cus_type").on("select2:select", function(e) {
        //
        //     var data = e.params.data;
        //     app.search.cus_type = data.id;
        // });

        $("#state_id").on("select2:select", function(e) {

            var data = e.params.data;
            app.search.state_id = data.id;
        });
    },

    methods: {

        // initTypes() {
        //     axios.get("/customer_type").then(({ data }) => (this.cus_types = data.data));
        //     //console.log(this.cus_types);
        //     $("#cus_type").select2();
        // },

        initTownships(page = 1) {

            $("#loading").show();
            let app = this;

            var search =
                "&state_id=" +
                app.search.state_id +
                "&township_name=" +
                app.search.township_name;

            axios.get("/get_townships?page=" + page + search).then(function(response) {
                // console.log(response.data);
                $("#loading").hide();
                let data = response.data.data;
                app.townships  = data.data;
                app.township_count = app.townships.length;
                app.pagination.last_page = data.last_page;
                app.pagination.next = data.next_page_url;
                app.pagination.prev = data.prev_page_url;
                app.pagination.total = data.total;
                app.pagination.current_page = data.current_page;
                app.pagination.next_page_url = data.next_page_url;
                app.rows = data.total;
                // app.currentPage = data.current_page;
            });
        },

        initStates() {
            axios.get("/state").then(({ data }) => (this.states = data.data));
            $("#state_id").select2();
        },

        changeStatus(id,status) {
            let app = this;
            var active = status;
            var t_id = id;

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
                        .get("/township_status/",{
                            params:{
                                'id':t_id,
                                'status':status
                            }
                        })
                        .then(function(response) {
                            $("#loading").hide();
                            app.initTownships();
                            swal("Success! Township has been updated as " + active+".", {
                                icon: "success"
                            });
                        });

                } else {

                }
            });
        },
    }
}
</script>

<style scoped>

</style>
