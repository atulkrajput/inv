 

<template>
  
  <div>
   <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Add Shipment
                                    <div class="card-tools float-right">
                                        <router-link class="btn btn-sm btn-dark" to="/shipment">
                                            <i class="fas fa-arrow-left fa-fw"></i> Back To List
                                        </router-link>
                                    </div>
                                </h3>
                            </div>
                         
                                <form @submit.prevent="addPurchase()">
                                <div class="full-form-body">
                                    <div class="pdetails">
                                        <div class="ptitle">
                                            <p class="m-0">Products
                                                <span class="float-right">
                                                    <button type="button" @click="addBar" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i></button>
                                                </span>
                                            </p>
                                            <span class="float-none"></span>
                                        </div>
                                        <div class="pbody table-responsive">
                                            <table class="table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th class="wf-40">SNo</th>
                                                        <th>Category</th>
                                                        <th>Item Code</th>
                                                        <th>Price</th>
                                                        <th>Stock</th>
                                                        <th>Image</th>
                                                        <th>Total</th>
                                                        <th class="wf-50">#</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(counter, cID) in countlist" :key="cID" :id="'row'+cID">
                                                        <td>{{counter}}</td>
                                                        <td>
                                                            <model-select :options="categories"
                                                                name="product_detail[]['category_id']"
                                                                v-model="form.product_detail__category_id[getIndex(counter, cID)]"
                                                                placeholder="Select Category">
                                                            </model-select>
                                                        </td>
                                                        <td>
                                                            <input v-model="form.product_detail__name[getIndex(counter, cID)]" class="form-control" placeholder="Enter item code" type="text" name="product_detail[]['name']">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.product_detail__purchase_cost[getIndex(counter, cID)]" class="form-control" @change="reTotal" value="0" type="text" name="product_detail[]['purchase_cost']" placeholder="0.00">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.product_detail__stock[getIndex(counter, cID)]" class="form-control" @change="reTotal" value="1" type="number" name="product_detail[]['stock']">
                                                        </td>
                                                        <td>
                                                            <input @change="uploadImage" class="form-control" type="file" name="product_detail[]['image']">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.product_detail__total[getIndex(counter, cID)]" class="form-control" type="text" readonly="readonly" name="product_detail[]['total']" value="0.00">
                                                        </td>
                                                        <td class="p-r-5">
                                                            <a href="javascript:void(0)" @click="removeBar(cID)" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="pfooter">
                                            <b>Total</b>
                                            <input value="0" type="text"  v-model="form.sub_total" name="sub_total" id="sub_total" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-10">

                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-block btn-sm btn-success">Submit</button>
                                        </div>
                                    </div>
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
            else{
                this.form.admin_id = User.id();
            }
    },
        data() {
            return {
                user_id : '',
                editmode: false,
                stocks: {},
                options: [],
                categories:[],
                countlist:[1,2,3],
                productSelected: [],
                product:[],
                products: [],
                limit: 10,
                activerow:'',
                imgurl: '../images/products/',
                form: new Form({
                    id:'',
                    sub_total:'',
                    admin_id:'',
                    product_detail__type:[],
                    product_detail__category_id:[],
                    product_detail__id:[],
                    product_detail__name:[],
                    product_detail__image:[],
                    product_detail__purchase_cost:[],
                    product_detail__stock:[],
                    product_detail__total:[],
                })
            }
        },
        methods: {
            getIndex(list, id) {
                return list;
            },
            activeKey(key) {
                this.activerow = key;
            },
            addPurchase(){
                this.form.post('/api/stock')
                .then(() => {
                    this.$router.push({ name: 'shipment'})
            	    Notification.success()
                })
                .catch(() => {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
                });
            },
            listProducts() {
                axios.get('/api/listProducts').then((data) => {this.products = data.data }).catch();
                axios.get('/api/listCategory').then((data) => {this.categories = data.data }).catch();
            },
            reTotal(){
               this.updateSubTotal();
            },
            updateSubTotal() {
                let counter = this.countlist;
                let stotal = 0; let total = 0;
                counter.forEach(element => {
                    if(element) {
                        this.form.product_detail__total[element] = this.makeNumber(this.form.product_detail__purchase_cost[element]) * this.makeNumber(this.form.product_detail__stock[element]);
                        stotal = stotal +  this.form.product_detail__total[element];
                    }
                });
                this.form.sub_total = stotal;
            },
            addBar(){
                let bcount = this.countlist.length;
                this.countlist.push(bcount+1);
            },
            makeNumber(val) {
                if(isNaN(val)){
                    return 0;
                }
                else {
                    return parseFloat(val);
                }
            },
            removeBar(barkey){
                // let bcount = this.countlist.length;
                // this.countlist.push(bcount+1);
               this.$delete(this.countlist, barkey);
            },
            uploadImage(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.product_detail__image[this.activerow] = reader.result;
                }
                reader.readAsDataURL(file);
                console.log(e);
            },
            productDetail(pid) {
                $('#productModal').modal('show');
                axios.get('/api/product/'+pid)
                    .then((data) => {this.product = data.data[0] })
                    .catch();
            },
            onProductSelect(product, key) {
                this.productSelected[key] = product;
            }
        },
        created() {
             if (!User.loggedIn()) {
                this.$router.push({name: '/'})
            }
            this.listProducts();
        }
    }
</script>

<style scoped>
.full-form-body .ptitle {
    line-height: 46px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .4px;
    background: #dee2e6;
    padding: 0 21px 0 10px;
    text-transform: uppercase;
}

.full-form-body .pfooter {
    background: #f1f5f9;
    color: #56688a;
    border: solid #dee2e6;
    border-width: 1px 1px 2px;
    text-align: right;
    padding-right: 15px;
    height: 31px;
    margin-bottom: 8px;
}
.full-form-body .pfooter input[type=text] {
    border: none;
    height: 28px;
    width: 190px;
    background: #e9ecef;
    padding: 3px 10px;
    font-weight: 700;
    color: #323232;
}

.full-form-body button.btn {
    padding: 0.2rem 0.4rem;
}

.m-0 {
    margin: 0!important;
}
.full-form-body button.btn {
    padding: 0.2rem 0.4rem;
}
.ui.fluid.dropdown {
    display: block;
    width: 100%;
    min-width:max-content;
}
</style>


