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
