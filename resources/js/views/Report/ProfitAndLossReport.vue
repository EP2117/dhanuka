<template>

    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profit & Loss Report</li>
            </ol>
        </nav>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="from_date">From Date</label>
                        <input type="text" autocomplete="off" class="form-control datetimepicker" id="from_date" name="from_date"
                               v-model="search.from_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="to_date">To Date</label>
                        <input type="text" class="form-control datetimepicker" id="to_date" name="to_date"
                               v-model="search.to_date" autocomplete="off">
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label >Monthly</label>
                        <select id="month_id" class="form-control"
                                 v-model="search.month" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="(m,key) in month" :value="key+1"  >{{m}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label>Yearly</label>
                        <select id="year_id" class="form-control"
                                v-model="search.year" style="width:100%">
                            <option value="">Select One</option>
                            <option v-for="(y,k) in year" :value="y"  >{{y}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getProfitAndLoss(1)"
                        ><i class="fas fa-search"></i> 
                        &nbsp;&nbsp;Search </button>
                    </div>
                     <!-- <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="printPDF()"
                        ><i class="fas fa-printi"></i> &nbsp;&nbsp;Print PDF </button>
                    </div> -->
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h4 class="m-0 font-weight-bold text-primary">Profit And Loss </h4>
            </div>
            <div class="card-body">
                <!-- <div class="table-responsive" > -->
                  <div class="text-right mb-2">
                    <button class="btn btn-primary btn-icon btn-sm" @click="exportPDF()"><i class="fas fa-print"></i> &nbsp;PDF</button>
                </div>
                <div class="table-responsive" v-if="profit_and_loss!='' || expense !='' || income!=''">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead class="thead-light">
                        <tr>
                            <!-- <th class="text-center">No.</th> -->
                            <th class="text-center"></th>
                            <th class="text-center">Amount</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="text-center"></th>
                            <th class="text-center">Amount</th>
                        </tr>
                        </tfoot>
                         <tbody>
                        <template v-for="(pl,index) in profit_and_loss">
                            <template v-if='index== "Revenue"' >
                             <h3 style="margin-left:100px">{{index}}</h3>
                             <!--<tr v-for="(r,i) in pl.revenue"> -->
                             <tr v-for="(r,i) in pl" >  
                                <td class="text-center">
                                  {{r.name}}
                                </td>
                                <td class="text-center">
                                  {{decimalFormat(r.amount).toLocaleString()}}
                                </td>
                             </tr>
                             <tr v-if="total_revenue != 0">
                                <td></td>
                                <td class="text-center">
                                    {{decimalFormat(total_revenue)}}
                                </td>
                            </tr>
                            </template>
                            <template v-if='index=="Cost of Revenue"'>
                                <h3 style="margin-left:100px">{{index}}</h3>
                                 <tr v-for="(cor,i) in pl">
                                    <td class="text-center">
                                        {{cor.name}}
                                    </td>
                                     <td class="text-center">
                                        {{decimalFormat(cor.amount).toLocaleString()}}
                                    </td>
                                  </tr>
                            </template>
                            <!-- <td class="text-center">{{((currentPage * perPage) - perPage) + (index+1)}}</td> -->
                        </template>
                        <tr class="total_row table-secondary " v-if='profit_and_loss!="" '  >
                               <td colspan="1" class="text-right mm-text" v-if="gross_profit>=0">
                                   <strong><h3>Gross Profit </h3></strong> 
                                    </td>
                               <td colspan="1" class="text-right mm-text" v-else-if="gross_profit<0"><strong><h3>Loss Profit </h3></strong>  </td>
                               <td colspan="1" class="text-center" >{{decimalFormat(gross_profit).toLocaleString()}}</td>
                          </tr>
                        <!-- <tr class="total_row table-secondary " v-else-if='profit_and_loss!="" && gross_profit<0'  >
                               <td colspan="1" class="text-right mm-text"><strong>Loss Profit </strong>  </td>
                               <td colspan="1" class="text-center ">{{(-1) *gross_profit}}</td>
                          </tr> -->
                        <template v-if="income!=''">
                             <h3 style="margin-left:100px">Income</h3>
                            <template v-for='(inc,k) in income'>
                                <h5  style="margin-left:250px">{{inc.account_head_name}}</h5>
                                          <tr v-for="(i,index) in inc.income">  
                                              <!-- <h4>{{k}}</h4> -->
                                            <td class="text-center">
                                                {{i.sub_account_name}}
                                            </td>
                                             <td class="text-center">
                                                {{decimalFormat(i.amount).toLocaleString()}}
                                            </td>
                                          </tr>
                                            <tr class="total_row">
                                                <td colspan="1" class="text-right mm-text"><strong>{{inc.account_head_name}} Total</strong>  </td>
                                                <td colspan="1" class="text-center ">{{decimalFormat(inc.total).toLocaleString()}}</td>
                                            </tr>
                                                        
                            </template>
                             <tr class="total_row table-secondary" v-if="income!=null">
                                   <td colspan="1" class="text-right mm-text"><strong>Total Income </strong>  </td>
                                   <td colspan="1" class="text-center ">{{decimalFormat(total_income).toLocaleString()}}</td>
                              </tr>
                        </template>
                        <template v-if="expense!=''">
                            <h3 style="margin-left:100px">Expense</h3>
                            <template v-for='(exp,k) in expense'>
                                <template v-if="exp.expense.length > 0">
                                     <h5 style="margin-left:250px">{{exp.account_head_name}}</h5>
                                      <tr v-for="(e,index) in exp.expense" >  
                                        <td class="text-center">
                                            {{e.sub_account_name}}
                                        </td>
                                         <td class="text-center" v-if="e.amount==null">
                                            {{e.amount}}
                                        </td>
                                        <td class="text-center" v-else>
                                            {{decimalFormat(e.amount).toLocaleString()}}
                                        </td>
                                      </tr>
                                </template>
                            </template>
                            <tr class="total_row" v-if="expense!=null" >
                                   <td colspan="1" class="text-right mm-text"><strong>Total Expense </strong>  </td>
                                   <td colspan="1" class="text-center ">{{decimalFormat(total_expense).toLocaleString()}}</td>
                              </tr>
                        </template>
                            <tr class="total_row table-secondary" v-if="net_profit!=''">
                                   <td colspan="1" class="text-right mm-text" v-if="net_profit>=0"><strong> <h3>Net Profit </h3></strong>  </td>
                                   <td colspan="1" class="text-right mm-text" v-if="net_profit<0"><strong> <h3> Net Loss </h3></strong>  </td>
                                   <td colspan="1" class="text-center ">{{decimalFormat(net_profit).toLocaleString()}}</td>
                              </tr>
                            <template>
                                  <tr>  
                                    <td class="text-center">
                                        Currency Gain
                                    </td>
                                     <td class="text-center">
                                        {{decimalFormat(c_gain).toLocaleString()}}
                                    </td>
                                  </tr>
                            </template>

                            <template>
                                  <tr>  
                                    <td class="text-center">
                                        Currency Loss
                                    </td>
                                     <td class="text-center">
                                        {{decimalFormat(c_loss).toLocaleString()}}
                                    </td>
                                  </tr>
                            </template>

                            <tr class="total_row table-secondary">
                                   <td colspan="1" class="text-right mm-text"><strong> <h5>Currency Gain/Loss Total</h5></strong>  </td> 
                                   <td colspan="1" class="text-center ">{{decimalFormat(gain_loss_total).toLocaleString()}}</td>
                            </tr>

                            <tr class="total_row table-secondary">
                                   <td colspan="1" class="text-right mm-text"><strong> <h3>After Gain/Loss Profit</h3></strong>  </td> 
                                   <td colspan="1" class="text-center ">{{decimalFormat(gain_loss_profit).toLocaleString()}}</td>
                            </tr>
                        </tbody> 
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No Profit & Loss found!</h5>
                </div>
            </div>
        </div>
        <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div>
        <!-- <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div> -->
    </div>
</template>

<script>
export default {
    data(){
        return{
            search:{
                from_date:'',
                to_date:'',
                month:'',
                year:'',
            },
            pagination: {
                total: "",
                next: "",
                prev: "",
                last_page: "",
                current_page: '',
                next_page_url:""
            },
            profit_and_loss:[],
            total_revenue:0,
            expense:[],
            income:[],
            net_profit:'',
            c_gain: '',
            c_loss: '',
            gain_loss_total: '',
            gain_loss_profit: '',
            total_income:'',
            total_expense:'',
            month:[],
            year:[],
            count_of_expense:0,
            count_of_revenue:0,
            count_of_income:0,
            gross_profit:'',
            perPage: 30,
            currentPage: 1,
            user_year:'',
            rows:'',
            credit:[],
            debit:[],
        }
    },
    created() {
        // console.log(this.perPage);
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
        this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

        if (this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
            var url = window.location.origin;
            window.location.replace(url);
        }
        // this.getReceipt();
    },
    mounted() {
        $("#loading").hide();
        var app=this;
        // this.initDebit();
        // this.initCredit();
        this.initMonth();
        this.initYear();
        $('#month_id').select2();
        $('#month_id').on('select2:select',function(e){
            app.search.from_date='';
            app.search.to_date='';
            var data=e.params.data;
            app.search.month=data.id;
        });
          $('#year_id').select2();
          $('#year_id').on('select2:select',function(e){
             app.search.from_date='';
            app.search.to_date='';
            var data=e.params.data;
            app.search.year=data.id;
        });
        $(document).on('change','#month_id',function(evt){
            $('#year_id').prop('required',true);
        });
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
                app.search.from_date = moment().format('YYYY-MM-DD');
                if(app.user_year < y) {
                    if(app.search.from_date == app.user_year+"-12-31" || app.search.from_date == '') {
                        app.search.from_date = app.user_year+"-12-31";
                    }
                }
            })
            .on("dp.change", function(e) {
                app.search.month='';
                $('#month_id').val('').trigger('change');
                app.search.yeaer='';
                $('#year_id').val('').trigger('change');
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
                app.search.to_date = moment().format('YYYY-MM-DD');
                var y = new Date().getFullYear();
                if(app.user_year < y) {
                    if(app.search.to_date == app.user_year+"-12-31" || app.search.to_date == '') {
                        app.search.to_date = app.user_year+"-12-31";
                    }
                }
            })
            .on("dp.change", function(e) {
                  app.search.month='';
                $('#month_id').val('').trigger('change');
                app.search.yeaer='';
                $('#year_id').val('').trigger('change');
                var formatedValue = e.date.format("YYYY-MM-DD");
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

        initMonth(){
            var month=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            // var month=['0'=>'Jan','1'=>'Feb'];
            this.month=month;
        },
        initYear(){
            var year = [];
            for(var $y=2020; $y<=this.user_year; $y++) {
                year.push($y);
            }
            //var year=['2020','2021'];
            this.year=year;
        },
        // initDebit(){
        //     axios.get('/sub_account/get_sub_account/'+"debit").then(({data})=>(this.debit=data.sub_account));
        // },
        // initCredit(){
        //     axios.get('/sub_account/get_sub_account/'+"credit").then(({data})=>(this.credit=data.sub_account));
        // },
        getProfitAndLoss(page=1) {
            // let app= this;            
              if(this.search.from_date == "" && this.search.to_date == "" && this.search.month == "" && this.search.year == "" ) {
                swal("Warning!", "Please must be filtered at least one!", "warning")
                return false;
            }
            $("#loading").show();
            // alert(page);
            let app = this;
            var search =
                "&from_date=" +
                app.search.from_date +
                "&to_date=" +
                app.search.to_date +
                "&month=" +
                app.search.month +
                "&year=" +
                app.search.year 
            axios.get('/report/profit_and_loss?page='+ page+search ).then(response=>{
            // console.log(response);
                $("#loading").hide();
                // console.log(response);
                app.profit_and_loss=response.data.profit_and_loss;
                app.gross_profit=response.data.gross_profit;
                app.total_revenue = response.data.total_revenue;
                app.income=response.data.income;
                app.c_gain = response.data.c_gain==null ? 0 : response.data.c_gain.amount;
                app.c_loss = response.data.c_loss==null ? 0 : response.data.c_loss.amount;
                app.total_income=response.data.total_income;
                // $.each(app.income,function(k,v){
                    // console.log(v.income);
                // });
                app.expense=response.data.expense;
                app.total_expense=response.data.total_expense;
                // app.count_of_income=Object.keys(response.data.income).length
                // app.count_of_expense=Object.keys(response.data.expense).length
                // app.count_of_revenue=Object.keys(response.data.profit_and_loss).length
                // app.gross_profit=response.data.gross_profit.toLocaleString();
                app.net_profit=response.data.net_profit==null ? 0 : response.data.net_profit;

                app.gain_loss_total = parseInt(app.c_gain) - parseInt(app.c_loss);
                app.gain_loss_profit = (parseInt(app.net_profit) + parseInt(app.c_gain))  - parseInt(app.c_loss);
                // // app.receipt=data.data;
                
               
            });
        },
        destroyReceipt(id) {
            let app = this;
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then(willDelete => {
                if (willDelete) {
                    axios.delete("/receipt/destroy/" + id).then(function() {
                        swal("Success! Receipt has been deleted!", {
                            icon: "success"
                        });
                        app.getProfitAndLoss();
                    });
                } else {
                    //
                }
            });
        },

        exportPDF(){
              let app = this;
            if(app.search.from_date == "" && this.search.month=="" && this.search.year=="") {                  
                swal("Warning!", "Please must be filtered at least one!", "warning")
                return false;
            } else {
                $("#loading").show();
            }

             var search =
            "&from_date=" +
            app.search.from_date +
            "&to_date=" +
            app.search.to_date +
            "&month=" +
            app.search.month +
            "&year=" +
            app.search.year 
            axios.get("/report/export_p_and_l_pdf?" + search, {responseType: 'blob'}).then(response => {
                $('#loading').hide();
                const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
                window.open(url);
                /*const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'daily_sale_rpt.pdf'); //or any other extension
                document.body.appendChild(link);
                link.click();*/

              })
              .catch(error => {
                console.log(error);
              });

        }
    }
}
</script>

<style scoped>

</style>
