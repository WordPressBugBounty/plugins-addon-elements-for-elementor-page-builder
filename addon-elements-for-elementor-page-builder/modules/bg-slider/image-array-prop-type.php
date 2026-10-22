<?php

namespace WTS_EAE\Modules\BgSlider;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A list of v4 images, edited with Elementor's repeatable control and image picker.
 * The key must match the `propKey` the control passes to the editor.
 */
class Image_Array_Prop_Type extends Array_Prop_Type {

	public static function get_key(): string {
		return 'eae-image-array';
	}

	protected function define_item_type(): Prop_Type {
		return Image_Prop_Type::make();
	}
}
