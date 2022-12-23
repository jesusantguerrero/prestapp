import { Link } from "@inertiajs/vue3"
export * from "./menus";

export const useAppMenu = t => {
    const appMenu =  [
        {
            icon: 'fa fa-home',
            name: 'home',
            label: 'Home',
            to: '/dashboard',
            as: Link
        },
        {
            icon: 'fas fa-users',
            label: 'Contactos',
            name: 'mealPlanner',
            to: '/clients',
            as: Link,
            isActiveFunction(url, currentPath) {
                return /clients/.test(currentPath)
            }
        },
        {
            icon: 'fas fa-dollar-sign',
            label: 'Prestamos',
            name: 'prestamos',
            to: '/loans',
            as: Link,
            isActiveFunction(url, currentPath) {
               return /loans/.test(currentPath)
            }
        },
        {
            icon: 'fas fa-heart',
            label:'Propiedades',
            to: '/properties/overview',
            as: Link,
            isActiveFunction(url, currentPath) {
              return /properties/.test(currentPath)
            }

        },
        {
            icon: 'fas fa-dollar-sign',
            label: 'Contabilidad',
            to: '/invoices?filter[type]=expense|invoice',
            as: Link,
            isActiveFunction(url, currentPath) {
                return /invoices/.test(currentPath)
             }
        },
        // {
        //   icon: 'fas fa-heart',
        //   label:'Documentos',
        //   to: '/rents',
        //   as: Link,
        //   isActiveFunction(url, currentPath) {
        //     return /rents/.test(currentPath)
        //  }
        // },
        // {
        //   icon: 'fas fa-heart',
        //   label:'Acuerdos de Pago',
        //   to: '/rents',
        //   as: Link,
        //   isActiveFunction(url, currentPath) {
        //     return /rents/.test(currentPath)
        //  }
        // },
        {
          icon: 'fas fa-heart',
          label:'Reportes',
          to: '/statements',
          as: Link,
          isActiveFunction(url, currentPath) {
            return /statements/.test(currentPath)
         }
      },
    ].filter(item => !item.hidden);

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

export const mapTeamFormServer = (team, prefix="team_") => {
    const regPrefix = new RegExp(prefix);
    return team.settings.reduce((acc, setting) => {
        const fieldName = setting.name.replace(regPrefix, '')
        acc[fieldName] = setting.value;
        return acc;
    }, {
        name: team.name,
    })
}

export const parseTeamForm = (team, prefix="team_") => {
    return Object.keys(team).reduce((acc, fieldName) => {
        acc[prefix+fieldName] = team[fieldName];
        return acc;
    }, {
        name: team.name,
    })
}
