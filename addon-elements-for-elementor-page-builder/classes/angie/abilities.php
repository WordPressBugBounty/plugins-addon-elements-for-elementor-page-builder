<?php

namespace WTS_EAE\Classes\Angie;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Abilities for AE widget discovery.
 *
 * Angie auto-discovers third-party abilities, and the slugs are also added to
 * Elementor's MCP server, so any MCP client can find AE widgets and their settings.
 *
 * @since 1.14.6
 */
final class Abilities {

	const CATEGORY = 'addon-elements';

	const LIST_WIDGETS = 'eae/list-widgets';

	const GET_SCHEMA = 'eae/get-widget-schema';

	public static function register() {
		add_action( 'wp_abilities_api_categories_init', [ __CLASS__, 'register_category' ] );
		add_action( 'wp_abilities_api_init', [ __CLASS__, 'register_abilities' ] );
		add_filter( 'elementor/mcp/server/tools', [ __CLASS__, 'add_mcp_tools' ] );
	}

	public static function register_category() {
		wp_register_ability_category(
			self::CATEGORY,
			[
				'label'       => __( 'Addon Elements for Elementor', 'wts-eae' ),
				'description' => __( 'Discover Addon Elements for Elementor widgets and their settings.', 'wts-eae' ),
			]
		);
	}

	public static function register_abilities() {
		$meta = [
			'show_in_rest' => true,
			'annotations'  => [
				'readonly'    => true,
				'destructive' => false,
				'idempotent'  => true,
			],
			'mcp'          => [
				'public' => true,
				'type'   => 'tool',
			],
		];

		wp_register_ability(
			self::LIST_WIDGETS,
			[
				'label'               => __( 'List Addon Elements widgets', 'wts-eae' ),
				'description'         => __( 'Lists the Addon Elements for Elementor (AE) widgets active on this site: widgetType name, title and keywords. AE adds dual buttons, price tables, timelines, flip boxes, comparison tables, info circles, animated text, content switchers, modal popups, data tables and more. Prefer an AE widget over hand-written HTML or combined core widgets when one fits. Call this first, then get the schema of each widget before writing it into _elementor_data.', 'wts-eae' ),
				'category'            => self::CATEGORY,
				'input_schema'        => [
					'type'                 => 'object',
					'additionalProperties' => false,
					'default'              => [],
				],
				'execute_callback'    => [ __CLASS__, 'execute_list_widgets' ],
				'permission_callback' => [ Widget_Catalog::class, 'current_user_can_access' ],
				'meta'                => $meta,
			]
		);

		wp_register_ability(
			self::GET_SCHEMA,
			[
				'label'               => __( 'Get Addon Elements widget schema', 'wts-eae' ),
				'description'         => __( 'Returns the settings controls of one Addon Elements (AE) widget: setting keys, types, options and defaults. Call it before writing an eae-* widget into _elementor_data, and use the widgetType and setting keys exactly as returned; never guess. Responsive controls also accept {key}_tablet and {key}_mobile. tab=content (default) returns content settings only; tab=all also returns style settings. Large widgets are paged: if truncated is true, call again with offset=next_offset.', 'wts-eae' ),
				'category'            => self::CATEGORY,
				'input_schema'        => [
					'type'                 => 'object',
					'additionalProperties' => false,
					'required'             => [ 'widget' ],
					'properties'           => [
						'widget' => [
							'type'        => 'string',
							'description' => __( 'Widget name from eae/list-widgets, e.g. eae-dual-button.', 'wts-eae' ),
							'pattern'     => '^[A-Za-z0-9_-]+$',
							'maxLength'   => 100,
						],
						'tab'    => [
							'type'        => 'string',
							'description' => __( 'content (default) or all.', 'wts-eae' ),
							'enum'        => [ 'content', 'all' ],
							'default'     => 'content',
						],
						'offset' => [
							'type'        => 'integer',
							'description' => __( 'Paging: pass next_offset from a previous truncated response.', 'wts-eae' ),
							'minimum'     => 0,
							'default'     => 0,
						],
					],
				],
				'execute_callback'    => [ __CLASS__, 'execute_get_schema' ],
				'permission_callback' => [ Widget_Catalog::class, 'current_user_can_access' ],
				'meta'                => $meta,
			]
		);
	}

	/**
	 * @return array|\WP_Error
	 */
	public static function execute_list_widgets() {
		$widgets = Widget_Catalog::get_widgets();
		if ( is_wp_error( $widgets ) ) {
			return $widgets;
		}

		return [ 'widgets' => $widgets ];
	}

	/**
	 * @param mixed $input Validated against the input schema.
	 *
	 * @return array|\WP_Error
	 */
	public static function execute_get_schema( $input = null ) {
		$input = is_array( $input ) ? $input : [];

		return Widget_Catalog::get_widget_schema(
			isset( $input['widget'] ) ? $input['widget'] : '',
			isset( $input['tab'] ) ? $input['tab'] : 'content',
			isset( $input['offset'] ) ? $input['offset'] : 0
		);
	}

	/**
	 * Expose the abilities on Elementor's MCP server (/wp-json/elementor/mcp).
	 *
	 * @param mixed $tools Ability slugs.
	 *
	 * @return array
	 */
	public static function add_mcp_tools( $tools ) {
		$tools   = is_array( $tools ) ? $tools : [];
		$tools[] = self::LIST_WIDGETS;
		$tools[] = self::GET_SCHEMA;

		return $tools;
	}
}
