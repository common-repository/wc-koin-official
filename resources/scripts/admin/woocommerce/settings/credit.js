import IMask from "imask";
class Credit
{
    constructor() {
        this.addClasses();
        this.setLogo();
        this.handleInstallments();

        this.handleCardRates();
        this.controllFields();
        this.createRateByInstallmentsFields();
        this.fillInstallmentsFields();
        this.updateInstallmentFields();
        this.setSimpleFeeMask();
    }

    handleCardRates() {
        const rateType = document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_type');

        if (rateType) {
            rateType.addEventListener('change', () => {
                this.controllFields();
            });
        }
    }

    setSimpleFeeMask() {
        const simple = document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_simple');

        if (simple) {
            this.setFeeFieldMask(simple);
        }
    }

    controllFields() {
        const rateType = document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_type');

        if (rateType) {
            switch (rateType.value) {
                case '1':
                    this.showSimpleRate();
                    break;
                case '2':
                    this.showRateByInstallment();
                    break;
                default:
                    this.hideTransferFields();
                    break;
            }
        }
    }

    hideTransferFields() {
        const elements = [
            document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_format'),
            document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_simple'),
            document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_by_installments')
        ];

        elements.forEach(element => {
            const parent = element.parentElement.parentElement.parentElement;
            if (parent) {
                parent.classList.add('deactive');
            }
        });
    }

    showSimpleRate() {
        this.hideTransferFields();

        const elements = [
            document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_format'),
            document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_simple')
        ];

        elements.forEach(element => {
            const parent = element.parentElement.parentElement.parentElement;
            if (parent) {
                parent.classList.remove('deactive');
            }
        });
    }

    showRateByInstallment() {
        this.hideTransferFields();

        const elements = [
            document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_format'),
            document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_by_installments')
        ];

        elements.forEach(element => {
            const parent = element.parentElement.parentElement.parentElement;
            if (parent) {
                parent.classList.remove('deactive');
            }
        });
    }

    createRateByInstallmentsFields() {
        const field = document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_by_installments');

        if (field) {
            field.style = 'display: none';
            const parent = field.parentElement;

            const context = document.createElement("div");
            context.classList.add('koin-installments-container-context');

            for (let index = 1; index <= 12; index++) {
                const span = document.createElement("span");
                span.innerText = index;

                const input = document.createElement("input");
                input.setAttribute("data-installment", index);
                input.classList.add('koin-installments-fields');
                this.setFeeFieldMask(input);

                const div = document.createElement("div");
                div.classList.add('koin-installments-container-fields');

                div.appendChild(span);
                div.appendChild(input);

                context.appendChild(div);
            }

            parent.appendChild(context);
        }

    }

    setFeeFieldMask(field) {
        if(!field) return;
        IMask(
            field,
            {
                mask: [
                    { mask: '' },
                    {
                        mask: 'num',
                        lazy: false,
                        blocks: {
                            num: {
                                mask: Number,
                                scale: 2,
                                padFractionalZeros: true,
                                radix: '.',
                                mapToRadix: [','],
                            }
                        }
                    }
                ]
              }
        );
      }

    fillInstallmentsFields() {
        const field = document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_by_installments');
        const data = JSON.parse(field.value);

        if (data) {
            const installments = document.querySelectorAll('.koin-installments-fields');

            installments.forEach(element => {
                if (data[element.getAttribute('data-installment')] !== undefined) {
                    element.value = data[element.getAttribute('data-installment')];
                }
            });
        }
    }

    updateInstallmentFields() {
        const installments = document.querySelectorAll('.koin-installments-fields');
        const field = document.querySelector('#woocommerce_wc-koin-credit_rate_transfer_by_installments');
        const data = JSON.parse(field.value);

        installments.forEach(element => {
            element.addEventListener('change', () => {

                if (data[element.getAttribute('data-installment')] !== undefined) {
                    data[element.getAttribute('data-installment')] = element.value;
                }

                field.value = JSON.stringify(data);
            });
        });
    }


    handleInstallments() {
        const input = document.querySelector('#woocommerce_wc-koin-credit_installments');

        if (input) {
            input.setAttribute('max', '12');
            input.setAttribute('min', '0');
        }
    }

    addClasses() {
        const wrap = document.querySelector("#mainform")
        wrap.classList.add("wrap-koin");

        const trs = document.querySelectorAll('tr');
        trs.forEach(tr => {
            tr.classList.add("top-koin");
        });
    }

    setLogo() {
        const title = document.querySelector(".wrap-koin h2");
        const imagePath = document.querySelector("#woocommerce_wc-koin-credit_koin_logo");

        const img = document.createElement('img');
        const hr  = document.createElement('hr');
        img.setAttribute("src", imagePath.value);

        if ( title ) {
            title.parentNode.insertBefore(img, title);
            title.parentNode.insertBefore(hr, title);
        }


    }

}

document.addEventListener("DOMContentLoaded", () => {
    const koin = document.querySelector("#woocommerce_wc-koin-credit_enabled");
    if ( koin ) {
        new Credit;
    }
});
