<template>
    <div>
         <div class="content bg-white">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="full-width-form">
                            <form @submit.prevent>
                                <div class="full-form-header">
                                    <h3 class="full-form-title">Adjustments  <span @click="refeshPage" class="btn btn-sm btn-warning text-dark text-uppercase"><i class="fas fa-recycle"></i> reset form</span></h3>
                                   
                                </div>
                                <div class="full-form-body">
                                    <div class="row">
                                        
                                        <div class="col-md-12">
                                            <div class="pdetails">
                                                <div class="ptitle">
                                                    <p class="m-0">Products
                                                        <span class="float-right">
                                                            <button type="button" @click="addBar" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i></button>
                                                        </span>
                                                    </p>
                                                    <span class="float-none"></span>
                                                </div>
                                                <div class="pbody">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th class="wf-75">SNo</th>
                                                                <th>Barcode</th>
                                                                <th>Product</th>
                                                                <th>Available Stock</th>
                                                                <th class="wf-150">Price</th>
                                                                <th class="wf-100">Qty</th>
                                                                <th class="wf-100">Total</th>
                                                                <th class="wf-50">#</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(counter, cID) in countlist" :key="cID" :id="'row'+cID">
                                                                <td>{{counter}} <button type="button" v-if="productSelected[getIndex(counter, cID)] >= 1" class="btn btn-sm btn-success" @click="productDetail(form.product_detail__stockid[getIndex(counter, cID)])"><i class="fas fa-laptop"></i></button>
                                                            <button type="button" v-else class="btn btn-sm btn-secondary"><i class="fas fa-laptop"></i></button></td>
                                                                <td><input type="text" name="product_detail[]['barcode']"
                                                                        v-model="form.product_detail__barcode[getIndex(counter, cID)]"
                                                                        placeholder="enter barcode"
                                                                        @input="onProductSelect(form.product_detail__barcode[getIndex(counter, cID)], getIndex(counter, cID))" class="form-control" autocomplete="off">
                                                                </td>
                                                                <td>
                                                                    <input disabled="disabled" readonly="readonly" v-model="form.product_detail__name[getIndex(counter, cID)]" class="form-control" type="text" name="product_detail[]['name']">
                                                                </td>
                                                                <td>
                                                                    <input disabled="disabled" readonly="readonly" v-model="form.product_detail__stock[getIndex(counter, cID)]" type="text" name="product_detail[]['stock']" class="form-control">
                                                                </td>
                                                                <td>
                                                                    <input disabled="disabled" readonly="readonly" v-model="form.product_detail__selling_cost[getIndex(counter, cID)]" class="form-control" value="0" type="text" name="product_detail[]['selling_cost']" placeholder="0">
                                                                </td>
                                                                <td>
                                                                    <input v-model="form.product_detail__qty[getIndex(counter, cID)]" class="form-control" type="number" name="product_detail[]['qty']" @change="reTotal" value="1">
                                                                </td>
                                                                <td>
                                                                    <input v-if="productSelected[cID] >= 1" v-model="form.product_detail__total[getIndex(counter, cID)]" class="form-control" type="text" readonly="readonly" name="product_detail[]['total']" value="0.00">
                                                                    <input v-else disabled="disabled" readonly="readonly" v-model="form.product_detail__total[getIndex(counter, cID)]" class="form-control" type="text" name="product_detail[]['total']" value="0.00">
                                                                </td>
                                                                <td>
                                                                    <button type="button" @click="removeBar(cID)" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="pfooter">
                                                    <b>Total</b>
                                                    <input value="0" type="text" v-model="form.sub_total" name="sub_total" id="sub_total" readonly="readonly" :class="{ 'is-invalid': form.errors.has('sub_total') }">
                                                     <button class="btn btn-sm btn-secondary" type="button" @click="reTotal">
                                                        <i class="fas fa-retweet"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-9">

                                                </div>
                                                <div class="col-md-3 my-3" v-if="loader">
                                                    <img style="height:32px" :src="svgurl+'loop.gif'" alt="updating">
                                                </div>
                                                <div class="col-md-3 my-3" v-else>
                                                    <button type="button" class="btn btn-block btn-success" @click="addAdjustment()">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <span v-if="selectedImage">
                                                <img :src="imgurl+selectedImage" alt="No Image available" style="max-width:100%">
                                            </span>
                                            <span v-else>
                                                Image will be displayed here
                                            </span>
                                        </div>
                                    </div>

                                   
                                </div>
                                <div class="card-footer">

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="productModal"  data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                       
                        <div class="modal-header">
                            <h5 class="modal-title">Product Details</h5>
                        </div>
                        <div class="modal-body">
                            <ul class="list-unstyled">
                            <li><p class="m-0"><img :src="imgurl+product.image" alt="" class="promodelimage"></p></li>
                            <li><p class="m-0"><b>Name</b><br>{{ product.name }}</p></li>
                            <li><p class="m-0"><b>Barcode</b><br>{{ product.barcode }}</p></li>
                            <li><p class="m-0"><b>Available Stock</b><br>{{ product.stock }}</p></li>
                            <li><p class="m-0"><b>Added On</b><br>{{ product.created_at | setdate }}</p></li>
                        </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal">Close</button>
                           
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
                loader: false,
                editmode: false,
                svgurl: '/svg/',
                stocks: {},
                options: [],
                supplier:[],
                sselected: '',
                advanceBook: '',
                newEnabled:'',
                countlist:[0,1,2],
                filteredOptions: [],
                productSelected: [],
                inputProps: {
                    id: "autosuggest__input",
                    name: 'mobile',
                    onInputChange: this.onInputChange,
                    placeholder: "search by mobile number",
                    maxlength:10,
                },
                product:[],
                products: [],
                limit: 10,
                selectedImage:'',
                imgurl: '../backend/product/',
                form: new Form({
                    id:'',
                    admin_id:'',
                    type:1,
                    invoice_number: '0',
                    customer_id:'',
                    sub_total:'',
                    mobile:'',
                    inv_date:'',
                    grand_total:'',
                    shipment_number:'',
                    payment_type:2,
                    payment_id:'',
                    payment_desc:'',
                    firstpay_amount: '',
                    payment_mode:'CASH',
                    product_detail__id:[],
                    product_detail__selling_cost:[],
                    product_detail__name:[],
                    product_detail__barcode:[],
                    product_detail__batch_no:[],
                    product_detail__stockid:[],
                    product_detail__stock:[],
                    product_detail__total:[],
                    product_detail__qty:[]
                })
            }
        },
        methods: {
            getIndex(list, id) {
                return id;
            },
            refeshPage() {
                this.form.reset();
            },
            addAdjustment(){
                this.loader = true;
                this.form.post('/api/adjustment')
                .then((data) => {
                    this.$router.push({ name: 'adjustmentlist'})
                  
                })
                .catch(() => {

                });
            },
            listProducts() {
                 axios.get('/api/listProducts')
                    .then((data) => {this.products = data.data })
                    .catch();
            },
            reTotal(){
               this.updateSubTotal(); this.updateGrandTotal();
            },
            updateGrandTotal() {
                this.form.grand_total = this.makeNumber(this.form.sub_total);
            },
            updateSubTotal() {
                let counter = this.countlist;
                let stotal = 0; let total = 0;
                counter.forEach((val, element) => {
                    this.form.product_detail__total[element] = this.makeNumber(this.form.product_detail__selling_cost[element]) * this.makeNumber(this.form.product_detail__qty[element]);
                    stotal = stotal +  this.form.product_detail__total[element];
                });
                this.form.sub_total = stotal;
            },
            addBar(){
                let bcount = this.countlist.length;
                this.countlist.push(bcount+1);
            },
            newEnabling(){
                this.newEnabled = 1;
                this.sselected = '';
                //console.log(this.form.mobile.target.value);
                this.form.mobile = this.form.mobile.target.value;
            },
            cancelNewEnabling(){
                this.newEnabled = '';
                this.sselected = '';
                this.form.name = '';
                this.form.email = '';
                this.form.address = '';
                this.form.mobile = '';
                this.form.customer_id = '';
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
                this.countlist.splice(barkey, 1);
                this.productSelected.splice(barkey, 1);
                this.form.product_detail__id.splice(barkey, 1);
                this.form.product_detail__selling_cost.splice(barkey, 1);
                this.form.product_detail__name.splice(barkey, 1);
                this.form.product_detail__barcode.splice(barkey, 1);
                this.form.product_detail__stockid.splice(barkey, 1);
                this.form.product_detail__stock.splice(barkey, 1);
                this.form.product_detail__total.splice(barkey, 1);
                this.form.product_detail__qty.splice(barkey, 1);
                this.reTotal();
            },
            productDetail(pid) {
                $('#productModal').modal('show');
                axios.get('/api/stock/'+pid)
                    .then((data) => {this.product = data.data })
                    .catch();
            },
            onSelected(option) {
                axios.get('/api/searchCustomerByPhone?q='+option.item)
                    .then((data) => {
                        this.sselected = data.data[0].id;
                        this.form.customer_id = data.data[0].id;
                        this.newEnabled = '';
                        this.form.name = data.data[0].name;
                        this.form.email = data.data[0].email;
                        this.form.address = data.data[0].address;
                        this.form.mobile = data.data[0].mobile;
                    })
                    .catch();
            },
            advanceAmount() {
                let pt = this.form.payment_type;
                if(pt == 1){
                    this.advanceBook = 1;
                } else {
                    this.advanceBook = '';
                    this.form.firstpay_amount= '';
                }
            },
            onInputChange(text) {
                if (text === '' || text === undefined) {
                    return;
                }
                axios.get('/api/searchCustomer?q='+text)
                    .then((data) => {
                        this.filteredOptions = [{
                            data: data.data  }] })
                    .catch();
            },
            onProductSelect(product, key) {
                this.selectedImage = 'loader.gif';
                axios.get('/api/searchStockByBarcode?q='+product)
                    .then((data) => {
                       // console.log(key);
                        if(data.data[0]){
                            //console.log(data.data[0]);
                            this.productSelected[key] = data.data[0].product_id;
                            this.form.product_detail__id[key] = data.data[0].product_id;
                            this.form.product_detail__selling_cost[key] = data.data[0].selling_cost;
                            this.form.product_detail__name[key] = data.data[0].name;
                            this.form.product_detail__stock[key] = data.data[0].stock;
                            this.form.product_detail__barcode[key] = data.data[0].barcode;
                            this.form.product_detail__stockid[key] = data.data[0].id;
                            this.form.product_detail__qty[key] = 1;
                            this.selectedImage = data.data[0].image;
                            this.reTotal();
                        }
                        /* this.sselected = data.data[0].id;
                        this.form.customer_id = data.data[0].id;
                        this.newEnabled = ''; */
                        /* this.form.name = data.data[0].name;
                        this.form.email = data.data[0].email;
                        this.form.address = data.data[0].address;
                        this.form.mobile = data.data[0].mobile; */
                    })
                    .catch();
                //console.log(product);
               //this.productSelected[key] = product;
            }
        },
        created() {
             if (!User.loggedIn()) {
            this.$router.push({name: '/'})
            }
            else{
                this.form.admin_id = User.id();
            }
           // this.listProducts();
        }
    }
</script>

<style scoped>
.promodelimage{
    width: 200px;
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
.full-form-body .pfooter b {
    margin-right: 10px;
    font-size: 14px;
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

.full-form-header {
    margin: 0 -15px;
    padding: 8px 15px 1px;
}

.full-form-title {
    font-size: .85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    margin-bottom: 6px;
}

.full-form-body .ptitle {
    line-height: 42px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .4px;
    padding: 0 21px 0 10px;
    text-transform: uppercase;
}


</style>
