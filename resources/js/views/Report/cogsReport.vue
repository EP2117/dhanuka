<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">COGS Report</li>
            </ol>
        </nav>
        <!-- Page Heading -->
     

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
                  
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="product_name"> Product Name</label>
                        <select id="product_name" class="form-control mm-txt"
                                name="product_name" v-model="search.product_name" style="width:100%" required
                        >
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="product_code"> Product Code</label>
                        <input type="text" class="form-control" id="product_code"  v-model="search.product_code">
                    </div>
                     <div class="form-group col-md-4 col-lg-3">
                        <label >Type</label>
                        <select id="type_id" class="form-control"
                                 v-model="search.type_id" style="width:100%">
                            <option value="">Select One</option>
                            <option value="sale">Sale</option>
                            <option value="credit_note">Credit Note</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getData(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">COGS Report</h6>
            </div>
            <div class="card-body">
            <!--<div class="text-right mb-2" v-if="data.length>0" >
                <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-print"></i> &nbsp;Export Excel</button>
            </div>-->
                <div class="text-right mb-2 mr-2" v-if="data.length>0" ><b>Total: {{total_cost_price}}</b></div>
              <div class="table-responsive" v-if="data.length > 0">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                 <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Product code</th>
                                    <th class="text-center">Transition Date</th>
                                    <th class="text-center">Transition No.</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Sale Cost Price</th>
                                    <th class="text-center">Credit Note Cost Price</th>
                                     <th class="text-center">Master Cost Price</th> 

                                    <!-- <th class="text-center"> Total </th>  -->
                                </tr>
                                <!-- <th class="text-center"> Total </th>  -->
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Product code</th>
                                    <th class="text-center">Transition Date</th>
                                    <th class="text-center">Transition No.</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Sale Cost Price</th>
                                    <th class="text-center">Credit Note Cost Price</th>
                                    <th class="text-center">Master Cost Price</th> 

                                <!-- <th class="text-center"> Total </th>  -->
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="p,index in data">
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td class="textalign">{{p.product_name}}</td>
                                <td class="textalign">{{p.product_code}}</td>
                                <td class="textalign">{{p.transition_date}}</td>
                                <td class="textalign">{{p.transition_no}}</td>
                                <td class="textalign">{{p.product_quantity}}</td> <!-- kamlesh -->
                                <td class="textalign">{{p.cost_price}}</td>
                                <td class="textalign">{{p.cn_cost_price}}</td>
                                <td class="textalign" v-if="p.cost_price != 0">{{parseInt(p.cost_price)/parseInt(p.product_quantity)}}</td>
                                <td class="textalign" v-else-if="p.cn_cost_price != 0">{{parseInt(p.cn_cost_price)/parseInt(p.product_quantity)}}</td>
                                <td v-else></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No record found!</h5>
                </div>

            </div>

            <div class="card-footer text-center">
          
              <!-- pagination start -->
              <div class="row" style="overflow:auto">
                <div class="col-12">
                  <template v-if="data.length > 0">
                    <div class="overflow-auto text-center" style="display:inline-block">
                      <!-- Use text in props -->
                      <b-pagination
                        v-model="currentPage"
                        :total-rows="rows"
                        :per-page="perPage"
                        first-text="First"
                        prev-text="Prev"
                        next-text="Next"
                        last-text="Last"
                      >
                        <template v-slot:first-text><span class="text-success" v-on:click="getData(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getData(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getData(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getData(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getData(page)">{{ page }}</span>
                        </template>
                      </b-pagination>
                    </div>
                  </template>
                </div>
              </div>
              <!-- pagination end -->
            </div>
        </div>
        <!-- table end -->
        <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div>
    </div>

</template>
<script>
    export default {

        data() {
            return {
                search: {
                    from_date: "",
                    to_date: "", 
                    product_code:'',
                    product_name:'',
                    month:'',
                    year:'',
                    type_id: '',
                },
                pagination: {
                    total: "",
                    next: "",
                    prev: "",
                    last_page: "",
                    current_page: "",
                    next_page_url:""
                },
                rows: "",
                perPage: "15",
                currentPage: 1,
                data: [],
                user_role: '',
                user_year: "",
                site_path: '',
                storage_path: '',
                sub_qty:'',
                user_role_id: '',
                total_cost_price: '',
                month:[],
                year:[],
            };
        },

        created() {
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            this.user_role_id = document.querySelector("meta[name='user-role-id']").getAttribute('content');
            
            /*if(this.user_role != "admin" && this.user_role != "system" && this.user_role_id != 19 && this.user_role_id != 16)
            {
                var url =  window.location.origin;
                window.location.replace(url);
            }*/
  
        },

        mounted() {
            $('#loading').hide();
            let app = this;
            //app.initWarehouses();
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
               // minDate: app.user_year+"-01-01",
               // maxDate: app.user_year+"-12-31",
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
                app.search.month='';
                $('#month_id').val('').trigger('change');
                app.search.year='';
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
               // minDate: app.user_year+"-01-01",
               // maxDate: app.user_year+"-12-31",
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
                app.search.month='';
                $('#month_id').val('').trigger('change');
                app.search.yeaer='';
                $('#year_id').val('').trigger('change');
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.to_date = formatedValue;
            });

            // Kamlesh Start

            $("#product_name").select2({
            allowClear: true,
            placeholder: "Select One",
            minimumInputLength: 3,
            ajax: {
                url: app.site_path+'/search_products',
            data: function (params) {
                var query = {
                term: params.term,           
                }

                return query;
            },
            processResults: function (data) {

                return {
                results: $.map(data, function (obj) {
                    return { 
                        'id': obj.id, 
                        'text': obj.product_name,
                        'datatsp': obj.category_id,
                    };
                })
                };
            }
            
            }
        });
        $("#product_name").on("select2:select", function(e) {
            var data = e.params.data
            app.search.product_name = data.id;
        });
         $("#product_name").on("select2:unselecting", function(e) {
                app.search.product_name = '';
            });
        // Kamlesh END

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

        },

        methods: {

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

             initWarehouses() {
              axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
              $("#to_warehouse").select2();
            },
            getData(page = 1) {

                /*if(this.search.from_date == "") {                  
                    swal("Warning!", "Date is required!", "warning")
                    return false;
                } else {*/
                $("#loading").show();
                let app = this;
                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                     "&product_code=" +
                    app.search.product_code +
                    "&month=" +
                    app.search.month +
                    "&year=" +
                    app.search.year +
                    "&type_id=" +
                    app.search.type_id +
                    "&product_name=" +
                    app.search.product_name;
                    axios.get("/report/cogs?page=" + page + search).then(function(response) {
                        $("#loading").hide();
                        let data = response.data.data;
                        app.data = data.data;
                        app.total_cost_price =response.data.total_cost_price;
                        app.pagination.last_page = data.last_page;
                        app.pagination.next = data.next_page_url;
                        app.pagination.prev = data.prev_page_url;
                        app.pagination.total = data.total;
                        app.pagination.current_page = data.current_page;
                        app.pagination.next_page_url = data.next_page_url;


                        app.rows = data.total;
                        app.currentPage = data.current_page;
                    }); 
                //}
            },
             exportExcel() { 
                let app = this;   
                 var search =
                "&from_date=" +
                app.search.from_date +
                "&to_date=" +
                app.search.to_date +
                 "&product_code=" +
                app.search.product_code +
                "&product_name=" +
                app.search.product_name;
                var baseurl = window.location.origin;
                window.open(this.site_path+'/report/cogs_export?'+search);
            },
            dateFormat(d) {
                //return moment(d).format('YYYY-MM-DD');
                return moment(d).format('DD-MM-YYYY');
            },
            numberWithCommas(x) {
                if(x != null) {
                  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                } else {
                  return x;
                }
            },

            localTime(utcTime) 
            {
                var utcDate = moment.utc(utcTime+'Z');
                // Apply a time zone
                var localTimezone = utcDate.tz('Asia/Rangoon');
                return localTimezone.format('YYYY-MM-DD HH:mm:ss');
            },

        },

        
    }
</script>