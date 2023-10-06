

<template>
  
  <div>

 <div class="row">
   <div class="col-md-12">
   <span class="float-right">
  <router-link to="/product" class="btn btn-primary">Back to Products</router-link>
   </span>  
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
                    <h1 class="h4 text-gray-900 mb-4">Add Product</h1>
                  </div>

      <form class="user" @submit.prevent="ProductInsert" enctype="multipart/form-data">

<div class="form-group">
  <div class="form-row">
    <div class="col-md-6">
      <label for="exampleFormControlSelect1">Category</label>
      <select class="form-control" id="exampleFormControlSelect1" v-model="form.category_id" data-placeholder="Select Category">
        <option value="" selected>Select Category</option>
        <option :value="category.id" :key="category.id" v-for="category in categories">{{ category.name }}</option>
      </select>    
    </div>

    <div class="col-md-6">
              <label for="exampleFormControlSelect1">Product Name</label>
         <input type="text" class="form-control" id="exampleInputFirstName" placeholder="Enter Your Product Name" v-model="form.name">
       <small class="text-danger" v-if="errors.name"> {{ errors.name[0] }} </small>
    </div>
  </div>
</div>

        <div class="form-group">
          <div class="form-row">
     <div class="col-md-6">
      <label for="exampleFormControlSelect1">Product Code</label>
         <input type="text" class="form-control" id="exampleInputFirstName" placeholder="Enter Your Product Code" v-model="form.model_no">
         <small class="text-danger" v-if="errors.model_no"> {{ errors.model_no[0] }} </small>
            </div>   

            <div class="col-md-5 mt-custom">
              <input type="file" class="custom-file-input" id="customFile" @change="onFileSelected">
              <small class="text-danger" v-if="errors.image"> {{ errors.image[0] }} </small>
              <label class="custom-file-label" for="customFile">Choose file</label>
            </div>  

               <div class="col-md-1 mt-custom">
                 <img :src="form.image" style="height: 40px; width: 40px;">
               </div>    
            
          </div>
        </div>
       
        
         <div class="form-group">

          <div class="form-row">
            
<div class="col-md-12">
  <label for="description">Description</label>
  <textarea name="description" class="form-control" v-model="form.description"></textarea>
</div>

          
            
          </div>
        </div>



        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-block">Submit</button>
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
        model_no: null,
        category_id: null,
        image: null,
        description: null
      },
      errors:{},
      categories:{}
    }
  },

  methods:{
    onFileSelected(event){
     let file = event.target.files[0];
     if (file.size > 1048770) {
      Notification.image_validation()
     }else{
      let reader = new FileReader();
      reader.onload = event =>{
        this.form.image = event.target.result
        console.log(event.target.result);
      };
      reader.readAsDataURL(file);
     }

    },
  ProductInsert(){
       axios.post('/api/product',this.form)
       .then(() => {
        this.$router.push({ name: 'product'})
        Notification.success()
       })
       .catch(error =>this.errors = error.response.data.errors)
     },
  },
  created(){
    axios.get('/api/get-category/')
    .then(({data}) => (this.categories = data))
  } 


  }
   
</script>


<style type="text/css" scoped>
  .mt-custom{
    margin-top:2rem;
  }
</style>