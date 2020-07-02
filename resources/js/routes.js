import Home from './views/Home'
import Company from './views/company/index'
import SingleCompany from './views/company/item'
import Customer from './views/customer/index'
import SingleCustomer from './views/customer/item'
import Product from './views/product/index'
import SingleProduct from './views/product/item'
import Team from './views/team/index'
import SingleTeam from './views/team/item'
import Ticket from './views/ticket/index'
import CreateTicket from './views/ticket/create'
import SingleTicket from './views/ticket/item'
import Login from './views/Auth/Login'
import Register from './views/Auth/Register'
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
        path: '/register',
        name: 'Register',
        component: Register
    },
    {
        path: '/home',
        name: 'Home',
        component: Ticket
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
        path: '/company/:id',
        name: 'Company Data',
        meta: {
            requiresAuth: true,
        },
        component: SingleCompany
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
        path: '/product/:id',
        name: 'Product Data',
        meta: {
            requiresAuth: true,
        },
        component: SingleProduct
    },
    {
        path: '/customer/:id',
        name: 'Customer Data',
        meta: {
            requiresAuth: true,
        },
        component: SingleCustomer
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
        path: '/team/:id',
        name: 'Team Data',
        meta: {
            requiresAuth: true,
        },
        component: SingleTeam
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
        path: '/ticket_create',
        name: 'Create Ticket',
        meta: {
            requiresAuth: true,
        },
        component: CreateTicket
    },
    {
        path: '/ticket/:id',
        name: 'Ticket',
        meta: {
            requiresAuth: true,
        },
        component: SingleTicket
    },
];
