import { Ref } from 'vue';
import { cloneDeep } from 'lodash';
import { Link } from "@inertiajs/vue3"
import IMdiPlus from '~icons/mdi/plus-thick';

export * from "./menus";
interface IAppMenuItem {
  icon: string;
  name: string;
  label: string;
  to: string;
  as: string | Object;
  hidden?: boolean;
  isActiveFunction?: (url: string, currentPath: string) => boolean
}
export const useAppMenu = (isTeamApproved: Ref<boolean>) => {
    const appMenu: IAppMenuItem[] =  [
        {
            icon: 'fa fa-home',
            name: 'home',
            label: 'Inicio',
            to: '/dashboard',
            as: Link
        },
        {
            icon: 'fas fa-money-check-alt',
            label: 'Prestamos',
            name: 'prestamos',
            to: '/loans',
            as: Link,
            isActiveFunction(url: string, currentPath: string) {
               return /loans|lender/.test(currentPath)
            },
            hidden: !isTeamApproved.value,
        },
        {
            icon: 'fas fa-building',
            label:'Propiedades',
            to: '/units?filter[status]=RENTED',
            as: Link,
            isActiveFunction(url: string, currentPath: string) {
              return /properties|units|tenant|owner/.test(currentPath)
            },
            hidden: !isTeamApproved.value,

        },
        {
            icon: 'fas fa-calculator',
            label: 'Contabilidad',
            to: '/invoices?filter[type]=expense|invoice',
            as: Link,
            isActiveFunction(url:string, currentPath: string) {
                return /invoices/.test(currentPath)
            },
            hidden: true,
        },
        {
          icon: 'fas fa-chart-bar',
          label:'Reportes',
          to: '/statements',
          as: Link,
          isActiveFunction(url: string, currentPath: string) {
            return /statements/.test(currentPath)
         },
         hidden: true,
      },
    ].filter(item => !item?.hidden);

    let mobileMenu = cloneDeep(appMenu)
    mobileMenu.splice(2, null, {
        name: 'add',
        label: 'Add',
        icon: IMdiPlus,
        action: 'openTransactionModal'
    });

    const headerMenu =  [
        {
            icon: 'fas fa-question',
            label: 'Ayuda y Soporte',
            to: '/help',
            as: Link
        },
        {
            icon: 'fas fa-cogs',
            label: 'Configuración',
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
