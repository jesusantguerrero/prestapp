const invoiceLayouts = {
  store: {
    headerLogoPosition: "left",
    accentColor: "rgb(244 114 182)",
    secondaryColor: "rgb(147 51 234)",
    subheaderCards: ["contactInfo", "businessInfo"],
  },
  freelanceBlack: {
    headerLogoPosition: "right",
    accentColor: "rgb(51 65 85)",
    secondaryColor: "rgb(37 99 235)",
    subheaderCards: ["contactInfo", "totalDue"],
  },
  premium: {
    headerLogoPosition: "left",
    headerHideInvoiceDetails: true,
    accentColor: "rgb(59 130 246)",
    secondaryColor: "rgb(51 65 85)",
    subheaderCards: ["contactInfo", "invoiceDetails"],
    tableHeaderCellDivider: true,
    tableHeaderCellAlign: "center",
  },
  premiumLeft: {
    headerLogoPosition: "right",
    headerLogoSize: "sm",
    headerLogoOnly: true,
    headerHideInvoiceDetails: true,
    accentColor: "rgb(59 130 246)",
    secondaryColor: "rgb(51 65 85)",
    subheaderCards: ["invoiceDetails", "contactInfo"],
    tableHeaderCellDivider: true,
    tableHeaderCellAlign: "center",
  }
}

export const getInvoiceLayout = (layoutName: string) => {
  return invoiceLayouts[layoutName] ?? invoiceLayouts.store;
}
