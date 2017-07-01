<?php

class PGA_Controller {

    public $urlAssinaturasCheckout = 'https://pagseguro.uol.com.br/v2/pre-approvals/request.html';
    public $baseUrlAssinatura = 'https://ws.pagseguro.uol.com.br/v2/pre-approvals/';
    public $gateway;

    function __construct(PGA_Gateway $pga_gateway) {
        $this->gateway = $pga_gateway;
        if ('yes' == $pga_gateway->sandbox) {
            $this->baseUrlAssinatura = 'https://ws.sandbox.pagseguro.uol.com.br/v2/pre-approvals/';
            $this->urlAssinaturasCheckout = 'https://sandbox.pagseguro.uol.com.br/v2/pre-approvals/request.html';
        }
    }

    function ativar() {
        global $wpdb;

//        $this->createHtaccessAuthorization();

        $instalacao_completa = get_option("pga_instalacao_completa");
        if (!$instalacao_completa) {
            add_option('pga_pga_chave_seguranca', '', '', 'yes');
            add_option('pga_instalacao_completa', false, '', 'yes');
            update_option('pagseguro-assinaturas-rcs-versao', '1.0');

            // $sql = self::readSql(plugins_url( "pagseguro-assinaturas-rcs/sqls/bd.sql" ));
            // try {
            // 	$wpdb->query('START TRANSACTION');
            // 	self::execute_multiline_sql($sql);
            // 	$wpdb->query('COMMIT');
            // } catch (Exception $e) {
            // 	exit("<h2>Não Foi Possível Instalar o Plugin</h2>");
            // }
        } else {
            $arrPluginData = get_plugin_data(WP_PLUGIN_DIR . "/pagseguro-assinaturas-rcs/pagseguro-assinaturas-rcs.php");
            $versaoAtual = get_option('pagseguro-assinaturas-rcs-versao');
            if ($arrPluginData['Version'] > $versaoAtual) {
                self::atualizar($versaoAtual);
            }
        }
    }

    function atualizar($versao) {
        global $wpdb;
        switch ($versao) {
            default:
                break;
        }
        //update_option('pagseguro-assinaturas-rcs', '1.1');
    }

    function desativar() {
        
    }

    function readSql($caminho) {
        $ponteiro = fopen($caminho, "r");
        while (!feof($ponteiro)) {
            $sql .= fgets($ponteiro, 4096);
        }
        fclose($ponteiro);
        return $sql;
    }

    function execute_multiline_sql($sql) {
        global $wpdb;
        $sqlParts = array_filter(explode(";", $sql));
        foreach ($sqlParts as $part) {
            if (trim($part)) {
                $wpdb->query($part);
                $wpdb->print_error();
                if ($wpdb->last_error != '') {
                    $error = new WP_Error("dberror", __("Database query error"), $wpdb->last_error);
                    $wpdb->query("rollback;");
                    return $error;
                }
            }
        }
        return true;
    }

    public static function formatBRL($value) {
        return self::formatNumber($value, 2, 5, ",", ".");
    }

    public static function formatPagseguro($value) {
        return number_format($value, 2, '.', '');
    }

    /**
     * @param type $valor número
     * @param type $minDecimals mínimo de decimais
     * @param type $maxDecimals máximo de decimais
     * @param type $d divisor de decimais
     * @param type $m divisor de milhares
     * @return string
     */
    public static function formatNumber($valor, $minDecimals, $maxDecimals, $d, $m) {
        $valNumber = round($valor, $maxDecimals);

        if (!is_numeric($valNumber)) {
            return "";
        }
        $arrNumber = explode($m, $valNumber);

        if (count($arrNumber) == 1 || strlen($arrNumber[1]) <= $minDecimals) {
            return number_format($valNumber, $minDecimals, $d, $m);
        }

        if (count($arrNumber) > 1) {
            $maxDecimals = strlen($arrNumber[1]) > $maxDecimals ? $maxDecimals : strlen($arrNumber[1]);
            return number_format($valNumber, $maxDecimals, $d, $m);
        } else {
            return number_format($valNumber, $minDecimals, $d, $m);
        }
    }

    function getErrorMessage() {
        global $retornoPagseguro;

        if ($retornoPagseguro['body']) {
            $body = get_object_vars(json_decode($retornoPagseguro['body']));
            if ($body['errors']) {
                foreach ($body['errors'] as $key => $value) {
                    $error = get_object_vars($value);
                    $mensagem .= $error['description'] . '. ';
                }
            }
        } else {
            $mensagem .= $retornoPagseguro['response']['message'];
        }
        return $mensagem;
    }

    function mensagem_pagseguro($url) {
        global $retornoPagseguro;

        $mensagem = $this->getErrorMessage();

        $url = add_query_arg('mensagem_pagseguro', $mensagem, $url);
        return add_query_arg('codigo_pagseguro', $retornoPagseguro['response']['code'], $url);
    }

    function last_requisicao_verificacao_sucesso() {
        global $retornoPagseguro;
        return $this->verificacao_sucesso($retornoPagseguro['response']['code']);
    }

    function verificacao_sucesso($codigo_resposta) {
        return $codigo_resposta >= 200 && $codigo_resposta < 400;
    }

    function admin_enqueue_style_scripts() {
        wp_enqueue_style('custom-admin-style', plugins_url("pagseguro-assinaturas-rcs/css/") . "admin.css");
    }

    function pga_wp_enqueue_scripts() {
        wp_enqueue_style('wc-pagseguro-assinaturas-rcs-checkout', plugins_url('pagseguro-assinaturas-rcs/css/checkout.css', plugin_dir_path(__FILE__)), array(), '', 'all');
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-blockui');

        wp_register_script('pagseguro-assinaturas-rcs-checkout', plugins_url("pagseguro-assinaturas-rcs/js/") . "checkout.js");
        $dadosPagseguro = array('token' => $this->gateway->token,
            'ajax_url' => admin_url('admin-ajax.php'),
            'processando_compra' => __('Aguarde... Você está sendo redirecionado para o Pagseguro', 'pagseguro-assinaturas-rcs')
        );
        if(!empty($_GET['code'])) {
            $dadosPagseguro['processando_compra'] = __('Aguarde... Estamos atualizando o status do seu pagamento', 'pagseguro-assinaturas-rcs');
        }
        wp_localize_script('pagseguro-assinaturas-rcs-checkout', 'arrPagseguro', $dadosPagseguro);
    }

    /**
     * Backwards compatibility with version prior to 2.1.
     *
     * @return object Returns the main instance of WooCommerce class.
     */
    public function woocommerce_instance() {
        if (function_exists('WC')) {
            return WC();
        } else {
            global $woocommerce;
            return $woocommerce;
        }
    }

    function status_mail(WC_Order $order, $status) {
        $user_meta = get_user_meta($order->user_id);
        $email = $user_meta['billing_email'][0];

        $titulo = PGA_WC_Pagseguro_Messages::get_status_titulo($status, $order);
        $mensagem = PGA_WC_Pagseguro_Messages::get_status_message($status, $order);

        $assunto = $titulo;

        $this->send_email($email, $assunto, $titulo, $mensagem);
    }

    /**
     * Send email notification.
     *
     * @param  string $subject Email subject.
     * @param  string $title   Email title.
     * @param  string $message Email message.
     *
     * @return void 
     */
    function send_email($email, $subject, $title, $message) {
        global $woocommerce;

        $mailer = $woocommerce->mailer();

        $mailer->send($email, $subject, $mailer->wrap_message($title, $message));
    }

}
