<?php

namespace WTS_EAE\Classes\Angie;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads the AI integrations whose dependencies are present.
 *
 * Must run on `plugins_loaded` or later: AE loads before Angie alphabetically,
 * so ANGIE_VERSION isn't defined when AE's main file runs.
 *
 * @since 1.14.6
 */
final class Integration {

	public static function register() {
		// Abilities API (WP 6.9+): Angie, Elementor MCP and external MCP clients.
		if ( function_exists( 'wp_register_ability' ) ) {
			Abilities::register();
		}

		// Angie browser MCP server (server instructions inside Angie).
		if ( defined( 'ANGIE_VERSION' ) ) {
			Ajax_Handler::register();
		}
	}
}
