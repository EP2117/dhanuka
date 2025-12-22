<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">Customer Opening Balance</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Customer Opening Balance</h4>
            <router-link :to="'/customer_ob/create'" class="d-sm-inline-block btn btn-primary shadow-sm inventory" >
                <i class="fas fa-plus"></i> Add New Customer Opening Balance
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
                        <label >Customer</label>
                        <select id="customer_id" class="form-control"
                                v-model="search.customer_id" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="c in customers" :value="c.id"  >{{c.cus_name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getCustomerOB(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Customer Opening Balance List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="customer_ob_count > 0">
                    <!-- sort by -->


                    <!-- end sort by -->
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Invoice No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Customer</th>
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
                            <th class="text-center">Customer</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">  </th>
                            <!--Kamlesh -->
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr v-for="(p,index) in customer_ob">
                            <!--                            <td></td>-->
                            <td class="text-center">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                            <td class="text-center">{{p.invoice_no}}</td>
                            <td class="text-center">{{p.invoice_date}}</td>
                            <td class="text-center">{{p.customer.cus_name}}</td>
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
                                            <router-link tag="span" :to="'/customer_ob/edit/'+p.id" >
                                                <a href="#" title="Edit/View" class="">
                                                    <i class="fas fa-edit"></i>
                                                </a>&nbsp;
                                            </router-link>
                                        </a>

                                        <a class="dropdown-item" v-if="p.collection_amount<=0">
                                            <a title="Delete" class="text-danger" @click="removeCustomerOB(p.id)" v-if=" user_role == 'system'">
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
                    <h5 class="text-center m-5"> Customer Opening Balance Not found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">
                <!-- pagination start -->
                <div class="row" style="overflow:auto">
                    <div class="col-12">
                        <template v-if="customer_ob_count > 0">
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
                                    <template v-slot:first-text><span class="text-success" v-on:click="getCustomerOB(1)">First</span></template>
                                    <template v-slot:prev-text><span class="text-danger" v-on:click="getCustomerOB(currentPage)">Prev</span></template>
                                    <template v-slot:next-text><span class="text-warning" v-on:click="getCustomerOB(currentPage)">Next</span></template>
                                    <template v-slot:last-text><span class="text-info" v-on:click="getCustomerOB(pagination.last_page)">Last</span></template>
                                    <template v-slot:ellipsis-text>
                                    </template>
                                    <template v-slot:page="{ page, active }">
                                        <span v-if="active"><b>{{ page }}</b></span>
                                        <span v-else @click="getCustomerOB(page)"><p>{{ page }}</p></span>
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
                customer_id:'',
            },
            pagination: {
                total: "",
                next: "",
                prev: "",
                last_page: "",
                current_page: '',
                next_page_url:""
            },
            customers:[],
            customer_ob:[],
            customer_ob_count:0,
            perPage: 30,
            currentPage: 1,
            rows:'',
            login_from_date: '',
            login_to_date: '',
            user_role:'',
            storage_path:'',
            site_path:'',
            user_year:'',
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
        this.getCustomerOB();
    },
    mounted() {
        let app=this;
        app.initCustomers();
        $("#customer_id").select2();
        $("#customer_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.search.customer_id = data.id;
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
                minDate: app.login_from_date,
                maxDate: app.login_to_date,

                //minDate: app.user_year+"-01-01",
                //maxDate: app.user_year+"-12-31",
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
            app.search.opening_date = formatedValue;
        });
    },
    methods:{
        initCustomers() {
            axios.get("/customers").then(({ data }) => (this.customers = data.data));
            $("#customer_id").select2();
        },
        getCustomerOB(page=1){
            $("#loading").show();
            // alert(page);
            let app = this;
            var search =
                "&opening_date=" +
                app.search.opening_date +
                "&invoice_no=" +
                app.search.invoice_no +
                "&customer_id=" +
                app.search.customer_id;
            axios.get('/customer_ob?page='+ page+search ).then(function (response){
                console.log(response);
                $("#loading").hide();
                let data=response.data.customer_ob;
                app.customer_ob=data.data;
                app.customer_ob_count = app.customer_ob.length;
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
        removeCustomerOB(id) {
            let app = this;
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then(willDelete => {
                if (willDelete) {
                    axios.delete("/customer_ob/" + id+"/destroy").then(function() {
                        app.getCustomerOB();
                    });
                    swal("Success! Customer Opening Balance has been deleted!", {
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
