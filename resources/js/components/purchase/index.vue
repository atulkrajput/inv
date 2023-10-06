 

<template>
  
  <div>


 <div class="row form-inline">
   <div class="col-md-6">
        <input type="text" v-model="form.barcode" autofocus autocomplete="off" class="form-control" style="width: 200px;" placeholder="Enter Barcode"> 
        <button class="btn btn-sm btn-success" @click="getShipment">Get Shipment</button>
        <button class="btn btn-sm btn-danger" @click="reset">Reset Search</button>
        <span :style="style" class="m-l-15"><img src="/svg/loop.gif" class="load-img"></span>
   
   </div>
   <div class="col-md-6">
  <router-link to="/store-shipment" class="btn btn-primary float-right">Add Shipment </router-link>
  </div>
   
 </div>


<br>

   <div class="row">
            <div class="col-lg-12 mb-4">
              <!-- Simple Tables -->
              <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Shipment List</h6>
                </div>
                <div class="table-responsive">
                  <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                      <tr>
                        <th>S.No</th>
                        <th>Shipment No</th>
                        <th>Product - Stock</th>
                        <th>Grand Total</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(purchase,count) in purchases.data" :key="purchase.id">
                        <td>{{ (purchases.current_page - 1)*(purchases.per_page) + count + 1 }}</td>
                        <td>{{ purchase.shipment_number }}</td>
                        <td> <label class="stock-label bg-primary m-b-0"> {{ purchase.stocks_count }}</label> </td>
                        <td> {{ purchase.grand_total | formatNumber }} </td>
            <td>
   <router-link :to="{name: 'view-shipment', params:{id:purchase.id}}" class="btn btn-sm btn-primary">View</router-link>
            </td>
                      </tr>
                    
                    </tbody>
                  </table>
                </div>
                 <div class="card-footer table-responsive">
                                <pagination class="m-0 float-right" :limit="10" :data="purchases" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
              </div>
            </div>
          </div>
          <!--Row-->


   
  </div>


</template>



<script type="text/javascript">
  
  export default {
    
    data(){
      return{
        purchases:{},
        form : {
          barcode:null 
        },
        style : {
          display : 'none',
        }
      }
    },
  
 
  methods:{
    allPurchase(){
      axios.get('/api/purchase/')
      .then(({data}) => (this.purchases = data))
      .catch()
    },
     getResults(page = 1) {
      axios.get('/api/purchase?page=' + page)
                    .then(response => {
                        this.purchases = response.data;
                    });
    },
    getShipment(){
      this.style.display = '';
      axios.post('/api/stocks/get-searched-shipment',this.form)
       .then(
         ({data}) => {
           this.purchases = data;
           this.style.display = 'none';
          }
       )
       .catch();
    },
    reset(){
        this.form.barcode = null;
        this.allPurchase();
    }

  },
  created(){
     if (!User.loggedIn()) {
        this.$router.push({name: '/'})
      }
    this.allPurchase();
  } 
  

  } 
</script>


<style type="text/css">
  #em_photo{
    height: 40px;
    width: 40px;
  }

.stock-label {
    border-radius: 2px;
    color: #fff;
    font-size: .72rem;
    margin-right: 4px;
    padding: 0 5px;
}

.load-img{
  height:27px;
}
</style>