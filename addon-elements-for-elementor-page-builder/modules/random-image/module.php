<?php

namespace WTS_EAE\Modules\RandomImage;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'RandomImage',
		];
	}

	public function get_name() {
		return 'eae-random-image';
	}

	public function get_title() {
		return __( 'Random Image', 'wts-eae' );
	}

}
