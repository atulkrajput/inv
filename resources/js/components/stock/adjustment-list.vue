 

<template>
  
  <div>


 <div class="row">
   <div class="col-md-12">
  <router-link to="/adjustments" class="btn btn-primary float-right">Add Adjustment </router-link>
  </div>
   
 </div>


<br>

   <div class="row">
            <div class="col-lg-12 mb-4">
              <!-- Simple Tables -->
              <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Today's Adjustments</h6>
                </div>
                <div class="table-responsive">
                  <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                      <tr>
                        <th>S.No</th>
                        <th>Adjustment No</th>
                        <th>Products</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(adjustment,count) in adjustments.data" :key="adjustment.id">
                        <td>{{ count+1 }}</td>
                        <td>{{ adjustment.id }}</td>
                        <td> {{ adjustment.adjustment_product_count }} </td>
                        <td> {{ parseFloat(adjustment.grand_total) }} </td>
                        <td>{{ adjustment.created_at | setdate }}</td>
            <td>

 <a @click="viewAdjustment(adjustment.id)" class="btn btn-sm btn-success">View</a>
            </td>
                      </tr>
                    
                    </tbody>
                  </table>
                </div>
                 <div class="card-footer table-responsive">
                                <pagination class="m-0 float-right" :limit="10" :data="adjustments" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
              </div>
            </div>
          </div>
          <!--Row-->

          <div class="modal fade" id="viewAdjustmentModal"  data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                
                <div class="modal-content">
                     <div class="modal-header">
                            <h5 class="modal-title">Adjustment View</h5>
                        </div>
                        <div class="modal-body">
                        <table class="table align-items-center table-flush">
                            <thead>
                                <tr>
                                    <th>S No</th>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <tr v-for="(adj,count) in adjustment.data" :key="adj.id">
                                        <td>{{ count+1 }}</td>
                                        <td><img :src="showProductImage(adj.image)" id="em_photo"></td>
                                        <td> Item Code : <b>{{ adj.model_no }}</b><br/>
                                            Barcode : <b>{{ adj.barcode }}</b>
                                        </td>
                                        <td> {{ adj.price }} </td>
                                        <td> {{ adj.qty }} </td>
                                        <td> {{ adj.qty * adj.price }} </td>
                      </tr>
                            </tbody>
                        </table>
                </div>
                 <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal">Close</button>
                        </div>
            </div>
        </div>
        </div>


   
  </div>


</template>



<script type="text/javascript">
  
  export default {
    created(){
      if (!User.loggedIn()) {
        this.$router.push({name: '/'})
      }
    },
    data(){
      return{
        adjustments:{},
        adjustment:{}
      }
    },
    // computed:{
    //   filtersearch(){
    //   return this.products.filter(product => {
    //      return product.name.match(this.searchTerm)
    //   }) 
    //   }
    // },
 
  methods:{
    allAdjustment(){
      axios.get('/api/adjustment/')
      .then(data => (this.adjustments = data))
      .catch()
    },
     showProductImage(img){
                 let photo = img == null ? "/backend/product/default.png" : "/backend/product/"+img;
			 return photo;
            },
    viewAdjustment(id){
        axios.get('/api/adjustment_product/' + id).then(data => (this.adjustment = data))
        .catch();
        $('#viewAdjustmentModal').modal('show');
    },
     getResults(page = 1) {
      axios.get('/api/adjustment?page=' + page)
                    .then(response => {
                        this.adjustments = response.data;
                    });
    },


  },
  created(){
    this.allAdjustment();
  } 
  

  } 
</script>


<style type="text/css">
  #em_photo{
    height: 40px;
    width: 40px;
  }
</style>