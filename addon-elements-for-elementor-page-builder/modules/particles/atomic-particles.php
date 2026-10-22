<?php

namespace WTS_EAE\Modules\Particles;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Textarea_Control;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use WTS_EAE\Classes\Atomic_Extensions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Particles background for Elementor 4 (v4) Flexbox, Div Block and Grid.
 */
final class Atomic_Particles {

	const NAME       = 'particles';
	const ENABLE_KEY = 'eae_particles';
	const JSON_KEY   = 'eae_particles_json';
	const HANDLE     = 'eae-atomic-particles';

	public static function register() {
		if ( ! class_exists( Section::class ) || ! class_exists( Switch_Control::class ) || ! class_exists( Textarea_Control::class ) ) {
			return;
		}

		Atomic_Extensions::register();

		add_filter( 'elementor/atomic-widgets/props-schema', [ __CLASS__, 'add_props' ] );
		add_filter( 'elementor/atomic-widgets/controls', [ __CLASS__, 'add_controls' ], 10, 2 );
		add_action( 'elementor/frontend/before_render', [ __CLASS__, 'collect_config' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_assets' ] );
	}

	/**
	 * The props-schema filter has no element context, so the props are added to every v4
	 * element (as Elementor Pro does for its own settings); only containers show and use them.
	 *
	 * @param array $schema Props schema.
	 * @return array
	 */
	public static function add_props( $schema ) {
		$schema[ self::ENABLE_KEY ] = Boolean_Prop_Type::make()->default( false );
		$schema[ self::JSON_KEY ]   = String_Prop_Type::make()->default( '' );

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
			->set_id( 'eae_particles_section' )
			->set_label( __( 'AE - Particles', 'wts-eae' ) )
			->set_items( [
				Switch_Control::bind_to( self::ENABLE_KEY )
					->set_label( __( 'Particle Background', 'wts-eae' ) ),
				Textarea_Control::bind_to( self::JSON_KEY )
					->set_label( __( 'Particle JSON', 'wts-eae' ) )
					->set_placeholder( __( 'Paste JSON from vincentgarreau.com/particles.js', 'wts-eae' ) ),
			] );

		return $controls;
	}

	/**
	 * @param object $element Element being rendered.
	 */
	public static function collect_config( $element ) {
		if ( ! Atomic_Extensions::is_container( $element ) ) {
			return;
		}

		$settings = $element->get_data( 'settings' );

		if ( ! Atomic_Extensions::get_value( $settings, self::ENABLE_KEY ) ) {
			return;
		}

		$config = json_decode( (string) Atomic_Extensions::get_value( $settings, self::JSON_KEY ), true );

		if ( is_array( $config ) ) {
			Atomic_Extensions::add_config( self::NAME, $element->get_id(), $config );
		}
	}

	public static function register_assets() {
		Atomic_Extensions::register_assets( self::NAME, self::HANDLE, 'assets/js/atomic-particles.js', [ 'eae-particles' ], 'assets/css/atomic-particles.css' );
	}
}
