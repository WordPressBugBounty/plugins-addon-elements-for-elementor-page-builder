<?php

namespace WTS_EAE\Modules\ImageStack;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'ImageStack',
		];
	}

	public function get_name() {
		return 'eae-image-stack';
	}

	public function get_title() {

		return __( 'Image Stack', 'wts-eae' );
	}

}
