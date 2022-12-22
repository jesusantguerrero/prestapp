export const PROPERTY_TYPE = {
    APARTMENT: 'apartment',
    COMMERCIAL: 'commercial',
    DUPLEX: 'duplex',
    HOUSE: 'house',
    MID_USE: 'mid-use',
    OTHER: 'other'
}

export const propertyTypes = [{
    name: PROPERTY_TYPE.APARTMENT,
    label: 'Apartamento'
}, {
    name: PROPERTY_TYPE.COMMERCIAL,
    label: 'Comercial'
},
{
    name: PROPERTY_TYPE.DUPLEX,
    label: 'Duplex'
}, 
{
    name: PROPERTY_TYPE.HOUSE,
    label: 'Casa'
},
{
    name: PROPERTY_TYPE.MID_USE,
    label: 'Compartida'
},
{
  name: PROPERTY_TYPE.OTHER,
  label: 'otro'
}
];

export const PROPERTY_STATUS = {
    AVAILABLE: {
      label : 'Disponible'
    },
    PARTIALLY_PAID: {
      label: 'Parcialmente pagado'
    },
    PAID: {
      label: 'Pagado'
    },
    PENDING: {
      label: 'Pendiente'
    }
}

export const STATUS = Object.keys(PROPERTY_STATUS).reduce((keys, key: string) => {
  keys[key] = PROPERTY_STATUS[key]?.label;
  return keys;
}, { })

export const getPropertyStatus = (status: string): string => {
    return STATUS[status] || status;
}

export  type stateTypes =  'info'| 'danger'|'warning'|'primary'
export const getPropertyStatusColor = (status: string): stateTypes => {
    return PROPERTY_STATUS[status] ? PROPERTY_STATUS[status].color as stateTypes : 'info' as stateTypes;
}
