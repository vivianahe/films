import Vue from "vue";
import VueRouter from "vue-router";
import ExampleComponent from "./components/ExampleComponent";
import prueba from "./components/prueba";



Vue.use(VueRouter);
const router = new VueRouter({
    mode: 'history',
    routes: [
        { path: '/home', name:'home', component: ExampleComponent },
        { path: '/prueba', name:'prueba', component: prueba }
    ]
});
export default router;

