import "./bootstrap";

import { Livewire, Alpine } from "../../vendor/livewire/livewire/dist/livewire.esm";
import Clipboard from "@ryangjchandler/alpine-clipboard";
import morph from "@alpinejs/morph";

window.Alpine = Alpine;

Alpine.plugin(morph);
Alpine.plugin(Clipboard);

Livewire.start();
