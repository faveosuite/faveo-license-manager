import { createWebHistory, createRouter } from "vue-router";

import routes  from './routes';

console.log(routes)

const router = createRouter({

    history: createWebHistory(),

    inkActiveClass: '',

    linkExactActiveClass :'active exact-active',

    routes
})

export default router;
