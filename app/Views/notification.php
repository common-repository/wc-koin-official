<?php

if ( ! defined( 'ABSPATH' ) ) exit;

$strong  = isset( $strong ) ? $strong : "";
$message = isset( $message ) ? $message : "";
$class   = isset( $class ) ? $class : '';
?>

<?php if ( isset( $dismissible ) && $dismissible == true ) : ?>
    <?php $class .= " is-dismissible"; ?>
<?php endif; ?>

<div class="<?php echo esc_attr( $class ) ?> koin-notice">
    <p>
        <strong><?php echo esc_html( $strong) ?></strong>
        <?php echo $message; ?>
    </p>
</div>
