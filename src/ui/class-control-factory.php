<?php
/**
 *  This file is part of WordPress Settings UI.
 *
 *  Copyright 2018 Peter Putzer.
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
 * A factory class for Controls.
 */
abstract class Control_Factory {

	/**
	 * Initialize controls for a plugin settings page.
	 *
	 * @param array   $defaults {
	 *        An array of control definitions, indexed by control ID.
	 *
	 *        @type array $control_id {
	 *              A control definition. There may be additional parameters that are passed to the control constructor.
	 *
	 *              @type string $ui           The UI object class name.
	 *              @type string $grouped_with The control ID of the control this one should be grouped with.
	 *        }
	 * }
	 * @param Options $options     The options handler.
	 * @param string  $options_key The options key.
	 *
	 * @return array {
	 *         An array of control objects, indexed by control ID.
	 *
	 *         @type Control $id A control object.
	 * }
	 */
	public static function initialize( array $defaults, Options $options, $options_key ) {

		// Create controls from default configuration.
		$controls = [];
		$groups   = [];
		foreach ( $defaults as $control_id => $control_info ) {
			$controls[ $control_id ] = $control_info['ui']::create( $options, $options_key, $control_id, $control_info );

			if ( ! empty( $control_info['grouped_with'] ) ) {
				$groups[ $control_info['grouped_with'] ][] = $control_id;
			}
		}

		// Group controls.
		foreach ( $groups as $group => $control_ids ) {
			foreach ( $control_ids as $control_id ) {
				$controls[ $group ]->add_grouped_control( $controls[ $control_id ] );
			}
		}

		return $controls;
	}
}
