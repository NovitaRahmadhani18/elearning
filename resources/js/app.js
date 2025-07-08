import "./bootstrap";

import { Livewire, Alpine } from "../../vendor/livewire/livewire/dist/livewire.esm";
import ajax from "@imacrayon/alpine-ajax";
import Clipboard from "@ryangjchandler/alpine-clipboard"; // Import it
import morph from "@alpinejs/morph";

window.Alpine = Alpine;

Alpine.plugin(ajax);
Alpine.plugin(morph);
Alpine.plugin(Clipboard); // Register the plugin

Livewire.start();
