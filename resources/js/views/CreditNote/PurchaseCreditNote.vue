<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/purchase_office'">Purchase</a></li>
                <li class="breadcrumb-item active" aria-current="page">Debit Note</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Debit Note</h4>
            <router-link :to="'/purchase_credit_note/create'" class="d-sm-inline-block btn btn-primary shadow-sm inventory">
                <i class="fas fa-plus"></i> Add New Debit Note
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
                        <label for="credit_note_no">Debit Note No.</label>
                        <input type="text" class="form-control" id="credit_note_no" name="credit_note_no" v-model="search.credit_note_no">
                    </div>
                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="supplier_id">Supplier</label>
                        <select id="supplier_id" class="form-control mm-txt"
                            name="supplier_id" v-model="search.supplier_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="sup in suppliers" :value="sup.id"  >{{sup.name}}</option>
                        </select>
                    </div> 
                     
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getCreditNote(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Debit Note List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="credit_note.length > 0">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Debit Note No</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Supplier</th>
                                <th class="text-center">Total Amt</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Debit Note No</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Supplier</th>
                                <th class="text-center">Total Amt</th>

                                <!-- <th class="text-center"> Total </th>  -->
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="cn,index in credit_note">
                                <td class="textalign">{{index+1}}</td>
                                <td class="textalign">{{cn.credit_note_no}}</td>
                                <td class="textalign" >{{dateFormat(cn.credit_note_date)}}</td>
                                <td class="textalign">{{cn.supplier.name}}</td>
                                <td class="textalign">{{numberWithCommas(cn.amount)}}</td>
                                <!-- <td class="textalign">{{cn.products.pivot.total_amount}}</td> -->

                                <!-- <td class="text-right">{{numberWithCommas(collection.total_paid_amount)}}</td> -->

                                <!--<td class="text-center">
                                    <router-link tag="span" :to="'/collection/edit/' + collection.id" >
                                        <a href="#" title="Edit/View" class="">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    </router-link>
                                        
                                    <a title="Delete" class="text-danger" @click="removeCollection(collection.id)">
                                        <i class="fas fa-trash"></i>
                                    </a>&nbsp;  

                                                                      
                                </td>-->

                                <!--Kamlesh Start-->
                                <td class="text-center">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <router-link tag="span" :to="'/purchase_credit_note/edit/' + cn.id" >
                                                    <a href="#" title="Edit/View" class="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>&nbsp;
                                                </router-link>
                                            </a>
                                            <a class="dropdown-item">
                                                <a title="Delete" class="text-danger" @click="removeCreditNote(cn.id)">
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
                    <h5 class="text-center m-5">No record found!</h5>
                </div>

            </div>
             <div class="card-footer text-center">

              <!-- pagination start -->
              <div class="row" style="overflow:auto">
                <div class="col-12">
                  <template v-if="credit_note_count > 0">
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
                        <template v-slot:first-text><span class="text-success" v-on:click="getSuppliers(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getSuppliers(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getSuppliers(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getSuppliers(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getCreditNote(page)">{{ page }}</span>
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
                    credit_note_no: "",
                    sale_invoice_no: "",
                    supplier_id: "",
                    state_id:'',
                    township_id:'',
                },
                  pagination: {
                    total: "",
                    next: "",
                    prev: "",
                    last_page: "",
                    current_page: "",
                    next_page_url:""
                },
                credit_note_count:0,
                 rows: "",
                perPage: "30",
                currentPage: 1,
                suppliers: [],
                credit_note: [],
                states:[],
                townships:[],
                user_role: '',
                user_year: "",
                branches: [],
                site_path: '',
                storage_path: '',
                user_role_id: '',
            };
        },

        created() {
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            
            /*if(this.user_role == "office_order_user")*/
            this.user_role_id = document.querySelector("meta[name='user-role-id']").getAttribute('content');
            
            if(this.user_role != "admin" && this.user_role != "system" && this.user_role_id != 15)
            {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            this.getCreditNote();    
        },

        mounted() {
            let app = this;
            app.initSuppliers();
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
             

            $("#supplier_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.supplier_id = data.id;
            });

            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.branch_id = data.id;
            });
           
        },

        methods: {
            initSuppliers() {
              axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
              $("#supplier_id").select2();
            },
             initStates() {
                    axios.get("/state").then(({ data }) => (this.states = data.data));
                    $("#state_id").select2();
            },
            initTownships() {
                if(this.search.state_id != "") {
                    axios.get("/township_by_state/" + this.search.state_id).then(({ data }) => (this.townships = data.data));
                    $("#township_id").select2();
                }else{
                      axios.get("/township/" + this.search.state_id).then(({ data }) => (this.townships = data.data));
                    $("#township_id").select2();
                }
            },
           

            getCreditNote(page = 1) {
                $("#loading").show();
                let app = this;
                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                     "&cn_from_date=" +
                    app.search.cn_from_date +
                    "&cn_to_date=" +
                    app.search.cn_to_date +
                     "&sale_invoice_no=" +
                    app.search.sale_invoice_no +
                    "&credit_note_no=" +
                    app.search.credit_note_no +
                     "&state_id=" +
                    app.search.state_id +
                    "&township_id=" +
                    app.search.township_id+
                    "&supplier_id=" +
                    app.search.supplier_id;
                    axios.get("/purchase_credit_note?" + search)
                    .then(function(response) {
                        $("#loading").hide();
                        // console.log(response.data);
                        let data=response.data.credit_note;
                        app.credit_note = data.data;
                        app.credit_note_count = data.data.length;
                        app.pagination.last_page = data.last_page;
                        app.pagination.next = data.next_page_url;
                        app.pagination.prev = data.prev_page_url;
                        app.pagination.total = data.total;
                        app.pagination.current_page = data.current_page;
                        app.pagination.next_page_url = data.next_page_url;
                        app.rows = data.total;
                        app.currentPage = data.current_page;
                    });
            },

            removeCreditNote(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/purchase_credit_note/destroy/" + id).then(function() {
                            swal("Success! CreditNote has been deleted!", {
                                icon: "success"
                            });
                            app.getCreditNote();
                        });
                           
                    } else {
                      //
                    }
                });
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