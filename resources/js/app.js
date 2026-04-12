import "./bootstrap";

import Alpine from "alpinejs";
import helper from "./helpers/helper";
import sort from "@alpinejs/sort";

import supplierPage from "./pages/supplier/supplier";
import layupManager from "./pages/layups/layupManager";
import conflictManager from "./pages/layups/conflictManager";
import importManager from "./pages/layups/importManager";

Alpine.plugin(sort);

window.Alpine = Alpine;
Alpine.data("helper", helper);
Alpine.data("supplierPage", supplierPage);
Alpine.data("layupManager", layupManager);
Alpine.data("conflictManager", conflictManager);
Alpine.data("importManager", importManager);

window.$helper = helper();

Alpine.start();
