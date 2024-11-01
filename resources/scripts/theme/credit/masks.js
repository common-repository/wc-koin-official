import IMask from "imask";

export class Masks {
  constructor() {
    if (!document.querySelector(".wc_payment_method .payment_method_wc-koin-credit"))
      return;

    this.setEvents();
  }

  setEvents() {
    this.cardMask();
    this.dateMask();
    this.cvvMask();
    this.ownerMask();
  }

  cardMask() {
    const card = document.querySelector("#wko-card-number");

    if (card) {
      var mask = {
        mask: '0000 0000 0000 0000'
      };
      IMask(card, mask);
    }
  }

  dateMask() {
    const date = document.querySelector("#wko-card-expiry");

    if (date) {
      var mask = {
        mask: '00/00'
      };

      IMask(date, mask);

      date.addEventListener('change', () => {
        const splitDate = date.value.split('/');

        const month = document.querySelector('#wko-card-month');
        const year = document.querySelector('#wko-card-year');
        console.log(month, year)
        if (month && year) {
          month.value = splitDate[0] || '';
          year.value = splitDate[1] ? `20${splitDate[1]}` : '';
        }
      });
    }
  }

  cvvMask() {
    const cod = document.querySelector("#wko-card-cvv");

    if (cod) {
      var mask = {
        mask: '0000'
      };
      IMask(cod, mask);
    }
  }

  ownerMask() {
    const cod = document.querySelector("#wko-card-owner");

    if (cod) {
      var mask = {
        mask: /^[A-Za-z\s]*$/
      };
      IMask(cod, mask);
    }
  }
}
