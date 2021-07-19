import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify';
import Rollbar from 'rollbar';

/**
 *  
import '@babel/polyfill'
import 'roboto-fontface/css/roboto/roboto-fontface.css'
import '@mdi/font/css/materialdesignicons.css'
**/

Vue.config.productionTip = false

// Set the Rollbar instance in the Vue prototype
// before creating the first Vue instance.
// This ensures it is available in the same way for every
// instance in your app.
Vue.prototype.$rollbar = new Rollbar({
  accessToken: 'b3b2d13c0a764154b5bb63f74fd855c9',
  captureUncaught: true,
  captureUnhandledRejections: true,
});
// If you have already set up a global error handler,
// just add `vm.$rollbar.error(err)` to the top of it.
// If not, this simple example will preserve the app’s existing
// behavior while also reporting uncaught errors to Rollbar.

Vue.config.errorHandler = (err, vm, info) => {
  vm.$rollbar.error(('conseiller : '+err));
  throw err; // rethrow
};

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
