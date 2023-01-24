class Accessory {
  element: HTMLElement;
  selected: boolean;
  name: string;
  price: number;

  constructor({ element }: { element: HTMLElement }) {
    this.element = element;
    this.selected = element.getAttribute("data-selected") === "true";
    this.name = element.getAttribute("data-product-name") || "";
    this.price = parseFloat(element.getAttribute("data-product-price")!) || 0;
  }

  toggleSelected() {
    this.element.classList.toggle("selected");
    if (this.selected) {
      this.element.setAttribute("data-selected", "false");
    } else {
      this.element.setAttribute("data-selected", "true");
    }
  }
}

export default Accessory;
