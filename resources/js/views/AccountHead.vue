<template>
<!--    <h1>Hello World</h1>-->
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">Account Head</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Account Head</h4>
            <router-link to="/account_head/new" class="d-sm-inline-block btn btn-primary shadow-sm text-right">
                <i class="fas fa-plus"></i> Add New Account Head
            </router-link>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3">
                        <label>Account Head</label>
                        <input type="text" class="form-control" id="acc_head" name="acc_head" v-model="search.account_head">
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label >Financial Type1</label>
                        <select id="type1_id" class="form-control"
                                name="financial_type1_id" v-model="search.financial_type1" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="f_type in financial_type1" :value="f_type.id"  >{{f_type.name}}</option>
                        </select>
                    </div>


                    <div class="form-group col-md-4 col-lg-3">
                        <label>Financial Type2</label>
                        <select id="type2_id" class="form-control"
                                name="type1_id" v-model="search.financial_type2" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="f_type in financial_type2" :value="f_type.id"  >{{f_type.name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getAccountHead(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Account Head List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="account_head_count > 0">
                    <!-- sort by -->


                    <!-- end sort by -->
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Fanincail Type 1</th>
                            <th class="text-center">Fanincial Type 2</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">  </th> <!--Kamlesh -->
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Fanincail Type 1</th>
                            <th class="text-center">Fanincial Type 2</th>
                            <th class="text-center">Status</th>

                            <th class="text-center">  </th> <!--Kamlesh -->
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr v-for="(ah,index) in account_head">
                            <!--                            <td></td>-->
                            <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                            <td class="text-center">{{ah.name}}</td>
                            <td class="text-center">{{ah.financial_type1.name}}</td>
                            <td class="text-center">{{ah.financial_type2.name}}</td>
                            <td class="text-center" v-if="ah.is_active == 1">
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
                                            <router-link tag="span" :to="'/account_head/edit/' + ah.id" >
                                                <a href="#" title="Edit/View" class="">
                                                    <i class="fas fa-edit"></i>
                                                </a>&nbsp;
                                            </router-link>
                                        </a>
                                        <a class="dropdown-item">
                                            <a class="badge badge-primary text-white"  @click="changeStatus(ah.id,'inactive')" v-if="ah.is_active == 1">Inactive</a>
                                            <a class="badge badge-primary text-white" @click="changeStatus(ah.id,'active')" v-else>Active</a>
                                        </a>
                                        <a class="dropdown-item">
                                            <a title="Delete" class="text-danger" @click="destroyAccountHead(ah.id)" v-if="user_role == 'admin' || user_role == 'system'">
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
                    <h5 class="text-center m-5">No Account Head found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">

                <!-- pagination start -->
                <div class="row" style="overflow:auto">
                    <div class="col-12">
                        <template v-if="account_head_count > 0">
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
                                    <template v-slot:first-text><span class="text-success" v-on:click="getAccountHead(1)">First</span></template>
                                    <template v-slot:prev-text><span class="text-danger" v-on:click="getAccountHead(currentPage)">Prev</span></template>
                                    <template v-slot:next-text><span class="text-warning" v-on:click="getAccountHead(currentPage)">Next</span></template>
                                    <template v-slot:last-text><span class="text-info" v-on:click="getAccountHead(pagination.last_page)">Last</span></template>
                                    <template v-slot:ellipsis-text>
                                    </template>
                                    <template v-slot:page="{ page, active }">
                                        <span v-if="active"><b>{{ page }}</b></span>
                                        <span v-else @click="getAccountHead(page)">{{ page }}</span>
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
                account_head:'',
                financial_type1:'',
                financial_type2:'',
            },
            pagination: {
                total: "",
                next: "",
                prev: "",
                last_page: "",
                current_page: '',
                next_page_url:""
            },
            financial_type1:[],
            financial_type2:[],
            perPage: 30,
            currentPage: 1,
            account_head_count: 0,
            account_head:[],
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
            //window.location.replace(url);
        }
        this.getAccountHead();
    },
    mounted(){
        $("#loading").hide();

        var vm=this;
        vm.initFinancialType1();
        vm.initFinancialType2();
    },
    methods:{


        initFinancialType1(){
            axios.get('/account_head/get_financial_type/'+ 1).then(({data})=>(this.financial_type1=data.financial_type));
            $("#financial_type1_id").select2();

        },
        initFinancialType2(){
            axios.get('/account_head/get_financial_type/'+ 2).then(({data})=>(this.financial_type2=data.financial_type));
            $("#financial_type2_id").select2();
        },
        getAccountHead(page=1) {
            $("#loading").show();
            // alert(page);

            let app = this;
            var search =
                "&name=" +
                app.search.account_head +
                "&financial_type1_id=" +
                app.search.financial_type1 +
                "&financial_type2_id=" +
                app.search.financial_type2;
            axios.get('/account_head/get_all?page='+ page + search).then(function (response){
                $("#loading").hide();
                let data=response.data.account_head;
                console.log(data);
                app.account_head=data.data;
                app.account_head_count = app.account_head.length;
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
        changeStatus(id,status) {
            let app = this;
            var active = status;
            var acc_id = id;

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
                        .get("/account_head/change_status/" + acc_id + "/" + active)
                        .then(function(response) {
                            $("#loading").hide();
                            app.getAccountHead(app.currentPage);
                            swal("Success! Customer has been updated as " + active+".", {
                                icon: "success"
                            });
                        });

                } else {

                }
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
        }

    }
}
</script>
<style scoped>

</style>
