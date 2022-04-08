<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/account'">Account</a></li>
                <li class="breadcrumb-item active" aria-current="page">CashBook</li>
            </ol>
        </nav>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="invoice_no">Invoice No.</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="search.vochur_no">
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="from_date">From Date</label>
                        <input type="text" autocomplete="off" class="form-control datetimepicker" id="from_date" name="from_date"
                               v-model="search.from_date">
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="to_date">To Date</label>
                        <input type="text" class="form-control datetimepicker" id="to_date" name="to_date"
                               v-model="search.to_date">
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="supplier_id">Sub Account</label>
                        <select id="supplier_id" class="form-control mm-txt"
                                name="sub_account_id" v-model="search.sub_account" style="width:100%" required>
                            <option value="">Select One</option>
                            <option v-for="s in sub_account" :value="s.id"  >{{s.sub_account_name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getCashbook(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Cashbook List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="cashbook_count > 0">
                    <!-- sort by -->
                    <!-- end sort by -->
                    <table class="table table-bordered " id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Vochur</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Account</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Vochur</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Description</th>
                            <th class="text-center" >Account</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                        </tr>
                        </tfoot>
                            <tbody id="c_body">

                        <template v-for="(at ,index) in cashbook">
                            <tr class="total_row"   v-if="at.opening_balance != 0 && !at.hide " >
                                <td colspan="5" class="text-right mm-txt"><strong>Opening Balance</strong></td>
                                <td class="text-center" colspan="1" v-if="at.opening_balance > 0 ">
                                    {{decimalFormat(at.opening_balance)}}
                                </td>
                                <td class="text-center" colspan="1" v-if="at.opening_balance < 0 ">
                                </td>
                                <td class="text-center" colspan="1" v-if="at.opening_balance < 0 ">
                                    {{decimalFormat(at.opening_balance*(-1))}}
                                </td>
                                <td class="text-center" colspan="1" v-if="at.opening_balance >0 ">
                                </td>
                            </tr>
                            <template v-if="at.cashbook_list.length>0">
                                <tr v-for="(c,key) in at.cashbook_list">
                                    <td class="text-right"></td>
                                    <td class="text-center">{{c.vochur_no}}</td>
                                    <td class="text-center">{{c.transition_date}}</td>
                                    <!--                            <td class="text-center">{{c.vochur_no}}</td>-->
                                    <td class="text-center">{{c.description}}</td>
                                    <td class="text-center" style="right: 4px ">{{c.sub_account.sub_account_name}}</td>
                                    <td class="text-center">{{c.debit!='' && c.debit != null ? decimalFormat(c.debit) : ''}} </td>
                                    <td class="text-center">{{c.credit!='' && c.credit != null? decimalFormat(c.credit) : ''}} </td>

                                </tr>
                            </template>
                            <template v-else-if="at.cashbook_list.length<=0 && !at.hide">
                                <tr>
                                    <td class="text-right"></td>
                                    <td class="text-center"></td>
                                    <td >{{at.date}}</td>
                                </tr>
                            </template>
                            
                            <tr class="total_row"    v-if="at.cashbook_list.length>0 && !at.hide">
                                <td colspan="5" class="text-right mm-txt"><strong>DailyTotal</strong></td>
                                <td class="text-center" colspan="1">
                                    {{decimalFormat(at.total_debit)}}
                                </td>
                                <td class="text-center" colspan="1">
                                    {{decimalFormat(at.total_credit)}}
                                </td>

                            </tr>
                            <tr class="total_row" v-if="!at.hide">
                                <td colspan="5" class="text-right mm-text"><strong>Closing Balance</strong></td>
                                <td class="text-center " colspan="1" v-if="at.closing_balance>0">
                                    {{decimalFormat(at.closing_balance)}}
                                    <!--                                121222-->
                                </td>
                                <td class="text-center " colspan="1" v-else-if="at.closing_balance<0">
                                    <!--                                121222-->
                                </td>
                                <td class="text-center " colspan="1" v-if="at.closing_balance < 0">
                                    {{decimalFormat(at.closing_balance*(-1))}}
                                    <!--                                121222-->
                                </td>
                                <td class="text-center " colspan="1" v-else-if="at.closing_balance > 0">
                                </td>
                            </tr>
                        </template>
                            </tbody>

                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No CashBook found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">
                <!-- pagination start -->
                <div class="row" style="overflow:auto">
                    <div class="col-12">
                        <template v-if="cashbook_count > 0">
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
                                    <template v-slot:first-text><span class="text-success" v-on:click="getCashbook(1)">First</span></template>
                                    <template v-slot:prev-text><span class="text-danger" v-on:click="getCashbook(currentPage)">Prev</span></template>
                                    <template v-slot:next-text><span class="text-warning" v-on:click="getCashbook(currentPage)">Next</span></template>
                                    <template v-slot:last-text><span class="text-info" v-on:click="getCashbook(pagination.last_page)">Last</span></template>
                                    <template v-slot:ellipsis-text>
                                    </template>
                                    <template v-slot:page="{ page, active }">
                                        <span v-if="active"><b>{{ page }}</b></span>
                                        <span v-else @click="getCashbook(page)"><p>{{ page }}</p></span>
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
                sub_account:'',
                from_date:'',
                to_date:'',
                vochur_no:'',
            },
            pagination: {
                total: "",
                next: "",
                prev: "",
                last_page: "",
                current_page: '',
                next_page_url:""
            },
            sub_account:[],
            opening_balance:'',
            closing_balance:'',
            cashbook:[],
            perPage: 30,
            currentPage: 1,
            cashbook_count: 0,
            count_of_days:0,
            total_debit:'',
            total_credit:'',
            user_year:'',
            rows:'',
            row_no:'',
        }
    },
    created(){
        // console.log(this.perPage);
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
        this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

        if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
            var url =  window.location.origin;
            window.location.replace(url);
        }
        // this.getCashbook();
    },
    mounted(){
        $("#loading").hide();
        var app=this;
        app.initSubAccount();
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
                console.log(app.user_year);
                // if(app.user_year < y) {
                //     if(app.search.from_date == app.user_year+"-12-31" || app.search.from_date == '') {
                //         app.search.from_date = app.user_year+"-12-31";
                //     }
                // }
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
            }).on("dp.show", function(e) {
            var y = new Date().getFullYear();
            // if(app.user_year < y) {
            //     if(app.search.to_date == app.user_year+"-12-31" || app.search.to_date == '') {
            //         app.search.to_date = app.user_year+"-12-31";
            //     }
            // }
        })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.to_date = formatedValue;
            });
    },
    methods:{

        decimalFormat(num)
        {
           var decimal_num = Number.isInteger(parseFloat(num))== true ?  parseInt(num) : parseFloat(num).toFixed(2);
           return decimal_num;
        },

        initSubAccount(){
            axios.get('/sub_account/get_all_sub_account').then(({data})=>(this.sub_account=data.sub_account));
        },
        getCashbook(page=1){
            let app = this;
            if(this.search.from_date == "") {
                swal("Warning!", "From Date must be added!", "warning")
                return false;
            }
            // if(app.search.from_date == null && app.search.to_date ==null){
            //     swal({
            //     title: "Required to fill date ",
            //     // text: "Once deleted, you will not be able to recover!",
            //     icon: "warning",
            //     buttons: true,
            //     dangerMode: true
            // });
            // }
            $("#loading").show();

            var search =
                "&sub_account=" +
                app.search.sub_account +
                "&vochur_no=" +
                app.search.vochur_no +
                "&from_date=" +
                app.search.from_date +
                "&to_date=" +
                app.search.to_date;
            axios.get('/report/get_all_cashbook?page='+ page + search).then(function (response){
                $("#loading").hide();
                // var a=$("#c_body tr").length;
                // var rows = document.getElementById("dataTable").rows.length;

                // var rows = document.getElementById('dataTable').getElementsByTagName("tr").length;
                console.log(response.data.cashbook);
                let data=response.data.cashbook;
                app.cashbook=data;
                app.cashbook_count=data.length;
                // app.count_of_days=response.data.count_of_days;




                //     app.opening_balance=response.data.opening_balance;
                //     if(response.data.closing_balance < 0){
                //         console.log('a');
                //     }else if(response.data.closing_balance >0);
                //     app.closing_balance=response.data.closing_balance;
                //     app.total_debit=response.data.total_debit;
                //     app.total_credit=response.data.total_credit;
                //     app.cashbook=data.data;
                //     app.cashbook_count = app.cashbook.length;
                //     app.pagination.last_page = data.last_page;
                //     app.pagination.next = data.next_page_url;
                //     app.pagination.prev = data.prev_page_url;
                //     app.pagination.total = data.total;
                //     app.pagination.current_page = data.current_page;
                //     app.pagination.next_page_url = data.next_page_url;
                //     app.currentPage = data.current_page;
                //     app.rows = data.total;
            });

        }

    }
}
</script>


