function block_screen() {
    jQuery.blockUI({
        message: arrPagseguro.processando_compra,
        baseZ: 99999,
        overlayCSS: {
            background: "#fff",
            opacity: 0.6
        },
        css: {
            padding: "20px",
            zindex: "9999999",
            textAlign: "center",
            color: "#555",
            border: "3px solid #aaa",
            backgroundColor: "#fff",
            cursor: "wait",
            lineHeight: "24px",
        }
    });
}

jQuery(function () {
    if (jQuery("#code_pagseguro").val() === "") {
        block_screen();
        jQuery.post(arrPagseguro.ajax_url,
                jQuery("#woocommerce-pagseguro-assinaturas-rcs-payment-form").serialize(),
                function (response) {
                    if (response.url_redirect !== undefined) {
                        document.location = response.url_redirect;
                    } else {
                        erro_html = '<div class="error">' +
                                '<b>Pagseguro:</b> ' + response.retorno +
                                '</div>';
                        jQuery("#mensagem_retorno").html(erro_html);
                    }
                }, "json");
    } else {
        block_screen();
        jQuery.post(arrPagseguro.ajax_url,
                jQuery("#woocommerce-pagseguro-assinaturas-rcs-payment-form").serialize(),
                function (response) {
                    jQuery(".hide_after_payment").hide();
                    jQuery(".show_after_payment").show();
                    jQuery.unblockUI();
                }, "json");
    }
});