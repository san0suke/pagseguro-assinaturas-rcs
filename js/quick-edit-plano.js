$ = jQuery;
$(document).ready(function(){
	var $wp_inline_edit = inlineEditPost.edit;
	inlineEditPost.edit = function( id ) {
		$wp_inline_edit.apply( this, arguments );

		// now we take care of our business

		// get the post ID
		var $post_id = 0;
		if ( typeof( id ) == 'object' )
			$post_id = parseInt( this.getId( id ) );

		if ( $post_id > 0 ) {
			var $pga_inline_data = jQuery('#pga_inline_' + $post_id );

			var _is_plano 				= $pga_inline_data.find('._is_plano').text();
			var _taxa_contratacao 		= $pga_inline_data.find('._taxa_contratacao').text();
			var _ciclo_plano 			= $pga_inline_data.find('._ciclo_plano').text();
			var _intervalo_ciclo_plano 	= $pga_inline_data.find('._intervalo_ciclo_plano').text();
			var _trial 					= $pga_inline_data.find('._trial').text();
			var _limite_assinaturas 	= $pga_inline_data.find('._limite_assinaturas').text();
			var _envios_por_ciclo 		= $pga_inline_data.find('._envios_por_ciclo').text();
			var _status_pagseguro 			= $pga_inline_data.find('._status_pagseguro').text();

			if (_is_plano=='yes') {
				jQuery('input[name="_is_plano"]', '.inline-edit-row').attr('checked', 'checked');
			} else {
				jQuery('input[name="_is_plano"]', '.inline-edit-row').removeAttr('checked');
			}
			jQuery('input[name="_taxa_contratacao"]', '.inline-edit-row').val(_taxa_contratacao);
			jQuery('input[name="_ciclo_plano"]', '.inline-edit-row').val(_ciclo_plano);
			jQuery('input[name="_intervalo_ciclo_plano"]', '.inline-edit-row').val(_intervalo_ciclo_plano);
			jQuery('input[name="_trial"]', '.inline-edit-row').val(_trial);
			jQuery('input[name="_limite_assinaturas"]', '.inline-edit-row').val(_limite_assinaturas);
			jQuery('input[name="_envios_por_ciclo"]', '.inline-edit-row').val(_envios_por_ciclo);
			jQuery('select[name="_status_pagseguro"] option[value="' + _status_pagseguro + '"]', '.inline-edit-row').attr('selected', 'selected');
		}
	};
});