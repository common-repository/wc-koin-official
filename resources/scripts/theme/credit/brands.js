export class Brands {
    constructor() {
      if (
        !document.querySelector(
          ".wc_payment_method .payment_method_wc-koin-credit"
        )
      )
        return;

        this.handleBrand();
    }


    handleBrand() {
      const card =
        document.querySelector("#wko-card-number");
      const cvv =
        document.querySelector("#wko-card-cvv");

      if (card) {
        if (card.value) {
            this.setBrand(this.getCard(card.value.replace(/\s/g, "")));
        }

        card.addEventListener("change", () => {
            this.setBrand(this.getCard(card.value.replace(/\s/g, "")));
        });
        card.addEventListener("keyup", () => {
            this.setBrand(this.getCard(card.value.replace(/\s/g, "")));
        });
      }

      if (cvv) {
        this.setCvv(cvv.value);

        cvv.addEventListener("change", () => {
            this.setCvv(cvv.value);
        });
        cvv.addEventListener("keyup", () => {
            this.setCvv(cvv.value);
        });
      }
    }

    setBrand(brand) {
      const img = document.querySelector(
        "#wko-credit-card-icon"
      );

      const hidden = document.querySelector(
        "#wko-card-brand"
      );

      if (img) {
        const name = this.translateBrand(brand);

        const attr = img.getAttribute("data-img");
        if (attr) {
          img.src = img.src.replace(attr, name);
          img.setAttribute("data-img", name);
        }
      }

      if (hidden) {
          hidden.value = brand;
      }
    }

    setCvv(cvv) {
      const img =
        document.querySelector("#wko-cvv-icon");

      const brand = cvv.length < 3 ? "mono/cvv" : "cvv";

      if (img) {
        const attr = img.getAttribute("data-img");
        if (attr) {
          img.src = img.src.replace(attr, brand);
          img.setAttribute("data-img", brand);
        }
      }
    }

    translateBrand(brand) {
      let name;

      switch (brand) {
        case 'AX':
          name = 'amex';
        break;
        case 'DC':
          name = 'diners';
        break;
        case 'DS':
          name = 'discover';
        break;
        case 'EC':
          name = 'elo';
        break;
        case 'HC':
          name = 'hipercard';
        break;
        case 'CA':
          name = 'mastercard';
        break;
        case 'VI':
          name = 'visa';
        break;
        case 'CL':
          name = 'generic';
        break;
        default:
          name = 'generic';
          break;
      }

      return name;
    }

    getCard(card) {
      const brandsRegex = {
        AX: new RegExp("^3[47][0-9]{13}$"),
        DC: new RegExp("^3(?:0[0-5]|[68][0-9])[0-9]{11}"),
        DS: new RegExp("^6(?:011|5[0-9]{2})[0-9]{12}"),
        EC: new RegExp("^((((636368)|(438935)|(504175)|(451416)|(636297))d{0,10})|((5067)|(4576)|(4011))d{0,12})"),
        HC: new RegExp("^606282|^3841(?:[0|4|6]{1})0"),
        CA: new RegExp("^(?:5[1-5][0-9]{14})"),
        VI: new RegExp("^4[0-9]{12}(?:[0-9]{3})"),
        CL: new RegExp('^(60420[1-9]|6042[1-9][0-9]|6043[0-9]{2}|604400)'),

      };

      for (let brand in brandsRegex) {
        if (brandsRegex[brand].test(card)) {
          return brand;
        }
      }

      return "N/A";
    }
  }
