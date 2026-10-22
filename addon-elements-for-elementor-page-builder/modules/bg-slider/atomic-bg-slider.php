<?php

namespace WTS_EAE\Modules\BgSlider;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Repeatable_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\PropTypes\Image_Src_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Url_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Array_Transformer;
use Elementor\Modules\AtomicWidgets\Utils\Image\Image_Sizes;
use Elementor\Modules\AtomicWidgets\Utils\Image\Placeholder_Image;
use WTS_EAE\Classes\Atomic_Extensions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Background slider (Vegas) for Elementor 4 (v4) Flexbox, Div Block and Grid.
 */
final class Atomic_Bg_Slider {

	const NAME           = 'bg-slider';
	const HANDLE         = 'eae-atomic-bg-slider';
	const IMAGES_KEY     = 'eae_bg_slider_images';
	const TRANSITION_KEY = 'eae_bg_slider_transition';
	const ANIMATION_KEY  = 'eae_bg_slider_animation';
	const OVERLAY_KEY    = 'eae_bg_slider_overlay';
	const COVER_KEY      = 'eae_bg_slider_cover';
	const DELAY_KEY      = 'eae_bg_slider_delay';
	const TIMER_KEY      = 'eae_bg_slider_timer';

	const TRANSITIONS = [ 'fade', 'fade2', 'slideLeft', 'slideLeft2', 'slideRight', 'slideRight2', 'slideUp', 'slideUp2', 'slideDown', 'slideDown2', 'zoomIn', 'zoomIn2', 'zoomOut', 'zoomOut2', 'swirlLeft', 'swirlLeft2', 'swirlRight', 'swirlRight2', 'burn', 'burn2', 'blur', 'blur2', 'flash', 'flash2', 'random' ];
	const ANIMATIONS  = [ 'kenburns', 'kenburnsUp', 'kenburnsDown', 'kenburnsRight', 'kenburnsLeft', 'kenburnsUpLeft', 'kenburnsUpRight', 'kenburnsDownLeft', 'kenburnsDownRight', 'random' ];
	const OVERLAYS    = [ '01', '02', '03', '04', '05', '06', '07', '08', '09' ];

	public static function register() {
		$required = [ Section::class, Repeatable_Control::class, Select_Control::class, Number_Control::class, Switch_Control::class, Image_Sizes::class, Array_Transformer::class ];
		foreach ( $required as $class ) {
			if ( ! class_exists( $class ) ) {
				return;
			}
		}

		Atomic_Extensions::register();

		add_filter( 'elementor/atomic-widgets/props-schema', [ __CLASS__, 'add_props' ] );
		add_filter( 'elementor/atomic-widgets/controls', [ __CLASS__, 'add_controls' ], 10, 2 );
		add_action( 'elementor/atomic-widgets/settings/transformers/register', [ __CLASS__, 'register_transformer' ] );
		add_action( 'elementor/frontend/before_render', [ __CLASS__, 'collect_config' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_assets' ] );
	}

	/**
	 * Added to every v4 element, as the filter has no element context; only containers use them.
	 *
	 * @param array $schema Props schema.
	 * @return array
	 */
	public static function add_props( $schema ) {
		$schema[ self::IMAGES_KEY ]     = Image_Array_Prop_Type::make()->default( [] );
		$schema[ self::TRANSITION_KEY ] = String_Prop_Type::make()->enum( self::TRANSITIONS )->default( 'fade' );
		$schema[ self::ANIMATION_KEY ]  = String_Prop_Type::make()->enum( self::ANIMATIONS )->default( 'kenburns' );
		$schema[ self::OVERLAY_KEY ]    = String_Prop_Type::make()->enum( array_merge( [ '' ], self::OVERLAYS ) )->default( '' );
		$schema[ self::COVER_KEY ]      = Boolean_Prop_Type::make()->default( true );
		$schema[ self::DELAY_KEY ]      = Number_Prop_Type::make()->default( 5000 );
		$schema[ self::TIMER_KEY ]      = Boolean_Prop_Type::make()->default( true );

		return $schema;
	}

	/**
	 * @param array  $controls Element controls.
	 * @param object $element  v4 element.
	 * @return array
	 */
	public static function add_controls( $controls, $element ) {
		if ( ! Atomic_Extensions::is_container( $element ) ) {
			return $controls;
		}

		$controls[] = Section::make()
			->set_id( 'eae_bg_slider_section' )
			->set_label( __( 'AE - Background Slider', 'wts-eae' ) )
			->set_items( [
				Repeatable_Control::bind_to( self::IMAGES_KEY )
					->set_repeaterLabel( __( 'Images', 'wts-eae' ) )
					->set_child_control_type( 'image' )
					->set_child_control_props( [ 'sizes' => Image_Sizes::get_all() ] )
					->set_initialValues( self::get_initial_image() )
					->set_patternLabel( __( 'Image', 'wts-eae' ) )
					->set_placeholder( __( 'Empty image', 'wts-eae' ) )
					->set_prop_key( Image_Array_Prop_Type::get_key() ),
				Select_Control::bind_to( self::TRANSITION_KEY )
					->set_label( __( 'Transition', 'wts-eae' ) )
					->set_options( self::to_options( self::TRANSITIONS ) ),
				Select_Control::bind_to( self::ANIMATION_KEY )
					->set_label( __( 'Animation', 'wts-eae' ) )
					->set_options( self::to_options( self::ANIMATIONS ) ),
				Select_Control::bind_to( self::OVERLAY_KEY )
					->set_label( __( 'Overlay', 'wts-eae' ) )
					->set_options( self::get_overlay_options() ),
				Switch_Control::bind_to( self::COVER_KEY )
					->set_label( __( 'Cover', 'wts-eae' ) ),
				Number_Control::bind_to( self::DELAY_KEY )
					->set_label( __( 'Delay (ms)', 'wts-eae' ) )
					->set_min( 500 )
					->set_step( 500 ),
				Switch_Control::bind_to( self::TIMER_KEY )
					->set_label( __( 'Timer Bar', 'wts-eae' ) ),
			] );

		return $controls;
	}

	/**
	 * @param object $transformers Settings transformers registry.
	 */
	public static function register_transformer( $transformers ) {
		$transformers->register( Image_Array_Prop_Type::get_key(), new Array_Transformer() );
	}

	/**
	 * @param object $element Element being rendered.
	 */
	public static function collect_config( $element ) {
		if ( ! Atomic_Extensions::is_container( $element ) ) {
			return;
		}

		$settings = $element->get_data( 'settings' );
		$images   = Atomic_Extensions::get_value( $settings, self::IMAGES_KEY );
		$slides   = [];

		foreach ( is_array( $images ) ? $images : [] as $item ) {
			$src = self::get_image_url( $item );
			if ( $src ) {
				$slides[] = [ 'src' => $src ];
			}
		}

		if ( empty( $slides ) ) {
			return;
		}

		$transition = Atomic_Extensions::get_value( $settings, self::TRANSITION_KEY );
		$animation  = Atomic_Extensions::get_value( $settings, self::ANIMATION_KEY );
		$overlay    = Atomic_Extensions::get_value( $settings, self::OVERLAY_KEY );
		$cover      = Atomic_Extensions::get_value( $settings, self::COVER_KEY );
		$delay      = absint( Atomic_Extensions::get_value( $settings, self::DELAY_KEY ) );
		$timer      = Atomic_Extensions::get_value( $settings, self::TIMER_KEY );

		Atomic_Extensions::add_config( self::NAME, $element->get_id(), [
			'slides'     => $slides,
			'transition' => in_array( $transition, self::TRANSITIONS, true ) ? $transition : 'fade',
			'animation'  => in_array( $animation, self::ANIMATIONS, true ) ? $animation : 'kenburns',
			'overlay'    => in_array( $overlay, self::OVERLAYS, true ) ? $overlay : '',
			'cover'      => null === $cover ? true : (bool) $cover,
			'delay'      => $delay ? max( 500, $delay ) : 5000,
			'timer'      => null === $timer ? true : (bool) $timer,
		] );
	}

	public static function register_assets() {
		Atomic_Extensions::register_assets( self::NAME, self::HANDLE, 'assets/js/atomic-bg-slider.js', [ 'jquery', 'vegas' ], 'assets/css/atomic-bg-slider.css' );
		// Vegas' own stylesheet is loaded on every page by the classic BG Slider module.
		wp_localize_script( self::HANDLE, 'eaeAtomicBgSlider', [
			'overlayUrl' => EAE_URL . 'assets/lib/vegas/overlays/',
		] );
	}

	/**
	 * URL of one repeater item, which is stored as a v4 image value.
	 *
	 * @param mixed $item Raw item.
	 * @return string
	 */
	private static function get_image_url( $item ) {
		if ( ! is_array( $item ) || ! empty( $item['disabled'] ) ) {
			return '';
		}

		$image = Atomic_Extensions::get_value( [ 'image' => $item ], 'image' );
		$src   = is_array( $image ) ? Atomic_Extensions::get_value( $image, 'src' ) : null;
		$size  = is_array( $image ) ? Atomic_Extensions::get_value( $image, 'size' ) : null;

		if ( ! is_array( $src ) ) {
			return '';
		}

		$id = absint( Atomic_Extensions::get_value( $src, 'id' ) );
		if ( $id ) {
			$url = wp_get_attachment_image_url( $id, is_string( $size ) && $size ? $size : 'full' );
			return $url ? $url : '';
		}

		$url = Atomic_Extensions::get_value( $src, 'url' );

		return is_string( $url ) ? esc_url_raw( $url ) : '';
	}

	private static function get_initial_image() {
		return [
			'src'  => Image_Src_Prop_Type::generate( [
				'id'  => null,
				'url' => Url_Prop_Type::generate( Placeholder_Image::get_placeholder_image() ),
			] ),
			'size' => String_Prop_Type::generate( 'full' ),
		];
	}

	/**
	 * @param string[] $values Option values.
	 * @return array
	 */
	private static function to_options( array $values ) {
		return array_map( function ( $value ) {
			return [
				'value' => $value,
				'label' => ucfirst( preg_replace( '/([a-z])([A-Z0-9])/', '$1 $2', $value ) ),
			];
		}, $values );
	}

	private static function get_overlay_options() {
		$options = [
			[
				'value' => '',
				'label' => __( 'None', 'wts-eae' ),
			],
		];

		foreach ( self::OVERLAYS as $index => $value ) {
			$options[] = [
				'value' => $value,
				/* translators: %d: overlay style number. */
				'label' => sprintf( __( 'Style %d', 'wts-eae' ), $index + 1 ),
			];
		}

		return $options;
	}
}
