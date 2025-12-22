<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">Supplier Opening Balance</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Supplier Opening Balance</h4>
            <router-link :to="'/supplier_ob/create'" class="d-sm-inline-block btn btn-primary shadow-sm inventory" >
                <i class="fas fa-plus"></i> Add New Opening Balance
            </router-link>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3">
                        <label >Opening Date</label>
                        <input type="text" class="form-control datetimepicker" id="date" name="opening_date"
                               v-model="search.opening_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label>Invoice No</label>
                        <input type="text" class="form-control" autocomplete="off" id="invoice_no" name="invoice_no" v-model="search.invoice_no">
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label >Supplier</label>
                        <select id="supplier_id" class="form-control"
                                v-model="search.supplier_id" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="sup in suppliers" :key="sup.id"  >{{sup.name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getSupplierOB(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Supplier Opening Balance List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="supplier_ob_count > 0">
                    <!-- sort by -->


                    <!-- end sort by -->
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Invoice No</th>
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
                            <th class="text-center">Invoice No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">  </th>
                            <!--Kamlesh -->
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr v-for="(p,index) in supplier_ob" :key="p.id">
                            <!--                            <td></td>-->
                            <td class="text-center">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                            <td class="text-center">{{p.invoice_no}}</td>
                            <td class="text-center">{{p.invoice_date}}</td>
                            <td class="text-center">{{p.supplier.name}}</td>
                            <td class="text-center">{{p.total_amount}}</td>
                            <!--                            <td class="text-center" v-if="sa.is_active == 1">-->
                            <!--                                <span class="badge badge-success">Active</span>-->
                            <!--                            </td>-->
                            <!--                            <td class="text-center" v-else>-->
                            <!--                                <span class="badge badge-danger">Inactive</span>-->
                            <!--                            </td>-->
                            <!--                            <td class="text-center"></td>-->
                            <td class="text-left">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item">
                                            <router-link tag="span" :to="'/supplier_ob/edit/'+p.id" >
                                                <a href="#" title="Edit/View" class="">
                                                    <i class="fas fa-edit"></i>
                                                </a>&nbsp;
                                            </router-link>
                                        </a>

                                        <a class="dropdown-item" v-if="p.collection_amount<=0">
                                            <a title="Delete" class="text-danger" @click="removeSupplierOB(p.id)" v-if="user_role == 'admin' || user_role == 'system'">
                                                <i class="fas fa-trash"></i>
                                            </a>&nbsp;
                                        </a>
                                        <!--                                        <a class="dropdown-item">-->
                                        <!--                                            <a class="badge badge-primary text-white"  @click="changeStatus(sa.id,'inactive')" v-if="sa.is_active == 1">Inactive</a>-->
                                        <!--                                            <a class="badge badge-primary text-white" @click="changeStatus(sa.id,'active')" v-else>Active</a>-->
                                        <!--                                        </a>-->
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
                    <h5 class="text-center m-5">No Supplier Opening Balance found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">
                <!-- pagination start -->
                <div class="row" style="overflow:auto">
                    <div class="col-12">
                        <template v-if="supplier_ob_count > 0">
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
                                    <template v-slot:first-text><span class="text-success" v-on:click="getSupplierOB(1)">First</span></template>
                                    <template v-slot:prev-text><span class="text-danger" v-on:click="getSupplierOB(currentPage)">Prev</span></template>
                                    <template v-slot:next-text><span class="text-warning" v-on:click="getSupplierOB(currentPage)">Next</span></template>
                                    <template v-slot:last-text><span class="text-info" v-on:click="getSupplierOB(pagination.last_page)">Last</span></template>
                                    <template v-slot:ellipsis-text>
                                    </template>
                                    <template v-slot:page="{ page, active }">
                                        <span v-if="active"><b>{{ page }}</b></span>
                                        <span v-else @click="getSupplierOB(page)"><p>{{ page }}</p></span>
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
               invoice_no:'',
               opening_date:'',
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
            supplier_ob:[],
            supplier_ob_count:0,
            perPage: 30,
            currentPage: 1,
            rows:'',
        }
    },
    created() {
        this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
        // console.log(this.perPage);
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
        this.getSupplierOB();
    },
    mounted() {
        let app=this;
        app.initSuppliers();
        $("#supplier_id").select2();
        $("#supplier_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.form.supplier_id = data.id;
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
            app.form.opening_date = formatedValue;
        });
    },
    methods:{
        initSuppliers() {
            axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
            $("#supplier_id").select2();
        },
        getSupplierOB(page=1){
            $("#loading").show();
            // alert(page);
            let app = this;
            var search =
                "&opening_date=" +
                app.search.opening_date +
                "&invoice_no=" +
                app.search.invoice_no +
                "&supplier_id=" +
                app.search.supplier_id;
            axios.get('/supplier_ob?page='+ page+search ).then(function (response){
                console.log(response);
                $("#loading").hide();
                let data=response.data.supplier_ob;
                app.supplier_ob=data.data;
                app.supplier_ob_count = app.supplier_ob.length;
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
        removeSupplierOB(id) {
            let app = this;
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then(willDelete => {
                if (willDelete) {
                    axios.delete("/supplier_ob/" + id+"/destroy").then(function() {
                        app.getSupplierOB();
                    });
                    swal("Success! Supplier Opening Balance has been deleted!", {
                        icon: "success"
                    });
                }
            });
        },
    }

}
</script>

<style scoped>

</style>
