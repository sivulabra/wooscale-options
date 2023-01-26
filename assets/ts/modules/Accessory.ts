class Accessory {
  element: HTMLElement;
  selected: boolean;
  name: string;
  price: number;
  productId: number;

  constructor({ element }: { element: HTMLElement }) {
    this.element = element;
    this.selected = element.getAttribute("data-selected") === "true";
    this.name = element.getAttribute("data-product-name") || "";
    this.price = parseFloat(element.getAttribute("data-product-price")!) || 0;
    this.productId = parseInt(element.getAttribute("data-product-id")!) || 0;
  }

  toggleSelected() {
    this.element.classList.toggle("selected");
    if (this.selected) {
      this.selected = false;
      this.element.setAttribute("data-selected", "false");
    } else {
      this.selected = true;
      this.element.setAttribute("data-selected", "true");
    }
  }
}

export default Accessory;
