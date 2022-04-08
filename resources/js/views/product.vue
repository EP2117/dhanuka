<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">Product</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Products</h4>
            <div style="inline-block" class="text-right">
            <router-link to="/product/new" class="d-sm-inline-block btn btn-primary shadow-sm">
                <i class="fas fa-plus"></i> Add New Product
            </router-link>

            <a onClick="history.go(-1);" class="btn btn-primary btn text-white" value="Back"><i class="fas fa-angle-double-left"></i> Back</a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">

                <div class="row">

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="brand_id">Brand</label>
                        <select id="brand_id" class="form-control form-control-alternative"
                            name="brand_id" v-model="search.brand_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="brand in brands" :value="brand.id">{{brand.brand_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="category_id">Internal Category</label>
                        <select id="category_id" class="form-control"
                            name="category_id" v-model="search.category_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="cat in categories" :value="cat.id">{{cat.category_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="product_code">Product Code</label>
                        <input type="text" class="form-control" id="product_code" name="product_code" v-model="search.product_code">
                    </div>

                     <div class="form-group col-md-4 col-lg-3">
                        <label for="product_name">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" v-model="search.product_name">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="status">Status</label>
                        <select id="status" class="form-control"
                            name="status" v-model="search.status"
                        >
                            <option value="">Select One</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getProducts(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Product List</h6>
            </div>
            <div class="card-body">

                <div class="table-responsive" v-if="product_count > 0">
                    <!-- sort by -->
                    <div class="form-group float-left pr-2">
                        <label for="sort_by">Sort By</label>
                        <select id="sort_by" class="form-control"
                            name="sort_by" v-model="search.sort_by" style="width:200px" @change="getProducts(1)"
                        >
                            <option value="">Select One</option>
                            <option value="name">Name</option>
                            <option value="code">Product Code</option>
                            <option value="brand">Brand</option>
                            <option value="category">Category</option>
                            <option value="uom">Warehouse UOM</option>
                        </select>
                    </div>
                    <div>

                        <!--<div class="float-left ml-3 pl-3">&nbsp;&nbsp;</div>
                        <div class="form-group ml-3 pl-3">
                            <label for="order">&nbsp;</label>
                            <select id="order" class="form-control"
                                name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getProducts(1)"
                            >
                                <option value="">Select One</option>
                                <option value="ASC">Ascending</option>
                                <option value="DESC">Descending</option>
                            </select>
                        </div> -->
                        <div class="form-group float-left">
                            <select id="order" class="form-control mt-2"
                                name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getProducts(1)"
                            >
                                <option value="">Select One</option>
                                <option value="ASC">Ascending</option>
                                <option value="DESC">Descending</option>
                            </select>
                        </div>
                        <div class="text-right form-group mt-4">
                            <label style="height:10px;">&nbsp;</label>
                            <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                        </div>
                    </div>

                    <!-- end sort by -->
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Product Code</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Warehouse UOM</th>
                                <!--<th class="text-center">Photo</th>-->
                                <th class="text-center">Status</th>                                
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Product Code</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Warehouse UOM</th>
                                <!--<th class="text-center">Photo</th>-->
                                <th class="text-center">Status</th>                                
                                <th class="text-center">Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="product,index in products.data">
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td>{{product.product_name}}</td>
                                <td>{{product.product_code}}</td>
                                <td>{{product.brand_name}}</td>
                                <td>{{product.category_name}}</td>
                                <td>{{product.uom_name}}</td>

                                <!--<td v-if="product.photos != null && product.photos.length > 0">
                                    <template v-for="p,i in product.photos">
                                    <a href="#" @click="showPhoto(p.photo)">Photo{{i+1}}</a><span v-if="i != product.photos.length-1">, </span>
                                    </template>
                                </td>
                                <td v-else></td>-->

                                <td v-if="product.is_active == 1">
                                    <span class="badge badge-success">Active</span>
                                </td>
                                <td v-else>
                                    <span class="badge badge-danger">Inactive</span>
                                </td>

                                <!--<td class="text-center">
                                    <input
                                        type="checkbox"
                                        name="chk_active[]"
                                        @change="checkActive($event.target)"
                                        :data-id = "product.id"
                                        :checked = "product.is_active"
                                    >
                                </td>-->

                                <td class="text-center" style="width:110px;">
                                    <router-link tag="span" :to="'/product/edit/' + product.id" >
                                        <a href="#" title="Edit/View" class="">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    </router-link>

                                    <!--<a href="#" title="Delete" class="text-danger" @click="deleteProduct(product.id)">
                                        <i class="fas fa-trash"></i>
                                    </a> -->
                                    <a class="badge badge-primary text-white"  @click="changeStatus(product.id,'inactive')" v-if="product.is_active == 1">Inactive</a>
                                    <a class="badge badge-primary text-white" @click="changeStatus(product.id,'active')" v-else>Active</a>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No product found!</h5>
                </div>
            </div>

            <div class="card-footer text-center">

              <!-- pagination start -->
              <div class="row" style="overflow:auto">
                <div class="col-12">
                  <template v-if="product_count > 0">
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
                        <template v-slot:first-text><span class="text-success" v-on:click="getProducts(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getProducts(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getProducts(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getProducts(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getProducts(page)">{{ page }}</span>
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
                    brand_id: '',
                    category_id: '',
                    product_code: '',
                    product_name: '',
                    product_code_type: '',
                    sort_by: '',
                    order: '',
                    status: '',
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
                product_count: 0,
                products: [],
                brands: [],
                categories: [],
                user_role: '',
                site_path: '',
                storage_path: '',
            };
        },

        created() {

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            this.getProducts();
        },

        mounted() {
            //$("#loading").hide();
            let app = this;
            console.log('Component mounted.');
            app.initBrands();
            app.initCategories();

            $("#brand_id").select2();
            $("#brand_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.brand_id = data.id;
            });

            $("#category_id").on("select2:select", function(e) {
                var data = e.params.data;
                app.search.category_id = data.id;
            });
        },

        methods: {

            showPhoto(p) {
                var wi = window.open();
                var html = "<div style='text-align:center'><img style='max-width:500px' src='"+p+"' /></div>";
                $(wi.document.body).html(html);
            },

            initBrands() {
              axios.get("/brands").then(({ data }) => (this.brands = data.data));
              $("#brand_id").select2();
            },

            initCategories() {
              axios.get("/categories").then(({ data }) => (this.categories = data.data));
              $("#category_id").select2();
            },

            checkActive(obj) {
                let app = this;
                var active ='';
                var is_active  = $(obj).prop("checked");
                var product_id = $(obj).attr("data-id");
                if(is_active) {
                    active = "active";
                } else {
                    active = "inactive"
                }

                swal({
                    title: "Are you sure to "+active+"?",
                    text: "",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                }).then(willDelete => {
                    if (willDelete) {
                        $("#loading").show();
                        axios
                        .get("/product_status/" + product_id + "/" + active)
                        .then(function(response) {
                            $("#loading").hide();
                            swal("Success! Product has been updated as " + active+".", {
                            icon: "success"
                          });
                        });

                    } else {
                        if(active == "active") {
                            $(obj).prop("checked",false);
                        } else {
                            $(obj).prop("checked",true);
                        }
                    }
                });
            },

            changeStatus(id,status) {
                let app = this;
                var active = status;
                var product_id = id;

                swal({
                    title: "Are you sure to "+active+"?",
                    text: "",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                }).then(willDelete => {
                    if (willDelete) {
                        $("#loading").show();
                        axios
                        .get("/product_status/" + product_id + "/" + active)
                        .then(function(response) {
                            $("#loading").hide();
                            app.getProducts(app.currentPage);
                            swal("Success! Product has been updated as " + active+".", {
                            icon: "success"
                          });
                        });

                    } else {

                    }
                });
            },

            getProducts(page = 1) {
                $("#loading").show();
                let app = this;

                var empty = true;
                $(":text, select").each(function() {
                   if($(this).val() !== "") {
                        empty = false;
                   }

                });

                if(!empty) {
                    var search =
                    "&brand_id=" +
                    app.search.brand_id +
                    "&category_id=" +
                    app.search.category_id +
                    "&product_code=" +
                    app.search.product_code +
                    "&product_name=" +
                    app.search.product_name +
                    "&status=" +
                    app.search.status +
                    "&order=" +
                    app.search.order +
                    "&sort_by=" +
                    app.search.sort_by;

                    axios.get("/product?page=" + page + search).then(function(response) {
                        $("#loading").hide();
                        let data = response.data.data;
                        app.products = data;
                        //console.log(app.products);
                        app.product_count = app.products.data.length;
                        app.pagination.last_page = data.last_page;
                        app.pagination.next = data.next_page_url;
                        app.pagination.prev = data.prev_page_url;
                        app.pagination.total = data.total;
                        app.pagination.current_page = data.current_page;
                        app.pagination.next_page_url = data.next_page_url;


                        app.rows = data.total;
                        app.currentPage = data.current_page;
                        //console.log(app.currentPage);
                    });
                } else {
                    /*$("#loading").hide();
                    swal("Warning!", "Please fill one field to search!", "warning");
                    return false;*/

                     var search =
                    "&brand_id=" +
                    app.search.brand_id +
                    "&category_id=" +
                    app.search.category_id +
                    "&product_code=" +
                    app.search.product_code +
                    "&product_name=" +
                    app.search.product_name +
                    "&status=" +
                    app.search.status +
                    "&order=" +
                    app.search.order +
                    "&sort_by=" +
                    app.search.sort_by;

                    axios.get("/product?page=" + page + search).then(function(response) {
                        $("#loading").hide();
                        let data = response.data.data;
                        app.products = data;
                        console.log(app.products);
                        app.product_count = app.products.data.length;
                        app.pagination.last_page = data.last_page;
                        app.pagination.next = data.next_page_url;
                        app.pagination.prev = data.prev_page_url;
                        app.pagination.total = data.total;
                        app.pagination.current_page = data.current_page;
                        app.pagination.next_page_url = data.next_page_url;


                        app.rows = data.total;
                        app.currentPage = data.current_page;
                        //console.log(app.currentPage);
                    });
                }

                /*var search = "";
                axios.get("/product?page=" + page + search).then(({ data }) => (app.products = data.data))
                .then(function() {
                    console.log(app.products);
                    $("#loading").hide();
                });*/

            },

            exportExcel() {

                let app = this;

                var search =
                    "&brand_id=" +
                    app.search.brand_id +
                    "&category_id=" +
                    app.search.category_id +
                    "&product_code=" +
                    app.search.product_code +
                    "&product_name=" +
                    app.search.product_name +
                    "&status=" +
                    app.search.status +
                    "&order=" +
                    app.search.order +
                    "&sort_by=" +
                    app.search.sort_by;


                //var baseurl = window.location.origin;
                this.site_path
                window.open(app.site_path+'/product_export?'+search);
            },

            deleteProduct(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/product/" + id).then(function() {
                            app.getProducts();
                        });
                        swal("Success! Product has been deleted!", {
                            icon: "success"
                          });
                        } else {
                          //
                    }
                });
            }
        }
    }
</script>
