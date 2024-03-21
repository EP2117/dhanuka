<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/account'">Account</a></li>
                <li class="breadcrumb-item active" aria-current="page">Journal Entry</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Journal Entry</h4>
            <router-link to="/journal_entry/new" class="d-sm-inline-block btn btn-primary shadow-sm text-right">
                <i class="fas fa-plus"></i> Add Journal Entry
            </router-link>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="from_date">From Date</label>
                        <input type="text" class="form-control datetimepicker" id="from_date" name="from_date"
                               v-model="search.from_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="to_date">To Date</label>
                        <input type="text" class="form-control datetimepicker" id="to_date" name="to_date"
                               v-model="search.to_date">
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label>Journal Entry No</label>
                        <input type="text" class="form-control" autocomplete="off" id="sub_account" name="sub_account" v-model="search.journal_entry_no">
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label >Credit</label>
                        <select id="debit_id" class="form-control"
                                v-model="search.debit" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="d in debit" :value="d.id"  >{{d.sub_account_name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label>Debit</label>
                        <select id="credit_id" class="form-control"
                                v-model="search.credit" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="c in debit" :value="c.id"  >{{c.sub_account_name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getEntry(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Journal Entry List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="entry_count > 0">
                    <!-- sort by -->


                    <!-- end sort by -->
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Journal Entry No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Remark</th>

                            <th class="text-center">  </th>
                            <!--Kamlesh -->
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Journal Entry No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Remark</th>
                            <th class="text-center">  </th>
                            <!--Kamlesh -->
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr v-for="(je,index) in entries">
                            <!--                            <td></td>-->
                            <td class="text-center">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                            <td class="text-center">{{je.journal_entry_no}}</td>
                            <td class="text-center">{{je.date}}</td>
                            <td class="text-center">{{je.debit.sub_account_name}}</td>
                            <td class="text-center">{{je.credit.sub_account_name}}</td>
                            <td class="text-center">{{je.amount}}</td>
                            <td class="text-center">{{je.remark}}</td>

                            <!--                            <td class="text-center" v-if="sa.is_active == 1">-->
                            <!--                                <span class="badge badge-success">Active</span>-->
                            <!--                            </td>-->
                            <!--                            <td class="text-center" v-else>-->
                            <!--                                <span class="badge badge-danger">Inactive</span>-->
                            <!--                            </td>-->
                            <!--                            <td class="text-center"></td>-->
                            <td class="text-left" v-if="user_role=='admin' || user_role=='system'">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow" >
                                        <a class="dropdown-item">
                                            <router-link tag="span" :to="'/journal_entry/edit/' + je.id" >
                                                <a href="#" title="Edit/View" class="">
                                                    <i class="fas fa-edit"></i>
                                                </a>&nbsp;
                                            </router-link>
                                        </a>
                                        <a class="dropdown-item">
                                            <a title="Delete" class="text-danger" @click="destroyEntry(je.id)" v-if="user_role == 'admin' || user_role == 'system'">
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
                    <h5 class="text-center m-5">No Record found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">
                <!-- pagination start -->
                <div class="row" style="overflow:auto">
                    <div class="col-12">
                        <template v-if="entry_count > 0">
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
                                    <template v-slot:first-text><span class="text-success" v-on:click="getEntry(1)">First</span></template>
                                    <template v-slot:prev-text><span class="text-danger" v-on:click="getEntry(currentPage)">Prev</span></template>
                                    <template v-slot:next-text><span class="text-warning" v-on:click="getEntry(currentPage)">Next</span></template>
                                    <template v-slot:last-text><span class="text-info" v-on:click="getEntry(pagination.last_page)">Last</span></template>
                                    <template v-slot:ellipsis-text>
                                    </template>
                                    <template v-slot:page="{ page, active }">
                                        <span v-if="active"><b>{{ page }}</b></span>
                                        <span v-else @click="getEntry(page)"><p>{{ page }}</p></span>
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
                from_date:'',
                to_date:'',
                journal_entry_no:'',
                debit:'',
                credit:'',
            },
            pagination: {
                total: "",
                next: "",
                prev: "",
                last_page: "",
                current_page: '',
                next_page_url:""
            },
            entries:[],
            entry_count:0,
            perPage: 30,
            currentPage: 1,
            rows:'',
            user_year:'',
            user_role:'',
            credit:[],
            debit:[],

        }
    },
    created() {
        // console.log(this.perPage);
        this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

        if (this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
            var url = window.location.origin;
            window.location.replace(url);
        }
        this.getEntry();
    },
    mounted() {
        let app=this;
        this.initDebit();
        this.initCredit();
        $("#from_date")
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
            })
            .on("dp.show", function(e) {
                var y = new Date().getFullYear();
                if(app.user_year < y) {
                    if(app.search.from_date == app.user_year+"-12-31" || app.search.from_date == '') {
                        app.search.from_date = app.user_year+"-12-31";
                    }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.from_date = formatedValue;
            });

        $("#to_date")
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
            })
            .on("dp.show", function(e) {
                var y = new Date().getFullYear();
                if(app.user_year < y) {
                    if(app.search.to_date == app.user_year+"-12-31" || app.search.to_date == '') {
                        app.search.to_date = app.user_year+"-12-31";
                    }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.to_date = formatedValue;
            });
    },
    methods:{
        initDebit(){
            axios.get('/sub_account/get_sub_account/'+"debit").then(({data})=>(this.debit=data.sub_account));
        },
        initCredit(){
            axios.get('/sub_account/get_sub_account/'+"credit").then(({data})=>(this.credit=data.sub_account));
        },
        getEntry(page=1) {
            $("#loading").show();
            // alert(page);
            let app = this;
            var search =
                "&from_date=" +
                app.search.from_date +
                "&to_date=" +
                app.search.to_date +
                "&journal_entry_no=" +
                app.search.journal_entry_no +
                "&debit=" +
                app.search.credit +
                "&credit=" +
                app.search.debit;
            axios.get('/journal_entry/get_all?page='+ page+search ).then(function (response){
                $("#loading").hide();
                // console.log(response);
                let data=response.data.entry;
                app.entries=data.data;
                // console.log(app.sub_account);
                // if(typeof app.sub_account!== "undefined"){
                app.entry_count = app.entries.length;
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
        destroyEntry(id) {
            let app = this;
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then(willDelete => {
                if (willDelete) {
                    axios.delete("/journal_entry/destroy/" + id).then(function() {
                        swal("Success! Journal Entry has been deleted!", {
                            icon: "success"
                        });
                        app.getEntry();
                    });
                } else {
                    //
                }
            });
        },
    }
}
</script>

<style scoped>

</style>
