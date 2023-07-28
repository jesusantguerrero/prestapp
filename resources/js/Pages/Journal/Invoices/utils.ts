export const getInvoiceTypeUrl = (invoice) => {
  const section = invoice.type == 'EXPENSE' ? 'bills' : 'invoices'
  return `/${section}/${invoice.id}/edit`;
}


export const hexToRgb = (hex: string) =>
hex.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i, (m, r, g, b) => '#' + r + r + g + g + b + b)
  ?.substring(1)?.match(/.{2}/g)
  .map(x => parseInt(x, 16)).join(' ')
