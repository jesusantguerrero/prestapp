import { Link } from "@inertiajs/vue3"
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
export const useAppMenu = () => {
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
            }
        },
        {
            icon: 'fas fa-building',
            label:'Propiedades',
            to: '/units?filter[status]=RENTED',
            as: Link,
            isActiveFunction(url: string, currentPath: string) {
              return /properties|units|tenant|owner/.test(currentPath)
            }

        },
        {
            icon: 'fas fa-calculator',
            label: 'Contabilidad',
            to: '/invoices?filter[type]=expense|invoice',
            as: Link,
            isActiveFunction(url:string, currentPath: string) {
                return /invoices/.test(currentPath)
             }
        },
        {
          icon: 'fas fa-chart-bar',
          label:'Reportes',
          to: '/statements',
          as: Link,
          isActiveFunction(url: string, currentPath: string) {
            return /statements/.test(currentPath)
         }
      },
    ].filter(item => !item?.hidden);

    const headerMenu =  [
        {
            icon: 'fas fa-question',
            label: 'Ayuda y Soporte',
            to: '/settings/help',
            as: Link
        },
        {
            icon: 'fas fa-cogs',
            label: 'Settings',
            name: 'settings',
            to: '/settings',
            as: Link
        },
    ];

    return {
        appMenu,
        headerMenu
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
