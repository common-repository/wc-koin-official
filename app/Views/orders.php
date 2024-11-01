<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$koin_order_id = isset( $koin_order_id ) ? $koin_order_id : "";
$installments  = isset( $installments ) ? $installments : "";
$payment_link  = isset( $payment_link ) ? $payment_link : "";
$method        = isset( $method ) ? $method : "";
$order_data    = isset( $order ) ? str_replace( "'s", "", json_encode( $order ) ) : "";
?>

<div>
    <div id="koin-order-container">
        <h3><?php echo esc_html(WKO_PLUGIN_NAME); ?></h3>
        <p>
            <div>
                <strong><?php echo esc_html__( "Payment method: ", 'wc-koin-official' ) ;?></strong>
                <span class="method"><?php echo esc_html( $method ); ?></span>
            </div>
            <div>
                <?php if ( $installments ):  ?>
                    <strong><?php echo esc_html__( "Installments: ", 'wc-koin-official' ); ?></strong>
                    <span><?php echo esc_html( $installments ); ?></span>
                <?php endif;  ?>
            </div>
            <div>
                <strong><?php echo esc_html__( "Koin Order ID: ", 'wc-koin-official' ); ?></strong>
                <span><?php echo esc_html( $koin_order_id ); ?></span>
            </div>
            <div>
                <?php if ( isset( $payment_link ) ):  ?>
                    <strong><?php echo esc_html__( "Koin Payment Link: ", 'wc-koin-official' ); ?></strong>
                    <a target="_blank" rel="noopener" href="<?php echo esc_attr( esc_url( $payment_link ) ); ?>"><?php echo esc_attr( esc_url( $payment_link ) ); ?></a>
                <?php endif;  ?>
            </div>
            <div>
                <button type="button" class="btn btn-sync" id="koin-sync-button" data-action="sync_order" data-order='<?php echo esc_attr( $order_data ); ?>'>
                    <i class="fas fa-sync-alt sync"></i><?php echo esc_html__( "Sync Koin order", 'wc-koin-official' )?>
                </button>
            </div>
        </p>
    </div>
</div>
