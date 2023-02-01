export const usePrint = (elementId: string) => {
  const print = (element = elementId) => {
    const modalInvoice = document.getElementById(element)
    const cloned = modalInvoice?.cloneNode(true)
    let section = document.getElementById("print")
  
    if (!section) {
       section  = document.createElement("div")
       section.id = "print"
       document.body.appendChild(section)
    }
  
    section.innerHTML = "";
    section.appendChild(cloned);
    window.print();
  }

  return {
    customPrint: print
  }
}