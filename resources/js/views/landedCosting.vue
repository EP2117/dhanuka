<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><router-link tag="span" :to="'/purchase/'+1+'/'" class="font-weight-normal"><a href="#">Purchase</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Product Costing Entry</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Product Costing Entry</h4>
            <router-link to="/product_costing/new" class="d-sm-inline-block btn btn-primary shadow-sm text-right">
                <i class="fas fa-plus"></i> Add Product Costing Entry
            </router-link>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="costing_no">Landed Costing No.</label>
                        <input type="text" class="form-control" id="costing_no" name="costing_no" v-model="search.costing_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="supplier_id">Supplier</label>
                        <select id="supplier_id" class="form-control mm-txt"
                                name="supplier_id" v-model="search.supplier_id" style="width:100%" required>
                            <option value="">Select One</option>
                            <option v-for="sup in suppliers" :value="sup.id"  >{{sup.name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getCostings(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Product Costing Entry List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="costing_count > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Landed Cost No.</th>
                                <th class="text-center">Supplier</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Landed Cost No.</th>
                                <th class="text-center">Supplier</th>
                                <th class="text-center">  </th> <!--Kamlesh -->
                            </tr>
                        </tfoot>
                        <tbody>
                            <template v-for="c,index in costings.data">
                            <tr>
                                <td class="text-center">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td class="mm-txt text-center">{{c.landed_costing_no}}</td>
                                <td class="mm-txt text-center">{{c.supplier.name}}</td>

                                <!--Kamlesh Start-->
                                <td class="text-center">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                            <router-link tag="span" :to="'/product_costing/edit/' + c.id" >
                                                <a href="#" title="Edit/View" class="">
                                                    <i class="fas fa-edit"></i>
                                                </a>&nbsp;
                                            </router-link>
                                            </a>

                                            <a class="dropdown-item">
                                                <a title="Print" class="text-primary" @click="printCosting(c.id)">
                                                    <i class="fas fa-print"></i>
                                                </a>        
                                            </a>
                                            <!--<a class="dropdown-item">
                                                <a title="Delete" class="text-danger" @click="removeCosting(c.id)" v-if="(user_role == 'system' || user_role == 'admin')">
                                                    <i class="fas fa-trash"></i>
                                                </a>           
                                            </a>-->
                                            <a class="dropdown-item">
                                                <a title="Delete" class="text-danger" @click="removeCosting(c.id)">
                                                    <i class="fas fa-trash"></i>
                                                </a>           
                                            </a>
                                        </div>
                                    </div>

                                </td>
                                <!-- Kamlesh End-->

                            </tr>
                            <!-- detial view for print -->
                            <template>
                                <div :id="c.id" style="display:none;">
                                    <div style="text-aling:center;display:inline-block; margin-top:10px; width:100%;">
                                        <div style="text-align:center">
                                            <h3 style="font-size:18px">Product Costing Invoice</h3>
                                        </div>
                                        <table style="border:none;" class="costing_tbl">
                                            <tr>
                                                <td class="mm-txt" style="min-width:150px; font-weight:bold;">Shipping Method</td>
                                                <td class="mm-txt" style="min-width:200px;">{{c.shipping_method}}</td>
                                                <td class="mm-txt" style="min-width:150px;font-weight:bold;">Container Number</td>
                                                <td>{{c.container_no}}</td>
                                            </tr>
                                            <tr v-if="c.shipping_method == 'container'">
                                                <td class="mm-txt" style="width:150px;font-weight:bold;">Bill Date</td>
                                                <td>{{c.bill_date}}</td>
                                                <td style="width:150px;font-weight:bold;">Bill Number</td>
                                                <td>{{c.bill_no}}</td>
                                            </tr>
                                            <tr>
                                                <td class="mm-txt" style="width:150px;font-weight:bold;">Supplier</td>
                                                <td class="mm-txt">{{c.supplier.name}}</td>
                                                <td class="mm-txt" style="width:150px;font-weight:bold;">Remark</td>
                                                <td>{{c.remark}}</td>
                                            </tr>
                                        </table>
                                        <div>
                                            <div style="text-decoration:underline;font-size:14px;font-weight:bold;margin-top:5px; margin-bottom:10px;">Product Details</div>
                                        </div>
                                        <table class="t-office-class costing_tbl" cellspacing="0" style="border: solid 1px #000;">
                                            <thead>
                                                <tr>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle; font-weight:bold;">Product Name</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">Total CTN</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">1CTN = PCS</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">Total PCS</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">RMB Rate</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">Total RMB <br /> Amount</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">1RMB = Kyat</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">MMK Rate</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">Duty Charges</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">Landed Cost <br />Per Product</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">Cost</td>
                                                    <td class="text-center" style="border: 1px solid #ccc;vertical-align:middle;font-weight:bold;">Total Cost</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="product,i in c.products">
                                                    <td style="border: 1px solid #ccc;">{{product.product_name}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.total_ctn))}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.pcs_per_ctn))}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.total_pcs))}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.rmb_rate))}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.total_rmb))}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.mmk_per_rmb))}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.mmk_rate))}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.duty_charges))}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.landed_cost_per_product))}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.cost))}}</td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(product.pivot.total_cost))}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right mm-txt" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Total </td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;"> {{decimalFormat(parseFloat(c.total_ctn))}}</td>
                                                    <td style="border: 1px solid #ccc;text-align:right;"></td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(c.total_pcs))}}</td>
                                                    <td style="border: 1px solid #ccc;text-align:right;"></td>
                                                    <td class="text-right" style="border: 1px solid #ccc;text-align:right;">{{decimalFormat(parseFloat(c.total_rmb))}}</td>
                                                    <td style="border: 1px solid #ccc;text-align:right;"></td>
                                                    <td style="border: 1px solid #ccc;text-align:right;"></td>
                                                    <td style="border: 1px solid #ccc;text-align:right;"></td>
                                                    <td style="border: 1px solid #ccc;text-align:right;"></td>
                                                    <td style="border: 1px solid #ccc;text-align:right;"></td>
                                                    <td style="border: 1px solid #ccc;text-align:right;"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">CONTAINER FRIGHT( 20077*260KS)</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.container_freight))}}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Agent Fees</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.agent_fees))}}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Bank Service Fees For Payment Order</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.bank_service_fees))}}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Maersk Line Myanmar Shipping Line Charges</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.shipping_line_charges))}}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Port Charges</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.port_charges))}}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Valution Charges</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.valuation_charges))}}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Insurance Charges</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.insurance_charges))}}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Labour Charges</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.labour_charges))}}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Document Charges</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.document_charges))}}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Port Exam Charges</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.port_exam_charges))}}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">Tracking Charges(MIP-SHWEPYITHAR-MOTTAMAL LOGISTICS)</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.tracking_charges))}}</td>
                                                </tr>
                                                
                                                
                                                <tr>
                                                    <td colspan="7" class="text-right" style="border: 1px solid #ccc;text-align:right;font-weight:bold;">TOTAL</td>
                                                    <td colspan="5" style="border: 1px solid #ccc;">&nbsp; {{decimalFormat(parseFloat(c.total))}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </template>
                            <!-- END detail view for print -->
                            </template>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No entry found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">
          
              <!-- pagination start -->
              <div class="row" style="overflow:auto">
                <div class="col-12">
                  <template v-if="costing_count > 0">
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
                        <template v-slot:first-text><span class="text-success" v-on:click="getCostings(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getCostings(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getCostings(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getCostings(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getCostings(page)">{{ page }}</span>
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
                    costing_no: '',
                    supplier_id: '',
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
                perPage: "30",
                currentPage: 1,
                costing_count: 0,
                costings: [],
                user_role: '',
                suppliers: [],
                is_readonly: false,
                isDisabled: false,
                site_path: '',
                storage_path: '',
            };
        },

        created() {

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            /**if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }**/

            this.getCostings();    
        },

        mounted() {
            //$("#loading").hide();
            let app = this;

            app.initSuppliers();

            $("#supplier_id").select2();
            $("#supplier_id").on("select2:select", function(e) {
                var data = e.params.data;
                app.search.supplier_id = data.id;
            });
        },

        methods: {

            decimalFormat(num)
            {
               var decimal_num = Number.isInteger(parseFloat(num))== true ?  parseInt(num) : num;
               return num;
            },

            initSuppliers() {
                axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
                $("#supplier_id").select2();
            },

            getCostings(page = 1) {
                $("#loading").show();
                let app = this;

                var search =
                    "&supplier_id=" +
                    app.search.supplier_id +
                    "&costing_no=" +
                    app.search.costing_no;

                axios.get("/landed_costing?page=" + page + search).then(function(response) {
                    $("#loading").hide();
                    let data = response.data.data;
                    app.costings = data;
                    app.costing_count = app.costings.data.length;
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

                /*axios.get("/customer").then(({ data }) => (app.customers = data.data))
                .then(function() {
                    console.log(app.customers);
                    $("#loading").hide();
                });*/
            },

            removeCosting(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/landed_costing/" + id).then(function() {
                            app.getCostings();
                        });
                        swal("Success! Product Costing Entry has been deleted!", {
                            icon: "success"
                        });   
                    } else {
                      //
                    }
                });
            },

            printCosting(objName)
            {
                var printWin = window.open('','Print','left=0,top=0,width=744,height=1052,toolbar=0,status =0');
   
               var printContents = document.getElementById(objName).innerHTML;    
              
                
               printWin.document.open();
               printWin.document.clear();
               printWin.document.writeln("<html>");
              
              // printWin.document.writeln("<head><title>PrintATestPage.com</title></head>");
               printWin.document.writeln('<html><head><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>' + document.title  + '</title>');

               // Make sure the relative URL to the stylesheet works:
                //printWin.document.writeln('<base href="' + location.origin + location.pathname + '">');
                printWin.document.writeln('<base href="' + location.origin + '/">');
    
                // Add the stylesheet link and inline styles to the new document:
                printWin.document.writeln('<link rel="stylesheet" href="' + location.origin + '/css/print.css" />');

                printWin.document.writeln('</head>');
              

               printWin.document.writeln("<body>");
               printWin.document.write(printContents);
               
               printWin.document.writeln("</body></html>");
               printWin.document.close(); 

               setTimeout(function () {
                    printWin.focus(); // necessary for IE >= 10*/
                    printWin.print();

                return true;
                }, 1000);
               
               //printWin.print();
            },
        }
    }
</script>