

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
                    <h1 class="h4 text-gray-900 mb-4">Resize Images</h1>
                  </div>

      <form class="user">

        <div class="form-group">
          <div class="form-row">
        

            <div class="col-md-10">
              <!-- <input type="file" id="customFile" multiple accept=".png, .jpg, .jpeg" @change="uploadImages"> 
              <label class="custom-file-label" for="customFile">Choose file</label> -->
               <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions"></vue-dropzone>
            </div>  
<div class="col-md-2">
    <span :style="style" class="m-l-15"><img src="/svg/loop.gif" class="load-img"></span>
</div>
        
            
          </div>
        </div>
       
        
         



        <div class="form-group">
          <button type="submit" @click="processQueue" class="btn btn-primary">Submit</button>
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
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
  export default {
    components: {
    vueDropzone: vue2Dropzone
    },
    created(){
      if (!User.loggedIn()) {
        this.$router.push({name: '/'})
      }
    },

    data(){
    return {
      form:new Form({
            pics:[],
            picname:[]
      }),
      style : {
          display : 'none',
        },
       dropzoneOptions: {
          url: '/api/resizeimages',
          thumbnailWidth: 300,
          uploadMultiple : true,
          maxFiles: 5,
          parallelUploads : 5
      }  
    }
  },

  methods:{
      processQueue() {
      this.$refs.myVueDropzone.processQueue();
    },
     uploadImages(e){
               let selectedfiles = e.target.files;
			            
			        for(var i=0 ;i<selectedfiles.length; i++){
			            let reader = new FileReader();
                        reader.onloadend = ({ selectedfiles }) => {
					    this.form.pics.push(reader.result);
					    // console.log(this.form.pics);
				    }
			        reader.readAsDataURL(selectedfiles[i]);
                     this.form.picname.push(selectedfiles[i].name);
			    }
		    },
             ImageInsert() {
                  this.style.display = '';
                this.form.post('/api/resizeimages')
                .then(() => {
                     this.style.display = 'none';
                     this.form.reset();
                     Toast.fire({
                            icon: 'success',
                            title: 'resized successfully'
                            });     

                })
                .catch(() => {

                });
            },
 
  },
  created(){
  
  } 


  }
   
</script>


<style type="text/css" scoped>
  .mt-custom{
    margin-top:2rem;
  }

  .load-img{
  height:27px;
}
</style>