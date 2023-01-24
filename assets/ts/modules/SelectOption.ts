import { Option, SelectOptionItem } from "../types";

class SelectOption implements Option {
  element: HTMLElement;
  label: string;
  items: SelectOptionItem[];
  selectedItem: SelectOptionItem | null;

  constructor({ element }: { element: HTMLSelectElement }) {
    this.element = element;
    this.label = element.getAttribute("data-label") || "";
    this.items = this.getItems();
    const selectedIndex = element.selectedIndex;
    this.selectedItem = selectedIndex !== 0 ? this.items[selectedIndex] : null;
  }

  getItems(): SelectOptionItem[] {
    const items: SelectOptionItem[] = [];
    for (let i = 0; i < this.element.children.length; i++) {
      const element = this.element.children[i] as HTMLElement;
      const label = element.getAttribute("data-label") || "";
      const price = parseFloat(element.getAttribute("data-price")!) || 0;
      items.push({
        label,
        price,
      });
    }
    return items;
  }

  getLabel(): string {
    return `${this.label} - ${this.selectedItem?.label}`;
  }

  getPrice(): number {
    return this.selectedItem?.price || 0;
  }

  isSelected(): boolean {
    return this.selectedItem !== null;
  }
}

export default SelectOption;
