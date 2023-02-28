export const MODULES = {
    CRM: 'CRM',
    LOAN: 'loan',
    PROPERTY: 'property',
    ACCOUNTING: 'accounting'
}

const menus = {
    [MODULES.CRM]: [{
        label: 'Overview',
        url: '/housing'
    },
    {
        label: 'Chores',
        url: '/housing/chores'
    },
    {
        label: 'Occurrence Checks',
        url: '/housing/occurrence'
    },
    {
        label: 'Plans',
        url: '/housing/plans'
    },
    {
        label: 'Equipment',
        url: '/housing/equipments'
    }],
    [MODULES.LOAN]: [
      {
        label: 'Prestamos',
        url: '/loans'
      },
      {
        label: 'Clientes',
        url: '/contacts/lender',
        isActiveFunction(currentPath: string) {
          return /contacts/.test(currentPath)
        },
      },
      {
        label: 'Centro de pago',
        url: '/payment-center'
      },
      {
        label: 'Carteras',
        url: '/wallets',
        hidden: true
      },
      {
        label: 'Tipos Prestamos',
        url: '/loan-products',
        hidden: true
      }
    ],
    [MODULES.PROPERTY]: [
    {
        label: 'Propiedades',
        url: '/units/'
    },
    {
        label: 'Contratos',
        url: '/rents/',
    },
    {
        label: 'Inquilinos',
        url: '/contacts/tenant?filter[status]=active&relationships=rent,rent.property',
        isActiveFunction(currentPath: string) {
          return /tenant/.test(currentPath)
        },
    },
    {
      label: 'Dueños',
      url: '/contacts/owner'
    },
    {
      label: 'Herramientas de agente',
      url: '/properties/management-tools/'
    },
    ],
    [MODULES.ACCOUNTING]: [{
        label: 'Banco',
        url: '/properties/overview'
    },
    {
        label: 'Transacciones',
        url: '/invoices?filter[type]=expense|invoice',
    },
    {
      label: 'Cuentas',
      url: '/accounts'
    }
    ]
}


export const getSectionMenu = (sectionName) => {
    return menus[sectionName].filter(item => !item.hidden)
}
