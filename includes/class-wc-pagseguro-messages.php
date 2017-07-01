<?php

/**
 * WC Pagseguro Messages Class.
 */
class PGA_WC_Pagseguro_Messages {

    /**
     * Beginning of message
     *
     * @return string
     */
    protected static function message_before() {
        $mensagem = __('<b>Sua transação foi processada por Pagseguro Pagamentos.</b>', 'pagseguro-assinaturas-rcs') . '<br />';
        if (get_current_user_id()) {
            $mensagem .= __('<b>Se desejar cancelar sua assinatura acesse a página da sua conta, e clique em "Cancelar".</b>', 'pagseguro-assinaturas-rcs') . '<br />';
        } else {
            $email = get_option('admin_email');
            $mensagem .= '<b>' . __('Se desejar cancelar sua assinatura envie um email solicitando para: ', 'pagseguro-assinaturas-rcs') . $email . '</b><br />';
        }
        return $mensagem;
    }

    /**
     * End of message
     *
     * @return string
     */
    protected static function message_after() {
        return __('<b>Se tiver qualquer dúvida em relação a sua transação, entre em contato conosco ou com Pagseguro.</b>', 'pagseguro-assinaturas-rcs');
    }

    public static function traducao_status_assinatura($status) {
        $pagseguroStatus['ACTIVE'] = __('ativo', 'pagseguro-assinaturas-rcs');
        $pagseguroStatus['SUSPENDED'] = __('suspenso', 'pagseguro-assinaturas-rcs');
        $pagseguroStatus['EXPIRED'] = __('expirado', 'pagseguro-assinaturas-rcs');
        $pagseguroStatus['OVERDUE'] = __('pagamento atrasado', 'pagseguro-assinaturas-rcs');
        $pagseguroStatus['CANCELED'] = __('cancelado', 'pagseguro-assinaturas-rcs');
        $pagseguroStatus['TRIAL'] = __('trial', 'pagseguro-assinaturas-rcs');

        return $pagseguroStatus[$status];
    }

    public static function traducao_status_fatura($codigo_status) {
        $pagseguroStatus['1'] = __('Em aberto', 'pagseguro-assinaturas-rcs');
        $pagseguroStatus['2'] = __('Aguardando confirmação', 'pagseguro-assinaturas-rcs');
        $pagseguroStatus['3'] = __('Pago', 'pagseguro-assinaturas-rcs');
        $pagseguroStatus['4'] = __('Não pago', 'pagseguro-assinaturas-rcs');
        $pagseguroStatus['5'] = __('Atrasada', 'pagseguro-assinaturas-rcs');

        return $pagseguroStatus[$codigo_status];
    }

    public static function get_status_message($codigo_status, WC_Order $order) {
        switch ((string) $codigo_status) {
            case 'INITIATED':
                $mensagem = __('Pagseguro: O comprador iniciou o processo de pagamento, mas abandonou o checkout e não concluiu a compra.', 'pagseguro-assinaturas-rcs');
                break;
            case 'PENDING':
                $mensagem = __('Pagseguro: O processo de pagamento foi concluído e a transação está em análise ou aguardando a confirmação da operadora.', 'pagseguro-assinaturas-rcs');
                break;
            case 'ACTIVE':
                $mensagem = __('Pagseguro: A criação da recorrência, transação validadora ou transação recorrente foi aprovada.', 'pagseguro-assinaturas-rcs');
                break;
            case 'PAYMENT_METHOD_CHANGE':
                $mensagem = __('Pagseguro: Uma transação retornou como "Cartão Expirado, Cancelado ou Bloqueado" e o cartão da recorrência precisa ser substituído pelo comprador.', 'pagseguro-assinaturas-rcs');
                break;
            case 'SUSPENDED':
                $mensagem = __('Pagseguro: A recorrência foi suspensa pelo vendedor', 'pagseguro-assinaturas-rcs');
                break;
            case 'CANCELLED':
                $mensagem = __('Pagseguro: A adesão da recorrência não foi aprovada e o pedido foi cancelado', 'pagseguro-assinaturas-rcs');
                break;
            case 'CANCELLED_BY_RECEIVER':
                $mensagem = __('Pagseguro: A recorrência foi cancelada a pedido do vendedor.', 'pagseguro-assinaturas-rcs');
                break;
            case 'CANCELLED_BY_SENDER':
                $mensagem = __('Pagseguro: A recorrência foi cancelada a pedido do comprador.', 'pagseguro-assinaturas-rcs');
                break;
            case 'EXPIRED':
                $mensagem = __('Pagseguro: A recorrência expirou por atingir a data limite da vigência ou por ter atingido o valor máximo de cobrança definido na cobrança do plano', 'pagseguro-assinaturas-rcs');
                break;
            default:
                break;
        }
        return $mensagem;
    }

    public static function get_status_titulo($codigo_status, WC_Order $order) {
        switch ((string) $codigo_status) {
            case 'INITIATED':
                $titulo = sprintf(__('O comprador iniciou o processo de pagamento do seu pedido #%d, mas abandonou o checkout e não concluiu a compra', 'pagseguro-assinaturas-rcs'), $order->id);
                break;
            case 'PENDING':
                $titulo = sprintf(__('Seu pedido #%d está aguardando pagamento', 'pagseguro-assinaturas-rcs'), $order->id);
                break;
            case 'ACTIVE':
                $titulo = sprintf(__('Seu pedido #%d foi pago com sucesso', 'pagseguro-assinaturas-rcs'), $order->id);
                break;
            case 'PAYMENT_METHOD_CHANGE':
                $titulo = sprintf(__('Pagseguro: Sua assinatura #%d teve uma transação que retornou como "Cartão Expirado, Cancelado ou Bloqueado" e o cartão da recorrência precisa ser substituído pelo comprador.', 'pagseguro-assinaturas-rcs'), $order->id);
                break;
            case 'SUSPENDED':
                $titulo = sprintf(__('Sua assinatura #%d foi suspensa pelo Pagseguro.', 'pagseguro-assinaturas-rcs'), $order->id);
                break;
            case 'CANCELLED':
                $titulo = sprintf(__('O pagamento do seu pedido #%d falhou', 'pagseguro-assinaturas-rcs'), $order->id);
                break;
            case 'CANCELLED_BY_RECEIVER':
                $titulo = sprintf(__('Sua assinatura #%d foi cancelada', 'pagseguro-assinaturas-rcs'), $order->id);
                break;
            case 'CANCELLED_BY_SENDER':
                $titulo = sprintf(__('Sua assinatura #%d foi cancelada', 'pagseguro-assinaturas-rcs'), $order->id);
                break;
            case 'EXPIRED':
                $titulo = sprintf(__('Sua assinatura #%d exipirou', 'pagseguro-assinaturas-rcs'), $order->id);
                break;
            default:
                break;
        }
        return $titulo;
    }

}
