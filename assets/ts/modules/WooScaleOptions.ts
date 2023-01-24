import $ from "jquery";
import Product from "./Product";
import Accessory from "./Accessory";
import SelectOption from "./SelectOption";
import { Option } from "../types";
import NumberOption from "./NumberOption";

class WooOptions {
  cssPrefix: string;
  container: HTMLElement | null;
  optionsContainer: HTMLElement | null;
  accessoriesContainer: HTMLElement | null;
  totalsContainer: HTMLElement | null;
  cartForm: HTMLElement | null;
  addToCartBtn: HTMLElement | null;
  product: Product;
  quantity: number | null;
  accessories: Accessory[];
  options: Option[];

  constructor() {
    this.cssPrefix = "wso";

    /**
     * Main plugin container.
     */
    const container = document.getElementById(`${this.cssPrefix}-container`);
    if (!container) console.error(`Missing plugin container`);
    this.container = container;

    /**
     * Options container.
     */
    const optionsContainer = document.getElementById(
      `${this.cssPrefix}-options`
    );
    this.optionsContainer = optionsContainer;

    /**
     * Accessories container.
     */
    const accessoriesContainer = document.getElementById(
      `${this.cssPrefix}-accessories`
    );
    this.accessoriesContainer = accessoriesContainer;

    /**
     * Totals container.
     */
    const totalsContainer = document.getElementById(`${this.cssPrefix}-totals`);
    if (!totalsContainer) console.error(`Missing totals container`);
    this.totalsContainer = totalsContainer;

    /**
     * Cart form.
     */
    const cartForm = document.querySelector("form.cart") as HTMLElement | null;
    if (!cartForm) console.error(`Missing cart form`);
    this.cartForm = cartForm;

    /**
     * Add to cart button.
     */
    const addToCartBtn = document.querySelector(
      "[name=add-to-cart]"
    ) as HTMLButtonElement;
    if (!addToCartBtn) console.error(`Missing add to cart button`);
    this.addToCartBtn = addToCartBtn;

    /**
     * Instantiate the product object.
     */
    const productIdString =
      this.totalsContainer?.getAttribute("data-product-id");
    const productId: number = productIdString ? parseInt(productIdString) : 0;
    const productName =
      this.totalsContainer?.getAttribute("data-product-name") || "";
    const productPriceString = this.totalsContainer?.getAttribute("data-price");
    const productPrice: number = productPriceString
      ? parseFloat(productPriceString)
      : 0;
    this.product = new Product({
      id: productId,
      name: productName,
      price: productPrice,
    });

    /**
     * Instantiate quantity.
     */
    this.quantity = this.getQuantity();

    /**
     * Instantiate the accessories and options.
     */
    this.accessories = this.getAccessories();
    this.options = this.getOptions();

    this.events();
  }

  events() {
    this.cartForm?.addEventListener("change", this.handleChange.bind(this));
    if (this.accessories.length > 0) {
      this.accessories.forEach((accessory) => {
        accessory.element.addEventListener(
          "click",
          this.handleAccessoryClick.bind(this, accessory)
        );
      });
    }
    $("input.qty").on("change", this.handleChange.bind(this));
  }

  handleChange() {
    if (!this.totalsContainer) return;
    this.totalsContainer.innerHTML = ``;

    /**
     * Update options and accessories.
     */
    this.options = this.getOptions();

    /**
     * Set totals container innerHTML to empty string if any of the options hasn't been chosen.
     */
    const emptyOption = this.options.find((option) => !option.isSelected());
    if (emptyOption) return;

    /**
     * Update quantity.
     */
    this.quantity = this.getQuantity();
    if (!this.quantity) return;

    /**
     * Start creating the totals list.
     */
    const totalsHTML = document.createElement("ul");
    totalsHTML.classList.add(`${this.cssPrefix}-totals-list`);

    /**
     * Product row.
     */
    const productRow = document.createElement("li");
    productRow.classList.add(`${this.cssPrefix}-totals-row`);
    const productRowLabel = document.createElement("span");
    productRowLabel.classList.add(`${this.cssPrefix}-totals-row-label`);
    productRowLabel.innerText = `${this.quantity} x ${this.product.name}`;
    const productRowPrice = document.createElement("span");
    productRowPrice.classList.add(`${this.cssPrefix}-totals-row-price`);
    productRowPrice.innerText = `${this.quantity * this.product.price} €`;
    productRow.append(productRowLabel);
    productRow.append(productRowPrice);

    /**
     * Option rows.
     */
    const optionsList = document.createElement("ul");
    optionsList.classList.add(`${this.cssPrefix}-totals-row-options`);
    this.options.forEach((option) => {
      const listItemElement = document.createElement("li");
      listItemElement.innerText = `${option.getLabel()} (+${option.getPrice()} €)`;
      optionsList.append(listItemElement);
    });
    productRow.append(optionsList);

    /**
     * Total price div.
     */
    const totalPrice = document.createElement("div");
    totalPrice.classList.add(`${this.cssPrefix}-totals-total`);
    totalPrice.innerText = "Yhteensä ";
    const totalPriceNumberSpan = document.createElement("span");
    totalPriceNumberSpan.classList.add(`${this.cssPrefix}-totals-total-price`);
    const totalPriceNumber =
      this.product.price +
      this.options.reduce((acc, obj) => {
        console.log(acc);
        return acc + obj.getPrice();
      }, 0);
    totalPriceNumberSpan.innerText = `${totalPriceNumber} € sis. ALV`;
    totalPrice.append(totalPriceNumberSpan);

    /**
     * Append the elements to the totals container.
     */
    totalsHTML.append(productRow);
    totalsHTML.append(totalPrice);
    this.totalsContainer.append(totalsHTML);

    /*
    this.totalsContainer.innerHTML = `
    <ul class="${this.cssPrefix}-totals-list">
      <li class="${this.cssPrefix}-totals-row">
        <span class="${this.cssPrefix}-totals-label">${this.quantity} x ${
      this.product.name
    }</span>
        <span class="${this.cssPrefix}-totals-price">${
      this.quantity * this.product.price
    } €</span>
      </li>
    </ul>
    <div class="${this.cssPrefix}-totals-total">Yhteensä <span class="${
      this.cssPrefix
    }-totals-total-price">0 €</span> (sis. ALV 24%)</div>
    `;
    */
  }

  handleAccessoryClick(accessory: Accessory, evt: MouseEvent) {
    evt.preventDefault();
    console.log("handleAccessoryClick");
    this.handleChange();
  }

  getAccessoryObject(accessoryElement: HTMLElement) {
    return new Accessory({ element: accessoryElement });
  }

  getAccessories(): Accessory[] {
    if (!this.accessoriesContainer) return [];
    const accessories = [];
    for (let i = 0; i < this.accessoriesContainer.children.length; i++) {
      const element = this.accessoriesContainer.children[i] as HTMLElement;
      accessories.push(
        new Accessory({
          element,
        })
      );
    }
    return accessories;
  }

  getQuantity(): number | null {
    const quantityElement = document.querySelector(
      "input.qty"
    ) as HTMLInputElement;
    if (!quantityElement) return null;
    const quantityString = quantityElement.value;
    return quantityString ? parseInt(quantityString) : 1;
  }

  getOptions(): Option[] {
    if (!this.optionsContainer) return [];
    const options: Option[] = [];
    const optionElements = this.optionsContainer.querySelectorAll(
      `[id*="${this.cssPrefix}-option"]`
    );
    for (let i = 0; i < optionElements.length; i++) {
      let element;
      const type = optionElements[i].getAttribute("data-type");
      if (type === "select") {
        element = optionElements[i] as HTMLSelectElement;
        options.push(
          new SelectOption({
            element,
          })
        );
      } else if (type === "number") {
        element = optionElements[i] as HTMLInputElement;
        options.push(
          new NumberOption({
            element,
          })
        );
      }
    }
    return options;
  }

  getSelectedOptions(): Option[] {
    if (!this.options) return [];
    return this.options.filter((option) => option.isSelected());
  }
}

export default WooOptions;
