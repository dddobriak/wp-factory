<?php
$coupon_load_all = get_option( 'coupon_load_all' );
?>
<div class="coupon-loader <?php echo $coupon_load_all ? 'reloading-process' : ''; ?>">
	<div class="loaded-coupon-list"></div>
	<div class="loading-action">
		<button id="load-coupons" class="button button-primary button-large">Load/Reload all coupons</button>
		<span class="spinner"></span>
	</div>
</div>
