import { Ajax } from "./../ajax";
class Order 
{
    constructor() {
        this.syncButton();
        this._ajax = new Ajax;
    }

    syncButton() {
        const btn = document.querySelector("#koin-sync-button");

        if ( ! btn ) return;

        btn.addEventListener("click", ()  => {
            const action = btn.getAttribute("data-action");
            const order  = btn.getAttribute("data-order");
            console.log(order);

            const url = `/wp-admin/?wko_action=${action}`;

            this._ajax.do_ajax(
                url, 
                JSON.parse(order), 
                `${window.location.pathname + window.location.search}`
            );
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    new Order;
});