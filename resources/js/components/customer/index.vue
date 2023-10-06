  

<template>
  
  <div>

 <div class="row">
   <div class="col-md-6">
    <input type="text" v-model="searchTerm" @keyup="searchCustomer" autofocus autocomplete="off" class="form-control" style="width: 300px;" placeholder="Search By Name">
  </div>
   <div class="col-md-6">
  <span class="float-right"><router-link to="/store-customer" class="btn btn-primary">Add Customer </router-link></span>
  </div>
 </div>
<br>
  


<br>

   <div class="row">
            <div class="col-lg-12 mb-4">
              <!-- Simple Tables -->
              <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Customer List</h6>
                </div>
                <div class="table-responsive">
                  <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                      <tr>
                        <th>S.No</th>
                        <th>Customer Name</th>
                        <th>Contact No</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Orders</th>
                        <th>Joined</th>
                        <th>Promotion</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(customer, count) in customers.data" :key="customer.id">
                        <td>{{ (customers.current_page - 1)*(customers.per_page) + count + 1 }}</td>
                        <td> {{ customer.name }} </td>
                        <td>{{ customer.mobile }}</td>
                        <td>{{ customer.email }}</td>
                        <td>{{ customer.address }}</td>
                        <td>{{ customer.sales_count }}</td>
                        <td>{{ customer.created_at | setdate }}</td>
                         <td class="text-center" v-if="customer.promotions == 1">
                            <button class="btn btn-success"><i class="fas fa-check text-white"></i></button>
                         </td>
                         <td class="text-center" v-else>
                            <button class="btn btn-danger"> <i class="fas fa-times text-white"></i> </button>
                         </td>
                       
            <td>
   <router-link :to="{name: 'edit-customer', params:{id:customer.id}}" class="btn btn-sm btn-primary">Edit</router-link>

 <a @click="deleteCustomer(customer.id)" class="btn btn-sm btn-danger"><font color="#ffffff">Delete</font></a>
            </td>
                      </tr>
                    
                    </tbody>
                  </table>

                  
                </div>
                <div class="card-footer table-responsive">
                                <pagination class="m-0 float-right" :limit="10" :data="customers" @pagination-change-page="getResults" :show-disabled="true">
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
      return {
        customers:{},
        searchTerm:''
      }
    },
    // computed:{
    //   filtersearch(){
    //   return this.customers.filter(customer => {
    //      return customer.name.toLowerCase().match(this.searchTerm.toLowerCase())
    //   }) 
    //   }
    // },
 
  methods:{
    allCustomer(){
      axios.get('/api/customer/')
      .then(({data}) => (this.customers = data))
      .catch()
    },
    getResults(page = 1) {
      axios.get('/api/customer?page=' + page)
                    .then(response => {
                        this.customers = response.data;
                    });
    },
  deleteCustomer(id){
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
                axios.delete('/api/customer/'+id)
               .then(() => {
                this.customers = this.customers.filter(customer => {
                  return customer.id != id
                })
               })
               .catch(() => {
                this.$router.push({name: 'customer'})
               })
                Swal.fire(
                  'Deleted!',
                  'Your file has been deleted.',
                  'success'
                )
              }
            })

  }, 
 searchCustomer: _.debounce(() => {
                Fire.$emit('searching');

            },100),
            findCustomer(){
                 let query = this.searchTerm;
                axios.get('/api/find-customer?q=' + query).then(
                    ({data}) => {
                        this.customers = data;
                    }
                )
                .catch(() => {

                });
            }
  },
  created(){
     Fire.$on('searching',() => {
               this.findCustomer();
            });
    this.allCustomer();
  } 
  

  } 
</script>


<style type="text/css">
  #em_photo{
    height: 40px;
    width: 40px;
  }
</style>