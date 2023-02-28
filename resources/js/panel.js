import Vue from 'vue';
import router from './routes.js';
import App from './components/App.vue';
import VueSweetalert2 from 'vue-sweetalert2';
import BootstrapVue from "bootstrap-vue";
Vue.use(BootstrapVue);

Vue.use(VueSweetalert2);

const app = new Vue({
    el: '#app',
    router,
    render: h => h(App)
});

export default app;
