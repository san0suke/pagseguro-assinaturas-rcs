<?php

/*
  Plugin Name: Pagseguro Assinaturas
  Plugin URI:
  Description: Forma de pagamento por assinaturas (recorrente) do Pagseguro
  Version: 1.0
  Author: Robson Cesar de Siqueira
  Author URI: 
 */

function loadCreboxPagseguroAssinaturas() {
    if (!class_exists('WC_Payment_Gateway')) {
        add_action('admin_notices', 'woocommerce_woocommerce_missing_notice');
        return false;
    }
    require_once("includes/class-wc-pagseguro-messages.php");
    require_once("PGA_Utils.class.php");
    require_once("PGA_Gateway.class.php");
    require_once("PGA_Controller.class.php");
    require_once("PGA_Assinaturas.class.php");
    require_once("PGA_Planos.class.php");
    require_once("PGA_Compra.class.php");

    $pga_gateway = new PGA_Gateway();
    $pga_controller = new PGA_Controller($pga_gateway);
    $planos = new PGA_Planos($pga_controller);
    $compra = new PGA_Compra($pga_controller);
    $assinaturas = new PGA_Assinaturas($pga_controller);

    //não permite compras sem registro
//	update_option( 'woocommerce_enable_guest_checkout', 'no' );
//	update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
    //gateway wordpress actions
    $pga_gateway->add_gateway_actions();

    // Display Fields
    add_action('woocommerce_product_options_general_product_data', array($planos, 'add_campos_plano'));

    //Campos da Edição rápida
    add_action('quick_edit_custom_box', array($planos, 'quick_edit'), 10, 3);
    add_action('bulk_edit_custom_box', array($planos, 'bulk_edit'), 10, 2);

    //Preenche os campos da edição rápida
    add_action('admin_footer-edit.php', array($planos, 'admin_edit_plano_foot'), 11);
    add_action('manage_product_posts_custom_column', array($planos, 'product_plano_columns'), 10, 2);

    // Save Fields
    add_action('woocommerce_process_product_meta', array($planos, 'save_campos_plano'));
    add_action('woocommerce_product_quick_edit_save', array($planos, 'quick_edit_save_plano'));
    
    // Avisos
    add_action('admin_notices', array($planos, 'pga_admin_notices'));
    $pga_gateway->add_admin_notices();

    //Estilos e scripts do admin
    add_action('admin_enqueue_scripts', array($pga_controller, 'admin_enqueue_style_scripts'));

    //Estilos e scripts da loja
    add_action('wp_enqueue_scripts', array($pga_controller, 'pga_wp_enqueue_scripts'));

    //Sempre recarregar o frete
    add_filter('woocommerce_cart_shipping_packages', array($compra, 'pagseguro_woocommerce_cart_shipping_packages'));

    //Se for plano remove as outras formas de pagamento
    add_filter('woocommerce_available_payment_gateways', array($compra, 'pagseguro_woocommerce_available_payment_gateways'));

    //Ajax finalizando compra
    add_action('wp_ajax_finalizarcompra', array($assinaturas, 'finalizar_compra'));
    add_action('wp_ajax_nopriv_finalizarcompra', array($assinaturas, 'finalizar_compra'));

    //Proibe mistura de produtos
    add_filter('woocommerce_add_to_cart_validation', array($compra, 'pga_woocommerce_add_to_cart_validation'), 10, 3);

    //Proibe mistura de produtos
    add_filter('woocommerce_add_to_cart_validation', array($compra, 'pga_woocommerce_add_to_cart_validation'), 10, 3);

    //Exibe a taxa de contratação na página do carrinho
    add_filter('woocommerce_product_meta_start', array($compra, 'pga_woocommerce_product_meta_start'), 10, 3);

    //Exibe a taxa de contratação no item do carrinho na página do carrinho
    add_filter('woocommerce_cart_item_subtotal', array($compra, 'pga_woocommerce_cart_item_subtotal'), 10, 3);

    //Exibe a taxa de contratação no subtotal do carrinho na página do carrinho
    add_filter('woocommerce_cart_subtotal', array($compra, 'pga_woocommerce_cart_subtotal'), 10, 3);

    //Exibe a taxa de contratação no total do carrinho na página do carrinho
    add_filter('woocommerce_review_order_after_order_total', array($compra, 'pga_woocommerce_review_order_after_order_total'), 10, 3);

    //Opções -> Suspender, reativar 
    add_filter('woocommerce_my_account_my_orders_actions', array($compra, 'pga_woocommerce_my_account_my_orders_actions'), 10, 3);

    //Controla cancelamento de ordens
    add_action('woocommerce_order_status_cancelled', array($assinaturas, 'pga_woocommerce_order_status_cancelled'), 10, 1);

    // Simple products
    add_filter('woocommerce_quantity_input_args', array($compra, 'pga_woocommerce_quantity_input_args'), 10, 2);

    //Muda o label do botão Add to cart se for plano
    add_filter('woocommerce_product_single_add_to_cart_text', array($compra, 'pga_woocommerce_product_single_add_to_cart_text'), 10, 2);
    add_filter('woocommerce_product_add_to_cart_text', array($compra, 'pga_woocommerce_product_single_add_to_cart_text'), 10, 2);

    //Controla a mudança de status pelo usuário
    add_action('init', array($compra, 'my_account_order_status_change'));

    //Aviso de taxa de contratação após a compra
    add_action('woocommerce_order_items_table', array($compra, 'pga_woocommerce_order_items_table'), 10, 1);

    //Botões de ações no admin
    add_action('woocommerce_admin_order_actions', array($assinaturas, 'pga_woocommerce_admin_order_actions'), 10, 1);

    //Ajax mudança de status no admin
    add_action('wp_ajax_pga_cancelar_assinatura', array($assinaturas, 'pga_cancelar_assinatura'));

    //Hook product description
    add_action('woocommerce_after_shop_loop_item', array($compra, 'pga_woocommerce_after_shop_loop_item'), 10, 1);
}

add_action('plugins_loaded', 'loadCreboxPagseguroAssinaturas', 0);

function woocommerce_woocommerce_missing_notice() {
    echo '<div class="error"><p>' . sprintf(__('Pagseguro Assinaturas depende da última versão do %s para funcionar!', 'pagseguro-assinaturas-rcs'), '<a href="https://github.com/claudiosmweb/woocommerce-extra-checkout-fields-for-brazil/" target="_blank">WooCommerce</a>') . '</p></div>';
}
