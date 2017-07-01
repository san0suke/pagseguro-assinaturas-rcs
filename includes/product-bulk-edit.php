<fieldset class="inline-edit-col-right">
	<div id="woocommerce-fields-bulk" class="inline-edit-col">

		<h4><?php _e( 'Pagseguro assinaturas', 'pagseguro-assinaturas-rcs' ); ?></h4>

		<div class="inline-edit-group">
			<label class="alignleft">
				<span class="title"><?php _e( 'É plano de assinatura?', 'pagseguro-assinaturas-rcs' ); ?></span>
			    <span class="input-text-wrap">
			    	<select class="_is_plano change_to" name="_is_plano">
					<?php
						$options = array(
							'' 	=> __( '— No Change —', 'woocommerce' ),
							'yes' => __( 'Yes', 'woocommerce' ),
							'no' => __( 'No', 'woocommerce' )
						);
						foreach ($options as $key => $value) {
							echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
						}
					?>
					</select>
				</span>
			</label>
		</div>

	</div>
</fieldset>
