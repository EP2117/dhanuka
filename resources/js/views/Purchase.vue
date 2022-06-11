<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item" ><a :href="site_path+'/purchase_office'">Office Purchase</a></li>
                <li class="breadcrumb-item active" aria-current="page">Purchase Invoice</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Purchase Invoice</h4>
            <router-link :to="'/purchase/'+purchase_type+'/create'" class="d-sm-inline-block btn btn-primary shadow-sm inventory" v-if="user_role == 'system' || user_role == 'office_user' || user_role == 'van_user'">
                <i class="fas fa-plus"></i> Add New Invoice
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
                        <label for="invoice_no">Invoice No.</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="search.invoice_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt" v-if="(user_role == 'system' || user_role == 'admin' || user_role == 'Country Head')">
                        <label for="branch_id">Branch</label>
                        <select id="branch_id" class="form-control mm-txt"
                                name="branch_id" v-model="search.branch_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="supplier_id">Supplier</label>
                        <select id="supplier_id" class="form-control mm-txt"
                                name="supplier_id" v-model="search.supplier_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="sup in suppliers" :value="sup.id"  >{{sup.name}}</option>
                        </select>
                    </div>

<!--                    <div class="form-group col-md-4 col-lg-3 mm-txt"  v-if="user_role != 'van_user' && user_role != 'office_order_user' && purchase_type == 1">-->
<!--                        <label for="sale_man_id">Purchase Man</label>-->
<!--                        <select id="sale_man_id" class="form-control mm-txt"-->
<!--                                name="sale_man_id" v-model="search.purchase_man_id" style="width:100%" required-->
<!--                        >-->
<!--                            <option value="">Select One</option>-->
<!--                            <option v-for="purchase_man in purchase_mans" :value="purchase_man.id"  >{{purchase_man.name}}</option>-->
<!--                        </select>-->
<!--                    </div>-->

<!--                    <div class="form-group col-md-4 col-lg-3 mm-txt"  v-if="(user_role == 'system' || user_role == 'admin' || user_role == 'office_user' || user_role == 'Country Head') && purchase_type == 1">-->
<!--                        <label for="office_sale_man_id">Office Sale Man</label>-->
<!--                        <select id="office_sale_man_id" class="form-control mm-txt"-->
<!--                                name="office_sale_man_id" v-model="search.office_sale_man_id" style="width:100%" required-->
<!--                        >-->
<!--                            <option value="">Select One</option>-->
<!--                            <option v-for="office_sale_man in office_sale_mans" :value="office_sale_man.id"  >{{office_sale_man.name}}</option>-->
<!--                        </select>-->
<!--                    </div>-->

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="ref_no">Reference No.</label>
                        <input type="text" class="form-control" id="ref_no" name="ref_no" v-model="search.ref_no">
                    </div>

<!--                    <div class="form-group col-md-4 col-lg-3" v-if="purchase_type == 1">-->
<!--                        <label for="invoice_type">Invoice Type</label>-->
<!--                        <select id="invoice_type" class="form-control mm-txt"-->
<!--                                name="invoice_type" v-model="search.invoice_type" style="width:100%" required-->
<!--                        >-->
<!--                            <option value="">Select One</option>-->
<!--                            <option value="approval">Approval Invoice</option>-->
<!--                            <option value="direct">Direct Invoice</option>-->
<!--                        </select>-->
<!--                    </div>-->
<!--                    <div class="form-group col-md-4 col-lg-3" v-if="purchase_type == 1">-->
<!--                        <label for="delivery_approve">Delivery Approve</label>-->
<!--                        <select id="delivery_approve" class="form-control mm-txt"-->
<!--                                name="delivery_approve" v-model="search.delivery_approve" style="width:100%" required-->
<!--                        >-->
<!--                            <option value="">Select One</option>-->
<!--                            <option value="true">Dagon</option>-->
<!--                            <option value="false">Others</option>-->
<!--                        </select>-->
<!--                        &lt;!&ndash;<input type="checkbox" class="form-control" id="delivery_approve" name="delivery_approve" v-model="search.delivery_approve" style="width:20px;height:20px;">&ndash;&gt;-->
<!--                    </div>-->

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small">&nbsp;</label>
                        <button
                            class="form-control btn btn-primary font-weight-bold"
                            @click="getPurchases(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- table start -->
        <div class="card shadow mb-4"   >
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Purchase List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="purchase_count > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Invoice No.</th>
                            <th class="text-center">Reference No.</th>
                            <th class="text-center">Invoice Date</th>

                            <th class="text-center">Branch</th>
                            <th class="text-center">Supplier</th>
<!--                            <th class="text-center">Purchase Man</th>-->
                            <th class="text-center">Office Purchase Man</th>
                            <th class="text-center"> Warehouse</th>
                            <th class="text-center">Sub Total</th>
                            <!--<th class="text-center">Pay Amount</th>
                            <th class="text-center">Balance</th>-->
                            <th class="text-center">Created Time</th>
                            <th class="text-center">Updated Time</th>
<!--                            <th class="text-center" v-if="sale_type == 1">Delivery Approve</th>-->
                            <th class="text-center"></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Invoice No.</th>
                            <th class="text-center">Reference No.</th>
                            <th class="text-center">Invoice Date</th>
                            <th class="text-center">Branch</th>
                            <th class="text-center">Supplier</th>
<!--                            <th class="text-center">Purchase Man</th>-->
                            <th class="text-center">Office Purchase Man</th>
                            <th class="text-center"> Warehouse</th>
                            <th class="text-center">Sub Total</th>
                            <!--<th class="text-center">Pay Amount</th>
                            <th class="text-center">Balance</th>-->
                            <th class="text-center">Created Time</th>
                            <th class="text-center">Updated Time</th>
<!--                            <th class="text-center" v-if="sale_type == 1">Delivery Approve</th>-->
                            <th class="text-center"></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <template v-for="(p,index) in purchases.data">
                            <tr>
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td class="textalign">{{p.invoice_no}}</td>
                                <td class="textalign">{{p.reference_no}}</td>
                                <td class="textalign">{{p.invoice_date}}</td>
                                <td v-if="p.branch != null">{{p.branch.branch_name}}</td>
                                <td v-else></td>
                                <td class="mm-txt">{{p.supplier.name}}</td>
<!--                                <td class="mm-txt" v-if="p.order != null && p.order.purchase_man != null">{{// p.order.sale_man.name}}</td>-->
<!--                                <td v-else></td>-->
                                <td class="mm-txt" v-if="p.office_purchase_man_id != null">{{p.office_purchase_man.name}}</td>
                                <td v-else></td>
                                <td>{{p.warehouse.warehouse_name}}</td>
                                <td class="text-right">{{parseInt(p.total_amount)-parseInt(p.discount)}}</td>
                                <!--<td class="text-right">{{p.pay_amount+p.collection_amount}}</td>
                                <td class="text-right">{{p.balance_amount-p.collection_amount}}</td>-->
                                <td>{{localTime(p.created_at)}}</td>
                                <td>{{localTime(p.updated_at)}}</td>
<!--                                <template v-if="sale_type == 1">-->
<!--                                    <td class="text-center" v-if="(user_role == 'system' || user_role == 'admin') && sale.delivery_status == 'Draft'">-->
<!--                                        <input-->
<!--                                            type="checkbox"-->
<!--                                            name="chk_approve[]"-->
<!--                                            @change="checkDelivery($event.target,sale.id)"-->
<!--                                            :checked="sale.delivery_approve"-->
<!--                                        >-->
<!--                                    </td>-->

<!--                                    <td class="text-center" v-else>-->
<!--                                        <input-->
<!--                                            type="checkbox"-->
<!--                                            name="chk_approve[]"-->
<!--                                            @change="checkDelivery($event.target,sale.id)"-->
<!--                                            :checked="sale.delivery_approve"-->
<!--                                            disabled-->
<!--                                        >-->
<!--                                    </td>-->
<!--                                </template>-->

                                <td class="text-center" >
<!--                                    temp command-->
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <router-link tag="span" :to="'/purchase/edit/' + p.id" >
                                                    <a href="#" title="Edit/View" class="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>&nbsp;
                                                </router-link>
                                            </a>

                                            <a title="Print MMK" class=" dropdown-item text-primary" @click="generatePDF(p.id)">
                                                <i class="fas fa-print"></i> &nbsp;MMK
                                            </a>
                                            <a :title="'Print '+p.currency.sign" class=" dropdown-item text-primary" @click="generateCurrencyPDF(p.id)" v-if="p.currency_id != 1">
                                                <i class="fas fa-print"></i> &nbsp;{{p.currency.sign}}
                                            </a>
                                            <a class="dropdown-item" v-if="(p.collection_amount==0 && p.payment_type=='credit') || p.payment_type=='cash' ">
                                                <a title="Delete" class="text-danger" @click="removePurchase(p.id)" v-if="(user_role == 'system' || user_role == 'office_user') ">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </a>
                                        </div>
                                    </div>

                                </td>
                            </tr>

                            <!-- detail view for each invoice -->

                            <!-- END detail view for each invoice -->
                        </template>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No record found!</h5>
                </div>

                <!-- detail view -->
                <!-- detail view end -->

            </div>
            <div class="card-footer text-center">

                <!-- pagination start -->
                <div class="row" style="overflow:auto">
                    <div class="col-12">
                        <template v-if="purchase_count > 0">
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
                                    <template v-slot:first-text><span class="text-success" v-on:click="getPurchases(1)">First</span></template>
                                    <template v-slot:prev-text><span class="text-danger" v-on:click="getPurchases(currentPage)">Prev</span></template>
                                    <template v-slot:next-text><span class="text-warning" v-on:click="getPurchases(currentPage)">Next</span></template>
                                    <template v-slot:last-text><span class="text-info" v-on:click="getPurchases(pagination.last_page)">Last</span></template>
                                    <template v-slot:ellipsis-text>
                                    </template>
                                    <template v-slot:page="{ page, active }">
                                        <span v-if="active"><b>{{ page }}</b></span>
                                        <span v-else v-on:click="getPurchases(page)">{{ page }}</span>
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
                invoice_no: "",
                supplier_id: "",
                // purchase_man_id: "",
                office_purchase_man_id: "",
                ref_no: "",
                // delivery_approve: "",
                // invoice_type: "",
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
            rows: "",
            perPage: "15",
            currentPage: 1,
            purchase_count: 0,
            purchases: [],
            suppliers: [],
            purchase_type: '',
            purchase_mans: [],
            office_purchase_mans: [],
            user_role: '',
            user_year: '',
            branches: [],
            site_path: '',
            storage_path: '',
        };
    },

    created() {
        // console.log(this);
        this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
        // console.log(this.site_path.split('/'));
        if(this.user_role == "office_order_user") {
            //var url =  window.location.origin;
            //window.location.replace(url);
        }
        var j;
        /*for(var i=0; i<200; i++) {
            j= i+1;
            localStorage.setItem('storedData'+j,'hello there1 hello there1 hello there1 hello there1 hello there1 hello there1');
        } */
        //localStorage.setItem('storedData2','hello there1 hello there1 hello there1');

        // localStorage.clear();
        this.purchase_type = this.$route.params.purchase_type;
        this.getPurchases();
        //var ls = localStorage.getItem('storedData') != null ?  localStorage.getItem('storedData') : 'no ls'
        //console.log(localStorage.length);
    },

    mounted() {
        let app = this;
        // app.initPurchaseMan();
        // app.initOfficePurchaseMan();
        app.initSuppliers();
        app.initBranches();
        //app.calcLStorageSize();
        // $("#purchase_man_id").on("select2:select", function(e) {
        //     var data = e.params.data;
        //     app.search.purchase_man_id = data.id;
        // });
        $("#office_purchase_man_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.search.office_purchase_man_id = data.id;
        });

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

        $("#supplier_id").on("select2:select", function(e) {

            var data = e.params.data;
            app.search.supplier_id = data.id;
        });
    },

    methods: {

        // initPurchaseMan() {
        //     axios.get("/sale_man").then(({ data }) => (this.purchase_mans = data.data));
        //     $("#sale_man_id").select2();
        // },

        initBranches() {
            axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
            $("#branch_id").select2();
        },

        // initOfficePurchaseMan() {
        //     axios.get("/office_sale_man").then(({ data }) => (this.office_sale_mans = data.data));
        //     $("#office_purchase_man_id").select2();
        // },
        initSuppliers() {
            axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
            $("#supplier_id").select2();
        },

        calcLStorageSize() {
            var lsTotal = 0;
            var  xLen, x;
            var  k = 0;
            for (x in localStorage) {
                if (!localStorage.hasOwnProperty(x)) {
                    continue;
                }
                k++;
                xLen = ((localStorage[x].length + x.length) * 2);
                lsTotal += xLen;
                console.log(x.substr(0, 50) + " = " + (xLen / 1024).toFixed(2) + " KB")
            };
            console.log("Total = " + (lsTotal / 1024).toFixed(2) + " KB");
        },

        getPurchases(page = 1) {
            // $("#loading").show();
            let app = this;
            // console.log(app.search.delivery_approve);
            var search =
                "&from_date=" +
                app.search.from_date +
                "&to_date=" +
                app.search.to_date +
                "&invoice_no=" +
                app.search.invoice_no +
                "&supplier_id=" +
                app.search.supplier_id +
                "&ref_no=" +
                app.search.ref_no +
                "&office_purchase_man_id=" +
                app.search.office_purchase_man_id +
                "&invoice_type=" +
                app.search.invoice_type +
                "&branch_id=" +
                app.search.branch_id ;

            axios.get("/purchase/get_purchase_list/?page=" + page + search).then(function(response) {
                $("#loading").hide();
                let data = response.data.data;
                // console.log(data);
                app.purchases = data;
                app.purchase_count = app.purchases.data.length;
                app.pagination.last_page = data.last_page;
                app.pagination.next = data.next_page_url;
                app.pagination.prev = data.prev_page_url;
                app.pagination.total = data.total;
                app.pagination.current_page = data.current_page;
                app.pagination.next_page_url = data.next_page_url;
                app.rows = data.total;
                app.currentPage = data.current_page;
                // console.log(app);
                // console.log('Current Page is '+app.currentPage);

                // console.log(app.currentPage);
            });

            /*axios.get("/sale/type/" +app.sale_type + "?" + search ).then(({ data }) => (app.sales = data.data))
            .then(function() {
                $("#loading").hide();
            });*/
        },

        removePurchase(id) {
            let app = this;
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then(willDelete => {
                if (willDelete) {
                    axios.get("/purchase/" + id+"/destroy").then(function() {
                        app.getPurchases();
                    });
                    swal("Success! Invoice has been deleted!", {
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

        getUomName(product,uom_id) {
            var key = product.selling_uoms.findIndex(x => x.pivot.uom_id == uom_id);
            if(key == -1) {
                return product.uom.uom_name;
            } else {
                return product.selling_uoms[key].uom_name;
            }
        },

        getUomRelation(product,uom_id) {
            var key = product.selling_uoms.findIndex(x => x.pivot.uom_id == uom_id);
            return product.selling_uoms[key].pivot.relation;
        },

        numberWithCommas(x) {
            if(x != null) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            } else {
                return x;
            }
        },

        generatePDF(purchase_id)
        {
            var baseurl = window.location.origin;
            window.open(this.site_path+'/generate_purchase_invoice/'+purchase_id);
        },

        generateCurrencyPDF(purchase_id)
        {
            var baseurl = window.location.origin;
            window.open(this.site_path+'/generate_purchase_invoice_currency/'+purchase_id);
        },

        printSale(objName)
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

        localTime(utcTime)
        {
            var utcDate = moment.utc(utcTime+'Z');
            // Apply a time zone
            var localTimezone = utcDate.tz('Asia/Rangoon');
            return localTimezone.format('YYYY-MM-DD HH:mm:ss');
        },

        printSalesssss(inv)
        {

            var base_url = window.location.origin;
            var mywindow = window.open('', 'PRINT', 'height=400,width=600');
            //document.getElementById(inv).style.display="block";
            var print_style = '<style type="text/css">@font-face{font-family: "ZawgyiOne2008"; src: url("http://localhost/likelink/public/fonts/zawgyi/ZawgyiOne2008.ttf") format("truetype"), url("http://localhost/likelink/public/fonts/zawgyi/ZawgyiOne2008.svg") format("svg");}.mm-txt{color: red !important;font-family: "ZawgyiOne2008" !important; font-size:11px;}</style>';

            mywindow.document.write('<html><head><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>' + document.title  + '</title>');



            // Make sure the relative URL to the stylesheet works:
            mywindow.document.write('<base href="' + location.origin + location.pathname + '">');

            // Add the stylesheet link and inline styles to the new document:
            mywindow.document.write('<link rel="stylesheet" href="' + location.origin + location.pathname + 'css/print.css" />');

            mywindow.document.write('</head><body >');
            mywindow.document.write(document.getElementById(inv).innerHTML);
            mywindow.document.write('</body></html>');
            //document.getElementById(inv).style.display="none";


            mywindow.document.close(); // necessary for IE >= 10


            setTimeout(function () {
                mywindow.focus(); // necessary for IE >= 10*/
                mywindow.print();
                mywindow.close();

                return true;
            }, 1000);



        },

        checkDelivery(obj, sale_id) {
            let app = this;
            $("#loading").show();
            var is_chk = $(obj).prop("checked");
            if(is_chk) {

                axios
                    .get("/sale_delivery_approval/" + sale_id + "/approve")
                    .then(function(response) {
                        $("#loading").hide();
                        app.getPurchases();
                        swal({
                            title: "Success!",
                            text: 'Approved Successfully!.',
                            icon: "success",
                            button: true
                        });
                    });

            } else {
                axios
                    .get("/sale_delivery_approval/" + sale_id + "/unapprove")
                    .then(function(response) {
                        $("#loading").hide();
                        app.getPurchases();
                        swal({
                            title: "Success!",
                            text: 'Unapproved Successfully!.',
                            icon: "success",
                            button: true
                        });
                    });
            }

        },
    },
}
</script>
