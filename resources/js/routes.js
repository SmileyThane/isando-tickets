import Home from './views/Home'
import User from './views/user/index'
import Company from './views/company/index'
import Customer from './views/customer/index'
import Product from './views/product/index'
import Team from './views/team/index'
import Ticket from './views/ticket/index'
import CreateTicket from './views/ticket/create'



export default [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/home',
        name: 'home',
        component: Home
    },
    {
        path: '/user',
        name: 'user',
        method:'get',
        component: User
    },
    {
        path: '/company',
        name: 'company',
        component: Company
    },
    {
        path: '/product',
        name: 'product',
        component: Product
    },
    {
        path: '/customer',
        name: 'customer',
        component: Customer
    },
    {
        path: '/team',
        name: 'team',
        component: Team
    },
    {
        path: '/tickets',
        name: 'tickets',
        component: Ticket
    },
    {
        path: '/ticket/create',
        name: 'createTicket',
        component: CreateTicket
    }
];
