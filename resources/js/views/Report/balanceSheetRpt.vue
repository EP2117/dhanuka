<template>

    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Balance Sheet Report</li>
            </ol>
        </nav>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!--<div class="form-group col-md-4 col-lg-3">
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
                    </div>-->
                    <div class="form-group col-md-4 col-lg-3">
                        <label>Year</label>
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
                            @click="getBalanceSheet(1)"
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
                <h4 class="m-0 font-weight-bold text-primary">Balance Sheet</h4>
            </div>
            <div class="card-body">
                <!-- <div class="table-responsive" > -->
                  <!--<div class="text-right mb-2">
                    <button class="btn btn-primary btn-icon btn-sm" @click="exportPDF()"><i class="fas fa-print"></i> &nbsp;PDF</button>
                </div>-->
                <div class="table-responsive" v-show="result!=''">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead class="thead-light">
                        <tr>
                            <!-- <th class="text-center">No.</th> -->
                            <th class="text-center"></th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                         <tbody id="bs">
                        </tbody> 
                    </table>
                </div>
                <div v-show="result=='' || result==0">
                    <h5 class="text-center m-5">No Record found!</h5>
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
            month:[],
            year:[],
            result: '',
            perPage: 30,
            currentPage: 1,
            user_year:'',
            rows:'',
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
        getBalanceSheet(page=1) {
            let app= this;
            app.result= '';
            $("#loading").show();
            var search ="&year=" +app.search.year; 

            axios.get('/report/balance_sheet?page='+ page+search ).then(response=>{
            // console.log(response);
                $("#loading").hide();
                app.result = '1';
                $("#bs").html(response.data.html);                
               
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
