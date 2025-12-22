<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/account_head" class="font-weight-normal"><a href="#">Account</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Account Head Form</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Account Head</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Account Head </h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Account Head  Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="form-group col-md-4 col-lg-3">
                                <label>Account Head</label>
                                <input type="text"  required class="form-control" id="acc_head" name="acc_head" v-model="form.name">
                            </div>
                            <div class="form-group col-md-4 col-lg-3">
                                <label >Financial Type1</label>
                                <select id="type1_id" class="form-control"
                                        name="financial_type1_id" required v-model="form.financial_type1" style="width:100%">
                                    <option value="">Select One</option>
                                    <option v-for="f_type in financial_type1" :value="f_type.id"  >{{f_type.name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-lg-3">
                                <label>Financial Type2</label>
                                <select id="type2_id" class="form-control" required
                                        v-model="form.financial_type2" style="width:100%">
                                    <option value="">Select One</option>
                                    <option v-for="f_type in financial_type2" :value="f_type.id"  >{{f_type.name}}</option>
                                </select>
                            </div>
                            <div class="form-group  row text-right pt-3">
                                <label class="col-lg-3 col-form-label form-control-label"></label>
                                <div class="col-lg-12">
                                    <input type="reset" class="btn btn-secondary btn-sm" value="Cancel" v-if="!isEdit">
                                    <input type="submit" class="btn btn-primary btn-sm" value="Save Changes" v-if="!isEdit || (isEdit && user_role !='Account')">
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
                name:'',
                financial_type1:'',
                financial_type2:'',
            }),
            account_head_id:'',
            financial_type1:[],
            financial_type2:[],

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
            //window.location.replace(url);
        }
        if(this.$route.params.id) {
            this.isEdit = true;
            this.account_head_id = this.$route.params.id;
            this.getAccountHead(this.account_head_id);
        }
        console.log(this.isEdit);
    },
    mounted(){
        var vm=this;
        vm.initFinancialType1();
        vm.initFinancialType2();
    },
    methods:{
        initFinancialType1(){
            axios.get('/account_head/get_financial_type/'+ 1).then(({data})=>(this.financial_type1=data.financial_type));
            // $("#type1_id").select2();

        },
        initFinancialType2(){
            axios.get('/account_head/get_financial_type/'+ 2).then(({data})=>(this.financial_type2=data.financial_type));
            // $("#financial_type2_id").select2();
        },
        getAccountHead(id){
            let app=this;
            axios.get('/account_head/edit/'+id).then(function (response){
                var ah=response.data.account_head;
                app.form.name=ah.name;
                app.form.financial_type1=ah.financial_type1_id;
                $('#type1_id').val(app.form.financial_type1).trigger('change');
                app.form.financial_type2=ah.financial_type2_id;
                $('#type2_id').val(app.form.financial_type2).trigger('change');
            });
        },
        onSubmit:function (event) {
            var app = this;
            $("#loading").show();
            if (!this.isEdit) {
                this.form.post('/account_head/store').then(function (data){
                    if(data.status=='success'){
                        console.log(data);
                        event.target.reset();
                        $("#financial_type1_id").select2();
                        $("#financial_type2_id").select2();
                        $("#loading").hide();
                        swal({
                            title: "Success!",
                            text: 'Account Head  Added.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            app.$router.push({name:'account_head'});

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
                var app=this;
                // console.log(this.form);
                this.form.patch('/account_head/update/'+app.account_head_id).then(function (data){
                    if(data.status=='success'){
                        event.target.reset();
                        $("#financial_type1_id").select2();
                        $("#financial_type2_id").select2();
                        $("#loading").hide();
                        swal({
                            title: "Success!",
                            text: 'Account Head Updated.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            app.$router.push({name:'account_head'});
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
