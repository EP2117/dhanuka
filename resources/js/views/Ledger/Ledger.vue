<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/account'">Account</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ledger</li>
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
                        <label> Type</label>
                        <select id="type_id" class="form-control"
                                name="type" v-model="search.type" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="(t,index) in types" :value="index+1"  :key='index+1'>{{t}}</option>
                        </select>
                    </div>
                     <div class="form-group col-md-4 col-lg-3 other" v-show="this.type==='other' ">
                        <label >Account Head</label>
                        <select id="account_head_id" class="form-control"
                                name="account_head" required v-model="search.account_head" style="width:100%">
                            <option value="" >Select One</option>
                            <option v-for="ah in account_head" :value="ah.id"  :key='ah.id'>{{ah.name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3 other " v-show="this.type==='other' ">
                        <label for="sub_account_id">Sub Account</label>
                        <select id="sub_account_id" class="form-control mm-txt"
                                name="sub_account_id" v-model="search.sub_account" style="width:100%" required>
                            <option value="">Select One</option>
                            <option v-for="s in sub_account" :value="s.id"  :key='s.id'>{{s.sub_account_name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3 customer" v-show="this.type==='customer' ">
                        <label for="customer_id">Customer</label>
                        <select id="customer_id" class="form-control mm-txt"
                            name="customer_id" v-model="search.customer_id" style="width:100%" required>
                            <option value="">Select One</option>
                            <option v-for="cus in customers" :value="cus.id"  :key='cus.id'>{{cus.cus_name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3 supplier" v-show="this.type==='supplier' ">
                        <label for="supplier_id">Supplier</label>
                        <select id="supplier_id" class="form-control mm-txt"
                                name="supplier_id" v-model="search.supplier_id" style="width:100%" required>
                            <option value="">Select One</option>
                            <option v-for="sup in suppliers" :value="sup.id"  :key='sup.id'>{{sup.name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getLedger(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ledger List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered " id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                        <tr>
                            <th clAass="text-center">No.</th>
                            <th class="text-center">Vochur</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Description</th>
                            <!-- <th class="text-center">Account</th> -->
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
                            <!-- <th class="text-center" >Account</th> -->
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                        </tr>
                        </tfoot>
                        <tbody id="c_body">
                            <template v-for="(at ,index) in ledger" >
                                <tr class="total_row">
                                    <!-- <template v-if="type=='other'"> -->
                                        <td colspan="4" class="text-right mm-txt"><strong>Opening Balance</strong></td>
                                        <td class="text-center" colspan="1" v-if="at.opening_balance >= 0 ">
                                            {{decimalFormat(at.opening_balance)}}
                                        </td>
                                        <td class="text-center" colspan="1" v-if="at.opening_balance < 0 ">
                                        </td>
                                        <td class="text-center" colspan="1" v-if="at.opening_balance < 0 ">
                                            {{decimalFormat(at.opening_balance*(-1))}}
                                        </td>
                                        <td class="text-center" colspan="1" v-if="at.opening_balance >0 ">
                                        </td>
                                    <!-- </template> -->
                                    <!-- <template v-else-if="type=='customer'">
                                         <td colspan="4" class="text-right mm-txt"><strong>Opening Balance</strong></td>
                                         <td class="text-center" colspan="1" v-if="at.opening_balance > 0 ">
                                        </td>
                                        <td class="text-center" colspan="1" v-else-if="at.opening_balance < 0 ">
                                            {{at.opening_balance* (-1)}}

                                        </td>
                                       
                                    </template> -->
                                </tr>
                                <template v-if="at.ledger_list.length>0">
                                    <tr v-for="(c,key) in at.ledger_list" >
                                        <td class="text-right"></td>
                                        <td class="text-center">{{c.vochur_no}}</td>
                                        <td class="text-center">{{c.transition_date}}</td>
                                        <!-- <td class="text-center">{{c.vochur_no}}</td> -->
                                        <template >
                                             <td class="text-center" v-if="type=='customer'" >
                                                 <span v-if='c.sub_account.sub_account_name=="Sale Account" || c.sub_account.sub_account_name=="Cash Sale"'>
                                                    To {{c.sub_account.sub_account_name }} For Inv {{c.vochur_no}} Invoice
                                                 </span>
                                                <span v-else>
                                                    By {{c.sub_account.sub_account_name }} For Inv {{c.vochur_no}} Invoice
                                                 </span>
                                             </td>
                                                <td class="text-center" v-if="type=='supplier'">
                                                <span v-if='c.sub_account.sub_account_name=="Purchase Account" || c.sub_account.sub_account_name=="Cash Purchase"'>
                                                    To {{c.sub_account.sub_account_name }} For Inv {{c.vochur_no}} Invoice
                                                 </span>
                                                <span v-else>
                                                    By {{c.sub_account.sub_account_name }} For Inv {{c.vochur_no}} Invoice
                                                 </span>
                                                <!-- By {{c.sub_account.sub_account_name }} For Inv {{c.vochur_no}} Invoice -->
                                                </td>
                                            <td class="text-center" v-if=' type=="other" && c.supplier_id!=null'>

                                              By {{c.supplier.name }} For Inv {{c.vochur_no}} Invoice</td>
                                            <td class="text-center" v-if=' type=="other" && c.customer_id!=null'>
                                               By {{c.customer.cus_name }} For Inv {{c.vochur_no}} Invoice</td> 
                                            <td class="text-center" v-if=' type=="other" && c.payment_id!=null'>
                                                {{c.description}}
                                             </td> 
                                            <td class="text-center" v-if='type=="other" && (c.status =="opening payment" || c.status == "opening receipt")'>
                                                {{c.description}}
                                             </td>
                                             <td class="text-center" v-if=' type=="other" && c.receipt_id!=null'>
                                                {{c.description}}
                                             </td>  
                                        </template>
                                        <!-- <template v-if="type=='customer'">
                                             <td class="text-center" >
                                            To {{c.sub_account.sub_account_name }} For Inv {{c.vochur_no}} Invoice</td>
                                        </template>
                                        <template v-if="type=='supplier'">
                                             <td class="text-center" >
                                            By {{c.sub_account.sub_account_name }} For Inv {{c.vochur_no}} Invoice</td>
                                        </template>
                                        <template v-if="type=='other'">
                                             <td class="text-center" v-if='c.supplier_id!=null'>
                                            By {{c.supplier.name }} For Inv {{c.vochur_no}} Invoice</td>
                                            <td class="text-center" v-if='c.customer_id!=null'>
                                            By {{c.cus_name }} For Inv {{c.vochur_no}} Invoice</td>
                                        </template> -->
                                        <template v-if="type=='customer'">
                                            <td class="text-center" >{{c.debit != '' && c.credit != null  ? decimalFormat(Math.abs(c.credit)) : ''}} </td>
                                            <td class="text-center" >{{c.credit !='' && c.debit != null  ? decimalFormat(Math.abs(c.debit)) : ''}}</td>
                                        </template>
                                        <template v-if="type=='supplier'">
                                            <td class="text-center">{{c.debit !='' &&  c.credit != null ? decimalFormat(Math.abs(c.credit)) : ''}} </td>
                                            <td class="text-center" >{{c.credit !='' &&  c.debit != null ? decimalFormat(Math.abs(c.debit)) : ''}} </td>
                                        </template>
                                         <template v-if="type=='other'">
                                            <td class="text-center">{{c.debit !='' && c.debit != null ? decimalFormat(Math.abs(c.debit)) : ''}} </td>
                                            <td class="text-center" >{{c.credit !='' && c.credit != null ? decimalFormat(Math.abs(c.credit)) : ''}} </td>
                                        </template>
                                        <!-- <td class="text-center">{{c.debit !='' && c.debit != null ?c.debit: ''}} </td> -->
                                        <!-- <td class="text-center" v-show="this.type=='customer'">{{c.credit !='' ?c.credit: ''}} </td> -->
                                        <!-- <td class="text-center">{{c.debit ?? ''}} </td> -->
                                        <!-- <td class="text-center">{{c.credit!=''? c.credit : ''}} </td> -->

                                    </tr>
                                </template>
                                <template v-else-if="at.ledger_list.length<=0 && !at.hide">
                                    <tr>
                                        <td class="text-right"></td>
                                        <td class="text-center"></td>
                                        <td >{{at.date}}</td>
                                    </tr>
                                </template>
                                <tr class="total_row"    v-if="at.ledger_list.length>0 && !at.hide">
                                    <template v-if="type=='customer'">
                                          <td colspan="4" class="text-right mm-txt"><strong>Total</strong></td>
                                        <td class="text-center" colspan="1">
                                            {{decimalFormat(at.total_credit)}}
                                        </td>
                                        <td class="text-center" colspan="1">
                                            {{decimalFormat(at.total_debit)}}
                                        </td>
                                    </template>
                                      <template v-if="type=='supplier'">
                                          <td colspan="4" class="text-right mm-txt"><strong>Total</strong></td>
                                        <td class="text-center" colspan="1">
                                            {{decimalFormat(at.total_credit)}}
                                        </td>
                                        <td class="text-center" colspan="1">
                                            {{decimalFormat(at.total_debit)}}
                                        </td>
                                    </template>
                                    <template v-if="type=='other'">
                                          <td colspan="4" class="text-right mm-txt"><strong>Total</strong></td>
                                        <td class="text-center" colspan="1">
                                            {{decimalFormat(at.total_debit)}}
                                        </td>
                                        <td class="text-center" colspan="1">
                                            {{decimalFormat(at.total_credit)}}
                                        </td>
                                    </template>
                                </tr>
                                <tr class="total_row" v-if="!at.hide">
                                         <td colspan="4" class="text-right mm-text"><strong>Closing Balance</strong></td>
                                        <td class="text-center " colspan="1" v-if="at.closing_balance>=0">
                                            {{decimalFormat(at.closing_balance)}}
                                        </td>
                                        <td class="text-center " colspan="1" v-else-if="at.closing_balance<0">
                                        </td>
                                        <td class="text-center " colspan="1" v-if="at.closing_balance < 0">
                                            {{decimalFormat(at.closing_balance*(-1))}}
                                        </td>
                                        <td class="text-center " colspan="1" v-else-if="at.closing_balance > 0">
                                        </td>
                                </tr>
                                 <!-- <tr class="total_row" v-else-if="!at.hide || type=='customer'">
                                    <td colspan="5" class="text-right mm-text"><strong>Closing Balance</strong></td>
                                    <td class="text-center " colspan="1" v-if="at.closing_balance>0">
                                        {{at.closing_balance}}
                                    </td>
                                    <td class="text-center " colspan="1" v-else-if="at.closing_balance<0">
                                    </td>
                                    <td class="text-center " colspan="1" v-if="at.closing_balance < 0">
                                        {{at.closing_balance*(-1)}}
                                    </td>
                                    <td class="text-center " colspan="1" v-else-if="at.closing_balance > 0">
                                    </td>
                                </tr> -->
                            </template>
                        </tbody> 
                        

                    </table>
                </div>
                <!-- <div v-else>
                    <h5 class="text-center m-5">No ledger found!</h5>
                </div> -->
            </div>
            <div class="card-footer text-center">
                <!-- pagination start -->
                <div class="row" style="overflow:auto">
                    <div class="col-12">
                        <template v-if="ledger_count > 0">
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
                                    <template v-slot:first-text><span class="text-success" v-on:click="getLedger(1)">First</span></template>
                                    <template v-slot:prev-text><span class="text-danger" v-on:click="getLedger(currentPage)">Prev</span></template>
                                    <template v-slot:next-text><span class="text-warning" v-on:click="getLedger(currentPage)">Next</span></template>
                                    <template v-slot:last-text><span class="text-info" v-on:click="getLedger(pagination.last_page)">Last</span></template>
                                    <template v-slot:ellipsis-text>
                                    </template>
                                    <template v-slot:page="{ page, active }">
                                        <span v-if="active"><b>{{ page }}</b></span>
                                        <span v-else @click="getLedger(page)"><p>{{ page }}</p></span>
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
                sub_account:'',
                from_date:'',
                supplier_id:'',
                customer_id:'',
                to_date:'',
                vochur_no:'',
                type:'',
            },
            pagination: {
                total: "",
                next: "",
                prev: "",
                last_page: "",
                current_page: '',
                next_page_url:""
            },
            types:['Customer','Supplier','Other'],
            sub_account:[],
            account_head:[],
            suppliers:[],
            customers:[],
            opening_balance:'',
            closing_balance:'',
            type:'',
            check_type:'',
            ledger:[],
            perPage: 30,
            currentPage: 1,
            ledger_count: 0,
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
        // this.getLedger();
    },
    mounted(){
        $("#loading").hide();
        var app=this;
        $("#sub_account_id").select2();
        $("#account_head_id").select2();
        app.initSubAccount();
        app.initAccountHead();
        app.initCustomers();
        app.initSuppliers();
        $("#type_id").select2();
        $('#type_id').on('select2:select',function(e){
            var data = e.params.data;
            app.search.type=data.text;
            if(data.text==='Other'){
                app.type='other';
                app.search.customer_id= app.search.supplier_id='';
                // app.search.supplier_id='';
            }else if(data.text==='Supplier'){
                app.type='supplier';
                app.search.customer_id= app.search.sub_account= app.search.head_account='';
            }else if(data.text==='Customer'){
                app.type='customer';
                app.search.supplier_id= app.search.sub_account= app.search.head_account='';

            }
        });
        $('#account_head_id').on('select2:select',function(e){
            var data=e.params.data;
            axios.get('/sub_account/get_sub_account_by_account_head/'+data.id).then(res=>{
                app.sub_account=res.data;
            });
        });
         $("#sub_account_id").select2();
        $("#account_head_id").select2();
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
         $("#customer_id").on("select2:select", function(e) {

            var data = e.params.data;
            app.search.customer_id = data.id;
        });
         $("#account_head_id").on("select2:select", function(e) {

            var data = e.params.data;
            app.search.account_head = data.id;
        });
          $("#sub_account_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.search.sub_account = data.id;
        });
      
         $("#supplier_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.search.supplier_id = data.id;
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
            $("#sub_account_id").select2();
        },
         initAccountHead(){
            axios.get('/sub_account/get_account_head').then(({data})=>(this.account_head=data.account_head));
            $("#account_head_id").select2();
        },
         initSuppliers() {
            axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
            $("#supplier_id").select2();
        },
          initCustomers() {
          axios.get("/customers").then(({ data }) => (this.customers = data.data));
          $("#customer_id").select2();
        },
        getLedger(page=1){
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
                app.search.to_date+
                "&account_head=" +
                app.search.account_head+
                "&supplier_id=" +
                app.search.supplier_id+
                "&customer_id=" +
                app.search.customer_id+
                "&type=" +
                app.search.type;
            axios.get('/report/get_all_ledger?page='+ page + search).then(response=>{
                // console.log('aaaa');
                console.log(response.data.ledger);
                app.ledger=response.data.ledger;
                // app.check_type=response.data.account_type;
                // console.log(response);
                // $("#loading").hide();
                // console.log(response.data.ledger);
                // let data=response.data.ledger;
                // app.ledger=data;
                // app.ledger_count=data.length;
                $("#loading").hide();
             
            });

        }

    }
}
</script>


