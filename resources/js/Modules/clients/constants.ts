export const DOCUMENT_TYPES = {
    DNI: 'DNI',
    PASSPORT: 'PASSPORT',
};

export const documentTypes = [{
    name: 'DNI',
    label: 'Cedula'
}, {
    name: 'PASSPORT',
    label: 'Pasaporte'
}];


export const CLIENT_STATUS = {
  ACTIVE: {
    label : 'Activo'
  },
  INACTIVE: {
    label : 'Inactivo',
    color: 'success'
  }
}

export const clientStatus = Object.entries(CLIENT_STATUS).map(([name, value]) => ({
  name,
  label: value.label
}));


export const getClientLink = (client: Record<string, any>, forcedType = 'lender') => {
  const type = forcedType ?? (client && Object.entries(client).reduce((type, [field, value]) => {
    if (field.match(/owner|tenant|lender/) && value == 1) {
      type = field.replace('is_', '');
    }
    return type;
  }, ""));

  return `/contacts/${client?.id}/${type}`
}
