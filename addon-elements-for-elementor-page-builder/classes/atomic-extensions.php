<?php

namespace WTS_EAE\Classes;

use Elementor\Plugin as EPlugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared plumbing for EAE extensions on Elementor 4 (v4) Flexbox, Div Block and Grid.
 *
 * v4 containers render from a fixed template, so an extension cannot add wrapper attributes.
 * Each extension collects its configs here while the page renders; they are printed as
 * `window.eaeAtomicConfigs[ name ][ elementId ]` for the runtime in assets/js/atomic-extensions.js.
 */
final class Atomic_Extensions {

	const RUNTIME_HANDLE = 'eae-atomic-extensions';
	const ELEMENTS       = [ 'e-flexbox', 'e-div-block', 'e-grid' ];

	/**
	 * Configs per extension name, keyed by element ID.
	 *
	 * @var array<string, array<string, array>>
	 */
	private static $configs = [];

	/**
	 * Script handle per extension name.
	 *
	 * @var array<string, string>
	 */
	private static $handles = [];

	private static $registered = false;

	public static function register() {
		if ( self::$registered ) {
			return;
		}
		self::$registered = true;

		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_runtime' ], 9 );
		// Before footer scripts print (priority 20), after the content has rendered.
		add_action( 'wp_footer', [ __CLASS__, 'print_configs' ], 5 );
	}

	public static function register_runtime() {
		wp_register_script( self::RUNTIME_HANDLE, EAE_URL . 'assets/js/atomic-extensions.js', [], EAE_VERSION, true );
	}

	/**
	 * Registers an extension's script and style (same handle), always loading them in the editor
	 * preview, where containers are added on the fly.
	 *
	 * @param string $name   Extension name, the key in window.eaeAtomicConfigs.
	 * @param string $handle Script and style handle.
	 * @param string $js     Script path relative to the plugin.
	 * @param array  $deps   Extra script dependencies.
	 * @param string $css    Optional style path relative to the plugin.
	 */
	public static function register_assets( $name, $handle, $js, $deps = [], $css = '' ) {
		self::$handles[ $name ] = $handle;

		wp_register_script( $handle, EAE_URL . $js, array_merge( [ self::RUNTIME_HANDLE ], $deps ), EAE_VERSION, true );
		if ( $css ) {
			wp_register_style( $handle, EAE_URL . $css, [], EAE_VERSION );
		}

		if ( EPlugin::$instance->preview->is_preview_mode() ) {
			self::enqueue( $handle );
		}
	}

	/**
	 * @param object $element Element being rendered.
	 * @return bool
	 */
	public static function is_container( $element ) {
		return is_object( $element ) && in_array( $element->get_name(), self::ELEMENTS, true );
	}

	/**
	 * Reads a plain value from raw v4 settings, where values are stored as { $$type, value }.
	 *
	 * @param mixed  $settings Raw v4 settings.
	 * @param string $key      Setting key.
	 * @return mixed
	 */
	public static function get_value( $settings, $key ) {
		if ( ! is_array( $settings ) || ! isset( $settings[ $key ] ) ) {
			return null;
		}

		$value = $settings[ $key ];

		return is_array( $value ) && array_key_exists( 'value', $value ) ? $value['value'] : $value;
	}

	/**
	 * @param string $name       Extension name.
	 * @param string $element_id Element ID.
	 * @param array  $config     Config for the extension's script.
	 */
	public static function add_config( $name, $element_id, array $config ) {
		self::$configs[ $name ][ $element_id ] = $config;
	}

	public static function print_configs() {
		foreach ( self::$configs as $name => $configs ) {
			if ( empty( self::$handles[ $name ] ) ) {
				continue;
			}

			$handle = self::$handles[ $name ];
			self::enqueue( $handle );
			wp_add_inline_script(
				$handle,
				'window.eaeAtomicConfigs = window.eaeAtomicConfigs || {}; window.eaeAtomicConfigs[' . wp_json_encode( $name ) . '] = ' . wp_json_encode( $configs ) . ';',
				'before'
			);
		}
	}

	/**
	 * @param string $handle Script and style handle.
	 */
	private static function enqueue( $handle ) {
		wp_enqueue_script( $handle );
		if ( wp_style_is( $handle, 'registered' ) ) {
			wp_enqueue_style( $handle );
		}
	}
}
