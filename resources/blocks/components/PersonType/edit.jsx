import { IMaskInput } from 'react-imask';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

export const Edit = ({ attributes, setAttributes }) => {
    const {cpf, cnpj, personType} = attributes;

    return (
        <div {...useBlockProps()} style={{display: 'flex', flexDirection: 'column', gap: '20px'}}>
            <div class="wc-block-components-address-form">
                <div class="wc-block-components-text-input is-active">
                    <select id="wc-koin-official_billing_persontype"
                            aria-label={__("Tipo de Pessoa", "wc-koin-official")}
                            required=""
                            style={{
                                width: '100%',
                                maxWidth: '100%',
                                height: '55px',
                                borderColor: '#000',
                                paddingTop: '15px'
                            }}
                            value={personType}
                            onChange={e => setAttributes({personType: e.target.value})}
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
                                    id="billing_cpf"
                                    value={cpf}
                                    required={cpf ? true : false}
                                    onChange={e => setAttributes({cnpj: e.target.value})}
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
                                    id="billing_cnpj"
                                    value={cnpj}
                                    required={cnpj ? true : false}
                                    onChange={e => setAttributes({cnpj: e.target.value})}
                        />
                    </div>
                    <label>{__("CNPJ", "wc-koin-official")}</label>
                </div>
            </div>
           )}
        </div>
    );
};
