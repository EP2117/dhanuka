<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/inventory'">Inventory</a></li>
                <li class="breadcrumb-item active" aria-current="page">Inventory Adjustment</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Inventory Adjustment</h4>
            <router-link to="/inventory/create_adjustment" class="d-sm-inline-block btn btn-primary shadow-sm inventory" v-if="user_role == 'office_user' || user_role == 'system'">
                <i class="fas fa-plus"></i> Add New 
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
                        <label >Invoice No.</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="search.invoice_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="branch_id">Branch</label>
                        <select id="branch_id" class="form-control mm-txt"
                            name="branch_id" v-model="search.branch_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="reference_no">Reference No.</label>
                        <input type="text" class="form-control" id="reference_no" name="reference_no" v-model="search.reference_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="product_name">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" v-model="search.product_name">
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getAdjustments(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div> 
        <!-- table start -->
         <div class="card shadow mb-4"> 
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Adjustment List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="adjustment_count > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Warehouse</th>
                                <th class="text-center">Invoice No.</th>
                                <th class="text-center">Adjustment Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Reference No.</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Warehouse</th>
                                <th class="text-center">Adjustment No.</th>
                                <th class="text-center">Adjustment Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Reference No.</th>
                                <th class="text-center"></th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="a,index in adjustments.data">
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td class="textalign">{{a.warehouse.warehouse_name}}</td>
                                <td class="textalign">{{a.invoice_no}}</td>
                                <td class="textalign">{{dateFormat(a.adjustment_date)}}</td>
                                <td v-if="a.branch != null" class="textalign">{{a.branch.branch_name}}</td>
                                <td v-else></td>
                                <td class="textalign">{{a.reference_no}}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <router-link tag="span" :to="'/inventory_adjustment/' + a.id +'/edit'" >
                                                    <a href="#" title="Edit/View" class="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>&nbsp;
                                                </router-link>
                                            </a>
                                            <a class="dropdown-item">
                                                <a href="#" title="Delete" class="text-danger" @click="removeAdjustment(a.id)" v-if="user_role == 'office_user' || user_role == 'system'">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </a>
                                        </div>
                                    </div>

                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No record found!</h5>
                </div>
            </div>

             <div class="card-footer text-center">
          
              <div class="row" style="overflow:auto">
                <div class="col-12">
                  <template v-if="adjustment_count > 0">
                    <div class="overflow-auto text-center" style="display:inline-block">
                      <b-pagination
                        v-model="currentPage"
                        :total-rows="rows"
                        :per-page="perPage"
                        first-text="First"
                        prev-text="Prev"
                        next-text="Next"
                        last-text="Last"
                      >
                        <template v-slot:first-text><span class="text-success" v-on:click="getAdjustments(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getAdjustments(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getAdjustments(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getAdjustments(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getAdjustments(page)">{{ page }}</span>
                        </template>
                      </b-pagination>
                    </div>
                  </template>
                </div>
              </div>
            </div>

        </div> 
        <!-- table end -->
        <!-- <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div> -->
    </div>

</template>

<script>
    export default {

        data() {
            return {
                search: {
                    from_date: "",
                    to_date: "",
                    invoice_no: "",
                    reference_no: "",
                    product_name: "",
                    branch_id: "",
                },
                pagination: {
                    total: "",
                    next: "",
                    prev: "",
                    last_page: "",
                    current_page: "",
                    next_page_url:""
                },
                adjustments: [],
                user_role: '',
                user_year: "",
                rows: "",
                perPage: "30",
                currentPage: 1,
                adjustment_count: 0,
                branches: [],
                site_path: '',
                storage_path: '',
            };
        },

        created() {
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }
            this.getAdjustments();    
        },

        mounted() {
            $("#loading").hide();
            let app = this;

            app.initBranches();
            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.branch_id = data.id;
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

        methods: {
            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            },

            getAdjustments(page = 1) {
                console.log(page);
                $("#loading").show();
                let app = this;

                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&invoice_no=" +
                    app.search.invoice_no +
                    "&reference_no=" +
                    app.search.reference_no +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&product_name=" +
                    app.search.product_name;

                /*** axios.get("/mainwarehouse_entry").then(({ data }) => (app.entries = data.data))
                .then(function() {
                    console.log(app.entries);
                    $("#loading").hide();
                }); ***/

                axios.get("/inventory_adjustment?page=" + page + search).then(function(response) {
                    $("#loading").hide();
                    let data = response.data.data;
                    app.adjustments = data;
                    app.adjustment_count = app.adjustments.data.length;
                    app.pagination.last_page = data.last_page;
                    app.pagination.next = data.next_page_url;
                    app.pagination.prev = data.prev_page_url;
                    app.pagination.total = data.total;
                    app.pagination.current_page = data.current_page;
                    app.pagination.next_page_url = data.next_page_url;


                    app.rows = data.total;
                    app.currentPage = data.current_page;
                    console.log(app.currentPage);
                });
            },

            // deleteAdjustment(id) {
            //     let app = this;
            //     swal({
            //         title: "Are you sure?",
            //         text: "Once deleted, you will not be able to recover!",
            //         icon: "warning",
            //         buttons: true,
            //         dangerMode: true
            //     }).then(willDelete => {
            //     if (willDelete) {
            //       axios.delete("/inventory_adjustment/" + id).then(function() {
            //         app.getAdjustments();
            //       });
            //       swal("Success! Adjustment has been deleted!", {
            //         icon: "success"
            //       });
            //     } else {
            //       //
            //     }
            //     });
            // },

            removeAdjustment(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/inventory_adjustment/" + id).then(function() {
                            app.getAdjustments();
                        });
                        swal("Success! Adjustment has been deleted!", {
                            icon: "success"
                        });   
                    } else {
                      //
                    }
                });
            },

            dateFormat(d) {
                return moment(d).format('YYYY-MM-DD');
            },
        },

        
    }
</script>