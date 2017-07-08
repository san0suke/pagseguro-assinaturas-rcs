<?php

class PGA_Gateway extends WC_Payment_Gateway {

    public $controller;

    /**
     * Constructor for the gateway.
     *
     * @return void
     */
    public function __construct() {
        $this->id = 'pagseguroassinaturas';
        $this->icon = apply_filters('woocommerce_' . $this->id . '_icon', plugins_url('pagseguro-assinaturas-rcs/images/pagseguro.png', plugin_dir_path(__FILE__)));
        $this->has_fields = false;
        $this->method_title = __('Pagseguro Assinaturas', 'pagseguro-assinaturas-rcs');

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Display options.
        $this->title = $this->get_option('title', __('Pagseguro Assinaturas', 'pagseguro-assinaturas-rcs'));
        $this->description = $this->get_option('description', __('Forma de pagamento Pagseguro para assinaturas', 'pagseguro-assinaturas-rcs'));

        // API options.
        $this->api = $this->get_option('api', 'tc');
        $this->token = $this->get_option('token');
        $this->email = $this->get_option('email');
        $this->token_notificacao = $this->get_option('token_notificacao');
        $this->outras_formas_pagseguro = $this->get_option('outras_formas_pagseguro', 'yes');

        // Debug options.
        $this->sandbox = $this->get_option('sandbox');
        $this->debug = $this->get_option('debug');

        //Personalização
        $this->label_assinar = $this->get_option('label_assinar');
        $this->aviso_produto_plano = $this->get_option('aviso_produto_plano');
        $this->aviso_produto_plano_font_size = $this->get_option('aviso_produto_plano_font_size');

        $this->faturas_desconto_texto = $this->get_option('faturas_desconto_texto');
        $this->entrega_gratis_texto = $this->get_option('entrega_gratis_texto');
        $this->entrega_cobrada_texto = $this->get_option('entrega_cobrada_texto');

        // Active logs.
        if ('yes' == $this->debug) {
            if (class_exists('WC_Logger')) {
                $this->log = new WC_Logger();
            } else {
                $this->log = $this->woocommerce_instance()->logger();
            }
        }
        $this->controller = new PGA_Controller($this);
    }

    function add_gateway_actions() {
        // Actions.
        add_action('woocommerce_api_pga_gateway', array($this, 'check_ipn_response'));
        add_action('valid_pga_gateway_ipn_request', array($this, 'successful_request'));
        add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
        add_filter('woocommerce_payment_gateways', array($this, 'add_pagseguro_assinaturas_gateway'));
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_register_post', array($this, 'pga_wc_custom_validate_register_fields'), 10, 3);
    }

    /**
     * Initialise Gateway Settings Form Fields.
     *
     * @return void
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Habilitar/Desabilitar', 'pagseguro-assinaturas-rcs'),
                'type' => 'checkbox',
                'label' => __('Habilitar gateway Pagseguro Assinaturas', 'pagseguro-assinaturas-rcs'),
                'default' => 'yes'
            ),
            'outras_formas_pagseguro' => array(
                'title' => __('Outras Formas de Pagamento?', 'pagseguro-assinaturas-rcs'),
                'type' => 'checkbox',
                'label' => __('Desabilitar outras formas de pagamento enquanto o cliente estiver comprando uma assinatura', 'pagseguro-assinaturas-rcs'),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __('Título', 'pagseguro-assinaturas-rcs'),
                'type' => 'text',
                'description' => __('Isto controla o título que o usuário vê durante o checkout.', 'pagseguro-assinaturas-rcs'),
                'desc_tip' => true,
                'default' => __('Pagseguro Assinaturas', 'pagseguro-assinaturas-rcs')
            ),
            'description' => array(
                'title' => __('Description', 'woocommerce'),
                'type' => 'textarea',
                'description' => __('Isto controla a descrição que o usuário vê durante o checkout.', 'pagseguro-assinaturas-rcs'),
                'default' => __('Pagar via Pagseguro Assinaturas', 'pagseguro-assinaturas-rcs')
            ),
            'api_section' => array(
                'title' => __('API de pagamento', 'pagseguro-assinaturas-rcs'),
                'type' => 'title',
                'description' => '',
            ),
//            'api' => array(
//                'title' => __('API de pagamento Pagseguro Assinaturas', 'pagseguro-assinaturas-rcs'),
//                'type' => 'select',
//                'default' => 'form',
//                'options' => array(
//                    'tc' => __('Checkout Transparente', 'pagseguro-assinaturas-rcs')
//                )
//            ),
            'url_notificacoes' => array(
                'title' => __('URL de Notificações', 'pagseguro-assinaturas-rcs'),
                'type' => 'title',
                'description' => __('Defina a URL de notficações no Pagseguro: ', 'pagseguro-assinaturas-rcs') . " <b>" . PGA_Utils::getIPN_URL() . "</b>",
                'desc_tip' => true,
                'default' => ''
            ),
            'token' => array(
                'title' => __('Token de acesso', 'pagseguro-assinaturas-rcs'),
                'type' => 'text',
                'description' => __('Digite seu Token de Accesso; ele é necessário para finalizar o pagamento.', 'pagseguro-assinaturas-rcs'),
                'desc_tip' => true,
                'default' => ''
            ),
            'email' => array(
                'title' => __('Email do Pagseguro', 'pagseguro-assinaturas-rcs'),
                'type' => 'text',
                'description' => __('Digite seu email do pagseguro; ele é necessário para finalizar o pagamento.', 'pagseguro-assinaturas-rcs'),
                'desc_tip' => true,
                'default' => ''
            ),
//            'token_notificacao' => array(
//                'title' => __('Token de notificação', 'pagseguro-assinaturas-rcs'),
//                'type' => 'text',
//                'description' => sprintf(__('Digite seu token de notificação; ele é necessário para que o Pagseguro possa enviar notificações para loja. %sClique para saber o seu%s.', 'pagseguro-assinaturas-rcs'), '<a href="https://conta.pagseguro.com.br/configurations/subscriptions_preferences" target="_blank">', '</a>'),
//                'default' => ''
//            ),
            'testing' => array(
                'title' => __('Testes do Gateway', 'pagseguro-assinaturas-rcs'),
                'type' => 'title',
                'description' => '',
            ),
            'sandbox' => array(
                'title' => __('Pagseguro Assinaturas sandbox', 'pagseguro-assinaturas-rcs'),
                'type' => 'checkbox',
                'label' => __('Ativa o Pagseguro Assinaturas sandbox', 'pagseguro-assinaturas-rcs'),
                'default' => 'no',
                'description' => sprintf(__('O Pagseguro Assinaturas sandbox pode ser usado para testar pagamentos. Registre-se como vendedor <a href="%s">aqui</a>.', 'pagseguro-assinaturas-rcs'), 'https://sandbox.pagseguro.uol.com.br/'),
            ),
            'debug' => array(
                'title' => __('Debug Log', 'pagseguro-assinaturas-rcs'),
                'type' => 'checkbox',
                'label' => __('Habilitar logs', 'pagseguro-assinaturas-rcs'),
                'default' => 'no',
                'description' => sprintf(__('Registrar eventos Pagseguro, tais como solicitações de API, dentro %s', 'pagseguro-assinaturas-rcs'), '<code>woocommerce/logs/pagseguro-assinaturas-rcs-' . sanitize_file_name(wp_hash('pagseguro-assinaturas-rcs')) . '.txt</code>'),
            ),
            'personalizar' => array(
                'title' => __('Personalizar', 'pagseguro-assinaturas-rcs'),
                'type' => 'title',
                'description' => '',
            ),
            'label_assinar' => array(
                'type' => 'text',
                'title' => __('Texto do botão assinar', 'pagseguro-assinaturas-rcs'),
                'label' => __('Texto do botão assinar', 'pagseguro-assinaturas-rcs'),
                'default' => __('Assinar', 'pagseguro-assinaturas-rcs'),
                'desc_tip' => true,
                'description' => sprintf(__('Este texto será exibido nos botões de adicionar ao carrinho de produtos do tipo plano.', 'pagseguro-assinaturas-rcs')),
            ),
            'aviso_produto_plano' => array(
                'type' => 'text',
                'title' => __('Texto avisando que o produto é um plano', 'pagseguro-assinaturas-rcs'),
                'label' => __('Texto avisando que o produto é um plano', 'pagseguro-assinaturas-rcs'),
                'default' => __('Este produto é um plano. O intervalo de pagamento é %s.', 'pagseguro-assinaturas-rcs'),
                'desc_tip' => true,
                'description' => sprintf(__('Este texto será exibido após os botões de adicionar ao carrinho produtos do tipo plano e também na descrição do produto, para explicar que o produto é um plano e qual sua periodicidade.', 'pagseguro-assinaturas-rcs')),
            ),
            'faturas_desconto_texto' => array(
                'type' => 'text',
                'title' => __('Texto "Faturas com Desconto"', 'pagseguro-assinaturas-rcs'),
                'label' => __('Texto "Faturas com Desconto"', 'pagseguro-assinaturas-rcs'),
                'default' => __('Faturas com Desconto', 'pagseguro-assinaturas-rcs'),
                'desc_tip' => true,
                'description' => sprintf(__('Este texto será exibido quando o campo "Repetições na assinatura" do cupom estiver preenchido.', 'pagseguro-assinaturas-rcs')),
            ),
            'entrega_gratis_texto' => array(
                'type' => 'text',
                'title' => __('Texto "Entrega Grátis"', 'pagseguro-assinaturas-rcs'),
                'label' => __('Texto "Entrega Grátis"', 'pagseguro-assinaturas-rcs'),
                'default' => __('Entrega Grátis', 'pagseguro-assinaturas-rcs'),
                'desc_tip' => true,
                'description' => sprintf(__('Este texto será exibido quando o campo "Habilitar frete grátis" do cupom estiver preenchido.', 'pagseguro-assinaturas-rcs')),
            ),
            'entrega_cobrada_texto' => array(
                'type' => 'text',
                'title' => __('Texto "A entrega não será cobrada até a fatura nº"', 'pagseguro-assinaturas-rcs'),
                'label' => __('Texto "A entrega não será cobrada até a fatura nº"', 'pagseguro-assinaturas-rcs'),
                'default' => __('A entrega não será cobrada até a fatura nº', 'pagseguro-assinaturas-rcs'),
                'desc_tip' => true,
                'description' => sprintf(__('Este texto será exibido quando o campo "Habilitar frete grátis" do cupom estiver preenchido.', 'pagseguro-assinaturas-rcs')),
            ),
            'aviso_produto_plano_font_size' => array(
                'type' => 'text',
                'title' => __('Tamanho da fonte para o aviso de que o produto é um plano', 'pagseguro-assinaturas-rcs'),
                'label' => __('Tamanho da fonte para o aviso de que o produto é um plano', 'pagseguro-assinaturas-rcs'),
                'default' => '',
                'desc_tip' => true,
                'description' => sprintf(__('Esta é o tamanho da fonte do texto que será exibido após os botões de adicionar ao carrinho produtos do tipo plano e também na descrição do produto, para explicar que o produto é um plano e qual sua periodicidade.', 'pagseguro-assinaturas-rcs')),
            ),
            'nota' => array(
                'title' => __('Nota', 'pagseguro-assinaturas-rcs'),
                'type' => 'title',
                'description' => __('Este plugin faz distinção entre produtos comuns e planos, e não é possível para o cliente comprar ambos ao mesmo tempo. Para produtos comuns é preciso outro gateway de pagamento.', 'pagseguro-assinaturas-rcs'),
            ),
        );
    }

    /**
     * Displays notifications when the admin has something wrong with the configuration.
     *
     * @return void
     */
    function add_admin_notices() {
        if (is_admin()) {
            // Valid for use.
            // Checks if token is not empty.
            if (empty($this->token) && empty($_POST['woocommerce_pagseguroassinaturas_token'])) {
                add_action('admin_notices', array($this, 'token_missing_message'));
            }

            // Checks if key is not empty.
            if (empty($this->email) && empty($_POST['woocommerce_pagseguroassinaturas_email'])) {
                add_action('admin_notices', array($this, 'email_missing_message'));
            }

            // Checks that the currency is supported
            if (!$this->using_supported_currency()) {
                add_action('admin_notices', array($this, 'currency_not_supported_message'));
            }
        }
    }

    /**
     * Returns a bool that indicates if currency is amongst the supported ones.
     *
     * @return bool
     */
    public function using_supported_currency() {
        return ( 'BRL' == get_woocommerce_currency() );
    }

    function add_pagseguro_assinaturas_gateway($methods) {
        $methods[] = 'PGA_Gateway';
        return $methods;
    }

    /**
     * Adds error message when not configured the token.
     *
     * @return string Error Mensage.
     */
    public function token_missing_message() {
        echo '<div class="error"><p><strong>' . __('Pagseguro Assinaturas desabilitado', 'pagseguro-assinaturas-rcs') . '</strong>: ' . sprintf(__('Informe o Token de acesso. %s', 'pagseguro-assinaturas-rcs'), '<a href="' . $this->admin_url() . '">' . __('Clique aqui para configurar!', 'pagseguro-assinaturas-rcs') . '</a>') . '</p></div>';
    }

    /**
     * Adds error message when not configured the key.
     *
     * @return string Error Mensage.
     */
    public function email_missing_message() {
        echo '<div class="error"><p><strong>' . __('Pagseguro Assinaturas desabilitado', 'pagseguro-assinaturas-rcs') . '</strong>: ' . sprintf(__('Informe o email do Pagseguro. %s', 'pagseguro-assinaturas-rcs'), '<a href="' . $this->admin_url() . '">' . __('Clique aqui para configurar!', 'pagseguro-assinaturas-rcs') . '</a>') . '</p></div>';
    }

    /**
     * Adds error message when an unsupported currency is used.
     *
     * @return string Error Mensage.
     */
    public function currency_not_supported_message() {
        echo '<div class="error"><p><strong>' . __('Pagseguro Assinaturas desabilitado', 'pagseguro-assinaturas-rcs') . '</strong>: ' . sprintf(__('Moeda <code>%s</code> não é suportada. Funciona apenas com <code>BRL</code> (Real Brasileiro).', 'pagseguro-assinaturas-rcs'), get_woocommerce_currency()) . '</p></div>';
    }

    /**
     * Gets the admin url.
     *
     * @return string
     */
    function admin_url() {
        if (version_compare(WOOCOMMERCE_VERSION, '2.1', '>=')) {
            return admin_url('admin.php?page=wc-settings&tab=checkout&section=pga_gateway');
        }

        return admin_url('admin.php?page=woocommerce_settings&tab=payment_gateways&section=PGA_Gateway');
    }

    /**
     * Returns a value indicating the the Gateway is available or not. It's called
     * automatically by WooCommerce before allowing customers to use the gateway
     * for payment.
     *
     * @return bool
     */
    public function is_available() {
        $api = (!empty($this->token) && !empty($this->email) );

        $available = ( 'yes' == $this->settings['enabled'] ) && $api && $this->using_supported_currency();

        return $available;
    }

    /**
     * Add error message in checkout.
     *
     * @param string $message Error message.
     *
     * @return string         Displays the error message.
     */
    public function add_error($message) {
        if (version_compare(WOOCOMMERCE_VERSION, '2.1', '>=')) {
            wc_add_notice($message, 'error');
        } else {
            $this->woocommerce_instance()->add_error($message);
        }
    }

    /**
     * Adds error message when an unsupported currency is used.
     *
     * @return string Error Mensage.
     */
    public function usuario_nao_registrado_message() {
        echo '<div class="error"><p><strong>' . __('Registre-se para efetuar a compra', 'pagseguro-assinaturas-rcs') . '</strong></p></div>';
    }

    /**
     * Output for the order received page.
     *
     * @param  object $order Order data.
     *
     * @return void
     */
    public function receipt_page($order) {
        echo $this->generate_transparent_checkout($order);
    }

    /**
     * Generate the form.
     *
     * @param int     $order_id Order ID.
     *
     * @return string           Payment form.
     */
    protected function generate_transparent_checkout($order_id) {
        $order = new WC_Order($order_id);
        $pgaUtils = new PGA_Utils();

        if ('yes' == $this->debug) {
            $this->log->add('pagseguro', 'Generating transparent checkout for order ' . $order->id);
        }

        ob_start();
        include 'includes/transparent-checkout.php';
        $html = ob_get_clean();

        wp_enqueue_script('pagseguro-assinaturas-rcs-checkout');
        return $html;
    }

    /**
     * Process the payment and return the result.
     *
     * @param int    $order_id Order ID.
     *
     * @return array           Redirect.
     */
    public function process_payment($order_id) {

        $order = new WC_Order($order_id);

        if (version_compare(WOOCOMMERCE_VERSION, '2.1', '>=')) {
            return array(
                'result' => 'success',
                'redirect' => $order->get_checkout_payment_url(true)
            );
        } else {
            return array(
                'result' => 'success',
                'redirect' => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
            );
        }
    }

    function successful_request($dadosPagseguro) {
        $arrAssinatura = $this->consultarAssinatura($dadosPagseguro);
    }

    function consultarAssinatura($dadosPagseguro) {
        if (!empty($dadosPagseguro['notificationType']) && $dadosPagseguro['notificationType'] == 'preApproval') {
            $data['email'] = $this->email;
            $data['token'] = $this->token;

            $data = http_build_query($data);

            $curl = curl_init($this->controller->baseUrlAssinatura . "notifications/{$dadosPagseguro['notificationCode']}?{$data}");

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $xml = curl_exec($curl);

            if ($xml == "Not Found") {
                echo "Não encontrado";
                exit;
            }
            if ($xml == 'Unauthorized') {
                echo "Não autorizada consultarAssinatura";
                exit;
            }
            curl_close($curl);

            $xml = simplexml_load_string($xml);

            if (count($xml->error) > 0) {
                echo "Erro do pagseguro: {$xml->error}";
                exit;
            }

            $status = current($xml->status);
            $order = new WC_Order(current($xml->reference));
            $this->change_status($order, $status);

            echo "Status gravado com sucesso: $status";
            exit;
        }
        return true;
    }

    function change_status(WC_Order $order, $codigo_status) {
        global $retornoPagseguro;
        $user_meta = get_user_meta($order->user_id);
        $email = $user_meta['billing_email'][0];

        $titulo = PGA_WC_Pagseguro_Messages::get_status_titulo($codigo_status, $order);
        $mensagem = PGA_WC_Pagseguro_Messages::get_status_message($codigo_status, $order);

        switch ((string) $codigo_status) {
            case 'INITIATED':
                $order->update_status('on-hold', $mensagem);
                break;
            case 'PENDING':
                $order->update_status('on-hold', $mensagem);
                break;
            case 'ACTIVE':
                $order->add_order_note($mensagem);
                $order->payment_complete();
                break;
            case 'PAYMENT_METHOD_CHANGE':
                $order->update_status('on-hold', $mensagem);
                break;
            case 'SUSPENDED':
                $order->update_status('failed', $mensagem);
                break;
            case 'CANCELLED':
                $order->update_status('failed', $mensagem);
                break;
            case 'CANCELLED_BY_RECEIVER':
                $order->update_status('failed', $mensagem);
                break;
            case 'CANCELLED_BY_SENDER':
                $order->update_status('failed', $mensagem);
                break;
            case 'EXPIRED':
                $order->update_status('completed', $mensagem);
                break;
            default:
                break;
        }

        if ($mensagem) {
            $assunto = $titulo;
            $this->controller->send_email($email, $assunto, $titulo, $mensagem);
        }
    }

    /**
     * Check API Response.
     *
     * @return void
     */
    public function check_ipn_response() {
        $this->add_to_log($_POST, "check_ipn_response");
        header('HTTP/1.0 200 OK');
        do_action('valid_pga_gateway_ipn_request', $_POST);
    }

    function add_to_log($logObj, $nomeLog = "") {
        if ('yes' == $this->debug) {
            ob_start();

            var_dump($nomeLog);
            var_dump($logObj);
            $debug_log = ob_get_clean();

            $this->log->add('pagseguro-assinaturas-rcs', $debug_log);
        }
    }
}
