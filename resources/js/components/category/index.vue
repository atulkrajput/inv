  

<template>
  
  <div>

 <div class="row">
   <div class="col-md-6">
    <input type="text" v-model="searchTerm" @keyup="searchCategory" autofocus autocomplete="off" class="form-control" style="width: 300px;" placeholder="Search By Name">
  </div>
   <div class="col-md-6">
  <span class="float-right"><router-link to="/store-category" class="btn btn-primary">Add Category </router-link></span>
  </div>
 </div>
<br>
  


<br>

   <div class="row">
            <div class="col-lg-12 mb-4">
              <!-- Simple Tables -->
              <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Category List</h6>
                </div>
                <div class="table-responsive">
                  <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                      <tr>
                        <th>S.No</th>
                        <th>Category Name</th>
                        <th>Products</th>
                        <th>Added On</th>
                        <th class="text-center">Status</th>
                          <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(category, count) in categories.data" :key="category.id">
                        <td>{{ (categories.current_page - 1)*(categories.per_page) + count + 1 }}</td>
                        <td> {{ category.name | capitalize }} </td>
                        <td>{{ category.products_count }}</td>
                        <td>{{ category.created_at | setdate }}</td>
                         <td class="text-center" v-if="category.publish == 1">
                            <button class="btn btn-success"><i class="fas fa-check text-white"></i></button>
                         </td>
                         <td class="text-center" v-else>
                            <button class="btn btn-danger"> <i class="fas fa-times text-white"></i> </button>
                         </td>
                       
            <td>
   <router-link :to="{name: 'edit-category', params:{id:category.id}}" class="btn btn-sm btn-primary">Edit</router-link>

 <a @click="deleteCategory(category.id)" class="btn btn-sm btn-danger"><font color="#ffffff">Delete</font></a>
            </td>
                      </tr>
                    
                    </tbody>
                  </table>

                  
                </div>
                <div class="card-footer table-responsive">
                                <pagination class="m-0 float-right" :limit="10" :data="categories" @pagination-change-page="getResults" :show-disabled="true">
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
        categories:{},
        searchTerm:''
      }
    },
    // computed:{
    //   filtersearch(){
    //   return this.categories.filter(category => {
    //      return category.name.toLowerCase().match(this.searchTerm.toLowerCase())
    //   }) 
    //   }
    // },
 
  methods:{
    allCategory(){
      axios.get('/api/category/')
      .then(({data}) => (this.categories = data))
      .catch()
    },
    getResults(page = 1) {
      axios.get('/api/category?page=' + page)
                    .then(response => {
                        this.categories = response.data;
                    });
    },
  deleteCategory(id){
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
                axios.delete('/api/category/'+id)
               .then(() => {
                this.categories = this.categories.filter(category => {
                  return category.id != id
                })
               })
               .catch(() => {
                this.$router.push({name: 'category'})
               })
                Swal.fire(
                  'Deleted!',
                  'Your file has been deleted.',
                  'success'
                )
              }
            })

  }, 
 searchCategory: _.debounce(() => {
                Fire.$emit('searching');

            },100),
            findCategory(){
                 let query = this.searchTerm;
                axios.get('/api/find-category?q=' + query).then(
                    ({data}) => {
                        this.categories = data;
                    }
                )
                .catch(() => {

                });
            }
  },
  created(){
     Fire.$on('searching',() => {
               this.findCategory();
            });
    this.allCategory();
  } 
  

  } 
</script>


<style type="text/css">
  #em_photo{
    height: 40px;
    width: 40px;
  }
</style>