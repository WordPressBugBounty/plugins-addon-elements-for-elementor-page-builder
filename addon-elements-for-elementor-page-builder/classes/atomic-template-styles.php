<?php

namespace WTS_EAE\Classes;

use Elementor\Modules\AtomicWidgets\Utils\Utils as Atomic_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads Elementor 4 (atomic) element styles for templates embedded by EAE widgets.
 *
 * Elementor 4 prints v4 element styles only for posts announced through `elementor/post/render`,
 * and merges global classes only for posts returned by `elementor/document/related_posts`.
 * EAE renders templates with get_builder_content(), which does neither, so v4 elements
 * inside those templates would render without their styles.
 */
final class Atomic_Template_Styles {

	/**
	 * Widgets that embed templates.
	 *
	 * repeater: settings key of the repeater holding the items, '' for widget-level settings.
	 * type:     settings key of the content type control.
	 * default:  default value of the content type control (unsaved when left unchanged).
	 * when:     extra item settings that must match for the template to render.
	 * map:      content type value => settings key holding the template ID.
	 */
	const WIDGETS = [
		'wts-modal-popup'      => [
			'repeater' => '',
			'type'     => 'content_type',
			'default'  => 'content',
			'map'      => [
				'savedsection'   => 'saved_sections',
				'savedpage'      => 'saved_pages',
				'aetemplate'     => 'saved_ae_template',
				'savedcontainer' => 'saved_container',
			],
		],
		'eae-dropbar'          => [
			'repeater' => '',
			'type'     => 'content_type',
			'default'  => 'plain_content',
			'map'      => [
				'saved_section'   => 'saved_section',
				'saved_page'      => 'saved_pages',
				'ae_template'     => 'ae_templates',
				'saved_container' => 'saved_container',
			],
		],
		'eae-content-switcher' => [
			'repeater' => 'content_list',
			'type'     => 'content_type',
			'default'  => 'plain_content',
			'map'      => [
				'saved_section'   => 'saved_section',
				'saved_page'      => 'saved_pages',
				'ae_template'     => 'ae_templates',
				'saved_container' => 'saved_container',
			],
		],
		'eae-data-table'       => [
			'repeater' => 'body_row_data',
			'type'     => 'eae_template_type',
			'default'  => 'saved_section',
			'when'     => [ 'body_text_type' => 'template' ],
			'map'      => [
				'saved_section' => 'table_saved_sections',
				'saved_page'    => 'table_saved_pages',
				'ae_template'   => 'table_ae_templates',
			],
		],
		'eae-faq'              => [
			'repeater' => 'faqs',
			'type'     => 'faq_content_type',
			'default'  => 'content',
			'map'      => [
				'saved_section'   => 'saved_section',
				'ae_template'     => 'ae_template',
				'saved_container' => 'saved_container',
			],
		],
		'eae-info-group'       => [
			'repeater' => 'list_items',
			'type'     => 'long_description_type',
			'default'  => 'content',
			'map'      => [
				'saved_section'   => 'saved_sections',
				'saved_page'      => 'saved_pages',
				'ae_template'     => 'saved_ae_template',
				'saved_container' => 'saved_container',
			],
		],
	];

	/**
	 * Template IDs per post, memoized for the request.
	 *
	 * @var array<int, int[]>
	 */
	private static $template_ids = [];

	/**
	 * Posts already announced through `elementor/post/render`, guards against template loops.
	 *
	 * @var array<int, true>
	 */
	private static $announced = [];

	public static function register() {
		// Elementor 3.x has no atomic styles to load.
		if ( ! class_exists( Atomic_Utils::class ) || ! method_exists( Atomic_Utils::class, 'traverse_post_elements' ) ) {
			return;
		}

		add_action( 'elementor/post/render', [ __CLASS__, 'announce_templates' ] );
		add_filter( 'elementor/document/related_posts', [ __CLASS__, 'add_related_posts' ], 10, 2 );
	}

	/**
	 * Announces templates embedded in a rendered post, so Elementor prints their v4 styles.
	 * Nested templates are reached because each announcement runs this handler again.
	 *
	 * @param int|string $post_id Rendered post ID.
	 */
	public static function announce_templates( $post_id ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return;
		}

		self::$announced[ $post_id ] = true;

		foreach ( self::get_template_ids( $post_id ) as $template_id ) {
			if ( isset( self::$announced[ $template_id ] ) ) {
				continue;
			}

			do_action( 'elementor/post/render', $template_id );
		}
	}

	/**
	 * Adds embedded templates to a post's related posts, so their global classes load with it.
	 *
	 * @param array      $related Related post IDs.
	 * @param int|string $post_id Post ID.
	 * @return array
	 */
	public static function add_related_posts( $related, $post_id ) {
		$related      = is_array( $related ) ? $related : [];
		$template_ids = self::get_template_ids( absint( $post_id ) );

		if ( empty( $template_ids ) ) {
			return $related;
		}

		return array_values( array_unique( array_merge( $related, $template_ids ) ) );
	}

	/**
	 * @param int $post_id Post ID.
	 * @return int[]
	 */
	private static function get_template_ids( $post_id ) {
		if ( ! $post_id ) {
			return [];
		}

		if ( isset( self::$template_ids[ $post_id ] ) ) {
			return self::$template_ids[ $post_id ];
		}

		$template_ids = [];

		try {
			Atomic_Utils::traverse_post_elements(
				(string) $post_id,
				function ( $element ) use ( &$template_ids ) {
					$template_ids = array_merge( $template_ids, self::get_element_template_ids( $element ) );
				}
			);
		} catch ( \Throwable $e ) {
			$template_ids = [];
		}

		$template_ids = array_values( array_diff( array_unique( $template_ids ), [ $post_id ] ) );

		self::$template_ids[ $post_id ] = $template_ids;

		return $template_ids;
	}

	/**
	 * @param array $element Element data.
	 * @return int[]
	 */
	private static function get_element_template_ids( $element ) {
		$widget_type = isset( $element['widgetType'] ) ? $element['widgetType'] : '';

		if ( ! array_key_exists( $widget_type, self::WIDGETS ) || empty( $element['settings'] ) || ! is_array( $element['settings'] ) ) {
			return [];
		}

		$config   = self::WIDGETS[ $widget_type ];
		$settings = $element['settings'];

		if ( '' === $config['repeater'] ) {
			$items = [ $settings ];
		} else {
			$items = isset( $settings[ $config['repeater'] ] ) && is_array( $settings[ $config['repeater'] ] ) ? $settings[ $config['repeater'] ] : [];
		}

		$template_ids = [];

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			if ( ! empty( $config['when'] ) ) {
				foreach ( $config['when'] as $key => $value ) {
					if ( ! isset( $item[ $key ] ) || $value !== $item[ $key ] ) {
						continue 2;
					}
				}
			}

			$type = isset( $item[ $config['type'] ] ) ? $item[ $config['type'] ] : $config['default'];

			if ( ! is_string( $type ) || ! isset( $config['map'][ $type ] ) ) {
				continue;
			}

			$id_key      = $config['map'][ $type ];
			$template_id = isset( $item[ $id_key ] ) && is_numeric( $item[ $id_key ] ) ? absint( $item[ $id_key ] ) : 0;

			if ( $template_id ) {
				$template_ids[] = $template_id;
			}
		}

		return $template_ids;
	}
}
