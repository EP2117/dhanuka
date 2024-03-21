<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/sub_account" class="font-weight-normal"><a href="#">Sub Account</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Sub Account Form</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Sub Account</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Sub Account </h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sub Account Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="form-group col-md-4 col-lg-3">
                                <label>Sub Account</label>
                                <input type="text"  required class="form-control" id="acc_head" name="acc_head" v-model="form.sub_account_name">
                            </div>
                            <div class="form-group col-md-4 col-lg-3">
                                <label >Account Head</label>
                                <select id="account_head_id" class="form-control"
                                        name="account_head" required v-model="form.account_head" style="width:100%">
                                    <option value="">Select One</option>
                                    <option v-for="ah in account_head" :value="ah.id"  >{{ah.name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-lg-3">
                                <label>Account Type</label>
                                <select id="account_type_id" class="form-control" required
                                        v-model="form.account_type" style="width:100%">
                                    <option value="">Select One</option>
                                    <option v-for="at in account_type" :value="at.id"  >{{at.name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-lg-3">
                                <label>Account Group</label>
                                <select id="account_group_id" class="form-control" 
                                        v-model="form.account_group" style="width:100%">
                                    <option value="">Select One</option>
                                    <option v-for="at in account_group" :value="at.id"  >{{at.name}}</option>
                                </select>
                            </div>
                            <div class="form-group  row text-right pt-3">
                                <label class="col-lg-3 col-form-label form-control-label"></label>
                                <div class="col-lg-12">
                                    <input type="reset" class="btn btn-secondary btn-sm" value="Cancel" v-if="!isEdit">
                                    <input type="submit" class="btn btn-primary btn-sm" value="Save Changes">
                                </div>
                            </div>
                            <!--                            <div class="form-group col-md-3 col-lg-2">-->
                            <!--                                <label class="small">&nbsp;</label>-->
                            <!--                                <button-->
                            <!--                                    class="form-control btn btn-primary font-weight-bold"-->
                            <!--                                    @click="getSuppliers(1)"-->
                            <!--                                ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>-->
                            <!--                            </div>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--        <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div>-->
    </div>

</template>

<script>
export default {
    data(){
        return{
            form:new Form({
                sub_account_name:'',
                account_group: '',
                account_head:'',
                account_type:'',
            }),
            sub_account_id:'',
            account_head:[],
            account_type:[],
            account_group:[],
            isEdit:false,
        }
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
        if(this.$route.params.id) {
            this.isEdit = true;
            this.sub_account_id = this.$route.params.id;
            this.getSubAccount(this.sub_account_id);
        }
    },
    mounted(){
        var vm=this;
        vm.initAccountHead();
        vm.initAccountType();
        vm.initAccountGroup();
    },
    methods:{
        initAccountHead(){
            axios.get('/sub_account/get_account_head').then(({data})=>(this.account_head=data.account_head));
            // $("#type1_id").select2();

        },
        initAccountGroup(){
            axios.get('/sub_account/get_account_group').then(({data})=>(this.account_group=data.account_group));
            // $("#financial_type2_id").select2();
        },
        initAccountType(){
            axios.get('/sub_account/get_account_type').then(({data})=>(this.account_type=data.account_type));
            // $("#financial_type2_id").select2();
        },
        getSubAccount(id){
            let app=this;
            axios.get('/sub_account/edit/'+id).then(function (response){
                var sa=response.data.sub_account;
                app.form.sub_account_name=sa.sub_account_name;
                app.form.account_head=sa.account_head_id;
                app.form.account_group=sa.account_group_id;
                $('#account_head_id').val(app.form.account_head).trigger('change');
                app.form.account_type=sa.account_type_id;
                $('#account_type_id').val(app.form.account_type).trigger('change');
            });
        },
        onSubmit:function (event) {
            var app = this;
            $("#loading").show();
            if (!this.isEdit) {
                this.form.post('/sub_account/store').then(function (data){
                    if(data.status=='success'){
                        console.log(data);
                        event.target.reset();
                        $("#account_head_id").select2();
                        $("#account_type_id").select2();
                        $("#loading").hide();
                        swal({
                            title: "Success!",
                            text: 'Sub Account Added.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            app.$router.push({name:'sub_account'});

                        });
                    }else {
                        $.notify("Error", {
                            autoHideDelay: 3000,
                            gap: 1,
                            className: "error"
                        });
                    }
                }) .catch(function (response)  {
                    var obj = $.parseJSON(response.errors);
                    var errmsg = "";
                    for (var key in obj) {
                        if (obj.hasOwnProperty(key)) {
                            errmsg += obj[key][0]+"\n";
                        }
                    }
                    $.notify(errmsg, {
                        autoHideDelay: 3000,
                        gap: 1,
                        className: "error"
                    });

                });
            }else{
                // console.log(this.form);
                this.form.patch('/sub_account/update/'+app.sub_account_id).then(function (data){
                    if(data.status=='success'){
                        event.target.reset();
                        $("#account_head_id").select2();
                        $("#account_type_id").select2();
                        $("#loading").hide();
                        swal({
                            title: "Success!",
                            text: 'Sub Account Updated.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            app.$router.push({name:'sub_account'});
                        });
                    }else {
                        $.notify("Error", {
                            autoHideDelay: 3000,
                            gap: 1,
                            className: "error"
                        });
                    }
                }) .catch(function (response)  {
                    var obj = $.parseJSON(response.errors);
                    var errmsg = "";
                    for (var key in obj) {
                        if (obj.hasOwnProperty(key)) {
                            errmsg += obj[key][0]+"\n";
                        }
                    }
                    $.notify(errmsg, {
                        autoHideDelay: 3000,
                        gap: 1,
                        className: "error"
                    });

                });
            }
        }
    }
}

</script>

<style scoped>

</style>
