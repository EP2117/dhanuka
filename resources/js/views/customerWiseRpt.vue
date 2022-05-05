<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Category Wise Contact Report</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Category Wise Contact Report</h4>
           <!-- <router-link to="/inventory/transfer/new" class="d-sm-inline-block btn btn-primary shadow-sm inventory">
                <i class="fas fa-plus"></i> Add New Transfer
            </router-link>-->
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <!--<div class="form-group col-md-4 col-lg-3">
                        <label for="from_date">From Date</label>
                        <input type="text" class="form-control datetimepicker" id="from_date" name="from_date"
                        v-model="search.from_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="to_date">To Date</label>
                        <input type="text" class="form-control datetimepicker" id="to_date" name="to_date"
                        v-model="search.to_date">
                    </div>-->

                    <!--<div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="category_id">Category</label>
                        <select id="category_id" class="form-control"
                            name="category_id" v-model="search.category_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="cat in categories" :value="cat.id">{{cat.category_name}}</option>
                        </select>
                    </div>-->

                    <div class="form-group col-md-4 col-lg-3">
                        <label>Category</label>
                        <select class="form-control categories"
                            name="categories[]" id="categories" style="width:100%" multiple>
                            <option value="">Select One</option>
                            <option v-for="cat in categories" :value="cat.id">{{cat.category_name}}</option>
                        </select>
                    </div>
                     <div class="form-group col-md-4 col-lg-3">
                        <label>Product</label>
                        <select class="form-control products"
                            name="products[]" id="products" style="width:100%" multiple>
                            <option value="">Select One</option>
                            <option v-for="p in products"  :value="p.product_id">{{p.name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="cus_code">Customer Code</label>
                        <input type="text" class="form-control" id="cus_code" name="cus_code" v-model="search.cus_code">
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="entry_date">Customer</label>
                        <select id="customer_id" class="form-control mm-txt"
                            name="customer_id" v-model="search.customer_id" style="width:100%" 
                        >
                            <option value="">Select One</option>
                            <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label >State</label>
                        <select id="state_id" class="form-control mm-txt"
                                 v-model="search.state_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="s in states" :value="s.id"  >{{s.state_name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="township_id">Township</label>
                        <select id="township_id" class="form-control mm-txt"
                            name="township_id" v-model="search.township_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="tsp in townships" :value="tsp.id"  >{{tsp.township_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getCustomerWise(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Category Wise Contact List</h6>
            </div>
            <div class="card-body">
                <div class="text-right form-group" >
                        <div class="mb-2" v-if="results.length > 0" style="display:inline-block">
                            <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                        </div>

                        <div class="mb-2 pl-2" v-if="results.length > 0" style="display:inline-block;">
                            <button class="btn btn-primary btn-icon btn-sm" @click="exportPdf()"><i class="fas fa-file-pdf"></i> &nbsp;Export to PDF</button>
                        </div>
                    </div>
                <!-- sort by -->
                <!--<div class="form-group float-left pr-2" v-if="approvals.length > 0">
                    <label for="sort_by">Sort By</label>
                    <select id="sort_by" class="form-control"
                        name="sort_by" v-model="search.sort_by" style="width:200px" @change="getSOProducts(1)"
                    >
                        <option value="">Select One</option>
                        <option value="order_no">Sale Order No.</option>
                    </select>
                </div>
                <div v-if="approvals.length > 0">

                    <div class="form-group float-left">
                        <select id="order" class="form-control mt-2"
                            name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getSOProducts(1)"
                        >
                            <option value="">Select One</option>
                            <option value="ASC">Ascending</option>
                            <option value="DESC">Descending</option>
                        </select>
                    </div>
                    <div class="text-right form-group mt-4" >
                        <div class="text-right mb-2" v-if="approvals.length > 0">
                            <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                        </div>
                    </div>
                </div>-->
                <!-- end sort by -->

               <div class="table-responsive">
                    <table class="table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th style="width:360px;">
                                 <div style="float:left;width:150px;padding:0;margin:0">Category</div>
                                 <div style="float:left;width:150px;padding:0;margin:0">Product</div>
                                </th>
                                <!--<th class="text-center">Product</th>-->
                                <th>Customer Code</th>
                                <th>Customer Name</th>
                                <th>Phone Number</th>
                            </tr>
                        </thead>
                        <tbody :set="j=0">
                            <template v-for="c,i in results" >
                                <tr v-if="i==0 || (c.state_id != results[i-1].state_id)">
                                    <td class="text-center" colspan="5" ><b>State - {{c.state_name}}</b></td>
                                </tr>
                                <tr v-if="i==0 || (c.township_id != results[i-1].township_id)">
                                    <td class="text-center" colspan="5" ><b>Township - {{c.township_name}}</b></td>
                                </tr>
                                <tr style="border:solid 1px #ccc">
                                    <td class="text-right" v-if="c.id != null" style="vertical-align:middle; border-left:solid 2px #ccc;border-right:solid 1px #ccc;">{{j= j+1}}</td>
                                    <td v-else style="vertical-align:middle;border-left:solid 2px #ccc;border-right:solid 1px #ccc;"></td>
                                    
                                    <td style="padding:0;margin:0;">
                                      <table cellpadding='0' cellspacing='0' border='0' width="100%">
                                      <template v-if="c.category_id != null">
                                        <template v-for="(cid,i) in c.category_id.split(',')" >
                                            <template v-if="search.categories.length == 0 || (search.categories.length > 0 && search.categories.indexOf(cid) !== -1) || (c.cat_product_id.split('_')[i].split(',').filter(function(item){ return search.products.indexOf(item) > -1})).length > 0">

                                            <tr style="border:solid 1px #ccc">

                                              <td style='background-color:#fff;width:150px;text-align:left;border-top:0px;border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;vertical-align:middle' >{{c.category_name.split(',')[i]}}
                                              </td>

                                              <template  v-if="c.product_id != null">

                                              <td style="padding:0;margin:0">
                                              
                                              <table cellpadding='0' cellspacing='0' border='0' width="100%">
                                               
                                                    <template v-for="(pid,j) in c.product_id.split(',')">
                                                    <tr v-if="c.cat_product_id.split('_')[i].split(',').indexOf(pid) !== -1" style="border:solid 1px #ccc">

                                                    <template v-if="search.products.length == 0 || (search.products.length > 0 && search.products.indexOf(pid) !== -1)">

                                                    <td style='text-align:left;background-color:#fff;width:150px;border-top:0px;border-bottom:solid 1px #ccc'>{{c.product_name.split(',')[j]}}</td>
                                                    </template>

                                                    </tr>
                                                    </template>
                                                
                                              </table>
                                              
                                            </td>
                                            </template>
                                            </tr>
                                        </template>
                                        </template>
                                        </template>
                                      </table>
                                    </td>                                    
                                    
                                    <td class="text-center" style="vertical-align:middle;border:solid 1px #ccc;">{{c.cus_code}}</td>
                                    <td class="mm-txt" style="vertical-align:middle;border:solid 1px #ccc;">{{c.cus_name}}</td>
                                    <td class="mm-txt" style="vertical-align:middle;border:solid 1px #ccc;">{{c.cus_phone}}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
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
                    category_id: '',
                    cus_code: "",
                    customer_id: "",
                    categories: [],
                    products: [],
                    state_id: '',
                    township_id: '',
                },
                results: [],
                customers:[],
                categories: [],
                products: [],
                user_year: '',
                user_role: '',
                site_path: '',
                storage_path: '',
                selected_categories: [],
                selected_products: [],
                townships: [],
                states: [],
            };
        },

        created() {
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
        },

        mounted() {
            $("#loading").hide();
            let app = this;

            app.initCustomers();
            app.initCategories();
            app.initProducts(); 

            app.initTownships();
            app.initStates();
            //app.initBrands();
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
                app.search.to_date = moment().format('YYYY-MM-DD');
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

            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.customer_id = data.id;
            });

            $(".categories").select2();                      

            $(".categories").on("select2:select", function(e) {
                var data = e.params.data;
                app.selected_categories.push(data.id); 

                var unique_categories = app.selected_categories.filter((a, b) => app.selected_categories.indexOf(a) === b);
                // console.log(unique_invoices);
                app.selected_categories = unique_categories;

                $('.categories').val(app.selected_categories).trigger('change');
            });

            $(".categories").on("select2:unselect", function(e) {
                var data = e.params.data;
                var unique_categories = app.selected_categories.filter((a, b) => app.selected_categories.indexOf(a) === b);
                app.selected_categories = unique_categories;
                const index = app.selected_categories.indexOf(data.id);
                if (index > -1) {
                  app.selected_categories.splice(index, 1);
                }
                $('.categories').val(app.selected_categories).trigger('change');
            });

            $(".products").select2();                      

            $(".products").on("select2:select", function(e) {
                var data = e.params.data;
                app.selected_products.push(data.id); 

                var unique_products = app.selected_products.filter((a, b) => app.selected_products.indexOf(a) === b);
                // console.log(unique_invoices);
                app.selected_products = unique_products;

                $('.products').val(app.selected_products).trigger('change');
            });

            $(".products").on("select2:unselect", function(e) {
                var data = e.params.data;
                var unique_products = app.selected_products.filter((a, b) => app.selected_products.indexOf(a) === b);
                app.selected_products = unique_products;
                const index = app.selected_products.indexOf(data.id);
                if (index > -1) {
                  app.selected_products.splice(index, 1);
                }
                $('.products').val(app.selected_products).trigger('change');
            });

            $("#state_id").select2();
            $("#state_id").on("select2:select", function(e) {
                app.townships=[];
                var data = e.params.data;
                app.search.state_id = data.id;
                if(app.search.state_id != '') {
                    axios.get("/township_by_state/"+ data.id).then(({ data }) => (app.townships = data.data));
                } else {
                    app.initTownships();
                }

            });

            $("#township_id").select2();
            $("#township_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.township_id = data.id;
            });
        },

        methods: {

            initStates() {
                axios.get("/state").then(({ data }) => (this.states = data.data));
                $("#state_id").select2();
            },

            initTownships() {
              axios.get("/township").then(({ data }) => (this.townships = data.data));
              $("#township_id").select2();
            },

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },

            initCategories() {
              axios.get("/categories").then(({ data }) => (this.categories = data.data));
              $("#categories").select2();
            },

            initProducts() {
              axios.get("/order/products/").then(({ data }) => (this.products = data.data));
              console.log(this.products);
              $(".products").select2();
            },

            localTime(utcTime) 
            {
                var utcDate = moment.utc(utcTime+'Z');
                // Apply a time zone
                var localTimezone = utcDate.tz('Asia/Rangoon');
                return localTimezone.format('YYYY-MM-DD hh:mm:ss');
            },

            dateFormat(d) {
                return moment(d).format('YYYY-MM-DD hh:mm');
            },

            getCustomerWise(page = 1) {

                /*if(this.search.from_date == "") {                  
                    swal("Warning!", "From Date must be added!", "warning")
                    return false;
                }*/
                let app = this;

                app.search.categories = [];
                $('#categories :selected').each(function() {                    
                    app.search.categories.push($(this).val());                   
                });

                console.log($("#categories").val());

                app.search.products = [];
                $('#products :selected').each(function() {                    
                    app.search.products.push($(this).val());                   
                });
                var categories = $("#categories").val().join(',');
                var products = $("#products").val().join(',');
                console.log(categories);
                console.log(products);
                $("#loading").show();


                var search =
                    "&category_id=" +
                    app.search.category_id +
                    "&township_id=" +
                    app.search.township_id +
                    "&state_id=" +
                    app.search.state_id +
                    "&cus_code=" +
                    app.search.cus_code +
                    "&categories=" +
                    categories +
                    "&products=" +
                    products +
                    "&customer_id=" +
                    app.search.customer_id;

                axios.get("/customer_wise_report?" + search).then(({ data }) => (app.results = data.data))
                .then(function() {
                    $("#loading").hide();
                });
            },

            exportExcel() {    

                let app = this;
               /* if(this.search.from_date == "") {                  
                    swal("Warning!", "From Date must be added!", "warning")
                    return false;
                } */
                var categories = $("#categories").val().join(',');
                var products = $("#products").val().join(',');
              var search =
                    "&category_id=" +
                    app.search.category_id +
                    "&township_id=" +
                    app.search.township_id +
                    "&state_id=" +
                    app.search.state_id +
                    "&cus_code=" +
                    app.search.cus_code +
                    "&categories=" +
                    categories +
                    "&products=" +
                    products +
                    "&customer_id=" +
                    app.search.customer_id;

                /*axios.get("/so_product_export?" + search)
                .then(function(response) {
                  console.log(response);
                })
                .catch(error => {
                  console.log(error);
                })
                .finally(() => loading.hide());*/

                var baseurl = window.location.origin;
                //window.open(baseurl+'/pending_approval_export?'+search);
                window.open(this.site_path+'/customer_wise_export?'+search);
            },

            exportPdf() {   

                let app = this;
                var categories = $("#categories").val().join(',');
                var products = $("#products").val().join(',');
              var search =
                    "&category_id=" +
                    app.search.category_id +
                    "&township_id=" +
                    app.search.township_id +
                    "&state_id=" +
                    app.search.state_id +
                    "&cus_code=" +
                    app.search.cus_code +
                    "&categories=" +
                    categories +
                    "&products=" +
                    products +
                    "&customer_id=" +
                    app.search.customer_id;

                swal({
                    title: "Do you want to show Product in PDF?",
                    text: "",
                    icon: "warning",
                    dangerMode: false,
                    buttons: ["No", "Yes"],
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.get("/customer_wise_export_pdf?pshow=1&" + search, {responseType: 'blob'}).then(response => {
                            $('#loading').hide();
                            const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
                            window.open(url);

                          })
                          .catch(error => {
                            console.log(error);
                          });   
                    } else {
                        axios.get("/customer_wise_export_pdf?pshow=0&" + search, {responseType: 'blob'}).then(response => {
                            $('#loading').hide();
                            const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
                            window.open(url);

                          })
                          .catch(error => {
                            console.log(error);
                          });
                    }
                });

                /***axios.get("/customer_wise_export_pdf?" + search, {responseType: 'blob'}).then(response => {
                    $('#loading').hide();
                    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
                    window.open(url);

                  })
                  .catch(error => {
                    console.log(error);
                  });***/

                
            },
        },

    }
</script>