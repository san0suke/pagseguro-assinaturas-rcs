$ = jQuery;
$(document).ready(function () {
    var $wp_inline_edit = inlineEditPost.edit;
    inlineEditPost.edit = function (id) {
        $wp_inline_edit.apply(this, arguments);

        // now we take care of our business

        // get the post ID
        var $post_id = 0;
        if (typeof (id) == 'object')
            $post_id = parseInt(this.getId(id));

        if ($post_id > 0) {
            var $pga_inline_data = jQuery('#pga_inline_' + $post_id);

            var _is_plano = $pga_inline_data.find('._is_plano').text();
            var _produto_outras_formas_pagseguro = $pga_inline_data.find('._produto_outras_formas_pagseguro').text();

            if (_is_plano == 'yes') {
                jQuery('input[name="_is_plano"]', '.inline-edit-row').attr('checked', 'checked');
            } else {
                jQuery('input[name="_is_plano"]', '.inline-edit-row').removeAttr('checked');
            }
            if (_produto_outras_formas_pagseguro == 'yes') {
                jQuery('input[name="_produto_outras_formas_pagseguro"]', '.inline-edit-row').attr('checked', 'checked');
            } else {
                jQuery('input[name="_produto_outras_formas_pagseguro"]', '.inline-edit-row').removeAttr('checked');
            }
        }
    };
});