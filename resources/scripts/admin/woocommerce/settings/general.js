class General
{
    constructor() {
        this.selectMode();
    }

    selectMode() {
        const select = document.querySelector('#wc_koin_settings_environment');
        if (select) {
            this.selectEnviroment(select);

            select.addEventListener("change", () => {
                this.selectEnviroment(select);
            });
        }
    }

    selectEnviroment(select) {
        if (select.value === 'production') {
            this.handleSandboxFields(select.value, true);
        }

        if (select.value === 'sandbox') {
            this.handleProductionFields(select.value, true);
        }
    }

    handleSandboxFields(hide) {
        const ids = [
            '#wc_koin_settings_code_test',
            '#wc_koin_settings_account_test',
            '#wc_koin_settings_secret_key_test',
            '#wc_koin_settings_org_id_test'
        ];

        ids.forEach(id => {
            const input = document.querySelector(id);

            if (input) {
                const parent = input.parentNode.parentNode;

                if (hide) {
                    parent.style.display = 'none';
                    this.handleProductionFields(false);   
                } else {
                    parent.style.display = 'table-row';
                }
            }
        });
    }

    handleProductionFields(hide) {
        const ids = [
            '#wc_koin_settings_code',
            '#wc_koin_settings_account',
            '#wc_koin_settings_secret_key',
            '#wc_koin_settings_org_id'
        ];

        ids.forEach(id => {
            const input = document.querySelector(id);

            if (input) {
                const parent = input.parentNode.parentNode;

                if (hide) {
                    parent.style.display = 'none';
                    this.handleSandboxFields(false);
                } else {
                    parent.style.display = 'table-row';
                }
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const koin = document.querySelector("#wc_koin_settings_environment");
    if ( koin ) {
        new General;
    }
});