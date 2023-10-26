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
            label: 'Academic',
            name: 'academic',
            to: '/academic',
            as: Link,
            isActiveFunction(url: string, currentPath: string) {
               return /academic/.test(currentPath)
            },
            items: getSectionMenu(MODULES.ACADEMIC),
        },
        {
          icon: MaterialSymbolsDashboard,
          name: 'students',
          label: 'Students',
          isActiveFunction(currentPath: string) {
            return /contacts\/\d+\/students/.test(currentPath)
          },
          to: '/contacts/students',
          as: Link
        },
        {
          icon: MaterialSymbolsDashboard,
          name: 'parents',
          label: "Parents",
          to: '/contacts/parents',
          as: Link,
          isActiveFunction(currentPath: string) {
            return /contacts\/\d+\/parents/.test(currentPath)
          },
        },
        {
          icon: MaterialSymbolsDashboard,
          name: 'teachers',
          label: "Teachers",
          to: '/contacts/teachers',
          as: Link,
          isActiveFunction(currentPath: string) {
            return /contacts\/\d+\/teachers/.test(currentPath)
          },
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
