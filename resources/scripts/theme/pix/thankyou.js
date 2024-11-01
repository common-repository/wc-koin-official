export class Thankyou {
    constructor() {
        this.copy();
    }

    copy() {
        const line = document.querySelector('#koin-copy-pix');

        if (line) {
            line.addEventListener("click", () => {
                const input = document.querySelector('#koin-pix-line');

                if (input) {
                    try {
                        navigator.clipboard.writeText(input.value);
                 
                    } catch (error) {
                        input.select();
                        document.execCommand('copy');
                    }
                }
            });
        }
    }
}