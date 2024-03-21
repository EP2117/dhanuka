<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sub Account</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Sub Account</h4>
            <router-link to="/sub_account/new" class="d-sm-inline-block btn btn-primary shadow-sm text-right">
                <i class="fas fa-plus"></i> Add New Sub Account
            </router-link>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3">
                        <label>Sub Account</label>
                        <input type="text" class="form-control" autocomplete="off" id="sub_account" name="sub_account" v-model="search.sub_account_name">
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label >Account Head</label>
                        <select id="account_head_id" class="form-control"
                                name="account_head" v-model="search.account_head" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="ah in account_head" :value="ah.id"  >{{ah.name}}</option>
                        </select>
                    </div>


                    <div class="form-group col-md-4 col-lg-3">
                        <label>Account Type</label>
                        <select id="account_type_id" class="form-control"
                                name="account_type" v-model="search.account_type" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="at in account_type" :value="at.id"  >{{at.name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label>Account Group</label>
                        <select id="account_group_id" class="form-control"
                                name="account_group" v-model="search.account_group" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="at in account_group" :value="at.id"  >{{at.name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getSubAccount(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sub Account List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="sub_account_count > 0">
                    <!-- sort by -->


                    <!-- end sort by -->
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Sub Account Name</th>
                            <th class="text-center">Head Account</th>
                            <th class="text-center">Account Type</th>
                            <th class="text-center">Account Group</th>
                            <th class="text-center">Status</th>

                            <th class="text-center">  </th> <!--Kamlesh -->
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Sub Account Name</th>
                            <th class="text-center">Head Account</th>
                            <th class="text-center">Account Type</th>
                            <th class="text-center">Account Group</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">  </th> <!--Kamlesh -->
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr v-for="(sa,index) in sub_account">
                            <!--                            <td></td>-->
                            <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                            <td class="text-center">{{sa.sub_account_name}}</td>
                            <td class="text-center">{{sa.account_head.name}}</td>
                            <td class="text-center">{{sa.account_type.name}}</td>
                            <td class="text-center" v-if="sa.account_group != null">{{sa.account_group.name}}</td>
                            <td v-else></td>
                            <td class="text-center" v-if="sa.is_active == 1">
                                <span class="badge badge-success">Active</span>
                            </td>
                            <td class="text-center" v-else>
                                <span class="badge badge-danger">Inactive</span>
                            </td>
                            <td class="text-center"></td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item">
                                            <router-link tag="span" :to="'/sub_account/edit/' + sa.id" >
                                                <a href="#" title="Edit/View" class="">
                                                    <i class="fas fa-edit"></i>
                                                </a>&nbsp;
                                            </router-link>
                                        </a>
                                        <a class="dropdown-item">
                                            <a class="badge badge-primary text-white"  @click="changeStatus(sa.id,'inactive')" v-if="sa.is_active == 1">Inactive</a>
                                            <a class="badge badge-primary text-white" @click="changeStatus(sa.id,'active')" v-else>Active</a>
                                        </a>
<!--                                        <a class="dropdown-item">-->
<!--                                            <a title="Delete" class="text-danger" @click="destroySubAccount(sa.id)" v-if="user_role == 'admin' || user_role == 'system'">-->
<!--                                                <i class="fas fa-trash"></i>-->
<!--                                            </a>&nbsp;-->
<!--                                        </a>-->

                                    </div>
                                </div>

                            </td>
                            <!-- Kamlesh End-->

                        </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No Sub Account found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">
                <!-- pagination start -->
                <div class="row" style="overflow:auto">
                    <div class="col-12">
                        <template v-if="sub_account_count > 0">
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
                                    <template v-slot:first-text><span class="text-success" v-on:click="getSubAccount(1)">First</span></template>
                                    <template v-slot:prev-text><span class="text-danger" v-on:click="getSubAccount(currentPage)">Prev</span></template>
                                    <template v-slot:next-text><span class="text-warning" v-on:click="getSubAccount(currentPage)">Next</span></template>
                                    <template v-slot:last-text><span class="text-info" v-on:click="getSubAccount(pagination.last_page)">Last</span></template>
                                    <template v-slot:ellipsis-text>
                                    </template>
                                    <template v-slot:page="{ page, active }">
                                        <span v-if="active"><b>{{ page }}</b></span>
                                        <span v-else @click="getSubAccount(page)"><p>{{ page }}</p></span>
                                    </template>
                                </b-pagination>
                            </div>
                        </template>
                    </div>
                </div>
                <!-- pagination end -->
            </div>
        </div>
        <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div>

    </div>

</template>

<script>
export default {
    data(){
        return{
            search:{
                sub_account_name:'',
                account_head:'',
                account_type:'',
                account_group: '',
            },
            pagination: {
                total: "",
                next: "",
                prev: "",
                last_page: "",
                current_page: '',
                next_page_url:""
            },
            account_head:[],
            account_type:[],
            account_group:[],
            perPage: 30,
            currentPage: 1,
            sub_account_count: 0,
            sub_account:[],
            rows:'',
        }
    },
    created(){
        // console.log(this.perPage);
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

        if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
            var url =  window.location.origin;
            window.location.replace(url);
        }
        this.getSubAccount();
    },
    mounted(){
        $("#loading").hide();
        var vm=this;
        vm.initAccountHead();
        vm.initAccountType();
        vm.initAccountGroup();
    },
    methods:{
        initAccountHead(){
            axios.get('/sub_account/get_account_head').then(({data})=>(this.account_head=data.account_head));

        },
        initAccountGroup(){
            axios.get('/sub_account/get_account_group').then(({data})=>(this.account_group=data.account_group));
        },
        initAccountType(){
            axios.get('/sub_account/get_account_type').then(({data})=>(this.account_type=data.account_type));
        },
        getSubAccount(page=1) {
            $("#loading").show();
            // alert(page);

            let app = this;
            var search =
                "&sub_account_name=" +
                app.search.sub_account_name +
                "&account_head=" +
                app.search.account_head +
                "&account_group=" +
                app.search.account_group +
                "&account_type=" +
                app.search.account_type;
            axios.get('/sub_account/get_all?page='+ page + search).then(function (response){
                $("#loading").hide();
                // console.log(response);
                let data=response.data.sub_account;
                console.log(data);
                app.sub_account=data.data;
                // console.log(app.sub_account);
                // if(typeof app.sub_account!== "undefined"){
                    app.sub_account_count = app.sub_account.length;
                // }
                app.pagination.last_page = data.last_page;
                app.pagination.next = data.next_page_url;
                app.pagination.prev = data.prev_page_url;
                app.pagination.total = data.total;
                app.pagination.current_page = data.current_page;
                app.pagination.next_page_url = data.next_page_url;
                app.currentPage = data.current_page;
                app.rows = data.total;
            });
        },
        destroyAccountHead(id){
            let app = this;
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then(willDelete => {
                if (willDelete) {
                    axios.delete("/account_head/destroy/" + id).then(function() {
                        swal("Success! Account Head has been deleted!", {
                            icon: "success"
                        });
                        app.getAccountHead();
                    });
                } else {

                }
            });
        },
        changeStatus(id,status) {
            let app = this;
            var active = status;
            var sub_id = id;

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
                        .get("/sub_account/change_status/" + sub_id + "/" + active)
                        .then(function(response) {
                            $("#loading").hide();
                            app.getSubAccount(app.currentPage);
                            swal("Success! Customer has been updated as " + active+".", {
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
