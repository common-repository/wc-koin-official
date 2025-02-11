<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$logo         = isset( $logo ) ? $logo : "";
$payment_link = isset( $payment_link ) ? $payment_link : "";
$blank_link   = isset($blank_link) ? $blank_link : false;

?>

<div class="order-thankyou-page">
    <img src="<?php echo esc_url( $logo )?>" alt="">
    <div>
        <p><?php echo esc_html__( "Access the link below to finalize the payment on the Koin platform.", 'wc-koin-official' ); ?></p>
        <div>
            <a target="_blank" rel="noopener" href="<?php echo esc_url( $payment_link ) ?>"><?php echo esc_url( $payment_link ) ?><svg class="svg-link"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M384 320c-17.67 0-32 14.33-32 32v96H64V160h96c17.67 0 32-14.32 32-32s-14.33-32-32-32L64 96c-35.35 0-64 28.65-64 64V448c0 35.34 28.65 64 64 64h288c35.35 0 64-28.66 64-64v-96C416 334.3 401.7 320 384 320zM502.6 9.367C496.8 3.578 488.8 0 480 0h-160c-17.67 0-31.1 14.32-31.1 31.1c0 17.67 14.32 31.1 31.99 31.1h82.75L178.7 290.7c-12.5 12.5-12.5 32.76 0 45.26C191.2 348.5 211.5 348.5 224 336l224-226.8V192c0 17.67 14.33 31.1 31.1 31.1S512 209.7 512 192V31.1C512 23.16 508.4 15.16 502.6 9.367z"/></svg></a>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        <?php if ($payment_link && $blank_link) : ?>

            let win = window.open("<?php echo esc_url( $payment_link ) ?>", '_blank');
            window.focus();

            if(!win || win.closed || typeof win.closed == 'undefined') {
                window.location = "<?php echo esc_url( $payment_link ) ?>";
            }

        <?php endif; ?>
    });
</script>
