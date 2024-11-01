import { registerPaymentMethod } from "@woocommerce/blocks-registry";
import { useEffect, useState, useRef } from "react";
import { decodeEntities } from "@wordpress/html-entities";
import { getSetting } from "@woocommerce/settings";
import { __ } from "@wordpress/i18n";
import { GatewayDescription } from "../../components/GatewayDescription";
import { IMaskInput } from "react-imask";

const name = "wc-koin-credit";
const settings = getSetting(`${name}_data`, {});
const defaultLabel = __("Credit Card - Koin", "wc-koin-official");
const label = decodeEntities(settings.title) || defaultLabel;
const sandbox = decodeEntities(settings.sandbox || "");
const description = decodeEntities(settings.description || "");
const brandUrl = decodeEntities(settings.brand || "");
const installments = decodeEntities(settings.installments || []);

const Content = (props) => {
  const { eventRegistration, emitResponse } = props;
  const { onPaymentSetup } = eventRegistration;

  const [number, setNumber] = useState("");
  const [owner, setOwner] = useState("");
  const [date, setDate] = useState("");
  const [code, setCode] = useState("");
  const [brand, setBrand] = useState(0);
  const [installment, setInstallment] = useState("1");
  const [ipAddress, setIpAddress] = useState("");
  const [session, setSession] = useState("");
  const [brandName, setBrandName] = useState("mono/generic");

  useEffect(() => {
    const event = new Event("wko_add_checkout_block_fields");
    document.dispatchEvent(event);

    const selectMethodEvent = new Event(`wc_koin_selected_event`);
    document.dispatchEvent(selectMethodEvent);
  }, []);

  const sessionRef = useRef(null);
  const ipRef = useRef(null);

  useEffect(() => {
    const mutationObserver = new MutationObserver((mutationsList, observer) => {
      for (const mutation of mutationsList) {
        if (
          mutation.type === "attributes" &&
          mutation.attributeName === "value"
        ) {
          if (mutation.target === sessionRef.current) {
            setSession(mutation.target.value);
          } else if (mutation.target === ipRef.current) {
            setIpAddress(mutation.target.value);
          }
        }
      }
    });

    if (sessionRef.current) {
      mutationObserver.observe(sessionRef.current, { attributes: true });
    }

    if (ipRef.current) {
      mutationObserver.observe(ipRef.current, { attributes: true });
    }

    return () => {
      mutationObserver.disconnect();
    };
  }, [setSession, setIpAddress]);

  useEffect(() => {
    const sendCustomFields = onPaymentSetup(async () => {
      const splitDate = date.split('/');
      return {
        type: emitResponse.responseTypes.SUCCESS,
        meta: {
          paymentMethodData: {
            "wko-card-holder_name": owner.toUpperCase(),
            "wko-card-number": number,
            "wko-card-expiration_month": splitDate[0] || '',
            "wko-card-expiration_year": splitDate[1] ? `20${splitDate[1]}` : '',
            "wko-card-security_code": code,
            "wko-card-installments": installment,
            "wko-card-brand": brand.toString(),
            "wko-session": session,
            "wko-ipaddress": ipAddress,
          },
        },
      };
    });

    return () => {
      sendCustomFields();
    };
  }, [
    emitResponse.responseTypes.ERROR,
    emitResponse.responseTypes.SUCCESS,
    onPaymentSetup,
    owner,
    number,
    code,
    date,
    installment,
    brand,
    ipAddress,
    session,
  ]);

  const setCardNumber = (value) => {
    const formatted = value.replace(/\s/g, "");
    const brands = getBrands();

    setNumber(formatted);

    let found;

    brands.forEach((brand) => {
      if (brand.regex.test(formatted) && !found) {
        if (value.length > 14 && formatted) {
          found = brand;
          setCardBrand(brand.name, brand.code);
        }
      }
    });
  };

  const getBrands = () => {
    return [
      {
        code: 'EC',
        name: "elo",
        regex:
          /^4011(78|79)|^43(1274|8935)|^45(1416|7393|763(1|2))|^50(4175|6699|67[0-6][0-9]|677[0-8]|9[0-8][0-9]{2}|99[0-8][0-9]|999[0-9])|^627780|^63(6297|6368|6369)|^65(0(0(3([1-3]|[5-9])|4([0-9])|5[0-1])|4(0[5-9]|[1-3][0-9]|8[5-9]|9[0-9])|5([0-2][0-9]|3[0-8]|4[1-9]|[5-8][0-9]|9[0-8])|7(0[0-9]|1[0-8]|2[0-7])|9(0[1-9]|[1-6][0-9]|7[0-8]))|16(5[2-9]|[6-7][0-9])|50(0[0-9]|1[0-9]|2[1-9]|[3-4][0-9]|5[0-8]))/,
      },
      {
        code: 'VI',
        name: "visa",
        regex: /^4/,
      },
      {
        code: 'CA',
        name: "mastercard",
        regex: /^5[1-5]/,
      },
      {
        code: 'AX',
        name: "amex",
        regex: /^3[47]/,
      },
      {
        code: 'DS',
        name: "discover",
        regex: /^(6011|622[126-925]\d|64[4-9]\d|65\d{2})/,
      },
      {
        code: 'HC',
        name: "hipercard",
        regex: /^(38\d{2}\d*|60\d{2}\d*)/,
      },
      {
        code: 'CL',
        name: "generic",
        regex: /.*/,
      },
    ];
  };

  const setCardBrand = (name, brandCode = 0) => {
    setBrandName(name);

    if (brandCode && brandCode != brand) {
      setBrand(brandCode);
    }
  };

  return (
    <>
      <GatewayDescription sandbox={sandbox} description={description} />
      <div className="koin-checkout-fields payment_method_wc-koin-credit">
        <div className="form-row form-row-wide line wko-card-owner koin-checkout-fields">
          <label htmlFor="wko-card-owner">
            {__("Card Owner", "wc-koin-official")}
            <span>*</span>
          </label>
          <div>
            <IMaskInput
              mask={/^[A-Za-z\s]*$/}
              type="text"
              id="wko-card-owner"
              className="wko-block-field"
              value={owner}
              onChange={(e) => setOwner(e.target.value)}
            />
          </div>
        </div>
        <div className="form-row form-row-wide line koin-checkout-fields">
          <label htmlFor="wko-card-number">
            {__("Card Number", "wc-koin-official")}
            <span>*</span>
          </label>
          <div className="wko-card-img">
            <img
              id="wko-brand-icon"
              src={`${brandUrl}/${brandName}.svg`}
              data-img={brandName}
              alt="Credit card brand"
            />
            <IMaskInput
              mask={"0000 0000 0000 0000"}
              type="text"
              id="wko-card-number"
              className="wko-block-field"
              value={number}
              placeholder="0000 0000 0000 0000"
              onAccept={(value, mask) => setCardNumber(value)}
            />
          </div>
        </div>
        <div className="line">
          <div className="form-row form-row-first">
            <label htmlFor="wko-card-date">
              {__("Expiry Date", "wc-koin-official")}
              <span>*</span>
            </label>
            <div>
              <IMaskInput
                mask={"00/00"}
                type="text"
                id="wko-card-date"
                className="wko-block-field"
                value={date}
                placeholder="MM/YY"
                onAccept={(value, mask) => setDate(value)}
              />
            </div>
          </div>
          <div className="form-row form-row-last koin-checkout-fields">
            <label htmlFor="wko-card-code">
              {__("Card Code", "wc-koin-official")}
              <span>*</span>
            </label>
            <div className="wko-card-img">
              <img
                id="wko-cvc-icon"
                src={`${brandUrl}/mono/cvv.svg`}
                data-img="mono/cvc"
                alt="Credit card CVC"
              />
              <IMaskInput
                mask={"0000"}
                type="text"
                id="wko-card-code"
                className="wko-block-field"
                value={code}
                placeholder="CVC"
                onAccept={(value, mask) => setCode(value)}
              />
            </div>
          </div>
        </div>
        <div className="form-row form-row-wide line select">
          <label htmlFor="wko-card-installments">
            {__("Installments", "wc-koin-official")}
            <span>*</span>
          </label>
          <div className="koin-checkout-fields">
            <select
              id="wko-card-installments"
              value={installment}
              onChange={(e) => setInstallment(e.target.value)}
            >
              {Object.keys(installments).map((key) => (
                <option key={key} value={key}>
                  {installments[key]}
                </option>
              ))}
            </select>
          </div>
        </div>
        <div className="wko-hiddens">
          <input
            type="hidden"
            name="wko-ipaddress"
            ref={ipRef}
            id="wko-ipaddress"
            value={ipAddress}
            onChange={(value) => setIpAddress(value)}
          />
          <input
            type="hidden"
            name="wko-session"
            ref={sessionRef}
            id="wko-session"
            value={session}
            onChange={(value) => setSession(value)}
          />
        </div>
      </div>
    </>
  );
};

const Label = (props) => {
  return <span style={{ width: "100%" }}>{label}</span>;
};

const Credit = {
  name: name,
  label: <Label />,
  content: <Content />,
  edit: <Content />,
  canMakePayment: () => true,
  ariaLabel: label,
  supports: {
    features: settings.supports,
  },
};

registerPaymentMethod(Credit);
