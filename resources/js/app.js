import "./bootstrap";

import Alpine from "alpinejs";
import ajax from "@imacrayon/alpine-ajax";
import morph from "@alpinejs/morph";

window.Alpine = Alpine;

Alpine.plugin(ajax);
Alpine.plugin(morph);
Alpine.start();
