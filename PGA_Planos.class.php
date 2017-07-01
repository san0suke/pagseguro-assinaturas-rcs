<?php

class PGA_Planos {

    public $controller;

    function __construct(PGA_Controller $controller) {
        $this->controller = $controller;
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

    function save_campos_plano($post_id) {
        $produto_is_plano = isset($_POST['_is_plano']) ? 'yes' : 'no';
        $produto_outras_formas = isset($_POST['_produto_outras_formas_pagseguro']) ? 'yes' : 'no';

        $plano["code"] = $post_id;
        $plano["name"] = $_POST['post_title'];

        update_post_meta($post_id, '_is_plano', esc_attr($produto_is_plano));
        update_post_meta($post_id, '_produto_outras_formas_pagseguro', esc_attr($produto_outras_formas));
    }

    function quick_edit_save_plano($produto) {
        global $retornoPagseguro;
        $this->save_campos_plano($produto->id);
    }

    function bulk_edit_save_plano($produto) {
        if ($_GET['_is_plano']) {
            update_post_meta($produto->id, '_is_plano', esc_attr($_GET['_is_plano']));
        }
        if ($_GET['_produto_outras_formas_pagseguro']) {
            update_post_meta($produto->id, '_produto_outras_formas_pagseguro', esc_attr($_GET['_produto_outras_formas_pagseguro']));
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