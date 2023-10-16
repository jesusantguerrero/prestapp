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
            label: 'Orders',
            name: 'orders',
            to: '/dropshipping-orders',
            as: Link,
            isActiveFunction(url: string, currentPath: string) {
               return /dropshipping-orders/.test(currentPath)
            },
            items: getSectionMenu(MODULES.ORDERS),
            hidden: true,
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
