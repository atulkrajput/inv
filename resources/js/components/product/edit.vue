  

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
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Edit Product</h1>
                  </div>

      <form class="user" @submit.prevent="ProductUpdate" enctype="multipart/form-data">

        <div class="form-group">

          <div class="form-row">
         
            <div class="col-md-6">
      <label for="exampleFormControlSelect1">Category</label>
  <select class="form-control" id="exampleFormControlSelect1" v-model="form.category_id">
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
                    <img :src="getProductImage()" style="height: 40px; width: 40px;">
                 </div>
            </div>
        </div>

      
         <div class="form-group">

          <div class="form-row">
            


         
            
          </div>
        </div>

 


        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-block">Update</button>
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
        name: '',
        model_no: '',
        category_id: '',
        image: '',
      },
      errors:{},
      categories:{}
    }
  },
  created(){
  	let id = this.$route.params.id
  	axios.get('/api/product/'+id)
  	.then(({data}) => (this.form = data))
  	.catch(console.log('error'))

// Category Collected 
 axios.get('/api/get-category/')
    .then(({data}) => (this.categories = data))

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
       
      };
      reader.readAsDataURL(file);
     }

    },
  ProductUpdate(){
  	  let id = this.$route.params.id
       axios.patch('/api/product/'+id,this.form)
       .then(() => {
        this.$router.push({ name: 'product'})
        Notification.success()
       })
       .catch(error =>this.errors = error.response.data.errors)
     },
      getProductImage(){
             let eimage,photo;   
                eimage = this.form.image;
             if(this.form.image == null)   
                this.form.image = 'default.png';
			       photo = (this.form.image.length > 200) ? this.form.image : "/backend/product/" + eimage;
			       return photo;
            },
  } 


  }
   
</script>


<style type="text/css" scoped>
  .mt-custom{
    margin-top:2rem;
  }
</style>