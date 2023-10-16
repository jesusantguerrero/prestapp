import { Link } from '@inertiajs/vue3';
import MaterialSymbolsHomeWorkOutline from '~icons/material-symbols/home-work-outline'
import ClarityContractLine from '~icons/clarity/contract-line'
import StreamlineInterfaceSecurityShieldPerson from '~icons/streamline/interface-security-shield-personshield-secure-security-person'
import FluentPeopleCommunity16Regular from '~icons/fluent/people-community-16-regular'
import IcOutlineRealEstateAgent from './IconComponent.vue';
import ClarityBankLine from '~icons/clarity/bank-line'

export const MODULES = {
    CRM: 'CRM',
    ORDERS: 'orders',
    LOAN: 'loan',
    INVOICING: 'invoicing',
    PROPERTY: 'property',
    AGENT: 'agent',
    ACCOUNTING: 'accounting',
    ADMIN: 'admin',
    REPORT: 'report'
}


const menus = {
    [MODULES.ORDERS]: [{
        label: 'Create order',
        to: '/dropshipping/invoices',
        as: Link,
    }],
    [MODULES.LOAN]: [
      {
        label: 'Prestamos',
        to: '/loans',
        as: Link,
        icon: ClarityBankLine,
      },
      {
        label: 'Clientes',
        to: '/contacts/lender',
        isActiveFunction(currentPath: string) {
          return /contacts/.test(currentPath)
        },
        as: Link,
        icon: FluentPeopleCommunity16Regular,

      },
      {
        label: 'Centro de pago',
        to: '/payment-center',
        as: Link,
        hidden: true
      },
      {
        label: 'Carteras',
        to: '/wallets',
        hidden: true
      },
      {
        label: 'Tipos Prestamos',
        to: '/loan-products',
        hidden: true
      }
    ],
    [MODULES.PROPERTY]: [
    {
        label: 'Properties',
        to: '/units/',
        isActiveFunction(currentPath: string) {
          return /properties|units/.test(currentPath)
        },
        icon: MaterialSymbolsHomeWorkOutline,
        as: Link,
    },
    {
        label: 'Rents',
        to: '/rents/',
        as: Link,
        icon:  ClarityContractLine
    },
    {
        label: 'Renewals',
        to: '/rent-renewals/',
        as: Link,
        icon:  ClarityContractLine
    },
    {
        label: 'Tenants',
        to: '/contacts/tenant',
        isActiveFunction(currentPath: string) {
          return /contacts\/\d+\/tenant/.test(currentPath)
        },
        as: Link,
        icon: FluentPeopleCommunity16Regular,
    },
    {
      label: 'Owners',
      to: '/contacts/owner',
      as: Link,
      isActiveFunction(currentPath: string) {
        return /contacts\/\d+\/owner/.test(currentPath)
      },
      icon: StreamlineInterfaceSecurityShieldPerson
    }],
    [MODULES.AGENT]: [
    {
        label: 'Distribuciones',
        to: '/agents/owner-draws',
        isActiveFunction(currentPath: string) {
          return /owner-draws/.test(currentPath)
        },
        icon: IcOutlineRealEstateAgent,
        as: Link,
    }, {
        label: 'Comisiones',
        to: '/agents/commissions',
        isActiveFunction(currentPath: string) {
          return /agents\/commissions/.test(currentPath)
        },
        icon: MaterialSymbolsHomeWorkOutline,
        as: Link,
    }],
    [MODULES.REPORT]: [
      {
        label: 'Ocupación',
        to: '/rent-reports/occupancy',
        as: Link,
        icon: IcOutlineRealEstateAgent,
      },
      {
        label: 'Renta mensual',
        to: '/rent-reports/monthly-summary',
        as: Link,
        icon: IcOutlineRealEstateAgent,
      }
    ],
    [MODULES.INVOICING]: [
      // {
      //     label: 'Estimates',
      //     to: '/properties/overview',
      //     as: Link,
      // },
      {
          label: 'Invoices',
          to: '/invoices',
          as: Link,
      },
      {
          label: 'Payments',
          to: '/payments',
          as: Link,
      },
      {
        label: 'Expenses',
        to: '/bills',
        as: Link,
      }
    ],
    [MODULES.ACCOUNTING]: [
      // {
      //   label: 'Banco',
      //   to: '/properties/overview'
      // },
      {
          label: 'Transacciones',
          to: '/invoices?filter[type]=expense|invoice',
      },
      // {
      //   label: 'Cuentas',
      //   to: '/accounts'
      // }
    ],
    [MODULES.ADMIN]: [
      {
        label: 'Dashboard',
        to: '/admin',
        isActiveFunction(currentPath: string) {
          return '/admin' == currentPath;
        },
      },
      {
        label: 'Teams',
        to: '/admin/teams',
      },
      {
        label: 'Subscriptions',
        to: '/admin/subscriptions',
      },
      {
        label: 'Users',
        to: '/admin/users',
      },
      {
        label: 'Commands',
        to: '/admin/commands',
      },
      {
        label: 'Backups',
        to: '/admin/backups',
      }
    ]
}

export const getSectionMenu = (sectionName: string, t: Function = (text: string) => text) => {
    return menus[sectionName].filter(item => !item.hidden)
    .map(item => ({
      ...item,
      label: t(item.label)
    }))
}
