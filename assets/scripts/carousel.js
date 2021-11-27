import $ from "jquery";

const jQuery = $;

export default class Carousel {
    static HIDDEN = "is-hidden";
    static SELECTORS = {
        figure: "figure",
        previous: ".previous",
        next: ".next",
        hidden: `.${Carousel.HIDDEN}`,
    };

    /**
     * @type {jQuery}
     * @private
     */
    $container;

    /**
     * @type {jQuery}
     * @private
     */
    $figures;

    /**
     * @param {HTMLElement} container
     */
    constructor(container) {
        this.$container = $(container);

        this.$figures = this.$container.find(Carousel.SELECTORS.figure);
    }

    /**
     * @return {Carousel}
     */
    attach() {
        this.$container.find(Carousel.SELECTORS.previous).on("click", this._previous.bind(this));
        this.$container.find(Carousel.SELECTORS.next).on("click", this._next.bind(this));

        return this;
    }

    /**
     * @param {jQuery.Event} event
     * @private
     */
    _previous(event) {
        event.preventDefault();
        event.stopPropagation();

        const $current = this._$current;
        const $previous = $current.prev(Carousel.SELECTORS.figure).length
            ? $current.prev(Carousel.SELECTORS.figure)
            : this.$figures.last(Carousel.SELECTORS.figure);

        $current.addClass(Carousel.HIDDEN);
        $previous.removeClass(Carousel.HIDDEN);
    }

    /**
     * @param {jQuery.Event} event
     * @private
     */
    _next(event) {
        event.preventDefault();
        event.stopPropagation();

        const $current = this._$current;
        const $next = $current.next(Carousel.SELECTORS.figure).length
            ? $current.next(Carousel.SELECTORS.figure)
            : this.$figures.first();

        $current.addClass(Carousel.HIDDEN);
        $next.removeClass(Carousel.HIDDEN);
    }

    /**
     * @private
     * @return {jQuery}
     */
    get _$current() {
        return this.$figures.filter(`:not(${Carousel.SELECTORS.hidden})`);
    }
};
