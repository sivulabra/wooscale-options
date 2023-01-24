import "../scss/global.scss";

import WooScaleOptions from "./modules/WooScaleOptions";

const cssPrefix = "wso";
const container = document.getElementById(`${cssPrefix}-container`);
const form = document.querySelector("form.cart");

if (container && form) {
  const app = new WooScaleOptions();
}
