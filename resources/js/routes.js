import Home from './views/Home'
import Company from './views/company/index'
import Customer from './views/customer/index'
import Product from './views/product/index'
import Team from './views/team/index'
import Ticket from './views/ticket/index'
import CreateTicket from './views/ticket/create'
import Login from './views/Auth/Login'
import Profile from './views/Auth/Profile'


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
        name: 'Home',
        component: Home
    },
    {
        path: '/user',
        name: 'Profile',
        meta: {
            requiresAuth: true,
        },
        component: Profile
    },
    {
        path: '/company',
        name: 'Companies',
        meta: {
            requiresAuth: true,
        },
        component: Company
    },
    {
        path: '/product',
        name: 'Products',
        meta: {
            requiresAuth: true,
        },
        component: Product
    },
    {
        path: '/customer',
        name: 'Customers',
        meta: {
            requiresAuth: true,
        },
        component: Customer
    },
    {
        path: '/team',
        name: 'Teams',
        meta: {
            requiresAuth: true,
        },
        component: Team
    },
    {
        path: '/tickets',
        name: 'Tickets',
        meta: {
            requiresAuth: true,
        },
        component: Ticket
    },
    {
        path: '/ticket/create',
        name: 'Create Ticket',
        meta: {
            requiresAuth: true,
        },
        component: CreateTicket
    }
];
