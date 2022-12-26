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
        label: 'Resumen de Prestamos',
        url: '/loans/overview'
      },
      {
        label: 'Prestamos',
        url: '/loans'
      },
      {
        label: 'Clientes',
        url: '/clients'
      },
      {
        label: 'Carteras',
        url: '/wallets'
      }, 
      {
        label: 'Tipos Prestamos',
        url: '/loan-products'
      }
    ],
    [MODULES.PROPERTY]: [{
        label: 'Resumen de Propiedades',
        url: '/properties/overview'
    },
    {
        label: 'Propiedades',
        url: '/properties/'
    },
    {
        label: 'Contratos',
        url: '/rents/'
    },
    {
        label: 'Clientes',
        url: '/clients/'
    },
    {
      label: 'Herramientas de agente',
      url: '/management-tools/'
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
