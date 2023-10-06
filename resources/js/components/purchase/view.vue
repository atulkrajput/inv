 

<template>
  
  <div>
   <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Shipments
                                    <div class="card-tools float-right">
                                        <router-link class="btn btn-sm btn-dark" to="/shipment">
                                            <i class="fas fa-arrow-left fa-fw"></i> Back To List
                                        </router-link>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table id="purchase" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Shipment No</th>
                                            <th>No Of Stocks</th>
                                            <th>Total value</th>
                                            <th>Shipment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ purchase.shipment_number }}</td>
                                            <td>
                                                <label class="stock-label bg-primary m-b-0">
                                                    {{ purchase.stocks_count }}
                                                </label>
                                            </td>
                                            <td>{{ purchase.grand_total | formatNumber }}</td>
                                            <td>{{ purchase.created_at | setdate }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr class="m-b-0 m-t-25">
                                <form @submit.prevent="editmode ? updateStock() : ''">
                                    <table id="stock-alert" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="wf-50">SNo</th>
                                                <th>Category</th>
                                                <th>Item Code</th>
                                                <th>Barcode</th>
                                                <th>Cost</th>
                                                <th>Total Stock</th>
                                                <th>Available</th>
                                                <th>Adjustment</th>
                                                <th>Added On</th>
                                                <th class="wf-100">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(stock, count) in stocks.data" :key="stock.id" :class="[(editmode && stock.id == form.id)? 'bg-warning' : '' ]">
                                                <td>{{ count + 1 }}</td>
                                                <td>{{ stock.category }}</td>
                                                <td>{{ stock.model_no }}</td>
                                                <td>{{ stock.barcode }}</td>
                                                <td v-if="(editmode && stock.id == form.id && form.type == 1)">
                                                    <input type="text" class="form-control" placeholder="modified price" v-model="form.selling_cost">
                                                </td>
                                                <td v-else>
                                                    {{ stock.selling_cost | formatNumber }}
                                                </td>
                                                <td v-if="(editmode && stock.id == form.id && form.type == 1)">
                                                    <input type="text" class="form-control" placeholder="modified stock" v-model="form.received_stock">
                                                </td>
                                                <td v-else>
                                                    {{ stock.received_stock | freeNumber }}
                                                </td>
                                                <td>{{ stock.stock | freeNumber }}</td>
                                                <td v-if="(editmode && stock.id == form.id && form.type == 2)">
                                                    <input type="text" class="form-control m-b-10
                                                    " placeholder="modified price" v-model="form.adjustment">
                                                     <input type="text" class="form-control" placeholder="enter reason" v-model="form.reason">
                                                </td>
                                                <td v-else>{{ stock.adjustment | freeNumber }}</td>
                                                <td>{{ stock.created_at | setdate }}</td>
                                                <td>
                                                    <span  v-if="(editmode && stock.id == form.id)">
                                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i></button>
                                                        <button type="button" class="btn btn-danger btn-sm" @click="cancelEdit"><i class="fas fa-times"></i></button>
                                                    </span>
                                                    <span v-else>
                                                        <button type="button" class="btn btn-warning btn-sm" @click="editStock(stock)"><i class="fas fa-edit"></i></button>
                                                        <button  type="button" class="btn btn-purple btn-sm" @click="makeAdjustment(stock)"><i class="fas fa-exchange-alt"></i></button>
                                                        <button type="button" class="btn btn-success btn-sm" @click="addStock(stock)"><i class="fas fa-plus"></i></button>
                                                   </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
          <!--Row-->

<div class="modal fade" id="addStockModal"  data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                        <form @submit.prevent="createStock()">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Stock</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="date">Date</label>
                                 <date-picker v-model="form.date" lang="en" format="YYYY-MM-DD"></date-picker>
                            </div>
                            <div class="form-group">
                                <label for="stock">Received Stock</label>
                                 <input type="number" class="form-control" v-model="form.newstock" name="stock" placeholder="Enter stock">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Submit </button>
                        </div>
                        </form>
                </div>
            </div>
        </div>

   
  </div>


</template>



<script>
    export default {
         created(){
      if (!User.loggedIn()) {
        this.$router.push({name: '/'})
      }
    },
        data() {
            return {
                editmode: false,
                activeId: this.$route.params.id,
                purchase: {},
                stocks:{},
                errors:{},
                form: new Form({
                    id:'',
                    barcode:'',
                    type:'',
                    selling_cost:'',
                    received_stock:'',
                    adjustment:'',
                    reason:'',
                    date:'',
                    newstock:'',
                })
            }
        },
        methods: {
            showPurchase() {
                axios.get('/api/purchase/'+this.activeId).then((data) => { this.purchase = data.data; });
                axios.get('/api/get-stocks-by-purchase/'+this.activeId).then((data) => { this.stocks = data; });
            },
            uploadMImage(e) {
                let vm = this.form;
                //let reader = new FileReader();
                var files = e.target.files;
               // var vm = this;   // HERE
                if(files){
                    var files_count = files.length;
                    for (let i=0; i<files_count; i++){
                    var reader = new FileReader();
                    reader.onload = function(e){
                        vm.images.push(e.target.result);    // HERE
                    }
                    reader.readAsDataURL(files[i]);
                    }
                }
            },
            makeAdjustment(stock) {
                this.editmode = true;
                this.form.fill(stock);
                this.form.type = 2;
            },
            editStock(stock) {
                this.editmode = true;
                this.form.fill(stock);
                this.form.type = 1;
               // $('#editStockModal').modal('show');
            },
            addStock(stock) {
                this.form.fill(stock);
                $('#addStockModal').modal('show');
            },
            createStock(){
                this.form.post('/api/stocks/revise',this.form)
                .then(() => {
                    this.form.reset();
                    $('#addStockModal').modal('hide');
                    Toast.fire({type:'success',   title:'Stock details added successfully'});
                })
                .catch(error =>this.errors = error.response.data.errors);
            },
            cancelEdit() {
                this.editmode = false;
                this.form.reset();
            },
            uploadImage(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.image = reader.result;
                }
                reader.readAsDataURL(file);
            },
            updateStock() {
                this.form.put('/api/stock/'+this.form.id)
                .then(() => {
                    axios.get('/api/get-stocks-by-purchase/'+this.activeId).then((data) => { this.stocks = data; });
                    this.editmode = false;
                    this.form.reset();
                    Toast.fire({type:'success',   title:'Stock details updated successfully'});
                })
                .catch(() => {

                });
            },
        },
        created() {
            this.showPurchase();
        }
    }
</script>

<style scoped>
.stock-label {
    border-radius: 2px;
    color: #fff;
    font-size: .72rem;
    margin-right: 4px;
    padding: 0 5px;
}

.mx-datepicker{
    display: block;
    width: 100%;
}
</style>
