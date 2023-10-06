<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-9 p-b-15">
                        <vue-search placer="Search by category name, item code, barcode, old barcode"></vue-search>
                    </div>
                   
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Stocks</h6>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">SNo</th>
                                            <th>Image</th>
                                            <th>Category</th>
                                            <th>Item Code</th>
                                            <th>Barcode</th>
                                            <th>Old Barcode</th>
                                            <th>Cost</th>
                                            <th>Total Stock</th>
                                            <th>Available</th>
                                            <th>Added On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(stock, count) in stocks.data" :key="stock.id">
                                            <td>{{ (stocks.current_page - 1)*(stocks.per_page) + count + 1 }}</td>
                                            <td v-if="stock.image">
                                                <img class="proimage" :src="imgurl+stock.image">
                                                <button class="btn btn-warning btn-sm" @click="editStock(stock)"><i class="fas fa-edit"></i></button>
                                            </td>
                                            <td v-else>
                                                <img :src="placeholder" alt="" class="proimage">
                                                <button class="btn btn-warning btn-sm" @click="editStock(stock)"><i class="fas fa-edit"></i></button>
                                            </td>
                                            <td>{{ stock.category }}</td>
                                            <td>{{ stock.model_no }}</td>
                                            <td>{{ stock.barcode }}</td>
                                            <td>{{ (stock.batch_no) ? stock.batch_no : '--' }}</td>
                                            <td>{{ stock.selling_cost | formatNumber }}</td>
                                            <td>{{ stock.received_stock | freeNumber }}</td>
                                            <td>{{ stock.stock | freeNumber }}</td>
                                            <td>{{ stock.created_at | setdate }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer table-responsive">
                                <pagination class="m-0 float-right" :data="stocks" :limit="12" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
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
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Update </button>
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
                stocks: {},
                asearch:'',
                imgurl: '/backend/product/',
                placeholder: 'backend/product/default.png',
                form: new Form({
                    id:'',
                    image:'',
                    barcode:''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/stock?page=' + page)
                    .then(response => {
                        this.stocks = response.data;
                });
            },
            showStock() {
                axios.get('/api/stock').then(({ data }) => {
                    this.stocks = data;
                });
            },
            editStock(stock) {
                this.form.fill(stock);
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
                this.form.put('/api/stock/'+this.form.id)
                .then(() => {
                    Fire.$emit('AfterCreate');
                    $('#editStockModal').modal('hide');
                    toast.fire({
                        icon:'success',
                        title:'Stock details updated successfully'
                    });
                })
                .catch(() => {

                });
            },
            /* deleteStock(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38c172',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.form.delete('/api/stocks/'+id)
                            .then(() => {
                                swal.fire('Deleted!', 'Record has been deleted.', 'success');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                swal.fire('Not Deleted!', 'Record can not be deleted.', 'error');
                            });
                    }
                });
            } */
        },
        created() {
            Fire.$on('searching', () => {
                axios.get('/api/findStock?q='+this.asearch)
                    .then((data) => {
                        this.stocks = data.data;
                    })
                    .catch();
            });

            this.showStock();
            Fire.$on('AfterCreate', () => {
                this.showStock();
            });
        }
    }
</script>

<style scoped>
.proimage{
    height: 40px;
    width: 40px;
}
.p-b-15{
    padding-bottom: 15px;
}
</style>
