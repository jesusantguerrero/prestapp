import { Link } from "@inertiajs/vue3"
import MaterialSymbolsDashboard from '~icons/material-symbols/dashboard'
import { MODULES, getSectionMenu } from './menus';

export default {
    menu: [
        {
            icon: MaterialSymbolsDashboard,
            name: 'home',
            label: 'Home',
            to: '/dashboard',
            as: Link
        },
        {
            icon: 'fas fa-money-check-alt',
            label: 'Invoices',
            name: 'invoices',
            to: '/dropshipping-invoices',
            as: Link,
            isActiveFunction(url: string, currentPath: string) {
               return /dropshipping-invoices/.test(currentPath)
            },
            items: getSectionMenu(MODULES.ORDERS),
        },
    ],
    headerMenu: [
        {
            icon: 'fas fa-question',
            label: 'Help & support',
            to: '/help',
            as: Link
        },
        {
            icon: 'fas fa-cogs',
            label: 'Configuration',
            name: 'settings',
            to: '/settings',
            as: Link
        },
    ]
}
