<?php
/**
 *  This file is part of WordPress Settings UI.
 *
 *  Copyright 2014-2019 Peter Putzer.
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 *  ***
 *
 *  @package mundschenk-at/wp-settings-ui
 *  @license http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Mundschenk\UI;

use Mundschenk\Data_Storage\Options;

/**
 * Abstract base class for HTML controls.
 */
abstract class Abstract_Control implements Control {

	/**
	 * Control ID (= option name).
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Tab ID.
	 *
	 * @var string
	 */
	protected $tab_id;

	/**
	 * Section ID.
	 *
	 * @var string
	 */
	protected $section;

	/**
	 * Short label. Optional.
	 *
	 * @var string|null
	 */
	protected $short;

	/**
	 * Label content with the position of the control marked as %1$s. Optional.
	 *
	 * @var string|null
	 */
	protected $label;

	/**
	 * Help text. Optional.
	 *
	 * @var string|null
	 */
	protected $help_text;

	/**
	 * Whether the help text should be displayed inline.
	 *
	 * @var bool
	 */
	protected $inline_help;

	/**
	 * The default value. Required, but may be an empty string.
	 *
	 * @var string|int
	 */
	protected $default;

	/**
	 * Additional HTML attributes to add to the main element (`<input>` etc.).
	 *
	 * @var array {
	 *      Attribute/value pairs.
	 *
	 *      string $attr Attribute value.
	 * }
	 */
	protected $attributes;

	/**
	 * Additional HTML attributes to add to the outer element (either `<fieldset>` or `<div>`).
	 *
	 * @var array {
	 *      Attribute/value pairs.
	 *
	 *      string $attr Attribute value.
	 * }
	 */
	protected $outer_attributes;

	/**
	 * Grouped controls.
	 *
	 * @var array {
	 *      An array of Controls.
	 *
	 *      Control $control Grouped control.
	 * }
	 */
	protected $grouped_controls = [];

	/**
	 * The Control this one is grouped with.
	 *
	 * @var Control|null
	 */
	protected $grouped_with = null;

	/**
	 * An abstraction of the WordPress Options API.
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * The base path for includes.
	 *
	 * @var string
	 */
	protected $base_path;

	/**
	 * The options key.
	 *
	 * @var string
	 */
	protected $options_key;

	/**
	 * Additional arguments passed to the `add_settings_field` function.
	 *
	 * @var array {
	 *      Attribute/value pairs.
	 *
	 *      string $attr Attribute value.
	 * }
	 */
	protected $settings_args;

	/**
	 * A sanitiziation callback.
	 *
	 * @var callable|null
	 */
	protected $sanitize_callback;

	const ALLOWED_INPUT_ATTRIBUTES = [
		'id'               => [],
		'name'             => [],
		'value'            => [],
		'checked'          => [],
		'type'             => [],
		'class'            => [],
		'aria-describedby' => [],
	];

	const ALLOWED_HTML = [
		'span'   => [ 'class' => [] ],
		'input'  => self::ALLOWED_INPUT_ATTRIBUTES,
		'select' => self::ALLOWED_INPUT_ATTRIBUTES,
		'option' => [
			'value'    => [],
			'selected' => [],
		],
		'code'   => [],
		'strong' => [],
		'em'     => [],
		'sub'    => [],
		'sup'    => [],
		'br'     => [],
	];

	const ALLOWED_DESCRIPTION_HTML = [
		'code'   => [],
		'strong' => [],
		'em'     => [],
		'sub'    => [],
		'sup'    => [],
		'br'     => [],
		'span'   => [ 'class' => [] ],
	];

	/**
	 * Create a new UI control object.
	 *
	 * @param Options       $options          Options API handler.
	 * @param string        $options_key      Database key for the options array. Passing '' means that the control ID is used instead.
	 * @param string        $id               Control ID (equivalent to option name). Required.
	 * @param string        $tab_id           Tab ID. Required.
	 * @param string        $section          Section ID. Required.
	 * @param string|int    $default          The default value. Required, but may be an empty string.
	 * @param string|null   $short            Optional. Short label. Default null.
	 * @param string|null   $label            Optional. Label content with the position of the control marked as %1$s. Default null.
	 * @param string|null   $help_text        Optional. Help text. Default null.
	 * @param bool          $inline_help      Optional. Display help inline. Default false.
	 * @param array         $attributes       Optional. Attributes for the main element of the control. Default [].
	 * @param array         $outer_attributes Optional. Attributes for the outer element (´<fieldset>` or `<div>`) of the control. Default [].
	 * @param array         $settings_args    Optional. Arguments passed to `add_settings_Field`. Default [].
	 * @param callable|null $sanitize_callback Optional. A callback to sanitize $_POST data. Default null.
	 */
	protected function __construct( Options $options, $options_key, $id, $tab_id, $section, $default, $short = null, $label = null, $help_text = null, $inline_help = false, array $attributes = [], array $outer_attributes = [], $settings_args = [], $sanitize_callback = null ) {
		$this->options           = $options;
		$this->options_key       = $options_key;
		$this->id                = $id;
		$this->tab_id            = $tab_id;
		$this->section           = $section;
		$this->short             = $short ?: '';
		$this->label             = $label;
		$this->help_text         = $help_text;
		$this->inline_help       = $inline_help;
		$this->default           = $default;
		$this->attributes        = $attributes;
		$this->outer_attributes  = $outer_attributes;
		$this->settings_args     = $settings_args;
		$this->sanitize_callback = $sanitize_callback;
		$this->base_path         = dirname( dirname( __DIR__ ) );
	}

	/**
	 * Prepares keyowrd arguments passed via an array for usage.
	 *
	 * @param array $args     Arguments.
	 * @param array $required Required argument names. 'tab_id' is always required.
	 *
	 * @return array
	 *
	 * @throws \InvalidArgumentException Thrown when a required argument is missing.
	 */
	protected function prepare_args( array $args, array $required ) {

		// Check for required arguments.
		$required = \wp_parse_args( $required, [ 'tab_id' ] );

		foreach ( $required as $property ) {
			if ( ! isset( $args[ $property ] ) ) {
				throw new \InvalidArgumentException( "Missing argument '$property'." );
			}
		}

		// Add default arguments.
		$defaults = [
			'section'           => $args['tab_id'],
			'short'             => null,
			'label'             => null,
			'help_text'         => null,
			'inline_help'       => false,
			'attributes'        => [],
			'outer_attributes'  => [],
			'settings_args'     => [],
			'sanitize_callback' => null,
		];
		$args     = \wp_parse_args( $args, $defaults );

		return $args;
	}

	/**
	 * Retrieve the current value for the control.
	 * May be overridden by subclasses.
	 *
	 * @return mixed
	 */
	public function get_value() {
		$key     = $this->options_key ?: $this->id;
		$options = $this->options->get( $key );

		if ( $key === $this->id ) {
			return $options;
		} elseif ( isset( $options[ $this->id ] ) ) {
			return $options[ $this->id ];
		} else {
			return null;
		}
	}

	/**
	 * Renders control-specific HTML.
	 *
	 * @return void
	 */
	protected function render_element() {
		echo $this->get_element_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Retrieves the control-specific HTML markup.
	 *
	 * @return string
	 */
	abstract protected function get_element_markup();

	/**
	 * Render the HTML representation of the control.
	 */
	public function render() {
		require $this->base_path . '/partials/control.php';
	}

	/**
	 * Retrieves additional HTML attributes as a string ready for inclusion in markup.
	 *
	 * @param array $attributes Required.
	 *
	 * @return string
	 */
	protected function get_html_attributes( array $attributes ) {
		$html_attributes = '';
		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $attr => $val ) {
				$html_attributes .= \esc_attr( $attr ) . '="' . \esc_attr( $val ) . '" ';
			}
		}

		return $html_attributes;
	}

	/**
	 * Retrieves additional HTML attributes for the inner element as a string
	 * ready for inclusion in markup.
	 *
	 * @return string
	 */
	protected function get_inner_html_attributes() {
		return $this->get_html_attributes( $this->attributes );
	}

	/**
	 * Retrieves additional HTML attributes for the outer element as a string
	 * ready for inclusion in markup.
	 *
	 * @return string
	 */
	protected function get_outer_html_attributes() {
		return $this->get_html_attributes( $this->outer_attributes );
	}

	/**
	 * Retrieve default value.
	 *
	 * @return string|int
	 */
	public function get_default() {
		return $this->default;
	}

	/**
	 * Retrieve control ID.
	 *
	 * @return string
	 */
	public function get_id() {
		if ( ! empty( $this->options_key ) ) {
			return "{$this->options->get_name( $this->options_key )}[{$this->id}]";
		} else {
			return "{$this->options->get_name( $this->id )}";
		}
	}


	/**
	 * Retrieves the markup for ID, name and class(es).
	 * Also adds additional attributes if they are set.
	 *
	 * @return string
	 */
	protected function get_id_and_class_markup() {
		$id   = \esc_attr( $this->get_id() );
		$aria = ! empty( $this->help_text ) ? " aria-describedby=\"{$id}-description\"" : '';

		// Set default ID & name, no class (except for submit buttons).
		return "id=\"{$id}\" name=\"{$id}\" {$this->get_inner_html_attributes()}{$aria}";
	}

	/**
	 * Determines if the label contains a placeholder for the actual control element(s).
	 *
	 * @return bool
	 */
	protected function label_has_placeholder() {
		return false !== strpos( $this->label, '%1$s' );
	}

	/**
	 * Determines if this control has an inline help text to display.
	 *
	 * @return bool
	 */
	protected function has_inline_help() {
		return $this->inline_help && ! empty( $this->help_text );
	}

	/**
	 * Retrieves the label. If the label text contains a string placeholder, it
	 * is replaced by the control element markup.
	 *
	 * @var string
	 */
	public function get_label() {
		if ( $this->label_has_placeholder() ) {
			return sprintf( $this->label, $this->get_element_markup() );
		} else {
			return $this->label;
		}
	}

	/**
	 * Register the control with the settings API.
	 *
	 * @param string $option_group Application-specific prefix.
	 */
	public function register( $option_group ) {

		// Register rendering callbacks only for non-grouped controls.
		if ( empty( $this->grouped_with ) ) {
			\add_settings_field( $this->get_id(), $this->short, [ $this, 'render' ], $option_group . $this->tab_id, $this->section, $this->settings_args );
		}
	}

	/**
	 * Group another control with this one.
	 *
	 * @param Control $control Any control.
	 */
	public function add_grouped_control( Control $control ) {
		// Prevent self-references.
		if ( $this !== $control ) {
			$this->grouped_controls[] = $control;
			$control->group_with( $this );
		}
	}

	/**
	 * Registers this control as grouped with another one.
	 *
	 * @param Control $control Any control.
	 */
	public function group_with( Control $control ) {
		// Prevent self-references.
		if ( $this !== $control ) {
			$this->grouped_with = $control;
		}
	}

	/**
	 * Sanitizes an option value.
	 *
	 * @param  mixed $value The unslashed post variable.
	 *
	 * @return mixed        The sanitized value.
	 */
	public function sanitize( $value ) {
		$sanitize = $this->sanitize_callback;

		if ( \is_callable( $sanitize ) ) {
			return $sanitize( $value );
		}

		return $value;
	}
}
