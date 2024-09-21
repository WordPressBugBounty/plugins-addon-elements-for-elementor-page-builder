<?php

namespace WTS_EAE\Modules\CouponCode;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'CouponCode',
		];
	}

	public function get_name() {
		return 'eae-coupon-code';
	}

	public function get_title() {
		return __( 'Coupon Code', 'wts-eae' );
	}

}
