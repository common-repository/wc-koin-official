class Checkout
{
    constructor() {
        this.createPopup();
        this.handlePopup();
        this.selectedGateway();
        this.blockSelectedGateway();
    }

    handlePopup() {
        const popup = document.querySelector("#koin-popup-about");
        popup.addEventListener("click", () => {
            popup.classList.remove("koin-popup-about-active");
        });
    }

    createPopup() {

        const hidden = document.querySelector("#koin-info-banner");

        const context = document.createElement("div");
        context.setAttribute("id", "koin-popup-about");
        context.classList.add("koin-popup-about");

        const div = document.createElement("div");

        const span = document.createElement("span");
        span.setAttribute("id", "koin-close-popup");
        span.innerHTML = "&times;";
        span.classList.add("close");

        const img = document.createElement("img");
        if ( hidden && hidden.value ) {
            img.setAttribute("src", hidden.value);
        } else {
            img.setAttribute("src", "");
        }

        div.appendChild(span);
        div.appendChild(img);
        context.appendChild(div)

        const body = document.querySelector("body");
        body.appendChild(context);

    }

    selectedGateway() {
        const gateways = document.querySelectorAll('form.checkout input[name^="payment_method"]');

        gateways.forEach(gateway => {
            gateway.addEventListener('change', () => {
                const event = new Event('update_checkout');
                document.body.dispatchEvent(event)
            });
        });
    }

    blockSelectedGateway() {
        document.addEventListener('wc_koin_selected_event', () => {
            window.wc.blocksCheckout.extensionCartUpdate( {
                namespace: 'wc-koin-gateway-update-total',
                data: {
                    payment_method: document.querySelector('input[name="radio-control-wc-payment-method-options"]:checked').value
                },
            });
        })

    }
}

(function($) {
    $(document).on("ready updated_checkout", () => {
        new Checkout;
    });
}(jQuery));

