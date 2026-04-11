import "./bootstrap";

import Alpine from "alpinejs";
import helper from "./helpers/helper";
import sort from "@alpinejs/sort";

import supplierPage from "./pages/supplier";
import layupManager from "./pages/layups/layupManager";
import conflictManager from "./pages/layups/conflictManager";

Alpine.plugin(sort);

window.Alpine = Alpine;
Alpine.data("helper", helper);
Alpine.data("supplierPage", supplierPage);
Alpine.data("layupManager", layupManager);
Alpine.data("conflictManager", conflictManager);

window.$helper = helper();

Alpine.start();
