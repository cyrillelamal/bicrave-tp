// Customer scripts
import $ from "jquery";

import Carousel from "./carousel";
import ProductControls from "./product-controls";

$(function () {
    $(".image-carousel").each(function () {
        new Carousel($(this)).attach();
    });

    $(".product-controls").each(function () {
        new ProductControls($(this)).attach();
    });
});
