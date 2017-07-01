<?php

class PGA_Planos {

    public $controller;

    function __construct(PGA_Controller $controller) {
        $this->controller = $controller;
    }

    function issetPlano($codigoPlano) {
        $response = $this->getDetalhesPlano($codigoPlano);
        $codeResponse = $response["response"]["code"];
        return $this->controller->verificacao_sucesso($codeResponse);
    }

    function getDetalhesPlano($codigoPlano) {
        $url = $this->controller->baseUrlAssinatura . 'plans/' . $codigoPlano;
        $params = array(
            'method' => 'GET',
            'body' => $json,
            'sslverify' => false,
            'timeout' => 60,
            'headers' => array(
                'Expect' => '',
                'Content-Type' => 'application/json;charset=UTF-8',
                'Authorization' => 'Basic ' . base64_encode($this->controller->gateway->token . ':' . $this->controller->gateway->key)
            )
        );
        return wp_remote_post($url, $params);
    }

    /**
     * Criaçao de planos
     */
    function criarPlano($arrPlano) {
        global $retornoPagseguro;

        $json = json_encode($arrPlano);

        $url = $this->controller->baseUrlAssinatura . 'plans';
        $params = array(
            'method' => 'POST',
            'body' => $json,
            'sslverify' => false,
            'timeout' => 60,
            'headers' => array(
                'Expect' => '',
                'Content-Type' => 'application/json;charset=UTF-8',
                'Authorization' => 'Basic ' . base64_encode($this->controller->gateway->token . ':' . $this->controller->gateway->key)
            )
        );
        $retornoPagseguro = wp_remote_post($url, $params);
    }

    /**
     * Alterar de planos
     */
    function alterarPlano($arrPlano) {
        global $retornoPagseguro;

        $json = json_encode($arrPlano);

        $url = $this->controller->baseUrlAssinatura . 'plans/' . $arrPlano['code'];
        $this->controller->gateway->add_to_log($json, "dadosAlterarPlano");

        $params = array(
            'method' => 'PUT',
            'body' => $json,
            'sslverify' => false,
            'timeout' => 60,
            'headers' => array(
                'Expect' => '',
                'Content-Type' => 'application/json;charset=UTF-8',
                'Authorization' => 'Basic ' . base64_encode($this->controller->gateway->token . ':' . $this->controller->gateway->key)
            )
        );
        $retornoPagseguro = wp_remote_post($url, $params);

        $this->controller->gateway->add_to_log($retornoPagseguro, "alterarPlano");
    }

    function add_campos_plano() {
        global $woocommerce, $post;

        echo '
		<fieldset class="inline-edit-col-left">
		<div class="options_group">';
        woocommerce_wp_checkbox(
                array(
                    'id' => '_is_plano',
                    'label' => __('É plano de assinatura?', 'pagseguro-assinaturas-rcs'),
                    'desc_tip' => 'true',
                    'description' => __('Selecione se for um produto de assinatura.', 'pagseguro-assinaturas-rcs')
                )
        );

        woocommerce_wp_checkbox(
                array(
                    'id' => '_produto_outras_formas_pagseguro',
                    'label' => __('Outras formas de pagamento?', 'pagseguro-assinaturas-rcs'),
                    'desc_tip' => 'true',
                    'description' => __('Se este campo for selecionado, outras formas de pagamentos serão permitidas.', 'pagseguro-assinaturas-rcs')
                )
        );


//        woocommerce_wp_text_input(
//                array(
//                    'id' => '_taxa_contratacao',
//                    'label' => sprintf(__('Taxa de contratação (%s)', 'pagseguro-assinaturas-rcs'), get_woocommerce_currency_symbol()),
//                    'desc_tip' => 'true',
//                    'description' => __('Taxa de contratação a ser cobrada na assinatura (se houver).', 'pagseguro-assinaturas-rcs'),
//                    'class' => 'short wc_input_price'
//                )
//        );

//        woocommerce_wp_text_input(
//                array(
//                    'id' => '_ciclo_plano',
//                    'label' => __('Ciclos do plano', 'pagseguro-assinaturas-rcs'),
//                    'desc_tip' => 'true',
//                    'description' => __('Quantidade de ciclos (faturas) que a assinatura terá até expirar (se não informar, não haverá expiração)', 'pagseguro-assinaturas-rcs'),
//                    'type' => 'number',
//                    'custom_attributes' => array(
//                        'step' => 'any',
//                        'min' => '0'
//                    )
//                )
//        );

//        woocommerce_wp_text_input(
//                array(
//                    'id' => '_intervalo_ciclo_plano',
//                    'label' => __('Intervalo em dias', 'pagseguro-assinaturas-rcs'),
//                    'desc_tip' => 'true',
//                    'description' => __('Intervalo em dias entre os ciclos. Campo obrigatório se o campo "ciclo do plano" estiver preenchido.', 'pagseguro-assinaturas-rcs'),
//                    'type' => 'number',
//                    'custom_attributes' => array(
//                        'step' => 'any',
//                        'min' => '0'
//                    )
//                )
//        );

//        woocommerce_wp_text_input(
//                array(
//                    'id' => '_trial',
//                    'label' => __('Período trial em dias', 'pagseguro-assinaturas-rcs'),
//                    'desc_tip' => 'true',
//                    'description' => __('Período experimental do plano em dias se houver.', 'pagseguro-assinaturas-rcs'),
//                    'type' => 'number',
//                    'custom_attributes' => array(
//                        'step' => 'any',
//                        'min' => '0'
//                    )
//                )
//        );

//        woocommerce_wp_text_input(
//                array(
//                    'id' => '_limite_assinaturas',
//                    'label' => __('Limite de assinaturas', 'pagseguro-assinaturas-rcs'),
//                    'desc_tip' => 'true',
//                    'description' => __('Não preencher se for ilimitado.', 'pagseguro-assinaturas-rcs'),
//                    'type' => 'number',
//                    'custom_attributes' => array(
//                        'step' => 'any',
//                        'min' => '0'
//                    )
//                )
//        );

//        woocommerce_wp_text_input(
//                array(
//                    'id' => '_envios_por_ciclo',
//                    'label' => __('Envios por ciclo', 'pagseguro-assinaturas-rcs'),
//                    'desc_tip' => 'true',
//                    'description' => __('Este campo é um multiplicador do valor de envio. Se for enviar o produto mais de uma vez por ciclo, preencha este campo.', 'pagseguro-assinaturas-rcs'),
//                    'type' => 'number',
//                    'custom_attributes' => array(
//                        'step' => 'any',
//                        'min' => '0'
//                    )
//                )
//        );

//        woocommerce_wp_select(
//                array(
//                    'id' => '_status_pagseguro',
//                    'label' => __('Status do plano no Pagseguro', 'pagseguro-assinaturas-rcs'),
//                    'desc_tip' => 'true',
//                    'description' => __('Não serão permitidos pagamentos de planos inativos no pagseguro.', 'pagseguro-assinaturas-rcs'),
//                    'options' => array(
//                        '1' => __('Ativo', 'woocommerce'),
//                        '0' => __('Inativo', 'woocommerce'),
//                    )
//                )
//        );

        echo '</div>
		</fieldset>';
    }

    /**
     * Custom quick edit - form
     *
     * @access public
     * @param mixed $column_name
     * @param mixed $post_type
     */
    function quick_edit($column_name, $post_type) {
        if ('price' != $column_name || 'product' != $post_type) {
            return;
        }

        include( 'includes/product-quick-edit.php' );
    }

    /**
     * Custom bulk edit - form
     *
     * @access public
     * @param mixed $column_name
     * @param mixed $post_type
     */
    function bulk_edit($column_name, $post_type) {
        if ('price' != $column_name || 'product' != $post_type) {
            return;
        }

        include( 'includes/product-bulk-edit.php' );
    }

    function get_real_price($post) {
        if ($post['_sale_price'] < $post['_regular_price'] && $post['_sale_price'] > 0) {
            if ($post["_sale_price_dates_from"] && $post["_sale_price_dates_to"]) {
                $dataAtual = new DateTime("now");
                $dateI = new DateTime($post["_sale_price_dates_from"]);
                $dateF = new DateTime($post["_sale_price_dates_to"]);
                if ($dataAtual >= $dateI && $dataAtual <= $dateF) {
                    $value = $post['_sale_price'];
                } else {
                    $value = $post['_regular_price'];
                }
            } else {
                $value = $post['_sale_price'];
            }
        } else {
            $value = $post['_regular_price'];
        }
        return $this->controller->pagseguro_value_formated($value);
    }

    function save_campos_plano($post_id) {
        $produto_is_plano = isset($_POST['_is_plano']) ? 'yes' : 'no';
        $produto_outras_formas = isset($_POST['_produto_outras_formas_pagseguro']) ? 'yes' : 'no';

        $plano["code"] = $post_id;
        $plano["name"] = $_POST['post_title'];

        if ($_POST['_taxa_contratacao']) {
            $plano["setup_fee"] = $this->controller->pagseguro_value_formated($_POST['_taxa_contratacao']);
        }
        if (is_numeric($_POST['_limite_assinaturas'])) {
            $plano["max_qty"] = $_POST['_limite_assinaturas'];
        } else if (isset($_POST['_limite_assinaturas'])) {
            $plano["max_qty"] = 0;
        }
        $plano["status"] = $_POST['_status_pagseguro'] == 1 ? 'ACTIVE' : 'INACTIVE';
        if ($_POST['_intervalo_ciclo_plano']) {
            $plano["interval"]["unit"] = "DAY";
            $plano["interval"]["length"] = is_numeric($_POST['_intervalo_ciclo_plano']) ? $_POST['_intervalo_ciclo_plano'] : 0;
        }
        $plano["billing_cycles"] = is_numeric($_POST['_ciclo_plano']) ? $_POST['_ciclo_plano'] : 0;
        $produto_trial = is_numeric($_POST['_trial']) ? $_POST['_trial'] : 0;
        if ($produto_trial > 0) {
            $plano["trial"]["days"] = $produto_trial;
            $plano["trial"]["enabled"] = true;
        } else {
            $plano["trial"]["days"] = 1;
            $plano["trial"]["enabled"] = false;
        }
        $envios_por_assinatura = is_numeric($_POST['_envios_por_ciclo']) && $_POST['_envios_por_ciclo'] > 0 ? $_POST['_envios_por_ciclo'] : 1;

        update_post_meta($post_id, '_is_plano', esc_attr($produto_is_plano));
        update_post_meta($post_id, '_produto_outras_formas_pagseguro', esc_attr($produto_outras_formas));
        update_post_meta($post_id, '_ciclo_plano', esc_attr($plano["billing_cycles"]));
        update_post_meta($post_id, '_taxa_contratacao', esc_attr($_POST['_taxa_contratacao']));
        update_post_meta($post_id, '_intervalo_ciclo_plano', esc_attr($plano["interval"]["length"]));
        update_post_meta($post_id, '_trial', esc_attr($produto_trial));
        update_post_meta($post_id, '_status_pagseguro', esc_attr($_POST['_status_pagseguro']));
        update_post_meta($post_id, '_envios_por_ciclo', esc_attr($envios_por_assinatura));
        update_post_meta($post_id, '_limite_assinaturas', esc_attr($_POST['_limite_assinaturas']));
    }

    function quick_edit_save_plano($produto) {
        global $retornoPagseguro;
        $this->save_campos_plano($produto->id);
        if (!$this->controller->verificacao_sucesso($retornoPagseguro['response']['code'])) {
            echo sprintf(__('Não foi possível realizar a integração com Pagseguro. Causa: %s', 'pagseguro-assinaturas-rcs'), $this->controller->getErrorMessage());
            exit;
        }
    }

    function bulk_edit_save_plano($produto) {
        if ($_GET['_is_plano']) {
            update_post_meta($produto->id, '_is_plano', esc_attr($_GET['_is_plano']));
        }

        $meta_values = get_post_meta($produto->id);

        if ($meta_values['_is_plano'][0] == 'yes') {
            $plano["code"] = $produto->id;
            $plano["name"] = $produto->get_title();
            $plano["amount"] = $this->controller->pagseguro_value_formated($produto->get_price());
            $plano["setup_fee"] = $this->controller->pagseguro_value_formated($meta_values['_taxa_contratacao'][0]);
            $plano["max_qty"] = $meta_values['_limite_assinaturas'][0];
            $plano["status"] = $meta_values['_status_pagseguro'][0] == 1 ? 'ACTIVE' : 'INACTIVE';
            if ($meta_values['_intervalo_ciclo_plano'][0]) {
                $plano["interval"]["unit"] = "DAY";
                $plano["interval"]["length"] = is_numeric($meta_values['_intervalo_ciclo_plano'][0]) ? $meta_values['_intervalo_ciclo_plano'][0] : 0;
            }
            $plano["billing_cycles"] = is_numeric($meta_values['_ciclo_plano'][0]) ? $meta_values['_ciclo_plano'][0] : 0;
            if ($meta_values['_trial'][0] > 0) {
                $plano["trial"]["days"] = $meta_values['_trial'][0];
                $plano["trial"]["enabled"] = true;
            } else {
                $plano["trial"]["days"] = 1;
                $plano["trial"]["enabled"] = false;
            }

            if ($this->issetPlano($produto->id)) {
                $this->alterarPlano($plano);
            } else {
                $this->criarPlano($plano);
            }
            add_filter('redirect_post_location', array($this->controller, 'mensagem_pagseguro'));
        }
    }

    function pga_admin_notices() {
        if (!empty($_GET['codigo_pagseguro'])) {
            $mensagem = $_GET['mensagem_pagseguro'];
            if ($_GET['codigo_pagseguro'] == '401') {
                $mensagem = __('O acesso não foi autorizado (401)', 'pagseguro-assinaturas-rcs');
            }
            if ($this->controller->verificacao_sucesso($_GET['codigo_pagseguro'])) {
                ?>
                <div class="updated">
                    <p><?php _e('<b>Pagseguro:</b> plano atualizado com sucesso!', 'pagseguro-assinaturas-rcs'); ?></p>
                </div>
                <?php
            } else {
                ?>
                <div class="error">
                    <p><?php echo sprintf(__('<b>Pagseguro:</b> %s', 'pagseguro-assinaturas-rcs'), $mensagem); ?></p>
                </div>
                <?php
            }
        }
    }

    function admin_edit_plano_foot() {
        $slug = 'product';

        # load only when editing a book
        if ((isset($_GET['page']) && $_GET['page'] == $slug) || (isset($_GET['post_type']) && $_GET['post_type'] == $slug)) {
            echo '<script type="text/javascript" src="', plugins_url('js/quick-edit-plano.js', __FILE__), '"></script>';
        }
    }

    function product_plano_columns($column, $post_id) {
        switch ($column) {
            case 'name' :
                $arr_meta_values = get_post_meta($post_id);
                echo '
						<div class="hidden" id="pga_inline_' . $post_id . '">';
                foreach ($arr_meta_values as $key => $meta_values) {
                    foreach ($meta_values as $key_val => $value) {
                        echo '<div class="' . $key . '">' . $value . '</div>';
                    }
                }
                echo '	</div>
					';
                break;
        }
    }

}
?>