<template>
  
  <div>

    <div class="row">
      <div class="col-xl-3 col-lg-3 col-md-3">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="card-setting">
                  <div>
                    <h6>Stock Reports</h6>
                    <p><i class="fas fa-calendar"></i> Full Stock Report</p>
                    <a href="/reports/full-stock-report" class="btn btn-primary btn-sm text-uppercase">get full stock report</a>
                    <hr>
                    <p><i class="fas fa-calendar"></i> Structured Stock Report</p>
                    <a href="/reports/structured-stock-report" class="btn btn-primary btn-sm text-uppercase">Get Full Structured Stock Report</a>
                    <hr>
                    <p><i class="fas fa-calendar"></i> Negative Stock Report</p>
                    <a href="/reports/negative-stock-report" class="btn btn-primary btn-sm text-uppercase">GET Negative stock report</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-3 col-md-3">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="card-setting">
                  <div>
                    <h6>Day Reports</h6>
                     <p><i class="fas fa-calendar"></i> Sale Report</p>
                        <form @submit.prevent="fetchM1Report()">
                                    <div class="form-group">
                                       <date-picker v-model="M1form.date" lang="en" type="date" format="YYYY-MM-DD" placeholder="Select Date"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                        </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-3 col-md-3">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="card-setting">
                  <div>
                    <h6>Month Reports</h6>
                    <p><i class="fas fa-calendar"></i> Full Report</p>
                        <form @submit.prevent="fetchM2Report()">
                                    <div class="form-group">
                                       <date-picker v-model="M2form.date" lang="en" type="month" format="YYYY-MM" placeholder="Select Date"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                        </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-3 col-md-3">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="card-setting">
                  <div>
                    <h6>Custom Date Reports</h6>
                     <p><i class="fas fa-calendar"></i> Adjustments Report</p>
                        <form @submit.prevent="fetchC1Report()">
                                    <div class="form-group">
                                       <date-picker v-model="C1form.drange" lang="en" type="date" range confirm format="YYYY-MM-DD" placeholder="Select Date Range"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                        </form>
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
 data() {
            return {
                M1form: new Form({
                    date:''
                }),
                M2form: new Form({
                    date:''
                }),
                C1form: new Form({
                    drange:''
                })
            }
        },
   
     methods: {
            fetchM1Report(){
                if(this.M1form.date){
                    let activeFullDate = new Date(this.M1form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/sales-day-report/'+adate, '_blank');
                } else {
                    Swal.fire({
                            icon: 'error',
                            title: 'Please select the date',
                        });
                }
             
            },
            fetchM2Report(){
                if(this.M2form.date){
                    let activeFullDate = new Date(this.M2form.date);
                    let adate2 = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/month-report/'+adate2, '_blank');
                } else {
                    Swal.fire({
                            icon: 'error',
                            title: 'Please select the date',
                        });
                }
             
            },
            fetchC1Report(){
                if(this.C1form.drange){
                    let activeFullDate = new Date(this.C1form.drange[0]);
                    let activeFullDate2 = new Date(this.C1form.drange[1]);
                    let ad1 = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    let ad2 = activeFullDate2.getFullYear() +'-'+ ("0" + parseInt(activeFullDate2.getMonth()+1)).slice(-2) +'-'+ activeFullDate2.getDate();
                    window.open('/reports/adjustment-report/'+ad1+'/'+ad2, '_blank');
                } else {
                    Swal.fire({
                            icon: 'error',
                            title: 'Please select the date',
                        });
                }
            }
        },

  created(){
    
  } 


  }
   
</script>


<style type="text/css" scoped>
  .card-setting{
    padding:20px;
  }
  .card-setting h6{
        text-align: center;
  }
  .card-setting p{
    font-size: 12px;
    margin-bottom: 5px;
  }
  .btn-sm{
    font-size: 10px;
  }
  .mx-datepicker-range{
    width: 210px;
  }
</style>