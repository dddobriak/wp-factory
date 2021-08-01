<?php
global $api_control;
?>
<h3><span class="dashicons dashicons-admin-settings"></span> Api control</h3>
<div class="api-control-list loading-action">
	<?php
	$apis = $api_control->get_api_list();
	foreach ( $apis as $api ) {
		?>
		<label class="api-control-<?php echo esc_html( strtolower( $api ) ); ?>">
			<input type="checkbox" <?php $api_control->get_checked( $api ); ?> name="<?php echo esc_html( $api ); ?>">
			<?php echo esc_html( $api ); ?>
		</label>
		<?php
	}
	?>
	<span class="spinner"></span>
</div>
