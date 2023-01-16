import Company from './views/company/index'
import SingleCompany from './views/company/item'
import Customer from './views/customer/index'
import SingleCustomer from './views/customer/item'
import Product from './views/product/index'
import SingleProduct from './views/product/item'
import CustomLicenses from './views/custom_license/index'
import CustomLicense from './views/custom_license/item'
import CustomLicenseUnassignedUsers from './views/custom_license/unassigned'
import Team from './views/team/index'
import SingleTeam from './views/team/item'
import Employee from './views/employee/index'
import SingleEmployee from './views/employee/item'
import Ticket from './views/ticket/index'
import CreateTicket from './views/ticket/create'
import SingleTicket from './views/ticket/item'
import Login from './views/auth/login'
import Register from './views/auth/register'
import Profile from './views/auth/profile'
import SystemSettings from './views/settings/system'
import AllCustomers from './views/customer/all'
import Notifications from './views/notify/index'
import SingleNotification from './views/notify/item'
import NotificationsHistory from './views/notify/history'
import SingleNotificationHistory from './views/notify/sent'
import TrackingDashboard from './views/tracking/dashboard'
import TrackingTracker from './views/tracking/tracker'
import TrackingTimesheet from './views/tracking/timesheet'
import TrackingCalendar from './views/tracking/calendar'
import TrackingProjects from './views/tracking/projects'
import TrackingProjectItem from './views/tracking/projects/item'
import TrackingReports from './views/tracking/reports'
import TrackingSettings from './views/tracking/settings'
import ResetPassword from './views/auth/reset-password'
import KnowledgeBase from './views/knowledge_base/initial'
import KnowledgeBaseArticle from './views/knowledge_base/item'
import KnowledgeBaseCreate from './views/knowledge_base/create'
import RoleManagement from './views/superadmin/roles'
import Landing from './layouts/App'
import RiskRepository from './views/risk_repository'
import RiskRepositoryMore from './views/risk_repository/more-page'
import IncidentReportingCreate from './views/incident_reporting/create'
import IncidentReportingActionBoards from './views/incident_reporting/action-boards'
import SettingsIncidentSettings from './views/settings/incident_settings'

import store from './store';

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
        name: 'home',
        component: Ticket
    },
    {
        path: '/user',
        name: 'profile',
        meta: {
            requiresAuth: true,
        },
        component: Profile
    },
    {
        path: '/company',
        name: 'companies',
        meta: {
            requiresAuth: true,
        },
        component: Company
    },
    {
        path: '/company/:id',
        name: 'company',
        meta: {
            requiresAuth: true,
        },
        component: SingleCompany
    },
    {
        path: '/product',
        name: 'products',
        meta: {
            requiresAuth: true,
        },
        component: Product
    },
    {
        path: '/product/:id',
        name: 'product',
        meta: {
            requiresAuth: true,
        },
        component: SingleProduct
    },
    {
        path: '/customer/:id',
        name: 'customer',
        meta: {
            requiresAuth: true,
        },
        component: SingleCustomer
    },
    {
        path: '/customer',
        name: 'customers',
        meta: {
            requiresAuth: true,
        },
        component: Customer
    },
    {
        path: '/all',
        name: 'all',
        meta: {
            requiresAuth: true,
        },
        component: AllCustomers
    },
    {
        path: '/team',
        name: 'teams',
        meta: {
            requiresAuth: true,
        },
        component: Team
    },
    {
        path: '/team/:id',
        name: 'team',
        meta: {
            requiresAuth: true,
        },
        component: SingleTeam
    },
    {
        path: '/employee',
        name: 'individuals',
        meta: {
            requiresAuth: true,
        },
        component: Employee
    },
    {
        path: '/employee/:id',
        name: 'individual',
        meta: {
            requiresAuth: true,
        },
        component: SingleEmployee
    },
    {
        path: '/tickets',
        name: 'ticket_list',
        meta: {
            requiresAuth: true,
        },
        component: Ticket
    },
    {
        path: '/ticket_create',
        name: 'create_ticket',
        meta: {
            requiresAuth: true,
        },
        component: CreateTicket
    },
    {
        path: '/ticket/:id',
        name: 'ticket',
        meta: {
            requiresAuth: true,
        },
        component: SingleTicket
    },
    {
        path: '/settings/system',
        name: 'system_settings',
        meta: {
            requiresAuth: true,
        },
        component: SystemSettings
    },
    {
        path: '/settings/incident',
        name: 'settings_incident',
        meta: {
            requiresAuth: true,
        },
        component: SettingsIncidentSettings
    },
    {
        path: '/notify',
        name: 'notify_customers',
        meta: {
            requiresAuth: true,
        },
        component: Notifications
    },
    {
        path: '/notify/:id',
        name: 'notification',
        meta: {
            requiresAuth: true,
        },
        component: SingleNotification
    },
    {
        path: '/notify_history',
        name: 'notifications_history',
        meta: {
            requiresAuth: true,
        },
        component: NotificationsHistory
    },
    {
        path: '/notify_history/:id',
        name: 'notification_history_single',
        meta: {
            requiresAuth: true,
        },
        component: SingleNotificationHistory
    },
    {
        path: '/reset-password',
        name: 'reset_password',
        meta: {
            requiresAuth: false,
        },
        component: ResetPassword
    },
    {
        path: '/tracking',
        meta: {
            requiresAuth: true
        },
        redirect: {name: 'tracking_dashboard'}
    },
    {
        path: '/tracking/dashboard',
        name: 'tracking_dashboard',
        meta: {
            requiresAuth: true
        },
        component: TrackingDashboard,
        beforeEnter: (to, from, next) => {
            const company = store.state.mainCompany;
            if (company && company.license && company.license) {
                next();
            } else {
                next(from);
            }
        }
    },
    {
        path: '/tracking/tracker',
        name: 'tracking_tracker',
        meta: {
            requiresAuth: true
        },
        component: TrackingTracker,
        beforeEnter: (to, from, next) => {
            const company = store.state.mainCompany;
            if (company && company.license && company.license) {
                next();
            } else {
                next(from);
            }
        }
    },
    {
        path: '/tracking/timesheet',
        name: 'tracking_timesheet',
        meta: {
            requiresAuth: true
        },
        component: TrackingTimesheet,
        beforeEnter: (to, from, next) => {
            const company = store.state.mainCompany;
            if (company && company.license && company.license) {
                next();
            } else {
                next(from);
            }
        }
    },
    {
        path: '/tracking/calendar',
        name: 'tracking_calendar',
        meta: {
            requiresAuth: true
        },
        component: TrackingCalendar,
        beforeEnter: (to, from, next) => {
            const company = store.state.mainCompany;
            if (company && company.license && company.license) {
                next();
            } else {
                next(from);
            }
        }
    },
    {
        path: '/tracking/projects',
        name: 'tracking_projects',
        meta: {
            requiresAuth: true
        },
        component: TrackingProjects,
        beforeEnter: (to, from, next) => {
            const company = store.state.mainCompany;
            if (company && company.license && company.license) {
                next();
            } else {
                next(from);
            }
        }
    },
    {
        path: '/tracking/projects/:id',
        name: 'tracking_project_item',
        meta: {
            requiresAuth: true
        },
        component: TrackingProjectItem,
        beforeEnter: (to, from, next) => {
            const company = store.state.mainCompany;
            if (company && company.license && company.license) {
                next();
            } else {
                next(from);
            }
        }
    },
    {
        path: '/tracking/reports',
        name: 'tracking_reports',
        meta: {
            requiresAuth: true
        },
        component: TrackingReports,
        beforeEnter: (to, from, next) => {
            const company = store.state.mainCompany;
            if (company && company.license && company.license) {
                next();
            } else {
                next(from);
            }
        }
    },
    {
        path: '/tracking/settings',
        name: 'tracking_settings',
        meta: {
            requiresAuth: true
        },
        component: TrackingSettings,
        beforeEnter: (to, from, next) => {
            const company = store.state.mainCompany;
            if (company && company.license && company.license) {
                next();
            } else {
                next(from);
            }
        }
    },
    {
        path: '/custom_license/:id',
        name: 'custom_license',
        meta: {
            requiresAuth: true,
        },
        component: CustomLicense
    },
    {
        path: '/custom_license_unassigned',
        name: 'custom_license_unassigned',
        meta: {
            requiresAuth: true,
        },
        component: CustomLicenseUnassignedUsers
    },
    {
        path: '/custom_license',
        name: 'custom_licenses',
        meta: {
            requiresAuth: true,
        },
        component: CustomLicenses
    },
    // {
    //     path: '/risk_repository',
    //     name: 'risk_repository',
    //     meta: {
    //         requiresAuth: true,
    //     },
    //     component: RiskRepository
    // },
    {
        path: '/risk_repository/:categoryId',
        name: 'risk_repository_category',
        meta: {
            requiresAuth: true,
        },
        component: RiskRepository
    },
    {
        path: '/risk_repository/:categoryId/:articleId',
        name: 'risk_repository_article',
        meta: {
            requiresAuth: true,
        },
        component: RiskRepositoryMore
    },
    {
        path: '/incident_reporting/create',
        name: 'incident_reporting_create',
        meta: {
            requiresAuth: true,
        },
        component: IncidentReportingCreate
    },
    {
        path: '/incident_reporting/scenarios',
        name: 'incident_reporting_scenarios',
        meta: {
            requiresAuth: true,
        },
        component: IncidentReportingActionBoards
    },
    {
        path: '/incident_reporting/list',
        name: 'incident_reporting_list',
        meta: {
            requiresAuth: true,
        },
        component: IncidentReportingActionBoards
    },
    {
        path: '/incident_reporting/list/:id',
        name: 'incident_reporting_list_item',
        meta: {
            requiresAuth: true,
        },
        component: IncidentReportingCreate
    },
    {
        path: '/incident_reporting/action_boards',
        name: 'incident_reporting_action_boards',
        meta: {
            requiresAuth: true,
        },
        component: IncidentReportingActionBoards
    },
    {
        path: '/:alias',
        name: 'knowledge_base',
        meta: {
            requiresAuth: true,
        },
        component: KnowledgeBase
    },
    {
        path: '/:alias/create',
        name: 'knowledge_base_create',
        meta: {
            requiresAuth: true,
        },
        component: KnowledgeBaseCreate
    },
    {
        path: '/admin/role',
        name: 'admin_role',
        meta: {
            requiresAuth: true,
        },
        component: RoleManagement
    },
    {
        path: '/admin/roles',
        name: 'admin_roles',
        meta: {
            requiresAuth: true,
        },
        component: RoleManagement
    },
    {
        path: '/:alias/:id/edit',
        name: 'knowledge_base_update',
        meta: {
            requiresAuth: true,
        },
        component: KnowledgeBaseCreate
    },
    {
        path: '/:alias/:id',
        name: 'knowledge_base_article',
        meta: {
            requiresAuth: true,
        },
        component: KnowledgeBaseArticle
    },
];
