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
            label: 'Carteras',
            url: '/wallets'
        }, {
            label: 'Prestamos',
            url: '/loans'
        },
        {
          label: 'Tipos Prestamos',
          url: '/loan-products'
      }
    ],
    [MODULES.PROPERTY]: [{
        label: 'Resumen',
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
    ],
    [MODULES.ACCOUNTING]: [{
        label: 'Banco',
        url: '/properties/overview'
    },
    {
        label: 'Transacciones',
        url: '/invoices'
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
