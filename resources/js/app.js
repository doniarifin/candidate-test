import "./bootstrap";

import Alpine from "alpinejs";
import supplierPage from "./pages/supplier";

window.Alpine = Alpine;
Alpine.data("supplierPage", supplierPage);

Alpine.start();
