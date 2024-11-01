import metadata from './block.json';

import { IMaskInput } from 'react-imask';
import { useState, useCallback } from '@wordpress/element';
import {useEffect} from "react";
import { __ } from '@wordpress/i18n'

const { registerCheckoutBlock } = wc.blocksCheckout;

const Block = ({ checkoutExtensionData }) => {
    const [personType, setPersonType] = useState(1);
    const [cpf, setCpf] = useState('');
    const [cnpj, setCnpj] = useState('');

    const { setExtensionData } = checkoutExtensionData;

    const onPersonChange = useCallback(
        (value) => {
            setPersonType(value);
            setExtensionData('wc-koin-official', '_billing_persontype', value);
        },
        [setPersonType, setExtensionData]
    );

    const onCpfChange = useCallback(
        (value) => {
            setCpf(value);
            setExtensionData('wc-koin-official', '_billing_persontype', personType);
            setExtensionData('wc-koin-official', '_billing_cpf', value);
        },
        [setCpf, setExtensionData, personType]
    );

    const onCnpjChange = useCallback(
        (value) => {
            setCnpj(value);
            setExtensionData('wc-koin-official', '_billing_persontype', personType);
            setExtensionData('wc-koin-official', '_billing_cnpj', value);
        },
        [setCnpj, setExtensionData, personType]
    );

    useEffect(() => {
        const event = new Event("add_checkout_block_fields");
        document.dispatchEvent(event);
    }, []);

    return (
        <div style={{display: 'flex', flexDirection: 'column', gap: '20px'}}>
            <div className="wc-block-components-address-form">
                <div className="wc-block-components-text-input is-active">
                    <select id="wc-koin-official__billing_persontype"
                            aria-label={__("Tipo de Pessoa", "wc-koin-official")}
                            required=""
                            style={{
                                width: '100%',
                                maxWidth: '100%',
                                height: '55px',
                                borderColor: '#000',
                                padding: '15px 5px 0',
                                borderRadius: '5px'
                            }}
                            value={personType}
                            onChange={e => onPersonChange(e.target.value)}
                    >
                        <option value={1}>{__("Pessoa Física", "wc-koin-official")}</option>
                        <option value={2}>{__("Pessoa Jurídica", "wc-koin-official")}</option>
                    </select>
                    <label>{__("Tipo de Pessoa", "wc-koin-official")}</label>
                </div>
            </div>
            {personType == 1 && (
                <div className='wc-block-components-address-form'>
                <div className='wc-block-components-text-input is-active'>
                    <div>
                        <IMaskInput mask={"000.000.000-00"}
                                    placeholder={"000.000.000-00"}
                                    type="text"
                                    id="_billing_cpf"
                                    value={cpf}
                                    required={cpf ? true : false}
                                    onChange={e => onCpfChange(e.target.value)}
                        />
                    </div>
                    <label>{__("CPF", "wc-koin-official")}</label>
                </div>
                </div>
            )}
           {personType == 2 && (
            <div className='wc-block-components-address-form'>
                <div className={'wc-block-components-text-input is-active'}>
                    <div>
                        <IMaskInput mask={"00.000.000/0000-00"}
                                    placeholder={"00.000.000/0000-00"}
                                    type="text"
                                    id="_billing_cnpj"
                                    value={cnpj}
                                    required={cnpj ? true : false}
                                    onChange={e => onCnpjChange(e.target.value)}
                        />
                    </div>
                    <label>{__("CNPJ", "wc-koin-official")}</label>
                </div>
            </div>
           )}
        </div>
    );
}


const options = {
    metadata,
    component: Block
};

registerCheckoutBlock( options );
