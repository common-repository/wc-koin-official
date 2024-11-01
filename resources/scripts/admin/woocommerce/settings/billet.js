class Billet
{
    constructor() {
        this.addClasses();
        this.setLogo();
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
        const imagePath = document.querySelector("#woocommerce_wc-koin-billet_koin_logo");

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
    const koin = document.querySelector("#woocommerce_wc-koin-billet_enabled");
    if ( koin ) {
        new Billet;
    }
});
