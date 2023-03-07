export const usePrint = (elementId?: string) => {
  const print = (element = elementId, {
    beforePrint,
    delay
  } = {
    beforePrint: () => {},
    delay: 0
  }) => {
    const modalInvoice = document.getElementById(element)
    const cloned = modalInvoice?.cloneNode(true)
    let section = document.getElementById("print")

    if (!section) {
       section  = document.createElement("div")
       section.id = "print"
       document.body.appendChild(section)
    }

    section.innerHTML = "";
    if (cloned) {
      section.appendChild(cloned);
      beforePrint()
      setTimeout(() => {
        window.print();
      }, delay)
    }
  }

  return {
    customPrint: print
  }
}
