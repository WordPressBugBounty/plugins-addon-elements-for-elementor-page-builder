<?php

namespace WTS_EAE\Modules\AnimatedLink;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'AnimatedLink',
		];
	}

	public function get_name() {
		return 'eae-animated-link';
	}

}
