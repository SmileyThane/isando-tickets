import Home from './views/Home'
import User from './views/user/index'
import Company from './views/company/index'
import Customer from './views/customer/index'
import Product from './views/product/index'
import Team from './views/team/index'
import Ticket from './views/ticket/index'
import CreateTicket from './views/ticket/create'
import Login from './views/Auth/Login'


export default [
    {
        path: '/',
        name: 'main',
        component: Login
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/home',
        name: 'home',
        component: Home
    },
    {
        path: '/user',
        name: 'user',
        meta: {
            requiresAuth: true,
        },
        component: User
    },
    {
        path: '/company',
        name: 'company',
        meta: {
            requiresAuth: true,
        },
        component: Company
    },
    {
        path: '/product',
        name: 'product',
        meta: {
            requiresAuth: true,
        },
        component: Product
    },
    {
        path: '/customer',
        name: 'customer',
        meta: {
            requiresAuth: true,
        },
        component: Customer
    },
    {
        path: '/team',
        name: 'team',
        meta: {
            requiresAuth: true,
        },
        component: Team
    },
    {
        path: '/tickets',
        name: 'tickets',
        meta: {
            requiresAuth: true,
        },
        component: Ticket
    },
    {
        path: '/ticket/create',
        name: 'createTicket',
        meta: {
            requiresAuth: true,
        },
        component: CreateTicket
    }
];
