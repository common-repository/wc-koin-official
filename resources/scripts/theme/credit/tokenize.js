export class Tokenize {

  cardObject = {
    number: null,
    holder_name: null,
    expiration_month: null,
    expiration_year: null,
    security_code: null,
  };

  lastCardObject = {
    number: null,
    holder_name: null,
    expiration_month: null,
    expiration_year: null,
    security_code: null,
  };

  constructor() {
    if (!document.querySelector(".wc_payment_method .payment_method_wc-koin-credit"))
      return;

      this.callFunctions();
  }

  callFunctions() {
    this.setExpireDate();
    this.setHolderName();
    this.setCardNumber();
    this.setCardCvv();
    this.verifyCardFields();

    this.tokenizeOnChange();
  }

  setExpireDate() {
    const input = document.querySelector("#wko-card-expiry");

    if (input) {
        input.addEventListener("keyup", () => {
            this.handleExpireDate(input)
        });
        input.addEventListener("change", () => {
            this.handleExpireDate(input)
        });
    }
  }

  handleExpireDate(input) {
      const splited = input.value.split("/");

      if (splited.length === 2) {
          if (splited[0].length === 2) {
              this.cardObject.expiration_month = splited[0];
          }
          if (splited[1].length === 2) {
              this.cardObject.expiration_year = splited[1];
          }
      }
  }

  setHolderName() {
    const input = document.querySelector("#wko-card-owner");

    if (input) {
        input.addEventListener("keyup", () => {
            this.cardObject.holder_name = input.value.toUpperCase();
        });
        input.addEventListener("change", () => {
            this.cardObject.holder_name = input.value.toUpperCase();
        });
    }
  }

  setCardNumber() {
    const input = document.querySelector("#wko-card-number");

    if (input) {
        input.addEventListener("keyup", () => {
            this.cardObject.number = input.value.replace(/\s/g,'');
        });

        input.addEventListener("change", () => {
            this.cardObject.number = input.value.replace(/\s/g,'');
        });
    }
  }

  setCardCvv() {
    const input = document.querySelector("#wko-card-cvv");

    if (input) {
        input.addEventListener("keyup", () => {
          if (input.value.length >= 3) {
            this.cardObject.security_code = input.value;
          }
        });
        input.addEventListener("change", () => {
            if (input.value.length >= 3) {
                this.cardObject.security_code = input.value;
            }
        });
    }
  }

  setCardBrand() {
    const number = document.querySelector("#wko-card-number");
    const input  = document.querySelector("#wko-card-brand");

    if (number && input) {
        number.addEventListener("keyup", () => {
          this.cardObject.brand_code = input.value;
        });
        number.addEventListener("change", () => {
            this.cardObject.brand_code = input.value;
        });
    }
  }

  setCardToken(token) {
    const input = document.querySelector("#wko-card-token");
    if (input) {
      input.value = token;
    }
  }

  setLastCardObject() {
    this.lastCardObject = {
      number: this.cardObject['number'],
      holder_name: this.cardObject['holder_name'],
      expiration_month: this.cardObject['expiration_month'],
      expiration_year: this.cardObject['expiration_year'],
      security_code: this.cardObject['security_code']
    }
  }

  cardObjectHasDifference() {
    let hasDifference = false;

    Object.keys(this.cardObject).forEach(key => {
      let element = this.cardObject[key];
      if (element !== this.lastCardObject[key]) {
        hasDifference = true;
      }
    });

    this.setLastCardObject();

    return hasDifference;
  }

  verifyCardFields() {
    const fields = [
      "card-expiry",
      "card-number",
      "card-expiry",
      "card-owner",
      "card-cvv"
    ];

    fields.forEach(field => {
      const element = document.querySelector(`#wko-${field}`);
      if (element) {
        element.addEventListener("keyup", () => {
          this.checkCardObjectFields();
        });
      }
    });
  }

  checkCardObjectFields() {
    let isFilled = true;

    Object.keys(this.cardObject).forEach(key => {
      let element = this.cardObject[key];
      if (element === null) {
        isFilled = false;
      }
    });

    if (isFilled) {
      this.createCardToken();
    }
  }

  tokenizeOnChange() {
      const woocommercePayments = document.querySelector('.woocommerce-checkout-payment');

      if (woocommercePayments) {
          const observer = new MutationObserver(() => {
              this.createCardToken();
          });

          observer.observe(woocommercePayments, {childList: true, subtree: true});
      }
  }


  getToken() {
    const hash = document.querySelector("#wko-card-hash");
    if (hash.value) {
      try {
        return atob(hash.value);
      } catch (error) {
        console.log(error);
      }
    }
    return '';
  }

  getRequestUrl() {
    const mode = document.querySelector("#wko-transaction-mode");
    if (mode.value && mode.value === 'production') {
      return 'https://api-secure.koin.com.br';
    }

    return 'https://api-sandbox.koin.com.br';
  }

  createCardToken() {
    const timestamp = new Date().getTime();

    const data = {
      card: this.cardObject,
      transaction: {
        reference_id: 'WC-' + btoa(`${this.cardObject.card}:${timestamp}`)
      }
    }

    if (!this.cardObjectHasDifference()) return;

    const placeHolder = document.querySelector("#place_order");
    placeHolder.setAttribute("disabled", true);

    fetch(`${this.getRequestUrl()}/v1/payment/tokenize`, {
      method: "POST",
      headers: {
        "accept": "application/json",
        "content-type": "application/json",
        "authorization": `Bearer ${this.getToken()}`
      },
      body: JSON.stringify(data),
    })
    .then((response) => response.json())
    .then((data) => {
      placeHolder.removeAttribute("disabled");

      if (data.secure_token) {
        this.setCardToken(data.secure_token);
      }
    });
  }
}
