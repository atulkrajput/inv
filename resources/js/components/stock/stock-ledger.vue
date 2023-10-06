<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="search-box">
                    <form @submit.prevent="makeSearch()">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="barcode" class="label-control">Barcode</label>
                                <input v-model="sform.barcode" class="form-control" placeholder="Enter barcode" type="text" name="barcode">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="submit" class="label-control">Actions</label><br/>
                                <input type="submit" class="btn btn-sm btn-success text-uppercase" value="Get ledger">
                                <button type="button" @click="resetSearch" class="btn btn-sm btn-danger text-uppercase">Reset ledger</button>
                                <span v-show="loader == 1" class="m-l-15">
                                    <img class="load-img" :src="loaderurl">
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card m-b-25">
                            <div class="card-header">
                                <h6 class="card-title">
                                    <span class="text-uppercase">Stocks details</span>
                                </h6>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Shipment No</th>
                                            <th>Image</th>
                                            <th>Category</th>
                                            <th>Item Code</th>
                                            <th>Barcode</th>
                                            <th>Cost</th>
                                            <th>Total Stock</th>
                                            <th>Available</th>
                                            <th>Adjustment</th>
                                            <th>Added On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="stock.id">
                                            <td>{{ stock.shipment_number }}</td>
                                            <td v-if="stock.image">
                                                <img class="img-doc"  v-img:imgurl+stock.image :src="imgurl+stock.image">
                                                <button class="btn btn-warning btn-sm" @click="editStock(stock)"><i class="fas fa-edit"></i></button>
                                            </td>
                                            <td v-else>
                                                <img :src="placeholder" alt="" class="proimage">
                                                <button class="btn btn-warning btn-sm" @click="editStock(stock)"><i class="fas fa-edit"></i></button>
                                            </td>
                                            <td>{{ stock.category }}</td>
                                            <td>{{ stock.model_no }}</td>
                                            <td>{{ stock.barcode }}</td>
                                            <td>{{ stock.selling_cost | formatNumber }}</td>
                                            <td>{{ stock.received_stock | freeNumber }}</td>
                                            <td>{{ stock.stock | freeNumber }}</td>
                                            <td>{{ stock.adjustment | freeNumber }}</td>
                                            <td>{{ stock.created_at | setdate }}</td>
                                        </tr>
                                        <tr v-else>
                                            <td colspan="11"><p class="text-danger">make search to view ledger</p> </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card m-b-25">
                            <div class="card-header">
                                <h6 class="card-title">
                                    <span class="text-uppercase">Stock Update</span>
                                </h6>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Received</th>
                                            <th>Old Received</th>
                                            <th>Old Available</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="revised.length >= 1">
                                            <td>{{ revised.date | setdate }}</td>
                                            <td>{{ revised.stock }}</td>
                                            <td>{{ revised.old_recevied }}</td>
                                            <td>{{ revised.available_stock }}</td>
                                        </tr>
                                        <tr v-else>
                                            <td colspan="11"><p class="text-danger">make search to view ledger</p> </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">
                                    <span class="text-uppercase">Sales details</span>
                                </h6>
                            </div>
                            <div class="card-body p-0 table-responsive mid-content-box">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SNo</th>
                                            <th>Type</th>
                                            <th>Reference</th>
                                            <th>Customer</th>
                                            <th>Qty</th>
                                            <th>Cost</th>
                                            <th>Total</th>
                                            <th>Sale On</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="sales.length >= 1">
                                        <tr v-for="(sale, count) in sales" :key="count">
                                            <td>{{ count+1 }}</td>
                                            <td v-if="sale.type == 1">
                                                <label class="status-label bg-teal text-white">I</label>
                                            </td>
                                            <td v-else>
                                                <label class="status-label text-white bg-pink">E</label>
                                            </td>
                                            <td>{{ (sale.type == 1) ? sale.invoice_number : sale.sale_id }}</td>
                                            <td>{{ sale.name }}</td>
                                            <td>{{ sale.qty }}</td>
                                            <td>{{ sale.price | formatNumber }}</td>
                                            <td>{{ (sale.qty*sale.price) | freeNumber }}</td>
                                            <td>{{ sale.inv_date | setdate }}</td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="11"><p class="text-danger">No sales found</p> </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">
                                    <span class="text-uppercase">Return details</span>
                                </h6>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SNo</th>
                                            <th>Type</th>
                                            <th>Sale No</th>
                                            <th>Customer</th>
                                            <th>Qty</th>
                                            <th>Cost</th>
                                            <th>Total</th>
                                            <th>Return On</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="returns.length >= 1">
                                        <tr v-for="(sale, count) in returns" :key="count">
                                            <td>{{ count+1 }}</td>
                                            <td v-if="sale.type == 1">
                                                <label class="status-label bg-teal text-white">I</label>
                                            </td>
                                            <td v-else>
                                                <label class="status-label text-white bg-pink">E</label>
                                            </td>
                                            <td>{{ (sale.type == 1) ? sale.invoice_number : sale.sale_id }}</td>
                                            <td>{{ sale.name }}</td>
                                            <td>{{ sale.qty }}</td>
                                            <td>{{ sale.price | formatNumber }}</td>
                                            <td>{{ (sale.qty*sale.price) | freeNumber }}</td>
                                            <td>{{ sale.rev_date | setdate }}</td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="11"><p class="text-danger">No returns found</p> </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editStockModal"  data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form @submit.prevent="updateStock()">
                        <div class="modal-header">
                            <h5 class="modal-title">Change Image</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="image">Upload Image</label>
                                <input type="file" @change="uploadImage" name="image" placeholder="select image" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <span v-if="loader == 2">
                                <img class="load-img" :src="loaderurl">
                            </span>
                            <span v-else>
                                <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                                <button type="submit" class="btn btn-wide btn-success"> Update </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                editmode: false,
                stock: [],
                loader:false,
                sales:[],
                returns:[],
                adjustments:[],
                revised: [],
                imgurl: '/backend/product/',
                loaderurl: '/svg/loop.gif',
                placeholder: '/backend/product/default.png',
                form: new Form({
                    id:'',
                    image:'',
                    barcode:''
                }),
                sform: new Form({
                    barcode:''
                })
            }
        },
        methods: {
            showStock() {
                axios.get('/api/stock').then(({ data }) => {this.stocks = data; });
            },
            editStock(stock) {
                this.form.fill(stock);
                this.form.image = '';
                $('#editStockModal').modal('show');
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
                this.loader = 2;
                this.form.put('/api/stock/'+this.form.id)
                .then(() => {
                    Fire.$emit('AfterCreate');
                    this.form.reset();
                    $('#editStockModal').modal('hide');
                    this.$toaster.success('Stock image has been updated successfully.');
                    this.loader = false;
                })
                .catch(() => {

                });
            },
            resetSearch() {
                this.sform.reset();
                this.stock = [];
                this.returns = [];
                this.sales = [];
                this.revised = [];
            },
            makeSearch() {
                if(this.sform.barcode.length < 7) {
                    this.$toaster.error('Please enter correct barcode.');
                    return;
                }
                this.loader = 1;
                this.sform.post('/api/stocks/get-stock-ledger').then((data) => {
                    this.stock = data.data.stock;
                    this.returns = data.data.returns;
                    this.sales = data.data.sales;
                    this.revised = data.data.revised;
                    this.loader = false;
                });
            }
        },
        created() {
            Fire.$on('AfterCreate', () => {
                this.makeSearch();
            });
        }
    }
</script>

<style scoped>
.m-b-25{
    margin-bottom: 25px;
}
.img-doc,.proimage{
    width: 50px;
}
</style>
