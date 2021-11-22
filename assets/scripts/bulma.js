import $ from "jquery";

$(function () {
    $(".navbar-burger").on("click", function () {
        $(".navbar-burger").toggleClass("is-active");
        $(".navbar-menu").toggleClass("is-active");
    });

    $(".notification .delete").on("click", function () {
        $(this).parent().remove();
    });
});
