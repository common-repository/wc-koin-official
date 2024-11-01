<?php

if ( ! defined( 'ABSPATH' ) ) exit;

$koin_order_id = isset( $koin_order_id ) ? $koin_order_id : "";
$installments = isset( $installments ) ? $installments : "";
$koin_payment_link = isset( $koin_payment_link ) ? $koin_payment_link : "";
$method_text = isset( $method ) ? $method : "";

?>

<div class="order-view-section">
    <div id="koin-order-container">
        <h3 class="section-title"><?php echo esc_html(WKO_PLUGIN_NAME); ?></h3>
        <p>
            <div class="section-row">
                <strong><?php echo esc_html__( "Payment method: ", 'wc-koin-official' ) ;?></strong>
                <a target="_blank" rel="noopener" href="<?php echo esc_url( "koin.com.br" ); ?>" class="method"><?php echo esc_html( $method_text ); ?></a>
            </div>
            <div class="section-row">
                <strong><?php echo esc_html__( "Installments:   ", 'wc-koin-official' ); ?></strong>
                <span><?php echo esc_html( $installments ); ?></span>
            </div>
            <div class="section-row">
                <strong><?php echo esc_html__( "Koin order ID:  ", 'wc-koin-official' ); ?></strong>
                <span><?php echo esc_html( $koin_order_id ); ?></span>
            </div>
            <div class="section-row">
                <strong><?php echo esc_html__( "Koin payment link:  ", 'wc-koin-official' ); ?></strong>
                <a target="_blank" rel="noopener" href="<?php echo esc_html( $koin_payment_link ); ?>"><?php echo esc_html( $koin_payment_link ); ?><svg class="svg-link" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M384 320c-17.67 0-32 14.33-32 32v96H64V160h96c17.67 0 32-14.32 32-32s-14.33-32-32-32L64 96c-35.35 0-64 28.65-64 64V448c0 35.34 28.65 64 64 64h288c35.35 0 64-28.66 64-64v-96C416 334.3 401.7 320 384 320zM502.6 9.367C496.8 3.578 488.8 0 480 0h-160c-17.67 0-31.1 14.32-31.1 31.1c0 17.67 14.32 31.1 31.99 31.1h82.75L178.7 290.7c-12.5 12.5-12.5 32.76 0 45.26C191.2 348.5 211.5 348.5 224 336l224-226.8V192c0 17.67 14.33 31.1 31.1 31.1S512 209.7 512 192V31.1C512 23.16 508.4 15.16 502.6 9.367z"/></svg></a>
            </div>
        </p>
    </div>
</div>
