// Customer scripts
import $ from "jquery";

import Carousel from "./carousel";
import ProductControls from "./product-controls";

$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
        },
    });

    $(".image-carousel").each(function () {
        new Carousel($(this)).attach();
    });

    $(".product-controls").each(function () {
        new ProductControls($(this)).attach();
    });
});
