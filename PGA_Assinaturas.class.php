<?php

class PGA_Assinaturas {

    public $controller;

    function __construct(PGA_Controller $controller) {
        $this->controller = $controller;
        $this->pga_utils = new PGA_Utils();
    }

    /**
     * Finalização de compra de plano
     */
    function finalizar_compra() {
        if (empty($_POST['code_pagseguro'])) {
            $this->pagamento();
        } else {
            $this->efetivarPagamento();
        }
    }
    
    function efetivarPagamento() {
        global $retornoPagseguro;
        
        $this->order = new WC_Order($_POST['order_id']);
        $this->controller->gateway->change_status($this->order, 'ACTIVE');
        update_post_meta($_POST['order_id'], 'code_pagseguro', $_POST['code_pagseguro']);
    }

    function pagamento() {
        global $retornoPagseguro;

        $this->order = new WC_Order($_POST['order_id']);
        $this->total_frete = $this->order->get_total_shipping();

        foreach ($this->order->get_items() as $key => $item) {
            if (self::is_plano($item)) {
                $this->registrar_assinatura($item);
                $this->total_frete = 0;
            }
        }
        die(json_encode($retornoPagseguro));
    }

    public static function is_plano($item) {
        return get_post_meta($item['product_id'], '_is_plano', true) == 'yes';
    }

    function registrar_assinatura($item) {
        global $retornoPagseguro;

        $produto = new WC_Product($item['product_id']);

        $valor = $this->order->get_total();
        $valorFormatado = PGA_Controller::formatBRL($valor);
        $data['email'] = $this->controller->gateway->email;
        $data['token'] = $this->controller->gateway->token;
        $data['currency'] = 'BRL';
        $data['itemId1'] = $item['product_id'];
        $data['itemDescription1'] = $item['name'];
        $data['itemAmount1'] = PGA_Controller::formatPagseguro($valor);
        $data['itemQuantity1'] = 1;
        $data['itemWeight1'] = empty($produto->get_weight()) ? '0' : $produto->get_weight();
        $data['reference'] = $this->order->id;
        $data['redirectURL'] = $_POST['url_retorno'];
        $data['preApprovalCharge'] = "auto";
        $data['preApprovalName'] = $item['name'];
        $data['preApprovalDetails'] = "Assinatura de '{$item['name']}' no valor mensal de R$$valorFormatado.";
        $data['preApprovalAmountPerPayment'] = PGA_Controller::formatPagseguro($valor);
        $data['preApprovalPeriod'] = "Monthly";

        $data = http_build_query($data);

        $curl = curl_init($this->controller->baseUrlAssinatura . 'request');

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $xml = curl_exec($curl);

        if ($xml == 'Unauthorized') {
            $retornoPagseguro['retorno'] = "Assinatura não autorizada";
        }
        curl_close($curl);

        $xml = simplexml_load_string($xml);

        if (count($xml->error) > 0) {
            $retornoPagseguro['retorno'] = $xml->error;
        }
        $retornoPagseguro['url_redirect'] = $this->controller->urlAssinaturasCheckout . '?code=' . $xml->code;
        $this->controller->gateway->add_to_log($retornoPagseguro, "registrar_assinatura");
        
        $this->controller->gateway->change_status($this->order, 'PENDING');
    }

    function pga_woocommerce_order_status_cancelled($order_id) {
        $order = new WC_Order($order_id);
        foreach ($order->get_items() as $cart_item_key => $cart_item) {
            $meta_values = get_post_meta($cart_item['product_id']);

            if ($meta_values['_is_plano'][0] != 'yes') {
                return false;
            }
            $this->requisicao_cancelar_pagseguro($order_id, $cart_item);
        }

        $this->controller->status_mail($order, 'cancel');
    }
    
    function requisicao_cancelar_pagseguro($order_id, $cart_item) {
        if(!empty(get_post_meta($order_id,"code_pagseguro"))) {
            $code = get_post_meta($order_id,"code_pagseguro")[0];
            $email = $this->controller->gateway->email;
            $token = $this->controller->gateway->token;
            $response = wp_remote_get( $this->controller->baseUrlAssinatura ."cancel/{$code}?email={$email}&token={$token}" );
        }
    }

    function pga_force_cancel($order_id) {
        $order = new WC_Order($order_id);
        $order->cancel_order();
    }

    function pga_woocommerce_admin_order_actions($actions) {
        global $the_order;

        foreach ($the_order->get_items() as $cart_item_key => $cart_item) {
            $meta_values = get_post_meta($cart_item['product_id']);

            if ($meta_values['_is_plano'][0] != 'yes') {
                return $actions;
            }
        }
        if ($the_order->status != 'cancelled') {
            $actions['cancelar'] = array(
                'url' => wp_nonce_url(admin_url('admin-ajax.php?action=pga_cancelar_assinatura&order_id=' . $the_order->id), 'pga-mark-order-cancel'),
                'name' => __('Cancel', 'woocommerce'),
                'action' => "cancelar"
            );
        }

        return $actions;
    }

    function pga_cancelar_assinatura() {
        $this->pga_force_cancel($_GET['order_id']);

        wp_safe_redirect(wp_get_referer());

        die();
    }

    public static function temAssinaturaNoCarrinho() {
        $cart_itens = WC()->cart->get_cart();
        $carrinho_tem_plano = false;
        if (!empty($cart_itens)) {
            foreach ($cart_itens as $cart_item) {
                $meta_values = get_post_meta($cart_item['product_id']);

                if ($meta_values['_is_plano'][0] == 'yes') {
                    $carrinho_tem_plano = true;
                }
            }
        }
        return $carrinho_tem_plano;
    }

}
