<fieldset class="inline-edit-col-left">
	<h4><?php _e( 'Pagseguro assinaturas', 'pagseguro-assinaturas-rcs' ); ?></h4>

	<p class="form-field _is_plano_field ">
		<label for="_is_plano"><?php _e( 'É plano de assinatura?', 'pagseguro-assinaturas-rcs' ); ?></label>
		<input type="checkbox" value="yes" id="_is_plano" name="_is_plano" class="checkbox"> <span class="description">Selecione se for um produto de assinatura.</span>
	</p>
	<p class="form-field _taxa_contratacao_field ">
		<label for="_taxa_contratacao"><?php echo sprintf(__( 'Taxa de contratação (%s)', 'pagseguro-assinaturas-rcs' ),get_woocommerce_currency_symbol()); ?></label>
		<input type="text" placeholder="" value="" name="_taxa_contratacao" class="short wc_input_price"> 
	</p>
	<p class="form-field _ciclo_plano_field ">
		<label for="_ciclo_plano"><?php _e( 'Ciclos do plano', 'pagseguro-assinaturas-rcs' ); ?></label>
		<input type="number" min="0" step="any" placeholder="" value="" id="_ciclo_plano" name="_ciclo_plano" class="short"> 
	</p>
	<p class="form-field _intervalo_ciclo_plano_field ">
		<label for="_intervalo_ciclo_plano"><?php _e( 'Intervalo em dias', 'pagseguro-assinaturas-rcs' ); ?></label>
		<input type="number" min="0" step="any" placeholder="" value="" id="_intervalo_ciclo_plano" name="_intervalo_ciclo_plano" class="short"> 
	</p>
	<p class="form-field _trial_field ">
		<label for="_trial"><?php _e( 'Período trial em dias', 'pagseguro-assinaturas-rcs' ); ?></label>
		<input type="number" min="0" step="any" placeholder="" value="" id="_trial" name="_trial" class="short"> 
	</p>
	<p class="form-field _limite_assinaturas_field ">
		<label for="_limite_assinaturas">Limite de assinaturas</label>
		<input type="number" min="0" step="any" placeholder="" value="" id="_limite_assinaturas" name="_limite_assinaturas" class="short"> 
	</p>
	<p class="form-field _envios_por_ciclo_field ">
		<label for="_envios_por_ciclo">Envios por ciclo</label>
		<input type="number" min="0" step="any" placeholder="" value="" id="_envios_por_ciclo" name="_envios_por_ciclo" class="short">
	</p>
	<p class="form-field _status_pagseguro_field ">
		<label for="_status_pagseguro"><?php _e( 'Status do plano no Pagseguro', 'pagseguro-assinaturas-rcs' ); ?></label>
		<select class="select short" name="_status_pagseguro" id="_status_pagseguro">
			<option value="1"><?php _e( 'Ativo', 'woocommerce' ); ?></option>
			<option value="0"><?php _e( 'Inativo', 'woocommerce' ); ?></option>
		</select> 
	</p>
</fieldset>