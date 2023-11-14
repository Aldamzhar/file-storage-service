import './bootstrap';
import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    { path: '/files', component: () => import('./components/FileList.vue') },
    { path: '/files/create', component: () => import('./components/FileForm.vue') },
    { path: '/files/:id/edit', component: () => import('./components/FileForm.vue') }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
