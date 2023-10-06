 

<template>
  
  <div>

 <div class="row">
   <div class="col-md-12">
  <span class="float-right"><router-link to="/category" class="btn btn-info">Categories List </router-link></span>
  </div>
   
 </div>



    <div class="row justify-content-center">
      <div class="col-xl-8 col-lg-8 col-md-8">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <div>
                    <h1 class="h4 text-gray-900 mb-4">Add Category</h1>
                  </div>

      <form class="user" @submit.prevent="categoryInsert">

        <div class="form-group">

          <div class="form-row">
            <div class="col-md-12 mb-3">
              <label for="name">Category Name</label>
         <input type="text" class="form-control" id="exampleInputFirstName" name="name" placeholder="Enter Your Category Name" v-model="form.name">
  <small class="text-danger" v-if="errors.name"> {{ errors.name[0] }} </small>
            </div>
            <div class="col-md-6">
               <div class="custom-control custom-radio">      
                    <input type="radio" v-model="form.publish" id="publish1" value="1" name="publish" class="custom-control-input">
                    <label class="custom-control-label" for="publish1">Publish Now</label>
               </div>
            </div>
            <div class="col-md-6">
                            <div class="custom-control custom-radio">
                            <input type="radio" v-model="form.publish" id="publish0" value="0" name="publish" class="custom-control-input">
                            <label class="custom-control-label" for="publish0">Publish Later</label>
                            </div>
            </div> 
                              <small class="text-danger" v-if="errors.publish"> {{ errors.publish[0] }} </small>
            
          </div>
        </div>
       
         
 


        <div class="form-group">
          <button type="submit" class="btn btn-success">Submit</button>
        </div>
        
      </form>
                  <hr>
                  <div class="text-center">
  
  
                  </div>
                  <div class="text-center">
                  </div>
                </div>
              </div>
            </div>
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
    return {
      form:{
        name: null,
        publish:null
      },
      errors:{}
    }
  },

  methods:{
    
  categoryInsert(){
       axios.post('/api/category',this.form)
       .then(() => {
        this.$router.push({ name: 'category'})
        Notification.success()
       })
       .catch(error =>this.errors = error.response.data.errors)
     },
  } 


  }
   
</script>


<style type="text/css">
  
</style>