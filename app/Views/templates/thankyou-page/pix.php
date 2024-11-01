<?php
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="order-thankyou-page order-thankyou-page-pix">
    <img src="<?php echo esc_url( $logo )?>" alt="Koin Logo">
    <div>
        <p><?php echo esc_html__( "Scan the QR Code or copy the typeable line.", 'wc-koin-official' ); ?></p>
        <div class="koin-pix-types">
            <div>
                <object data="data:image/png;base64,<?php echo esc_attr( $qr_code ? $qr_code : '' ); ?>"></object>
            </div>
            <div class="saparator">
                <span></span>
            </div>
            <div>
                <div class="koin-pix-line">
                    <textarea aria-label="pix line" id="koin-pix-line" cols="30" rows="10"><?php echo esc_attr( $line ? $line : '' ); ?></textarea>
                    <div>
                        <button id="koin-copy-pix">
                            <?php echo esc_html__( 'Copy', 'wc-koin-official' ); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M448 384H256c-35.3 0-64-28.7-64-64V64c0-35.3 28.7-64 64-64H396.1c12.7 0 24.9 5.1 33.9 14.1l67.9 67.9c9 9 14.1 21.2 14.1 33.9V320c0 35.3-28.7 64-64 64zM64 128h96v48H64c-8.8 0-16 7.2-16 16V448c0 8.8 7.2 16 16 16H256c8.8 0 16-7.2 16-16V416h48v32c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V192c0-35.3 28.7-64 64-64z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
