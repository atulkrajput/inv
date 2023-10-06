  

<template>
  
  <div>

 <div class="row">
 <div class="col-md-12">
  <span class="float-right"><router-link to="/customers" class="btn btn-info">Back to List </router-link></span>
  </div>
 </div>



    <div class="row justify-content-center">
      <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4"> Customer Update</h1>
                  </div>

      <form class="user" @submit.prevent="customerUpdate">

      <div class="form-group">

          <div class="form-row">
            <div class="col-md-6">
         <label for="exampleFormControlTextarea1"><b>Customer Name </b></label>
         <input type="text" class="form-control" id="exampleInputFirstName" placeholder="Enter Your Full Name" v-model="form.name">
       <small class="text-danger" v-if="errors.name"> {{ errors.name[0] }} </small>
            </div>


     
            <div class="col-md-6">
       <label for="exampleFormControlTextarea1"><b>Customer Phone </b></label>
         <input type="text" class="form-control" id="exampleInputFirstName" placeholder="Enter Your Mobile No" v-model="form.mobile">
         <small class="text-danger" v-if="errors.mobile"> {{ errors.mobile[0] }} </small>
            </div> 
            
          </div>
        </div> 
       
        
         <div class="form-group">

          <div class="form-row">

<div class="col-md-6">
       <label for="exampleFormControlTextarea1"><b>Customer Email </b></label>
         <input type="email" class="form-control" id="exampleInputFirstName" placeholder="Enter Your Email" v-model="form.email">
         <small class="text-danger" v-if="errors.email"> {{ errors.email[0] }} </small>
            </div>    

            <div class="col-md-6">
               <label for="exampleFormControlTextarea1"><b>Customer Address </b></label>
         <input type="text" class="form-control" id="exampleInputFirstName" placeholder="Enter Your Address" v-model="form.address">
         <small class="text-danger" v-if="errors.address"> {{ errors.address[0] }} </small>
            </div>


     
            
            <div class="col-md-6">
        <input v-model="form.promotions" type="checkbox" id="promotions" name="promotions"> <label for="promotions"> Allow to receive promotional call, sms or emails.</label>
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
        id : null,
        name: null,
        email: null,
        mobile: null,
        address: null,
        promotions: null
       
      },
      errors:{}
    }
  },
  created(){
  	let id = this.$route.params.id
  	axios.get('/api/customer/'+id)
  	.then(({data}) => (this.form = data))
  	.catch(console.log('error'))
  },

  methods:{
  customerUpdate(){
  	  let id = this.$route.params.id
       axios.patch('/api/customer/'+id,this.form)
       .then(() => {
        this.$router.push({ name: 'customer'})
        Notification.success()
       })
       .catch(error =>this.errors = error.response.data.errors)
     },
  } 


  }
   
</script>


<style type="text/css">
  
</style>