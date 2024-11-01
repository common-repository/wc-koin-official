<?php

if ( ! defined( 'ABSPATH' ) ) exit;

use WKO\Helpers\Config;

?>

<div class="koin-checkout-fields">
    <div class="form-row form-row-wide line wko-brands-section">
        <div class="label">
            <label><?php echo esc_html__("Accepted card brands", "wc-koin-official"); ?></label>
        </div>
        <div class="brands">
        </div>
    </div>
    <div class="form-row form-row-wide line wko-card-owner">
        <label for="wko-card-owner"><?php echo esc_html__("Card Owner", "wc-koin-official"); ?> <span class="required">*</span></label>
        <input type="text" required autocomplete="off" name="wko-card-holder_name" id="wko-card-owner">
    </div>
    <div class="form-row form-row-wide line">
        <label for="wko-card-number"><?php echo esc_html__("Card Number", "wc-koin-official"); ?> <span class="required">*</span></label>
        <div class="wko-card-img">
            <img id="wko-credit-card-icon" src="<?php echo esc_url(Config::__image("icons/brands/mono/generic.svg")); ?>" data-img="mono/generic" alt="Credit card brand">
            <input type="text" required autocomplete="off" id="wko-card-number" name="wko-card-number" placeholder="0000 0000 0000 0000">
        </div>
    </div>
    <div class="line">
        <div class="form-row form-row-first ">
            <label for="wko-card-expiry"><?php echo esc_html__("Expiry Date", "wc-koin-official"); ?> <span class="required">*</span></label>
            <input type="text" required autocomplete="off" name="wko-card-expiry" id="wko-card-expiry" placeholder='<?php echo esc_html__("MM/YY", "wc-koin-official"); ?>'>
        </div>
        <div class="form-row form-row-last">
            <label for="wko-card-cvv"><?php echo esc_html__("Card Code", "wc-koin-official"); ?> <span class="required">*</span></label>
            <div class="wko-card-img">
                <img id="wko-cvv-icon" src="<?php echo esc_url(Config::__image("icons/brands/mono/cvv.svg")); ?>" data-img="mono/cvv" alt="Credit card CVV">
                <input type="text" required placeholder="CVV" id="wko-card-cvv" name="wko-card-security_code" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="form-row form-row-wide line select">
        <label for="wko-card-installments"><?php echo esc_html__("Installments", "wc-koin-official"); ?> <span class="required">*</span></label>
        <select name="wko-card-installments" id="wko-card-installments">
            <?php if (isset($installments)) : ?>
                <?php foreach ($installments as $key => $installment) : ?>
                    <option value="<?php echo esc_attr($key); ?>">
                        <?php echo esc_html($installment); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div class="wko-hiddens">
        <input type="hidden" name="wko-session" id="wko-session">
        <input type="hidden" name="wko-card-expiration_month" id="wko-card-month" />
        <input type="hidden" name="wko-card-expiration_year" id="wko-card-year" />
        <input type="hidden" name="wko-ipaddress" id="wko-ipaddress" />
        <input type="hidden" name="wko-card-brand" id="wko-card-brand" />
    </div>
</div>
