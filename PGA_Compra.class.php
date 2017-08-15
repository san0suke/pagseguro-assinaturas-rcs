<?php

class PGA_Compra {

    public $controller;

    function __construct(PGA_Controller $controller) {
        $this->controller = $controller;
    }

    function pagseguro_woocommerce_cart_shipping_packages($packages) {
        foreach ($packages as $key => $package) {
            $packages[$key]['no_cache'] = rand();
        }
        return $packages;
    }

    function permitir_apenas_pga($available_gateways) {
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();

        $permitirOutros = false;
        foreach ($items as $key => $item) {
            $produto_meta = get_post_meta($item['product_id']);

            if (@$produto_meta['_produto_outras_formas_pagseguro'][0] == 'yes') {
                $permitirOutros = true;
            }
        }
        if ($this->controller->gateway->outras_formas_pagseguro == 'yes' && !$permitirOutros) {
            foreach ($available_gateways as $gateway_id => $gateway) {
				if ( method_exists( $this->controller->gateway, 'get_id' ) ) {  
					$controller_gateway_id = $this->controller->gateway->get_id();
				} else {
					$controller_gateway_id = $this->controller->gateway->id;
				}
				if ($gateway_id != $controller_gateway_id) {
					unset($available_gateways[$gateway_id]);
				}
            }
        }
        return $available_gateways;
    }

    function nao_permitir_pga($available_gateways) {
        foreach ($available_gateways as $gateway_id => $gateway) {
			if ( method_exists( $this->controller->gateway, 'get_id' ) ) {  
				$controller_gateway_id = $this->controller->gateway->get_id();
			} else {
				$controller_gateway_id = $this->controller->gateway->id;
			}
            if ($gateway_id == $controller_gateway_id) {
                unset($available_gateways[$gateway_id]);
            }
        }
        return $available_gateways;
    }

    function pagseguro_woocommerce_available_payment_gateways($available_gateways) {
        if (!empty($_GET['order-pay'])) {
            $order = new WC_Order($_GET['order-pay']);
            $itens = $order->get_items();
        } else {
            $itens = WC()->cart->get_cart();
        }
        foreach ($itens as $cart_item_key => $cart_item) {
            $produto_meta = get_post_meta($cart_item['product_id']);

            if ($produto_meta['_is_plano'][0] == 'yes') {
                $available_gateways = $this->permitir_apenas_pga($available_gateways);
            } else {
                $available_gateways = $this->nao_permitir_pga($available_gateways);
            }
        }
        return $available_gateways;
    }

    function verificar_mistura_produtos($produto_id) {
        $add_produto_meta = get_post_meta($produto_id);
        $add_prod_is_plano = $add_produto_meta['_is_plano'][0] != null ? $add_produto_meta['_is_plano'][0] : 'no';

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $cart_produto_meta = get_post_meta($cart_item['product_id']);
            $cart_prod_is_plano = empty($cart_produto_meta['_is_plano']) || $cart_produto_meta['_is_plano'][0] == null ? 'no' : $cart_produto_meta['_is_plano'][0];

            if ($add_prod_is_plano != $cart_prod_is_plano) {
                wc_add_notice(__('Desculpe, não é possível assinar planos e comprar outros tipos de produtos ao mesmo tempo.', 'pagseguro-assinaturas-rcs'), 'error');
                wc_add_notice(__('Finalize a compra para prosseguir.', 'pagseguro-assinaturas-rcs'), 'error');
                return false;
            }

            if ($cart_prod_is_plano == 'yes' || $add_produto_meta == 'yes') {
                wc_add_notice(__('Desculpe, só é permitido 1 plano no carrinho por vez.', 'pagseguro-assinaturas-rcs'), 'error');
                wc_add_notice(__('Finalize a compra para continuar comprando.', 'pagseguro-assinaturas-rcs'), 'error');
                return false;
            }
        }
        return true;
    }

    function pga_woocommerce_add_to_cart_validation($status, $product_id, $quantity) {
        return $this->verificar_mistura_produtos($product_id);
    }

    function pga_woocommerce_product_meta_start() {
        $produto_meta = get_post_meta(get_the_id());

        if ($produto_meta['_is_plano'][0] == 'yes') {
            if ($produto_meta['_taxa_contratacao'][0] > 0) {
                ?>
                <span class="price">
                    <ins>
                        <span class="amount"><?php echo __('Taxa de contratação: ', 'pagseguro-assinaturas-rcs') . PGA_Utils::format_money_currency($produto_meta['_taxa_contratacao'][0]); ?></span>
                    </ins>
                </span>
                <?php
            }
//            echo $this->plano_description($produto_meta);
        }
    }

    function pga_woocommerce_cart_item_subtotal($subtotal, $cart_item, $cart_item_key) {
        $produto_meta = get_post_meta($cart_item['product_id']);

        $total_taxa = !empty($produto_meta['_taxa_contratacao'][0]) ? $produto_meta['_taxa_contratacao'][0] * $cart_item['quantity'] : 0;
        $subtotal .= ' <span class="amount">' . $this->intervalo_description($produto_meta) . '</span>';
        if ($total_taxa > 0 && $produto_meta['_is_plano'][0] == 'yes') {
            $subtotal .= '<br>';
            $subtotal .= PGA_Utils::format_money_currency($total_taxa) . __(' (taxa de contratação)', 'pagseguro-assinaturas-rcs');
        }
        return $subtotal;
    }

    function get_taxa_contratacao_total_carrinho(WC_Order $order = null) {
        $total_taxa = 0;

        if ($order == null) {
            $itens = WC()->cart->get_cart();
        } else {
            $itens = $order->get_items();
        }
        foreach ($itens as $cart_item_key => $cart_item) {
            $produto_meta = get_post_meta($cart_item['product_id']);
            $this->produto_intervalo = $this->intervalo_description($produto_meta);

            $subtotal_taxa = empty($produto_meta['_taxa_contratacao']) ? 0 : $produto_meta['_taxa_contratacao'][0];
            if ($subtotal_taxa > 0 && $produto_meta['_is_plano'][0] == 'yes') {
                $total_taxa += $subtotal_taxa;
            }
        }
        return $total_taxa;
    }

    function pga_woocommerce_cart_subtotal($cart_subtotal, $compound, $cart) {
        $this->total_taxa = $this->get_taxa_contratacao_total_carrinho();

        if(!empty($this->produto_intervalo)) {
            $cart_subtotal .= ' <span class="amount">' . $this->produto_intervalo . '</span>';
        }
        if ($this->total_taxa > 0) {
            $cart_subtotal .= '<br>';
            $cart_subtotal .= PGA_Utils::format_money_currency($this->total_taxa) . __(' (taxa de contratação)', 'pagseguro-assinaturas-rcs');
        }
        return $cart_subtotal;
    }

    function pga_woocommerce_review_order_after_order_total() {
        if ($this->total_taxa > 0) {
            ?>
            <tr class="order-total">
                <th><?php _e('Total taxa de <br> contratação ', 'pagseguro-assinaturas-rcs'); ?></th>
                <td><?php echo PGA_Utils::format_money_currency($this->total_taxa); ?></td>
            </tr>
            <?php
        }
    }

    function pga_woocommerce_my_account_my_orders_actions($actions, $order) {
        if ($order->status != 'cancelled' && !array_key_exists('cancel', $actions)) {
			if ( method_exists( $order, 'get_id' ) ) {
				$actions['force_cancel']['url'] = add_query_arg('force_cancel', $order->get_id(), PGA_Utils::get_current_url());
			} else {
				$actions['force_cancel']['url'] = add_query_arg('force_cancel', $order->id, PGA_Utils::get_current_url());
			}            
            $actions['force_cancel']['name'] = __('Cancelar', 'woocommerce');
        }

        return $actions;
    }

    function my_account_order_status_change() {
        $assinaturas = new PGA_Assinaturas($this->controller);

        if (!empty($_GET['force_cancel']) && is_numeric($_GET['force_cancel'])) {
            $assinaturas->pga_force_cancel($_GET['force_cancel']);
            wp_redirect(get_permalink(wc_get_page_id('myaccount')));
            exit;
        }
    }

    function pga_woocommerce_order_items_table($order) {
        $this->total_taxa = $this->get_taxa_contratacao_total_carrinho($order);
        $this->pga_woocommerce_review_order_after_order_total();
    }

    function pga_woocommerce_product_single_add_to_cart_text($texto, $produto) {
		if ( method_exists( $produto, 'get_id' ) ) {
			$is_plano = get_post_meta($produto->get_id(), '_is_plano', true) == 'yes';
		} else {
			$is_plano = get_post_meta($produto->id, '_is_plano', true) == 'yes';
		}
        if ($is_plano) {
            $label = $this->controller->gateway->label_assinar;
            if ($label) {
                return $label;
            } else {
                return __('Assinar', 'pagseguro-assinaturas-rcs');
            }
        }
        return $texto;
    }

    function pga_woocommerce_is_sold_individually($is_sold, $produto) {
		if ( method_exists( $produto, 'get_id' ) ) {
        	$is_sold = get_post_meta($produto->get_id(), '_is_plano', true) == 'yes';
		} else {
        	$is_sold = get_post_meta($produto->id, '_is_plano', true) == 'yes';
		}        
        return $is_sold;
    }

    function plano_description($produto_meta) {
        $intervalo = @$produto_meta['_intervalo_ciclo_plano'][0] > 0 ? @$produto_meta['_intervalo_ciclo_plano'][0] : 30;
        if ($intervalo % 30 == 0 && $intervalo > 30) {
            $intervalo_descricao = sprintf(__('de %d mêses', 'pagseguro-assinaturas-rcs'), ($intervalo / 30));
        } else if ($intervalo == 30) {
            $intervalo_descricao = __(' mensal', 'pagseguro-assinaturas-rcs');
        } else {
            $intervalo_descricao = sprintf(__('de %d dias', 'pagseguro-assinaturas-rcs'), $intervalo);
        }

        $style = "";
        if ($this->controller->gateway->aviso_produto_plano_font_size) {
            $style = "style='font-size: {$this->controller->gateway->aviso_produto_plano_font_size}px' ";
        }
        if ($this->controller->gateway->aviso_produto_plano) {
            return sprintf(__("<span class='plano_descricao' {$style}>{$this->controller->gateway->aviso_produto_plano}</span>"), $intervalo_descricao);
        }
        return sprintf(__("<span class='plano_descricao' {$style}>Este produto é um plano. O intervalo de pagamento é %s.</span>"), $intervalo_descricao);
    }

    function intervalo_description($produto_meta) {
        if (!empty($produto_meta['_is_plano']) && $produto_meta['_is_plano'][0] == 'yes') {
            $intervalo = @$produto_meta['_intervalo_ciclo_plano'][0] > 0 ? @$produto_meta['_intervalo_ciclo_plano'][0] : 30;
            if ($intervalo % 30 == 0 && $intervalo > 30) {
                $intervalo_descricao = sprintf(__('a cada %d mêses', 'pagseguro-assinaturas-rcs'), ($intervalo / 30));
            } else if ($intervalo == 30) {
                $intervalo_descricao = __(' por mês', 'pagseguro-assinaturas-rcs');
            } else {
                $intervalo_descricao = sprintf(__('a cada %d dias', 'pagseguro-assinaturas-rcs'), $intervalo);
            }
            return $intervalo_descricao;
        }
        return "";
    }

    function get_order_intervalo(WC_Order $order) {
        foreach ($order->get_items() as $item) {
            $produto_meta = get_post_meta($item['product_id']);
            return $this->plano_description($produto_meta);
        }
    }

    function pga_woocommerce_after_shop_loop_item() {
        global $product;
		if ( method_exists( $product, 'get_id' ) ) {
			$post_meta = get_post_meta($product->get_id());
		} else {
			$post_meta = get_post_meta($product->id);
		}		
        if (!empty($post_meta['_is_plano']) && $post_meta['_is_plano'][0] == 'yes') {
//            echo $this->plano_description($post_meta);
        }
    }

    function pga_woocommerce_quantity_input_args($args, $produto) {
		if ( method_exists( $produto, 'get_id' ) ) {
        	$is_plano = get_post_meta($produto->get_id(), '_is_plano', true) == 'yes';
		} else {
        	$is_plano = get_post_meta($produto->id, '_is_plano', true) == 'yes';
		}
        if ($is_plano) {
            $args['input_value'] = 1; // Starting value
            $args['max_value'] = 1; // Maximum value
        }
        return $args;
    }

}
