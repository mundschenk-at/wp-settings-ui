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

use Mundschenk\Data_Storage\Options;

/**
 * HTML <input> element.
 */
class Submit_Input extends Input {
	/**
	 * Optional HTML class for buttons.
	 *
	 * @var string
	 */
	protected $button_class;

	/**
	 * Optional button label.
	 *
	 * @var string
	 */
	protected $button_label;

	/**
	 * Create a new input control object.
	 *
	 * @param Options $options      Options API handler.
	 * @param string  $options_key  Database key for the options array. Passing '' means that the control ID is used instead.
	 * @param string  $id           Control ID (equivalent to option name). Required.
	 * @param array   $args {
	 *    Optional and required arguments.
	 *
	 *    @type string      $tab_id       Tab ID. Required.
	 *    @type string      $section      Optional. Section ID. Default Tab ID.
	 *    @type string|int  $default      The default value. Required, but may be an empty string.
	 *    @type string      $button_class Required.
	 *    @type string|null $short        Optional. Short label. Default null.
	 *    @type string|null $label        Optional. The actual button label. Default null (browser dependant).
	 *    @type array       $attributes   Optional. Default [],
	 * }
	 *
	 * @throws \InvalidArgumentException Missing argument.
	 */
	public function __construct( Options $options, $options_key, $id, array $args ) {
		// Ensure that there is a button class argument.
		$args = $this->prepare_args( $args, [ 'button_class' ] );

		// Ensure proper button label handling.
		$this->button_label = $args['label'];
		$args['label']      = null;

		// Force these addtional arguments.
		$args['input_type'] = 'submit';

		// Store button class attribute.
		$this->button_class = $args['button_class'];

		// Call parent.
		parent::__construct( $options, $options_key, $id, $args );
	}

	/**
	 * Retrieve the current button name.
	 *
	 * @return string
	 */
	public function get_value() {
		return $this->button_label;
	}

	/**
	 * Markup ID and class(es).
	 *
	 * @return string
	 */
	protected function get_id_and_class_markup() {
		return parent::get_id_and_class_markup() . ' class="' . \esc_attr( $this->button_class ) . '"';
	}
}
