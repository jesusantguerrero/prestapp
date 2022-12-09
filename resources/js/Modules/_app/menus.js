export const MODULES = {
    CRM: 'CRM',
    LOAN: 'loan',
    PROPERTY: 'property',
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
            label: 'Planner',
            url: '/meal-planner'
        }, {
            label: 'Recipes',
            url: '/meals'
        },
        {
            label: 'Ingredients',
            url: '/ingredients'
        }, {
            label: 'Menus',
            url: '/meals/menus',
            hidden: true
    }],
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
        label: 'Pagos',
        url: '/payments'
    },
     {
        label: 'Trends',
        url: '/trends'
    }]
}


export const getSectionMenu = (sectionName) => {
    return menus[sectionName].filter(item => !item.hidden)
}
