<?php
/**
 *  This file is part of WordPress Settings UI.
 *
 *  Copyright 2017-2018 Peter Putzer.
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

namespace Mundschenk\UI\Controls;

use Mundschenk\UI\Control;
use Mundschenk\UI\Abstract_Control;

use Mundschenk\Data_Storage\Options;

/**
 * HTML <textarea> element.
 */
class Textarea extends Abstract_Control {

	/**
	 * Create a new textarea control object.
	 *
	 * @param Options $options      Options API handler.
	 * @param string  $options_key  Database key for the options array. Passing '' means that the control ID is used instead.
	 * @param string  $id           Control ID (equivalent to option name). Required.
	 * @param array   $args {
	 *    Optional and required arguments.
	 *
	 *    @type string      $tab_id           Tab ID. Required.
	 *    @type string      $section          Optional. Section ID. Default Tab ID.
	 *    @type string|int  $default          The default value. Required, but may be an empty string.
	 *    @type string      $short            Optional. Short label.
	 *    @type string|null $label            Optional. Label content with the position of the control marked as %1$s. Default null.
	 *    @type string|null $help_text        Optional. Help text. Default null.
	 *    @type array       $attributes       Optional. Default [],
	 *    @type array       $outer_attributes Optional. Default [],
	 *    @type array       $settings_args    Optional. Default [],
	 * }
	 *
	 * @throws \InvalidArgumentException Missing argument.
	 */
	public function __construct( Options $options, $options_key, $id, array $args ) {
		$args     = $this->prepare_args( $args, [ 'tab_id', 'default' ] );
		$sanitize = 'sanitize_textarea_field';

		parent::__construct( $options, $options_key, $id, $args['tab_id'], $args['section'], $args['default'], $args['short'], $args['label'], $args['help_text'], false, $args['attributes'], $args['outer_attributes'], $args['settings_args'], $sanitize );
	}

	/**
	 * Retrieves the control-specific HTML markup.
	 *
	 * @var string
	 */
	protected function get_element_markup() {
		$value = $this->get_value();
		$value = ! empty( $value ) ? \esc_textarea( $value ) : '';

		return "<textarea class=\"large-text\" {$this->get_id_and_class_markup()}>{$value}</textarea>";
	}

	/**
	 * Creates a new textarea control
	 *
	 * @param Options $options      Options API handler.
	 * @param string  $options_key  Database key for the options array. Passing '' means that the control ID is used instead.
	 * @param string  $id           Control ID (equivalent to option name). Required.
	 * @param array   $args {
	 *    Optional and required arguments.
	 *
	 *    @type string      $tab_id        Tab ID. Required.
	 *    @type string      $section       Section ID. Required.
	 *    @type string|int  $default       The default value. Required, but may be an empty string.
	 *    @type array       $option_values The allowed values. Required.
	 *    @type string|null $short         Optional. Short label. Default null.
	 *    @type string|null $label         Optional. Label content with the position of the control marked as %1$s. Default null.
	 *    @type string|null $help_text     Optional. Help text. Default null.
	 *    @type bool        $inline_help   Optional. Display help inline. Default false.
	 *    @type array       $attributes    Optional. Default [],
	 * }
	 *
	 * @return Control
	 *
	 * @throws \InvalidArgumentException Missing argument.
	 */
	public static function create( Options $options, $options_key, $id, array $args ) {
		return new static( $options, $options_key, $id, $args );
	}
}
