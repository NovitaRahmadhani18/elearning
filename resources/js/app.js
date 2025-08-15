import "./bootstrap";

import { Livewire, Alpine } from "../../vendor/livewire/livewire/dist/livewire.esm";
import Clipboard from "@ryangjchandler/alpine-clipboard";
import ajax from "@imacrayon/alpine-ajax";
import morph from "@alpinejs/morph";

window.Alpine = Alpine;

Alpine.plugin(ajax);
Alpine.plugin(morph);
Alpine.plugin(Clipboard);

Livewire.start();
