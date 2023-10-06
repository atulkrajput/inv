

<template>
  
  <div>

 <div class="row">
   <div class="col-md-12">
   
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
                    <h1 class="h4 text-gray-900 mb-4">Settings</h1>
                  </div>

      <form class="user" @submit.prevent="updatePassword">
        <div class="form-group">
          <div class="form-row">
            <div class="col-md-12 mb-3">
              <label for="old_password">Old Password</label>
              <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Enter Your Old Password" v-model="form.old_password">
              <has-error :form="form" field="old_password"></has-error>
            </div>
            <div class="col-md-12 mb-3">
              <label for="new_password">New Password</label>
              <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter Your New Password" v-model="form.new_password">
            <has-error :form="form" field="new_password"></has-error>
            </div>
            <div class="col-md-12 mb-3">
              <label for="confirm_password">Confirm New Password</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your New Password" v-model="form.confirm_password">
             <has-error :form="form" field="confirm_password"></has-error>
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
     data() {
            return {
                 imgurl: 'images/',
                profile: {},
                editmode: false,
                form: new Form({
                    id:'',
                    old_password:'',
                    new_password:'',
                    confirm_password:''
                })
            }
        },

   methods: {
            getProfile() {
                axios.get('api/get-profile')
                    .then(response => {
                        this.profile = response.data;
                    });
            },
            updatePassword(){
                this.form.post('api/users/update-password')
                .then(() => {
                    Toast.fire({
                        icon:'success',
                        title:'password updated successfully'
                    });
                    this.form.reset();
                })
                .catch(() => {

                });
            }
        },
        created() {
           if (!User.loggedIn()) {
              this.$router.push({name: '/'})
          }
          else{
            this.form.id = User.id();
          }
             this.getProfile();
        }


  }
   
</script>


<style type="text/css" scoped>
  .mt-custom{
    margin-top:2rem;
  }
</style>