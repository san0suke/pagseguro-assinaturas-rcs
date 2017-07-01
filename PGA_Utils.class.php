<?php

class PGA_Utils {

    static function pagseguro_money_format($valor) {
        $strlen = strlen($valor);
        $inteiro_val = substr($valor, 0, $strlen - 2);
        $decimal_val = substr($valor, $strlen - 2, $strlen);
        $valor = $inteiro_val . '.' . $decimal_val;

        return self::format_money_currency((float) $valor);
    }

    static function format_money($number) {
        return number_format($number, 2, ',', '.');
    }

    static function format_money_currency($number) {
        return get_woocommerce_currency_symbol() . self::format_money($number);
    }

    function get_ddd_from_telefone($telefone) {
        $arrNumero = explode(')', $telefone);
        return str_replace(array('(', '-', ' ', ')'), '', $arrNumero[0]);
    }

    function get_numero_from_telefone($telefone) {
        $arrNumero = explode(')', $telefone);
        return str_replace(array('(', '-', ' ', ')'), '', $arrNumero[1]);
    }

    function get_dia_from_data($data) {
        $arrData = explode('/', $data);
        return $arrData[0];
    }

    function get_mes_from_data($data) {
        $arrData = explode('/', $data);
        return $arrData[1];
    }

    function get_ano_from_data($data) {
        $arrData = explode('/', $data);
        return $arrData[2];
    }

    function clean_cpf_cnpj($cpf_cnpj) {
        return str_replace(array('.', '-', '/'), '', $cpf_cnpj);
    }

    public static function getAllHeaders() {
        if (function_exists('getallheaders')) {
            return getallheaders();
        } else {
            $headers = null;
            foreach ((array) $_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }
    }

    public static function getIPN_URL() {
        return str_replace('https:', 'http:', add_query_arg('wc-api', 'PGA_Gateway', home_url('/')));
    }

    public static function get_current_url() {
        global $wp;
        return add_query_arg($wp->query_string, '', home_url($wp->request));
    }
    
    public static function getUrlAtual() {
        return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

}
