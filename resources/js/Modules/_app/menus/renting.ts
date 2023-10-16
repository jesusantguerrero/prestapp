import { cloneDeep } from 'lodash';
import { Link } from "@inertiajs/vue3"
import MaterialSymbolsDashboard from '~icons/material-symbols/dashboard'
import { MODULES, getSectionMenu } from './menus';

export default {
  name: "shein-store",
  label: "Store",
  html: "<span> IC</span><span>Store</span>",
  menu: [{
            icon: MaterialSymbolsDashboard,
            name: 'home',
            label: 'Home',
            to: '/dashboard',
            as: Link
        },
        {
            icon: 'fas fa-money-check-alt',
            label: 'Loans',
            name: 'loans',
            to: '/loans',
            as: Link,
            isActiveFunction(url: string, currentPath: string) {
               return /loans|lender/.test(currentPath)
            },
            items: getSectionMenu(MODULES.LOAN),
            hidden: true,
        },
        {
            icon: 'fas fa-building',
            name: 'properties',
            label:'Properties',
            to: '/units?filter[status]=RENTED',
            as: Link,
            isActiveFunction(url: string, currentPath: string) {
              return /properties|units|tenant|owner/.test(currentPath)
            },
            items: getSectionMenu(MODULES.PROPERTY),

        },
        {
            icon: 'fas fa-calculator',
            label: 'Accounting',
            to: '/invoices?filter[type]=expense|invoice',
            as: Link,
            isActiveFunction(url:string, currentPath: string) {
                return /invoices/.test(currentPath)
            },
            hidden: true,
        },
        {
          icon: 'fas fa-chart-bar',
          label:'Invoice',
          to: '/statements',
          as: Link,
          hidden: true,
          isActiveFunction(url: string, currentPath: string) {
            return /invoice/.test(currentPath)
          },
          items: getSectionMenu(MODULES.INVOICING),
        },
        {
          icon: 'fas fa-users',
          label:'Agent Tools',
          to: '/agent-tools',
          as: Link,
          isActiveFunction(url: string, currentPath: string) {
            return /agents/.test(currentPath)
          },
          items: getSectionMenu(MODULES.AGENT),
        },
        {
          icon: 'fas fa-chart-bar',
          label:'Reports',
          to: '/statements',
          as: Link,
          isActiveFunction(url: string, currentPath: string) {
            return /statements/.test(currentPath)
          },
          items: getSectionMenu(MODULES.REPORT),
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
