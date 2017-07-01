<?php
/**
 * Transparent Checkout template.
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$pga_compra = new PGA_Compra($this->controller);
$total_taxa_contr = $pga_compra->get_taxa_contratacao_total_carrinho($order);
$plano_description = $pga_compra->get_order_intervalo($order);
?>

<div id="extra_order_details" >
    <ul class="order_details">
        <?php
        if ($total_taxa_contr > 0) {
            ?>
            <li class="order">
                <?php _e('Taxa de contratação:', 'pagseguro-assinaturas-rcs'); ?>
                <strong><?php echo PGA_Utils::format_money_currency($total_taxa_contr); ?></strong>
            </li>
            <?php
        }
        ?>
    </ul>
</div>
<p class="hide_after_payment"><?php echo apply_filters('woocommerce_pagseguro_transparent_checkout_message', __('Este pagamento será processado pelo Pagseguro.', 'pagseguro-assinaturas-rcs')); ?></p>
<p><?php echo $plano_description; ?></p>

<form action="" method="post" id="woocommerce-pagseguro-assinaturas-rcs-payment-form">
    <input type="hidden" name="code_pagseguro" id="code_pagseguro" value="<?php echo !empty($_GET['code']) ? $_GET['code'] : ""; ?>" />
    <input type="hidden" name="order_id" id="woocommerce-pagseguro-order-id" value="<?php echo intval($order->id); ?>" />
    <input type="hidden" name="action" id="action" value="finalizarcompra" />
    <input type="hidden" name="url_retorno" id="url_retorno" value="<?php echo $this->get_return_url($order); ?>" />
</form>

<div class="show_after_payment" style="display:none">
    <h3><?php echo sprintf(__('Seu pedido #%d foi pago com sucesso', 'pagseguro-assinaturas-rcs'), $order->id); ?></h3>
    <p><?php echo __('Seu pagamento foi recebido no pelo Pagseguro.', 'pagseguro-assinaturas-rcs'); ?></p>
</div>