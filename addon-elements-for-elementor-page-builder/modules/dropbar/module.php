<?php

namespace WTS_EAE\Modules\Dropbar;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'Dropbar',
		];
	}

	public function get_name() {
		return 'eae-dropbar';
	}

}
