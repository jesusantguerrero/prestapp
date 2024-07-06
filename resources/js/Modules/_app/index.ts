import { cloneDeep } from 'lodash';
import { Link } from "@inertiajs/vue3"
import IMdiPlus from '~icons/mdi/plus-thick';
import { MaybeRef } from "@vueuse/core";
import sheinStore from "./menus/sheinStore";
import renting from "./menus/renting";
import admin from "./menus/admin";

export const modules = {
    renting,
    "store": sheinStore,
    admin
};

export * from "./menus/menus";

interface IAppMenuItem {
  icon?: string | Object;
  name: string;
  label: string;
  to?: string;
  as: string | Object;
  items?: IAppMenuItem[];
  action?: string;
  hideMobile?: boolean;
  hidden?: boolean;
  roles?: string;
  isActiveFunction?: (url: string, currentPath: string) => boolean
}
export const useAppMenu = (isTeamApproved: MaybeRef<boolean>, t: Function, moduleName: string, role: string) => {
    const module = modules[moduleName];
    const appMenu: IAppMenuItem[] = module.menu.reduce((visibleItems: IAppMenuItem[], item: IAppMenuItem ) => {
      if ((!item.roles || item.roles.includes(role)) && !item?.hidden) {

        visibleItems.push({
          ...item,
          label: t(item.label),
          ...(item.items ? {
            items: item.items.map((subItem) => ({
              ...subItem,
              label: t(subItem.label),
            }))
          } : {})
        })
      }
      return visibleItems;
    }, []);

    let mobileMenu = cloneDeep(appMenu).filter( item => !item.hideMobile);
    mobileMenu.splice(2, 0, {
        name: 'add',
        label: t('Add'),
        icon: IMdiPlus,
        action: 'openAddModal',
        as: 'button'
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
