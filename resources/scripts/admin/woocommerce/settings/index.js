import "./billet";
import "./credit";
import "./pix";
import "./general";
import IMask from "imask";

class Gateways
{
	constructor() {
		this.setDiscountFieldMask()
	}

	setDiscountFieldMask() {
		const discount = document.querySelector('.wko-discount-value');

		if(!discount) return;

		IMask(
			discount,
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
}

document.addEventListener("DOMContentLoaded", () => {
	new Gateways();
});
