

require('./bootstrap');


import _ from 'lodash';
import moment from 'moment';
import numeral from 'numeral';
import Vue from 'vue'
import VueRouter from 'vue-router'
import Vue2Filters from 'vue2-filters'
import Form from 'vform';
import { HasError,AlertError } from 'vform/src/components/bootstrap4';
import { ModelSelect } from 'vue-search-select';
import 'vue-search-select/dist/VueSearchSelect.css';
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
Vue.use(VueRouter)
Vue.use(Vue2Filters)

 // Router Imported
 import {routes} from './routes';


 // Import User Class
 import User from './Helpers/User';
 window.User = User

 // Import Notification Class
 import Notification from './Helpers/Notification';
 window.Notification = Notification

 window.Form = Form;

 Vue.component('pagination', require('laravel-vue-pagination'));
 Vue.component('model-select', ModelSelect);
 Vue.component('date-picker', DatePicker);
 Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

Vue.component('vue-search', require('./components/custom/Search.vue').default);


 // Sweet Alert start 
import Swal from 'sweetalert2'
window.Swal = Swal;

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  onOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
});

window.Toast = Toast;

Vue.filter('setdate', function(mydate){
  return moment(mydate).format('DD.MM.YYYY');
});
Vue.filter("formatNumber", function (value) {
  return numeral(value).format("0,0.00");
});
Vue.filter("freeNumber", function (value) {
  return numeral(value).format("0,0");
});
 
// Sweet Alert End 

window.Reload = new Vue();

//Fire
window.Fire = new Vue(); 

const router = new VueRouter({
  routes,
  mode: 'history',
  linkActiveClass: "active"
})






const app = new Vue({
    el: '#app',
    router
});
