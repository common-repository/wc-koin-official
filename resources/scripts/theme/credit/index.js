import { Brands } from "./brands";
import { Masks } from "./masks";

class Credit
{
    constructor() {
        this.generateSession();
        this.getCustomerIp();
    }

    generateSession() {
        if (typeof getSessionID !== "undefined") {
            getSessionID(function (sessionId) {
                const session = document.querySelector('#wko-session');
                if (session) {
                    session.value = sessionId;
                }
            });
        }
    }

    getCustomerIp() {
        fetch('https://api.ipify.org?format=json')
            .then(response => response.json())
            .then((data) => {
                if (data.ip) {
                    const ipAddress = document.querySelector('#wko-ipaddress');
                    if (ipAddress) {
                        ipAddress.value = data.ip;
                    }
                }
            });
    }
}

(function($) {
    $(document).on("ready updated_checkout", () => {
        new Credit;
        new Masks;
        new Brands;
    });

    $(document).on("wko_add_checkout_block_fields", () => {
        new Credit;
    });

}(jQuery));
