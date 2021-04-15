import VueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(VueRouter);

import Index from "./views/Index";
import Test from "./views/Test";
import Settings from "./views/Settings";
import Process from "./views/Process";
import Analytics from "./views/Analytics";

const routes = [
    {
        path: "/",
        component: Test
    },
    {
        path: "/process",
        component: Process
    },
    {
        path: "/analytics",
        component: Analytics
    },
    {
        path: "/settings",
        component: Settings
    }
];

export default new VueRouter({
    mode: "history",
    routes: routes
});
