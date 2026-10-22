<?php

namespace WTS_EAE\Classes\Angie;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Angie browser MCP server: script enqueue and its two read-only AJAX endpoints.
 *
 * @since 1.14.6
 */
final class Ajax_Handler {

	const NONCE_ACTION = 'eae_angie_integration';

	const SCRIPT_HANDLE = 'eae-angie-integration';

	public static function register() {
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );

		add_action( 'wp_ajax_eae_angie_widget_catalog', [ __CLASS__, 'ajax_widget_catalog' ] );
		add_action( 'wp_ajax_eae_angie_widget_schema', [ __CLASS__, 'ajax_widget_schema' ] );
	}

	/**
	 * Load the MCP server wherever the Angie sidebar can open (any wp-admin screen
	 * and the Elementor editor), so it isn't scoped to a single admin hook.
	 */
	public static function enqueue_scripts() {
		if ( ! Widget_Catalog::current_user_can_access() ) {
			return;
		}

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			EAE_URL . 'build/angie-integration' . EAE_SCRIPT_SUFFIX . '.js',
			[],
			EAE_VERSION,
			true
		);

		wp_localize_script(
			self::SCRIPT_HANDLE,
			'eaeAngie',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
				'version' => EAE_VERSION,
			]
		);
	}

	public static function ajax_widget_catalog() {
		if ( ! self::verify_request() ) {
			return;
		}

		$widgets = Widget_Catalog::get_widgets();
		if ( is_wp_error( $widgets ) ) {
			self::send_error( $widgets );
			return;
		}

		wp_send_json_success( [ 'widgets' => $widgets ] );
	}

	public static function ajax_widget_schema() {
		if ( ! self::verify_request() ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Verified in verify_request().
		$widget = isset( $_POST['widget'] ) ? sanitize_text_field( wp_unslash( $_POST['widget'] ) ) : '';
		$tab    = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : 'content';
		$offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
		// phpcs:enable

		$schema = Widget_Catalog::get_widget_schema( $widget, $tab, $offset );
		if ( is_wp_error( $schema ) ) {
			self::send_error( $schema );
			return;
		}

		wp_send_json_success( $schema );
	}

	/**
	 * Nonce + capability gate shared by both endpoints.
	 *
	 * @return bool
	 */
	private static function verify_request() {
		if ( false === check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce.', 'wts-eae' ) ], 403 );
			return false;
		}

		if ( ! Widget_Catalog::current_user_can_access() ) {
			wp_send_json_error( [ 'message' => __( 'Insufficient permissions.', 'wts-eae' ) ], 403 );
			return false;
		}

		return true;
	}

	/**
	 * @param \WP_Error $error
	 */
	private static function send_error( $error ) {
		$data   = $error->get_error_data();
		$status = is_array( $data ) && isset( $data['status'] ) ? absint( $data['status'] ) : 400;

		wp_send_json_error( [ 'message' => $error->get_error_message() ], $status );
	}
}
