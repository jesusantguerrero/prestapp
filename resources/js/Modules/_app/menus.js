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
        label: 'Alquileres',
        url: '/rents'
    },
    {
      label: 'Plantillas',
      url: '/templates'
    },
    {
        label: 'Pagos',
        url: '/invoices/'
    },
     {
        label: 'Trends',
        url: '/trends'
    }],
    [MODULES.ACCOUNTING]: [{
        label: 'Banco',
        url: '/properties/overview'
    },
    {
        label: 'Ingresos',
        url: '/invoices'
    },
    {
        label: 'Egresos',
        url: '/expenses'
    },
    {
      label: 'Pagos',
      url: '/payments'
    },
    {
      label: 'Cuentas',
      url: '/accounts'
    },
    {
      label: 'Trends',
      url: '/trends'
    }]
}


export const getSectionMenu = (sectionName) => {
    return menus[sectionName].filter(item => !item.hidden)
}
