import $ from "jquery";

export default class ProductControls {
    static HIDDEN = "is-hidden";
    static SELECTORS = {
        add: ".add",
        amount: ".amount",
        pickUp: ".pick-up",
    };

    /**
     * @type {jQuery}
     * @protected
     */
    $container;

    /**
     * @type {jQuery}
     * @protected
     */
    $add;

    /**
     * @type {jQuery}
     * @protected
     */
    $amount;

    /**
     * @type {jQuery}
     * @protected
     */
    $pickUp;

    /**
     * @param {HTMLElement} container
     */
    constructor(container) {
        this.$container = $(container);

        this.$add = this.$container.find(ProductControls.SELECTORS.add);
        this.$amount = this.$container.find(ProductControls.SELECTORS.amount);
        this.$pickUp = this.$container.find(ProductControls.SELECTORS.pickUp);
    }

    /**
     * @return {ProductControls}
     */
    attach() {
        this.$add.on("click", this._add.bind(this));
        this.$pickUp.on("click", this._pickUp.bind(this));

        return this;
    }

    /**
     * @param {jQuery.Event} event
     * @protected
     */
    _add(event) {
        event.preventDefault();
        event.stopPropagation();

        this.$pickUp.removeClass(ProductControls.HIDDEN);
        this.$amount
            .removeClass(ProductControls.HIDDEN)
            .text(Number(this.$amount.text().trim()) + 1);

        $.ajax({
            method: "PATCH",
            url: this.$add.data("action"),
            success: this._success.bind(this),
        });
    }

    /**
     * @param {jQuery.Event} event
     * @protected
     */
    _pickUp(event) {
        event.preventDefault();
        event.stopPropagation();

        const amount = Number(this.$amount.text().trim() - 1);

        if (amount < 1) {
            this.$pickUp.addClass(ProductControls.HIDDEN);
            this.$amount.addClass(ProductControls.HIDDEN);
        }

        this.$amount.text(amount < 1 ? 0 : amount);

        $.ajax({
            method: "DELETE",
            url: this.$pickUp.data("action"),
            success: this._success.bind(this),
        });
    }

    /**
     * @param {Object} cart
     * @param {Array} cart.demands
     * @param {String} cart.total
     * @protected
     */
    _success({data: cart}) {
        $(".cart-total").text(cart.total);
    }
}
