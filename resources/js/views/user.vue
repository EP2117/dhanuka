<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">User</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">User</h4>
            <router-link :to="'/user/new'" class="d-sm-inline-block btn btn-primary shadow-sm inventory">
                <i class="fas fa-plus"></i> Add New User
            </router-link>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="role_id">User Role</label>
                        <select id="role_id" class="form-control mm-txt"
                            name="role_id" v-model="search.role_id" style="width:100%" 
                        >
                            <option value="">Select One</option>
                            <option v-for="role in roles" :value="role.id"  >{{role.role_name}}</option>
                        </select>
                    </div> 

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="status">Status</label>
                        <select id="status" class="form-control mm-txt"
                            name="status" v-model="search.status" style="width:100%" 
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
                          @click="getUsers(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="users.length > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Online Status</th>
                                 <th class="text-center">Status</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Online Status</th>
                                 <th class="text-center">Status</th>
                                <th class="text-center"></th>
                            </tr>
                        </tfoot>
                        <tbody>
                             <tr v-for="user,index in users">
                                <td class="text-right">{{index+1}}</td>
                                <td>{{user.name}}</td>
                                <td>{{user.email}}</td>
                                <td class="mm-txt textalign">{{user.role.role_name}}</td>
                                <td>{{user.phone == null ? '' : user.phone}}</td>
                                <td class="text-center" v-if="user.online_status == 1">
                                    <span class="btn btn-success btn-sm">Online</span>
                                </td>
                                <td class="text-center" v-else><span class="btn btn-secondary btn-sm">Offline</span></td>
                                <td class="text-center">
                                    <span class="badge badge-success" v-if="user.is_active == 1">Active</span>
                                    <span class="badge badge-danger" v-else>Inactive</span>
                                </td>
                                <td class="text-center">
                                    <!--<router-link tag="span" :to="'/user/edit/' + user.id" >
                                        <a href="#" title="Edit/View" class="">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    </router-link>

                                    <button type="button" title="Offline" class="btn btn-secondary btn-sm" @click="offUser(user.id)" v-if="user_role == 'system' && user.online_status == 1">
                                        Offline
                                    </button>&nbsp; -->

                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <router-link tag="span" :to="'/user/edit/' + user.id" >
                                                    <a href="#" title="Edit/View" class="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>&nbsp;
                                                </router-link>
                                            </a>
                                            <a class="dropdown-item" v-if="user_role == 'system' && user.online_status == 1">
                                                <button type="button" title="Offline" class="btn btn-secondary btn-sm" @click="offUser(user.id)" v-if="user_role == 'system' && user.online_status == 1">
                                                    Offline
                                                </button>&nbsp;
                                            </a>

                                            <a class="dropdown-item" v-if="user_role == 'system'">
                                                <a class="btn btn-primary btn-sm text-white"  @click="changeStatus(user.id,'inactive')" v-if="user.is_active == 1">Inactive</a>
                                                <a class="btn btn-primary btn-sm text-white" @click="changeStatus(user.id,'active')" v-else>Active</a>  
                                            </a>
                                        </div>
                                    </div>
                                        
                                    <!--<a title="Delete" class="text-danger" @click="removeUser(user.id)" v-if="user_role == 'system'">
                                        <i class="fas fa-trash"></i>
                                    </a>&nbsp; --> 

                                                                      
                                </td>
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
                    role_id: "",
                    status: "",
                },
                roles: [],
                users: [],
                user_role: '',
                user_year: "",
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
            
            if(this.user_role != "system") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            this.getUsers();    
        },

        mounted() {
            let app = this;
            app.initRoles();

            $("#role_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.role_id = data.id;
            });
        },

        methods: {
            initRoles() {
              axios.get("/role").then(({ data }) => (this.roles = data.data));
              console.log(this.roles);
              $("#role_id").select2();
            },

            getUsers(page = 1) {
                $("#loading").show();
                let app = this;

                var search =
                    "&status="+app.search.status+
                    "&role_id=" +
                    app.search.role_id;

                    axios.get("/user?" + search).then(({ data }) => (app.users = data.data))
                    .then(function() {
                        $("#loading").hide();
                    });
            },

            changeStatus(id,status) {
                let app = this;
                var active = status;
                var user_id = id;

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
                        .get("/user_status/" + user_id + "/" + active)
                        .then(function(response) {
                            $("#loading").hide();
                            app.getUsers(app.currentPage);
                            swal("Success! User has been updated as " + active+".", {
                            icon: "success"
                          });
                        });
                        
                    } else {
                       
                    }
                });
            },

            offUser(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.post("/user_offline/" + id).then(function() {
                            swal("Success! User has been updated as offline!", {
                                icon: "success"
                            });
                            app.getUsers();
                        });
                           
                    } else {
                      //
                    }
                });
            },

            removeUser(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/user/" + id).then(function() {
                            swal("Success! User has been deleted!", {
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