import { cloneDeep } from 'lodash';
import { Link } from "@inertiajs/vue3"
import IMdiPlus from '~icons/mdi/plus-thick';
import MaterialSymbolsDashboard from '~icons/material-symbols/dashboard'
import { MaybeRef } from "@vueuse/core";
import { MODULES, getSectionMenu } from './menus';

export * from "./menus";
interface IAppMenuItem {
  icon?: string | Object;
  name: string;
  label: string;
  to: string;
  as: string | Object;
  hidden?: boolean;
  isActiveFunction?: (url: string, currentPath: string) => boolean
}
export const useAppMenu = (isTeamApproved: MaybeRef<boolean>, t: Function) => {
    const appMenu: IAppMenuItem[] =  [
        {
            icon: MaterialSymbolsDashboard,
            name: 'home',
            label: t('Home'),
            to: '/dashboard',
            as: Link
        },
        {
            icon: 'fas fa-money-check-alt',
            label: t('Loans'),
            name: 'loans',
            to: '/loans',
            as: Link,
            isActiveFunction(url: string, currentPath: string) {
               return /loans|lender/.test(currentPath)
            },
            items: getSectionMenu(MODULES.LOAN),
            hidden: !isTeamApproved.value,
        },
        {
            icon: 'fas fa-building',
            name: 'properties',
            label:t('Properties'),
            to: '/units?filter[status]=RENTED',
            as: Link,
            isActiveFunction(url: string, currentPath: string) {
              return /properties|units|tenant|owner/.test(currentPath)
            },
            items: getSectionMenu(MODULES.PROPERTY),
            hidden: !isTeamApproved.value,

        },
        {
            icon: 'fas fa-calculator',
            label: t('Accounting'),
            to: '/invoices?filter[type]=expense|invoice',
            as: Link,
            isActiveFunction(url:string, currentPath: string) {
                return /invoices/.test(currentPath)
            },
            hidden: true,
        },
        {
          icon: 'fas fa-chart-bar',
          label:t('Invoice'),
          to: '/statements',
          as: Link,
          hidden: true,
          isActiveFunction(url: string, currentPath: string) {
            return /invoice/.test(currentPath)
          },
          items: getSectionMenu(MODULES.INVOICING),
        },
        {
          icon: 'fas fa-chart-bar',
          label:t('Agent Tools'),
          to: '/agent-tools',
          as: Link,
          hideMobile: true,
          isActiveFunction(url: string, currentPath: string) {
            return /agents/.test(currentPath)
          },
          items: getSectionMenu(MODULES.AGENT),
        },
        {
          icon: 'fas fa-chart-bar',
          label:t('Reports'),
          to: '/statements',
          as: Link,
          isActiveFunction(url: string, currentPath: string) {
            return /statements/.test(currentPath)
          },
          items: getSectionMenu(MODULES.REPORT),
        },
    ].filter(item => !item?.hidden);

    let mobileMenu = cloneDeep(appMenu).filter( item => !item.hideMobile);
    mobileMenu.splice(2, null, {
        name: 'add',
        label: 'Add',
        icon: IMdiPlus,
        action: 'openTransactionModal'
    });

    const headerMenu =  [
        {
            icon: 'fas fa-question',
            label: t('Help & support'),
            to: '/help',
            as: Link
        },
        {
            icon: 'fas fa-cogs',
            label: t('Configuration'),
            name: 'settings',
            to: '/settings',
            as: Link
        },
    ];

    return {
        appMenu,
        headerMenu,
        mobileMenu
    }
}

export const DEFAULT_TIMEZONE = "UTC";

export const defaultDateFormats = ['dd MMM, yyyy', 'dd.MM.yyyy', 'MM/dd/yyyy', 'yyyy.MM.dd']

export const mapTeamFormServer = (team: Record<string, any>, prefix="team_") => {
    const regPrefix = new RegExp(prefix);
    return team.settings.reduce((acc: Record<string, any>, setting: Record<string, any>) => {
        const fieldName = setting.name.replace(regPrefix, '')
        acc[fieldName] = setting.value;
        return acc;
    }, {
        name: team.name,
    })
}

export const parseTeamForm = (team: Record<string, any>, prefix="team_") => {
    return Object.keys(team).reduce((acc: Record<string, any>, fieldName) => {
        acc[prefix+fieldName] = team[fieldName];
        return acc;
    }, {
        name: team.name,
    })
}
