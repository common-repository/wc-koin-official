import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { useEffect } from "react";
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';
import {__} from "@wordpress/i18n";
import {GatewayDescription} from "../../components/GatewayDescription";

const name = 'wc-koin-pix'
const settings = getSetting(`${name}_data`, {});
const defaultLabel = __('Pix - Koin', 'wc-koin-official');

const label = decodeEntities(settings.title) || defaultLabel;
const sandbox = decodeEntities(settings.sandbox || '');
const description = decodeEntities(settings.description || '');

const Content = (props) => {
    useEffect(() => {
        const selectMethodEvent = new Event(`wc_koin_selected_event`);
        document.dispatchEvent(selectMethodEvent);
    }, []);

    return (
        <>
            <GatewayDescription sandbox={sandbox} description={description}/>
        </>
    );
};

const Label = (props) => {
    return (
        <span style={{ width: '100%' }}>
            {label}
        </span>
    );
};


const Pix = {
    name: name,
    label: <Label />,
    content: <Content />,
    edit: <Content />,
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    }
};

registerPaymentMethod(Pix);
