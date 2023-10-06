<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="search-box">
                    <form @submit.prevent="makeSearch()">
                        <div class="row">
                            <div class="col-md-2 form-group">
                                <label for="category_id" class="label-control">Category</label>
                                <model-select :options="categories"
                                    name="category_id"
                                    v-model="sform.category_id"
                                    placeholder="Select Category">
                                </model-select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="product_code" class="label-control">Product</label>
                                <input v-model="sform.product_code" class="form-control" placeholder="Enter item code" type="text" name="product_code">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="barcode" class="label-control">Barcode</label>
                                <input v-model="sform.barcode" class="form-control" placeholder="Enter barcode" type="text" name="barcode">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="submit" class="label-control">Actions</label>
                                <br/>
                                <input type="submit" class="btn btn-sm btn-success text-uppercase" value="Search Now">
                                <button type="button" @click="resetSearch" class="btn btn-sm btn-danger text-uppercase">Reset Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <span class="text-uppercase">Stocks</span>
                                    <small v-show="sform.search">{{ stocks.length | freeNumber }} Results Found.</small>
                                </h5>
                            </div>
                            <div class="card-body p-0 table-responsive content-box-with-search">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-50">SNo</th>
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
                                    <tbody v-if="stocks.length >= 1">
                                        <tr v-for="(stock, count) in stocks" :key="stock.id">
                                            <td>{{ count + 1 }}</td>
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
                                            <td>{{ (stock.batch_no) ? stock.batch_no : '--' }}</td>
                                            <td>{{ stock.selling_cost | formatNumber }}</td>
                                            <td>{{ stock.received_stock | freeNumber }}</td>
                                            <td>{{ stock.stock | freeNumber }}</td>
                                            <td>{{ stock.created_at | setdate }}</td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else-if="(sform.search == 1) && (stocks.length == 0)">
                                        <tr>
                                            <td class="p-0" colspan="10">
                                                <p class="alert alert-danger m-0 b-0">
                                                    <i>No result has been found.</i>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td class="p-0" colspan="10">
                                                <p class="alert alert-danger m-0 b-0">
                                                    <i>No search has been made yet.</i>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
                categories:[],
                imgurl: '/images/products/',
                placeholder: 'images/placeholder.jpg',
                sform: new Form({
                    category_id:'',
                    product_code:'',
                    barcode:'',
                    search:''
                })
            }
        },
        methods: {
            showStock() {
                axios.get('/api/stock').then(({ data }) => {this.stocks = data; });
            },
            getPrimaryAssets() {
                 axios.get('/api/listCategory').then((data) => {this.categories = data.data }).catch();
            },
            resetSearch() {
                this.sform.reset();
                 this.stocks = [];
            },
            makeSearch() {
                this.sform.search = 1;
                this.sform.post('/api/stocks/search-stock').then((data) => {
                    this.stocks = data.data;
                });
            }
        },
        created() {
            this.getPrimaryAssets();
        }
    }
</script>
