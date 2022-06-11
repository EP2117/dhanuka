<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item" v-if="sale_type == 1"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item" v-else><a :href="site_path+'/van'">Van Sale</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Invoice</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Sale Invoice</h4>
            <router-link :to="'/sale/'+sale_type+'/new'" class="d-sm-inline-block btn btn-primary shadow-sm inventory" v-if="user_role == 'system' || user_role == 'office_user' || user_role == 'van_user'">
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
                        <label for="customer_id">Customer</label>
                        <select id="customer_id" class="form-control mm-txt"
                            name="customer_id" v-model="search.customer_id" style="width:100%" required
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
                    <div class="form-group col-md-4 col-lg-3 mm-txt"  v-if="user_role != 'van_user' && user_role != 'office_order_user' && sale_type == 1">
                        <label for="sale_man_id">Sale Man</label>
                        <select id="sale_man_id" class="form-control mm-txt"
                            name="sale_man_id" v-model="search.sale_man_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="sale_man in sale_men" :value="sale_man.id"  >{{sale_man.sale_man}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3 mm-txt"  v-if=" user_role == 'admin'">
                        <label for="office_sale_man_id">Office Sale Man</label>
                        <select id="office_sale_man_id" class="form-control mm-txt"
                            name="office_sale_man_id" v-model="search.office_sale_man_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="office_sale_man in office_sale_mans" :value="office_sale_man.id"  >{{office_sale_man.name}}</option>
                        </select>
                    </div> 

                    <!--<div class="form-group col-md-4 col-lg-3">
                        <label for="ref_no">Reference No.</label>
                        <input type="text" class="form-control" id="ref_no" name="ref_no" v-model="search.ref_no">
                    </div>-->

                    <div class="form-group col-md-4 col-lg-3" v-if="sale_type == 1">
                        <label for="invoice_type">Invoice Type</label>
                        <select id="invoice_type" class="form-control mm-txt"
                            name="invoice_type" v-model="search.invoice_type" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option value="direct">Direct Invoice</option>
                            <option value="order">Order Invoice</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3" v-if="sale_type == 1">
                        <label for="delivery_approve">Delivery Approve</label>
                        <select id="delivery_approve" class="form-control mm-txt"
                            name="delivery_approve" v-model="search.delivery_approve" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option value="true">Dagon</option>
                            <option value="false">Others</option>
                        </select>
                        <!--<input type="checkbox" class="form-control" id="delivery_approve" name="delivery_approve" v-model="search.delivery_approve" style="width:20px;height:20px;">-->
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getSales(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sale List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="sale_count > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Invoice No.</th>
                                <!--<th class="text-center">Reference No.</th>-->
                                <th class="text-center">Invoice Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Sale Man</th>
                                <th class="text-center" v-if="user_role=='admin'">Office User</th>
                                <!--<th class="text-center">Office Sale Man</th>
                                <th class="text-center">Sale Warehouse</th>-->
                                <!--<th class="text-center">Net Total</th>
                                <th class="text-center">Pay Amount</th>-->
                                <th class="text-center">Net Total</th>
                                <th class="text-center" v-if="sale_type == 1">Delivery Approve</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Invoice No.</th>
                                <th class="text-center">Invoice Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Sale Man</th>
                                <th class="text-center" v-if="user_role=='admin'">Office User</th>

                                <!--<th class="text-center">Sub Total</th>
                                <th class="text-center">Pay Amount</th>-->
                                <th class="text-center">Net Total</th>
                                <th class="text-center" v-if="sale_type == 1">Delivery Approve</th>
                                <th class="text-center"></th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <template v-for="sale,index in sales.data">
                            <tr>
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td class="textalign">{{sale.invoice_no}}</td>
                                <td class="textalign">{{sale.invoice_date}}</td>
                                <td v-if="sale.branch != null">{{sale.branch.branch_name}}</td>
                                <td v-else></td>
                                <td class="mm-txt">{{sale.customer.cus_name}}</td>
                                <td class="mm-txt" v-if="sale.office_sale_man_id != null">{{sale.sale_man.sale_man}}</td>
                                <td v-else></td>
                                <td class="mm-txt" v-if="user_role=='admin'">{{sale.created_user.name}}</td>
                                <!--<td class="text-right">{{sale.total_amount}}</td>
                                <td class="text-right">{{sale.pay_amount}}</td>-->
                                <td class="text-right">{{sale.net_total}}</td>
                                <template v-if="sale_type == 1">
                                <td class="text-center" v-if="(user_role == 'system' || user_role == 'admin') && sale.delivery_status == 'Draft'">
                                    <input
                                        type="checkbox"
                                        name="chk_approve[]"
                                        @change="checkDelivery($event.target,sale.id)"
                                        :checked="sale.delivery_approve"
                                    >
                                </td> 

                                <td class="text-center" v-else>
                                    <input
                                        type="checkbox"
                                        name="chk_approve[]"
                                        @change="checkDelivery($event.target,sale.id)"
                                        :checked="sale.delivery_approve"
                                        disabled
                                    >
                                </td>
                                </template>

                                <td class="text-center">
                                    <!-- before delivery -->
                                    <!--<router-link tag="span" :to="'/sale/'+sale_type+'/edit/' + sale.id" >
                                        <a href="#" title="Edit/View" class="">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    </router-link>

                                    <a title="Print" class="text-primary" @click="printSale(sale.id)" v-if="sale_type == 2 && user_role != 'Country Head' && user_role != 'Local Supervisor' && user_role != 'office_order_user'">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <a title="Print" class="text-primary" @click="generatePDF(sale.id)" v-if="sale_type == 1 && user_role != 'Country Head' && user_role != 'Local Supervisor' && user_role != 'office_order_user'">
                                        <i class="fas fa-print"></i>
                                    </a>
                                        
                                    <a title="Delete" class="text-danger" @click="removeSale(sale.id)" v-if="(user_role == 'system' || user_role == 'admin') && sale.collections.length == 0">
                                        <i class="fas fa-trash"></i>
                                    </a>&nbsp;-->

                                    <!-- after delivery -->

                                    <!--<router-link tag="span" :to="'/sale/'+sale_type+'/edit/' + sale.id" v-if="user_role != 'delivery'">
                                        <a href="#" title="Edit/View" class="">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    </router-link>

                                    <a title="Print" class="text-primary" @click="printSale(sale.id)" v-if="sale_type == 2 && user_role != 'Country Head' && user_role != 'Local Supervisor' && user_role != 'office_order_user'">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <a title="Print" class="text-primary" @click="generatePDF(sale.id)" v-if="sale_type == 1 && user_role != 'Country Head' && user_role != 'Local Supervisor' && user_role != 'office_order_user'">
                                        <i class="fas fa-print"></i>
                                    </a>
                                        
                                    <a title="Delete" class="text-danger" @click="removeSale(sale.id)" v-if="(user_role == 'system' || user_role == 'admin') && sale.collections.length == 0 && sale.delivery_approve == 0 && sale.deliveries.length == 0">
                                        <i class="fas fa-trash"></i>
                                    </a>&nbsp;-->

                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <router-link tag="span" :to="'/sale/'+sale_type+'/edit/' + sale.id" v-if="user_role != 'delivery'">
                                                    <a href="#" title="Edit/View" class="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>&nbsp;
                                                </router-link>        
                                            </a>

                                            <a class="dropdown-item">
                                                <a title="Print" class="text-primary" @click="printSale(sale.id)" v-if="sale_type == 2 && user_role != 'Country Head' && user_role != 'Local Supervisor' && user_role != 'office_order_user'">
                                                    <i class="fas fa-print"></i>
                                                </a>        
                                            </a>

                                            <a class="dropdown-item">
                                                <a title="Print MMK" class="text-primary" @click="generatePDF(sale.id)" v-if="sale_type == 1 && user_role != 'Country Head' && user_role != 'Local Supervisor' && user_role != 'office_order_user'">
                                                    <i class="fas fa-print"></i> &nbsp;MMK
                                                </a>          
                                            </a>

                                            <a class="dropdown-item">
                                                <a :title="'Print '+sale.currency.sign" class="text-primary" @click="generateCurrencyPDF(sale.id)" v-if="sale.currency_id != 1 && sale_type == 1 && user_role != 'Country Head' && user_role != 'Local Supervisor' && user_role != 'office_order_user'">
                                                    <i class="fas fa-print"></i> &nbsp;{{sale.currency.sign}}
                                                </a>          
                                            </a>

                                            <a class="dropdown-item">
                                                <a title="Delete" class="text-danger" @click="removeSale(sale.id)" v-if="(user_role == 'system' || user_role == 'office_user') && sale.collections.length == 0 && sale.delivery_approve == 0 && sale.deliveries.length == 0  && sale.return_amount == 0 && sale.cash_return_amount == 0">
                                                    <i class="fas fa-trash"></i>
                                                </a>           
                                            </a>
                                        </div>
                                    </div>
                                                                      
                                </td>
                            </tr>

                            <!-- detail view for each invoice -->
                            <template v-if="sale_type == 2">
                                <div :id="sale.id" style="display:none;">
                                    <div style="text-aling:center;display:inline-block; margin:0 auto; width:100%;">
                                        <h4 class="text-center title">Like Link Co., Ltd.</h4>
                                        <!--<div style="text-align:center">
                                            <img src="images/LikeLinklogo.png" width="100" />
                                        </div>-->
                                        <table style="border:none;">
                                            <tr>
                                                <td class="mm-txt">၀ယ္သူ</td>
                                                <td class="mm-txt">{{sale.customer.cus_name}}</td>
                                                <td class="mm-txt" style="text-align:right">ရက္စဲြ</td>
                                                <td style="text-align:right">{{sale.invoice_date}}</td>
                                            </tr>
                                            <tr>
                                                <td class="mm-txt">လိပ္စာ</td>
                                                <td>{{sale.customer.cus_shipping_address}}</td>
                                                <td style="text-align:right">Invoice No.</td>
                                                <td style="text-align:right">{{sale.invoice_no}}</td>
                                            </tr>
                                        </table>

                                        <table class="t-class" cellspacing="2" style="margin-top:20px;">
                                            <thead>
                                                <tr>
                                                    <td class='mm-txt font-bold'>စဥ္</td>
                                                    <td class='mm-txt font-bold'>အမ်ိဳးအမည္</td>
                                                    <td class='mm-txt font-bold' style="min-width:50px;">အေရအတြက္</td>
                                                    <td class='mm-txt font-bold' style="min-width:30px;">ေစ်းႏႈန္း</td>
                                                    <td class='mm-txt font-bold'>သင့္ေငြ</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="product,i in sale.products">
                                                    <td>{{i+1}}</td>
                                                    <td>{{product.product_name}}</td>

                                                    <td>
                                                        {{product.pivot.product_quantity}} {{getUomName(product,product.pivot.uom_id)}}
                                                    </td>

                                                    <td v-if="product.pivot.is_foc == '0'">{{product.pivot.price}}</td>
                                                    <td v-else>FOC</td>
                                                    <td v-if="product.pivot.is_foc == '0'" class="text-right">{{product.pivot.total_amount}}</td>
                                                    <td v-else></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right mm-txt">စုစုေပါင္း</td>
                                                    <td class="text-right">{{numberWithCommas(sale.total_amount)}}</td>
                                                </tr>
                                                <!--<tr>
                                                    <td colspan="4" class="text-right mm-txt">လက္က်န္ေငြ</td>
                                                    <td class="text-right">{{sale.balance_amount}}</td>
                                                </tr>-->
                                            </tbody>
                                        </table>

                                        <h4 class="text-center">Thanks for your purchase!</h4>
                                    </div>

                                </div>
                            </template>

                            <template v-else>
                                <div :id="sale.id" style="display:none;">
                                    <div style="text-aling:center;display:inline-block; margin-top:212px; width:100%;">
                                        <!--<div style="text-align:center">
                                            <img src="images/LikeLinklogo.png" width="100" />
                                        </div>-->
                                        <table style="border:none;">
                                            <tr>
                                                <td class="mm-txt">၀ယ္သူ</td>
                                                <td class="mm-txt">{{sale.customer.cus_name}}</td>
                                                <td class="mm-txt" style="text-align:right">ရက္စဲြ</td>
                                                <td style="text-align:right">{{sale.invoice_date}}</td>
                                            </tr>
                                            <tr>
                                                <td class="mm-txt">လိပ္စာ</td>
                                                <td>{{sale.customer.cus_shipping_address}}</td>
                                                <td style="text-align:right">Invoice No.</td>
                                                <td style="text-align:right">{{sale.invoice_no}}</td>
                                            </tr>
                                        </table>

                                        <table class="t-office-class" cellspacing="0" style="margin-top:20px;">
                                            <thead>
                                                <tr>
                                                    <td class='mm-txt font-bold' style="text-align:center;">စဥ္</td>
                                                    <td class='mm-txt font-bold' style="text-align:center;">အမ်ိဳးအမည္</td>
                                                    <td class='mm-txt font-bold' style="min-width:50px;text-align:center;">အေရအတြက္</td>
                                                    <td class='mm-txt font-bold' style="min-width:30px;text-align:center;">ေစ်းႏႈန္း</td>
                                                    <td class='mm-txt font-bold' style="text-align:center;">သင့္ေငြ</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="product,i in sale.products">
                                                    <td>{{i+1}}</td>
                                                    <td>{{product.product_name}}</td>

                                                    <td v-if="product.pivot.uom_id == product.uom_id">
                                                        {{product.pivot.product_quantity}} {{getUomName(product,product.pivot.uom_id)}}
                                                    </td>

                                                    <td v-else>
                                                        {{product.pivot.product_quantity}} {{getUomName(product,product.pivot.uom_id)}} x {{getUomRelation(product,product.pivot.uom_id)}} {{getUomName(product,product.uom_id)}}
                                                    </td>

                                                    <td v-if="product.pivot.is_foc == '0'">{{product.pivot.price}}</td>
                                                    <td v-else>FOC</td>
                                                    <td v-if="product.pivot.is_foc == '0'" class="text-right">{{product.pivot.total_amount}}</td>
                                                    <td v-else></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right mm-txt">စုစုေပါင္း</td>
                                                    <td class="text-right">{{numberWithCommas(sale.total_amount)}}</td>
                                                </tr>
                                                <!--<tr>
                                                    <td colspan="4" class="text-right mm-txt">လက္က်န္ေငြ</td>
                                                    <td class="text-right">{{sale.balance_amount}}</td>
                                                </tr>-->
                                            </tbody>
                                        </table>

                                        <table style="border:none;width:100%;margin-top:50px;">
                                            <tr>
                                                <td class="mm-txt">Check By</td>
                                                <td style="text-align:right">Receive By</td>
                                            </tr>
                                        </table>
                                    </div>

                                </div>
                            </template>
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
                  <template v-if="sale_count > 0">
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
                        <template v-slot:first-text><span class="text-success" v-on:click="getSales(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getSales(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getSales(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getSales(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getSales(page)">{{ page }}</span>
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
                    customer_id: "",
                    sale_man_id: "",
                    office_sale_man_id: "",
                    // created_user:'',
                    ref_no: "",
                    delivery_approve: "",
                    invoice_type: "",
                    branch_id: "",
                    state_id: "",
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
                sale_count: 0,
                sales: [],
                customers: [],
                sale_type: '',
                sale_mans: [],
                office_sale_mans: [],
                user_role: '',
                user_year: '',
                branches: [],
                states:[],
                site_path: '',
                storage_path: '',
                sale_men: [],
            };
        },

        created() {
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            console.log(this.site_path.split('/'));
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
            this.sale_type = this.$route.params.sale_type;
            this.getSales(); 
            //var ls = localStorage.getItem('storedData') != null ?  localStorage.getItem('storedData') : 'no ls'
            //console.log(localStorage.length);
        },

        mounted() {
            let app = this;
            //app.initSaleMan();
            app.initSaleMen();
            app.initOfficeSaleMan();
            app.initCustomers();
            app.initBranches();
            app.initStates();
            
            // app.initCreatedUser();
            //app.calcLStorageSize();

            $("#sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.sale_man_id = data.id;
            });
            $("#office_sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.office_sale_man_id = data.id;
            });

            $("#office_sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.office_sale_man_id = data.id;
            });

            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.branch_id = data.id;
            });
             $("#state_id").select2();
            $("#state_id").on("select2:select", function(e) {
                app.townships=[];
                var data = e.params.data;
                app.search.state_id = data.id;
                axios.get("/township_by_state/"+ data.id).then(({ data }) => (app.townships = data.data));

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

            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.customer_id = data.id;
            });
        },

        methods: {

            initSaleMen() {
              axios.get("/sale_men").then(({ data }) => (this.sale_men = data.data));              
              $("#sale_man_id").select2();
            },
             initStates() {
                    axios.get("/state").then(({ data }) => (this.states = data.data));
                    $("#state_id").select2();
            },
            initSaleMan() {
              axios.get("/sale_man").then(({ data }) => (this.sale_mans = data.data));
              //$("#sale_man_id").select2();
            },

            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            },

            initOfficeSaleMan() {
              axios.get("/office_sale_man").then(({ data }) => (this.office_sale_mans = data.data));
              $("#office_sale_man_id").select2();
            },

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
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
                console.log(k);
                console.log("Total = " + (lsTotal / 1024).toFixed(2) + " KB");
            },

            getSales(page = 1) {
                $("#loading").show();
                let app = this;
                console.log(app.search.delivery_approve);

                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&invoice_no=" +
                    app.search.invoice_no +
                    "&customer_id=" +
                    app.search.customer_id +
                    "&ref_no=" +
                    app.search.ref_no +
                    "&office_sale_man_id=" +
                    app.search.office_sale_man_id +
                    "&delivery_approve=" +
                    app.search.delivery_approve +
                    "&invoice_type=" +
                    app.search.invoice_type +
                    "&branch_id=" +
                    app.search.branch_id +
                     "&state_id=" +
                    app.search.state_id+
                    "&sale_man_id=" +
                    app.search.sale_man_id; 
                axios.get("/sale/type/" +app.sale_type + "?page=" + page + search).then(function(response) {
                    $("#loading").hide();
                    let data = response.data.data;
                    app.sales = data;
                    app.sale_count = app.sales.data.length;
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

                /*axios.get("/sale/type/" +app.sale_type + "?" + search ).then(({ data }) => (app.sales = data.data))
                .then(function() {
                    $("#loading").hide();
                });*/
            },

            removeSale(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/sale/" + id).then(function() {
                            app.getSales();
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
            generatePDF(sale_id)
            {
                var baseurl = window.location.origin;
                window.open(this.site_path+'/generate_invoice/'+sale_id);
            },

            generateCurrencyPDF(sale_id)
            {
                var baseurl = window.location.origin;
                window.open(this.site_path+'/generate_invoice_currency/'+sale_id);
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
                            app.getSales();
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
                            app.getSales();
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