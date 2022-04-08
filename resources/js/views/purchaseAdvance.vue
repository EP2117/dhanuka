<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office</a></li>
                <li class="breadcrumb-item active" aria-current="page">Purchase Advance</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Purchase Advance</h4>
            <router-link :to="'/purchase_advance/new'" class="d-sm-inline-block btn btn-primary shadow-sm inventory" >
                <i class="fas fa-plus"></i> Add Purchase Advance
            </router-link>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="form-group col-md-4 col-lg-3">
                        <label>Advance No</label>
                        <input type="text" class="form-control" autocomplete="off" id="advance_no" name="advance_no" v-model="search.advance_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label >Advance Date</label>
                        <input type="text" class="form-control datetimepicker" id="date" name="date"
                               v-model="search.date">
                    </div>
                    
                    <div class="form-group col-md-4 col-lg-3">
                        <label >Supplier</label>
                        <select id="supplier_id" class="form-control"
                                v-model="search.supplier_id" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="s in suppliers" :value="s.id"  >{{s.name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getAdvance(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Purchase Advance List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="purchase_advance_count > 0">
                    <!-- sort by -->


                    <!-- end sort by -->
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Advance No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Amount</th>

                            <th class="text-center">  </th>
                            <!--Kamlesh -->
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Advance No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">  </th>
                            <!--Kamlesh -->
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr v-for="(advance,index) in purchase_advances">
                            <!--                            <td></td>-->
                            <td class="text-center">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                            <td class="text-center">{{advance.advance_no}}</td>
                            <td class="text-center">{{advance.advance_date}}</td>
                            <td class="text-center">{{advance.supplier.name}}</td>
                            <td class="text-center">{{advance.amount}}</td>
                            <td class="text-left">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item">
                                            <router-link tag="span" :to="'/purchase_advance/edit/'+advance.id" >
                                                <a href="#" title="Edit/View" class="">
                                                    <i class="fas fa-edit"></i>
                                                </a>&nbsp;
                                            </router-link>
                                        </a>

                                        <a class="dropdown-item">
                                            <a title="Delete" class="text-danger" @click="removeAdvance(advance.id)">
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
                    <h5 class="text-center m-5"> Purchase Advance Not found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">
                <!-- pagination start -->
                <div class="row" style="overflow:auto">
                    <div class="col-12">
                        <template v-if="purchase_advance_count > 0">
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
                                    <template v-slot:first-text><span class="text-success" v-on:click="getAdvance(1)">First</span></template>
                                    <template v-slot:prev-text><span class="text-danger" v-on:click="getAdvance(currentPage)">Prev</span></template>
                                    <template v-slot:next-text><span class="text-warning" v-on:click="getAdvance(currentPage)">Next</span></template>
                                    <template v-slot:last-text><span class="text-info" v-on:click="getAdvance(pagination.last_page)">Last</span></template>
                                    <template v-slot:ellipsis-text>
                                    </template>
                                    <template v-slot:page="{ page, active }">
                                        <span v-if="active"><b>{{ page }}</b></span>
                                        <span v-else @click="getAdvance(page)"><p>{{ page }}</p></span>
                                    </template>
                                </b-pagination>
                            </div>
                        </template>
                    </div>
                </div>
                <!-- pagination end -->
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            search:{
                advance_no:'',
                date:'',
                supplier_id:'',
            },
            pagination: {
                total: "",
                next: "",
                prev: "",
                last_page: "",
                current_page: '',
                next_page_url:""
            },
            suppliers:[],
            purchase_advances:[],
            purchase_advance_count:0,
            perPage: 30,
            currentPage: 1,
            rows:'',
            login_from_date: '',
            login_to_date: '',
            user_role:'',
            storage_path:'',
            site_path:'',
            user_year:'',
            login_from_date: '',
            login_to_date: '',
        }
    },
    created() {
        this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
        // console.log(this.perPage);
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

        this.login_from_date = document.querySelector("meta[name='login_from_date']").getAttribute('content');
        alert(this.login_from_date);
        this.login_to_date = document.querySelector("meta[name='login_to_date']").getAttribute('content');

        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
    },
    mounted() {
        let app=this;
        app.initSuppliers();

        app.getAdvance();
        $("#supplier_id").select2();
        $("#supplier_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.search.supplier_id = data.id;
        });
        $("#date")
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
            }).on("dp.show", function(e) {
            var y = new Date().getFullYear();
            // if(app.user_year < y) {
            //     if(app.form.date == app.user_year+"-12-31" ||  app.form.date == '') {
            //         // app.form.date = app.user_year+"-12-31";
            //     }
            // }
        }).on("dp.change", function(e) {
            // alert('a');
            // console.log(e);
            var formatedValue = e.date.format("YYYY-MM-DD");
            app.search.date = formatedValue;
        });
    },
    methods:{
        initSuppliers() {
            axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
            $("#supplier_id").select2();
        },
        getAdvance(page=1){
            //alert('k');
            $("#loading").show();
            // alert(page);
            let app = this;
            var search =
                "&date=" +
                app.search.date +
                "&advance_no=" +
                app.search.advance_no +
                "&supplier_id=" +
                app.search.supplier_id;
            axios.get('/purchase_advance?page='+ page+search ).then(function (response){
                console.log(response.data.data);
                $("#loading").hide();
                app.purchase_advances=response.data.data.data;
                app.purchase_advance_count = app.purchase_advances.length;
                app.pagination.last_page = response.data.data.last_page;
                app.pagination.next = response.data.data.next_page_url;
                app.pagination.prev = response.data.data.prev_page_url;
                app.pagination.total = response.data.data.total;
                app.pagination.current_page = response.data.data.current_page;
                app.pagination.next_page_url = response.data.data.next_page_url;
                app.currentPage = response.data.data.current_page;
                app.rows = response.data.data.total;
            });
        },
        removeAdvance(id) {
            let app = this;
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then(willDelete => {
                if (willDelete) {
                    axios.delete("/purchase_advance/" + id).then(function(response) {
                        if(response.data.message == 'invalid'){
                            swal("Invalid! Purchase Advance has been used!", {
                                icon: "warning"
                            });
                        } else {
                            app.getAdvance();
                            swal("Success! Purchase Advance has been deleted!", {
                                icon: "success"
                            });
                        }
                    });
                    
                }
            });
        },
    }

}
</script>

<style scoped>

</style>
