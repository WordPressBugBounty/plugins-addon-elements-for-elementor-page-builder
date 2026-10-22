<?php

namespace WTS_EAE\Classes\Angie;

use Elementor\Plugin as EPlugin;
use Elementor\User;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read-only description of the AE widgets registered on this site, for AI agents.
 *
 * Shared by the WP abilities (Angie, Elementor MCP, external MCP clients) and the
 * Angie browser MCP server AJAX endpoints.
 *
 * @since 1.14.6
 */
final class Widget_Catalog {

	const CATEGORY = 'wts-eae';

	/**
	 * Hard cap on any single string handed to the agent. Everything returned here
	 * ends up in an LLM context, so unbounded strings are an injection surface.
	 */
	const MAX_STRING_LENGTH = 200;

	/**
	 * Control keys an agent needs to write valid settings. Everything else
	 * (selectors, render/editor UI keys, ...) is dropped.
	 */
	const KEEP_KEYS = [
		'type',
		'label',
		'description',
		'placeholder',
		'title',
		'options',
		'default',
		'fields',
		'min',
		'max',
		'step',
		'size_units',
		'range',
		'multiple',
		'return_value',
		'label_on',
		'label_off',
		'condition',
		'conditions',
		'tab',
	];

	/**
	 * Larger option lists are usually built from site content; omit them.
	 */
	const MAX_OPTIONS = 100;

	/**
	 * Approximate JSON budget for one schema response. Larger widgets are paged
	 * with `offset` / `next_offset` so a single call can't flood the agent context.
	 */
	const MAX_SCHEMA_BYTES = 100000;

	/**
	 * Control types that hold no setting value.
	 */
	const SKIP_TYPES = [
		'section',
		'tabs',
		'tab',
		'heading',
		'divider',
		'raw_html',
		'alert',
		'notice',
		'deprecated_notice',
		'button',
	];

	/**
	 * Whether the current user may use AE's AI discovery tools.
	 *
	 * Author+ (edit_posts + edit_published_posts), and not excluded in
	 * Elementor's Role Manager.
	 *
	 * @return bool
	 */
	public static function current_user_can_access() {
		$can_access = current_user_can( 'edit_posts' ) && current_user_can( 'edit_published_posts' );

		// Inverted name upstream: returns TRUE when the user is NOT excluded.
		if ( $can_access && class_exists( '\Elementor\User' ) && ! User::is_current_user_in_editing_black_list() ) {
			$can_access = false;
		}

		if ( ! $can_access ) {
			return false;
		}

		/**
		 * Filters whether the current user can use AE's Angie / MCP widget tools.
		 * Can only restrict access, never grant it.
		 *
		 * @since 1.14.6
		 *
		 * @param bool $can_access
		 */
		return true === apply_filters( 'eae/angie/current_user_can_access', true );
	}

	/**
	 * Active AE widgets: name, title, keywords.
	 *
	 * Disabled widgets are never registered with Elementor, so they never appear.
	 *
	 * @return array|\WP_Error
	 */
	public static function get_widgets() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return new \WP_Error( 'eae_elementor_inactive', __( 'Elementor is not active.', 'wts-eae' ), [ 'status' => 400 ] );
		}

		$widgets = [];

		foreach ( (array) EPlugin::$instance->widgets_manager->get_widget_types() as $widget ) {
			if ( ! self::is_eae_widget( $widget ) ) {
				continue;
			}

			$widgets[] = [
				'name'     => self::clean( $widget->get_name() ),
				'title'    => self::clean( $widget->get_title() ),
				'keywords' => self::clean_list( (array) $widget->get_keywords() ),
			];
		}

		return $widgets;
	}

	/**
	 * Settings schema for one AE widget.
	 *
	 * @param string $name Widget name, e.g. `eae-dual-button`.
	 * @param string $tab    `content` (default) or `all`.
	 * @param int    $offset Index of the first control to return (paging).
	 *
	 * @return array|\WP_Error
	 */
	public static function get_widget_schema( $name, $tab = 'content', $offset = 0 ) {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return new \WP_Error( 'eae_elementor_inactive', __( 'Elementor is not active.', 'wts-eae' ), [ 'status' => 400 ] );
		}

		// Not sanitize_key(): some AE widget names are camelCase (wts-AnimatedText).
		$name = is_string( $name ) ? preg_replace( '/[^A-Za-z0-9_-]/', '', $name ) : '';
		if ( '' === $name ) {
			return new \WP_Error( 'eae_missing_widget', __( 'Missing widget name.', 'wts-eae' ), [ 'status' => 400 ] );
		}

		$tab    = 'all' === $tab ? 'all' : 'content';
		$offset = max( 0, (int) $offset );

		$widget = EPlugin::$instance->widgets_manager->get_widget_types( $name );
		if ( ! self::is_eae_widget( $widget ) ) {
			return new \WP_Error( 'eae_unknown_widget', __( 'Unknown Addon Elements widget.', 'wts-eae' ), [ 'status' => 404 ] );
		}

		// false = the widget's own controls only, without the shared Advanced tab.
		$stack    = $widget->get_stack( false );
		$controls = isset( $stack['controls'] ) && is_array( $stack['controls'] ) ? $stack['controls'] : [];

		$controls = self::trim_controls( $controls, $tab );
		$total    = count( $controls );
		$page     = [];
		$bytes    = 0;

		foreach ( array_slice( $controls, $offset, null, true ) as $key => $control ) {
			$size = strlen( (string) wp_json_encode( $control ) ) + strlen( $key ) + 4;
			if ( $page && $bytes + $size > self::MAX_SCHEMA_BYTES ) {
				break;
			}
			$page[ $key ] = $control;
			$bytes       += $size;
		}

		$schema = [
			'widget'         => self::clean( $widget->get_name() ),
			'title'          => self::clean( $widget->get_title() ),
			'tab'            => $tab,
			'total_controls' => $total,
			'controls'       => $page,
		];

		$next = $offset + count( $page );
		if ( $next < $total ) {
			$schema['truncated']   = true;
			$schema['next_offset'] = $next;
		}

		return $schema;
	}

	/**
	 * Whether a widget genuinely belongs to AE.
	 *
	 * The Elementor category is self-declared, so ownership is also checked by
	 * namespace and by the file the class is defined in.
	 *
	 * @param mixed $widget Widget instance from Elementor's widgets manager.
	 *
	 * @return bool
	 */
	public static function is_eae_widget( $widget ) {
		if ( ! $widget instanceof Widget_Base ) {
			return false;
		}

		$is_eae = in_array( self::CATEGORY, (array) $widget->get_categories(), true )
			&& 0 === strpos( strtolower( get_class( $widget ) ), 'wts_eae\\' )
			&& self::is_defined_in_plugin( get_class( $widget ) );

		/**
		 * Filters whether a widget counts as an AE widget for the AI tools.
		 *
		 * Whatever this returns true for is described to an AI agent that can
		 * write pages, so only add widgets you own.
		 *
		 * @since 1.14.6
		 *
		 * @param bool        $is_eae
		 * @param Widget_Base $widget
		 */
		return true === apply_filters( 'eae/angie/is_eae_widget', $is_eae, $widget );
	}

	/**
	 * @param string $class
	 *
	 * @return bool
	 */
	private static function is_defined_in_plugin( $class ) {
		$plugin_path = realpath( EAE_PATH );
		if ( ! $plugin_path ) {
			return false;
		}

		try {
			$file = ( new \ReflectionClass( $class ) )->getFileName();
		} catch ( \ReflectionException $e ) {
			return false;
		}

		$file = $file ? realpath( $file ) : false;
		if ( ! $file ) {
			return false;
		}

		return 0 === strpos( wp_normalize_path( $file ), trailingslashit( wp_normalize_path( $plugin_path ) ) );
	}

	/**
	 * Reduce a control stack to what an agent needs to write valid settings.
	 *
	 * @param array  $controls Raw control stack.
	 * @param string $tab      `content` or `all`.
	 *
	 * @return array
	 */
	private static function trim_controls( $controls, $tab ) {
		$trimmed = [];

		foreach ( $controls as $key => $control ) {
			if ( ! is_array( $control ) ) {
				continue;
			}

			// Responsive device copies (e.g. button_align_tablet); the base control is flagged instead.
			if ( ! empty( $control['parent'] ) ) {
				continue;
			}

			$type = isset( $control['type'] ) ? $control['type'] : '';
			if ( in_array( $type, self::SKIP_TYPES, true ) ) {
				continue;
			}

			if ( 'all' !== $tab && isset( $control['tab'] ) && 'content' !== $control['tab'] ) {
				continue;
			}

			$is_responsive = isset( $control['responsive'] ) || ! empty( $control['is_responsive'] );

			$control = array_intersect_key( $control, array_flip( self::KEEP_KEYS ) );

			if ( 'all' !== $tab ) {
				unset( $control['tab'] );
			}

			if ( isset( $control['options'] ) && is_array( $control['options'] ) ) {
				if ( count( $control['options'] ) > self::MAX_OPTIONS ) {
					unset( $control['options'] );
					$control['options_omitted'] = true;
				} else {
					$control['options'] = self::clean_options( $control['options'] );
				}
			}

			// Repeater fields carry their own nested control stacks.
			$fields = isset( $control['fields'] ) && is_array( $control['fields'] ) ? $control['fields'] : null;
			unset( $control['fields'] );

			// Every remaining string (defaults, option keys, conditions) goes to the LLM.
			$control = self::clean_deep( $control );

			foreach ( [ 'label', 'description', 'placeholder', 'title' ] as $text_key ) {
				if ( isset( $control[ $text_key ] ) && '' === $control[ $text_key ] ) {
					unset( $control[ $text_key ] );
				}
			}

			if ( null !== $fields ) {
				$control['fields'] = self::trim_controls( self::key_fields( $fields ), 'all' );
			}

			if ( $is_responsive ) {
				$control['responsive'] = true;
			}

			$trimmed[ $key ] = $control;
		}

		return $trimmed;
	}

	/**
	 * Repeater fields may be a list; key them by control name.
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	private static function key_fields( $fields ) {
		$keyed = [];

		foreach ( $fields as $key => $field ) {
			if ( is_array( $field ) && ! empty( $field['name'] ) && is_string( $field['name'] ) ) {
				$key = $field['name'];
			}
			$keyed[ $key ] = $field;
		}

		return $keyed;
	}

	/**
	 * Select/choose options: keep the value keys, flatten labels to plain strings.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	private static function clean_options( $options ) {
		$cleaned = [];

		foreach ( $options as $value => $label ) {
			if ( is_array( $label ) ) {
				$label = isset( $label['title'] ) ? $label['title'] : '';
			}
			$cleaned[ $value ] = self::clean( $label );
		}

		return $cleaned;
	}

	/**
	 * Clean every string key and value of a nested array.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	private static function clean_deep( $value ) {
		if ( is_array( $value ) ) {
			$cleaned = [];
			foreach ( $value as $key => $item ) {
				$cleaned[ is_string( $key ) ? self::clean( $key ) : $key ] = self::clean_deep( $item );
			}
			return $cleaned;
		}

		return is_string( $value ) ? self::clean( $value ) : $value;
	}

	/**
	 * Flatten a string for agent consumption: no markup, no control characters
	 * (so a value can't fake a new line of instructions), capped length.
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public static function clean( $value ) {
		if ( ! is_scalar( $value ) ) {
			return '';
		}

		$value = wp_strip_all_tags( (string) $value );
		// Byte class only, so it's multibyte-safe without /u (which fails on malformed input).
		$value = preg_replace( '/[\x00-\x1F\x7F]+/', ' ', $value );
		$value = trim( preg_replace( '/\s+/', ' ', $value ) );

		if ( function_exists( 'mb_substr' ) ) {
			return mb_substr( $value, 0, self::MAX_STRING_LENGTH );
		}

		return substr( $value, 0, self::MAX_STRING_LENGTH );
	}

	/**
	 * @param array $values
	 *
	 * @return string[]
	 */
	private static function clean_list( $values ) {
		$cleaned = [];

		foreach ( $values as $value ) {
			$value = self::clean( $value );
			if ( '' !== $value ) {
				$cleaned[] = $value;
			}
		}

		return array_values( array_unique( $cleaned ) );
	}
}
