export interface Option {
  getLabel(): string;
  getPrice(): number;
  isSelected(): boolean;
}

export type SelectOptionItem = {
  label: string;
  price: number;
};
