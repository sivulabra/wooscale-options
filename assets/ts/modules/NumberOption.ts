import { Option } from "../types";

class NumberOption implements Option {
  element: HTMLInputElement;
  label: string;
  value: string;
  pricePerUnit: number;
  quantity: number;
  unit: string;

  constructor({ element }: { element: HTMLInputElement }) {
    this.element = element;
    this.label = element.getAttribute("data-label") || "";
    this.value = element.value;
    const pricePerUnitString = element?.getAttribute("data-price-per-unit");
    this.pricePerUnit = pricePerUnitString ? parseFloat(pricePerUnitString) : 0;
    this.quantity = parseInt(this.value);
    this.unit = "kpl";
  }

  getLabel(): string {
    return `${this.label} - ${this.value} ${this.unit}`;
  }

  getPrice(): number {
    return this.quantity * this.pricePerUnit;
  }

  isSelected(): boolean {
    return this.value !== "";
  }
}

export default NumberOption;
