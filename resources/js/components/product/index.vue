 

<template>
  
  <div>


 <div class="row">
   <div class="col-md-6">
      <input type="text" v-model="searchTerm" @keyup="searchProduct" autofocus autocomplete="off" class="form-control" style="width: 300px;" placeholder="Search Here">
   </div>
   <div class="col-md-6">
  <router-link to="/store-product" class="btn btn-primary float-right">Add Product </router-link>
  </div>
   
 </div>


<br>

   <div class="row">
            <div class="col-lg-12 mb-4">
              <!-- Simple Tables -->
              <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Product List</h6>
                </div>
                <div class="table-responsive">
                  <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                      <tr>
                        <th>S.No</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Product Name</th>
                        <th>Product Code</th>
                        <th>Added On</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(product,count) in products.data" :key="product.id">
                        <td>{{ (products.current_page - 1)*(products.per_page) + count + 1 }}</td>
                        <td><img :src="showProductImage(product.image)" id="em_photo"></td>
                        <td>{{ product.cname }}</td>
                        <td> {{ product.name }} </td>
                        <td> {{ product.model_no }} </td>
                        <td>{{ product.created_at | setdate }}</td>
            <td>
   <router-link :to="{name: 'edit-product', params:{id:product.id}}" class="btn btn-sm btn-primary">Edit</router-link>

 <a @click="deleteProduct(product.id)" class="btn btn-sm btn-danger"><font color="#ffffff">Delete</font></a>
            </td>
                      </tr>
                    
                    </tbody>
                  </table>
                </div>
                 <div class="card-footer table-responsive">
                                <pagination class="m-0 float-right" :limit="10" :data="products" @pagination-change-page="getResults" :show-disabled="true">
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
    created(){
      if (!User.loggedIn()) {
        this.$router.push({name: '/'})
      }
    },
    data(){
      return{
        products:{},
        searchTerm:''
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
    allProduct(){
      axios.get('/api/product/')
      .then(({data}) => (this.products = data))
      .catch()
    },
      showProductImage(img){
                 let photo = img == null ? "/backend/product/default.png" : "/backend/product/"+img;
			 return photo;
            },
     getResults(page = 1) {
      axios.get('/api/product?page=' + page)
                    .then(response => {
                        this.products = response.data;
                    });
    },
  deleteProduct(id){
             Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
              if (result.value) {
                axios.delete('/api/product/'+id)
               .then(() => {
                this.products = this.products.filter(product => {
                  return product.id != id
                })
               })
               .catch(() => {
                this.$router.push({name: 'product'})
               })
                Swal.fire(
                  'Deleted!',
                  'Your file has been deleted.',
                  'success'
                )
              }
            })

  },
  searchProduct: _.debounce(() => {
                Fire.$emit('searching');

            },100),
            findProduct(){
                 let query = this.searchTerm;
                axios.get('/api/find-product?q=' + query).then(
                    ({data}) => {
                        this.products = data;
                    }
                )
                .catch(() => {

                });
            } 

  },
  created(){
    Fire.$on('searching',() => {
               this.findProduct();
            });
    this.allProduct();
  } 
  

  } 
</script>


<style type="text/css">
  #em_photo{
    height: 40px;
    width: 40px;
  }
</style>