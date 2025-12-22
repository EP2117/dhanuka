<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/inventory'">Inventory</a></li>
                <li class="breadcrumb-item active" aria-current="page">Internal Transfer</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Internal Transfer</h4>
            <router-link to="/inventory/transfer/new" class="d-sm-inline-block btn btn-primary shadow-sm inventory" v-if="user_role != 'admin'">
                <i class="fas fa-plus"></i> Add New Transfer
            </router-link>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Transfer List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="transfers.length > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Transfer No.</th>
                                <th class="text-center">Transfer Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">From Warehouse</th>
                                <th class="text-center">To Warehouse</th>
                                <th class="text-center">Received</th>
                                <th class="text-center">Received Date</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Transfer No.</th>
                                <th class="text-center">Transfer Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">From Warehouse</th>
                                <th class="text-center">To Warehouse</th>
                                <th class="text-center">Received</th>
                                <th class="text-center">Received Date</th>
                                <th class="text-center"></th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="transfer,index in transfers">
                                <td class="text-right">{{index+1}}</td>
                                <td class="textalign">{{transfer.transfer_no}}</td>
                                <td class="textalign">{{transfer.transfer_date}}</td>
                                <td v-if="transfer.branch != null">{{transfer.branch.branch_name}}</td>
                                <td v-else></td>
                                <td>{{transfer.from_warehouse.warehouse_name}}</td>
                                <td>{{transfer.to_warehouse.warehouse_name}}</td>
                                <td class="text-center textalign" v-if="transfer.received_date != null">
                                    <i class="fas fa-check-circle text-success" style="font-size: 28px;"></i>
                                </td>
                                <td class="text-warning text-center textalign" v-else>Pending</td>
                                <td v-if="transfer.received_date != null" class="textalign">
                                   {{transfer.received_date}}
                                </td>
                                <td v-else></td>

                                <td class="text-center" v-if="transfer.received_date == null">
                                    <!-- <router-link tag="span" :to="'/inventory/transfer/edit/' + transfer.id" >
                                        <a href="#" title="Edit/View" class="">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    </router-link>
                                        
                                    <a href="#" title="Delete" class="text-danger" @click="removeTransfer(transfer.id)" v-if="user_role != 'admin'">
                                        <i class="fas fa-trash"></i>
                                    </a> -->

                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <router-link tag="span" :to="'/inventory/transfer/edit/' + transfer.id" >
                                                    <a href="#" title="Edit/View" class="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>&nbsp;
                                                </router-link>
                                            </a>

                                            <a class="dropdown-item">
                                                <a href="#" title="Delete" class="text-danger" @click="removeTransfer(transfer.id)" v-if="user_role != 'admin'">
                                                    <i class="fas fa-trash"></i>
                                                </a> 
                                            </a>
                                        </div>
                                    </div>                                   
                                </td>
                                <td v-else></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No record found!</h5>
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
                    customer: "",
                },
                transfers: [],
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
            
            if(this.user_role == "office_order_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            this.getTransfers();    
        },

        mounted() {
            //$("#loading").hide();
            console.log('Component mounted.');
        },

        methods: {
            getTransfers(page = 1) {
                console.log(page);
                $("#loading").show();
                let app = this;

                var search = "";
                axios.get("/transfer").then(({ data }) => (app.transfers = data.data))
                .then(function() {
                    console.log(app.transfers);
                    $("#loading").hide();
                });
            },

            removeTransfer(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/transfer/" + id).then(function() {
                            app.getTransfers();
                        });
                        swal("Success! Entry has been deleted!", {
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