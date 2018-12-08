<?php
/**
 *  This file is part of WordPress Settings UI.
 *
 *  Copyright 2017-2018 Peter Putzer.
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or ( at your option ) any later version.
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
 *  @package mundschenk-at/wp-settings-ui/tests
 *  @license http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Mundschenk\UI\Tests;

use Mundschenk\UI\Control_Factory;
use Mundschenk\Data_Storage\Options;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use Mockery as m;

/**
 * Mundschenk\UI\Control_Factory unit test.
 *
 * @coversDefaultClass \Mundschenk\UI\Control_Factory
 * @usesDefaultClass \Mundschenk\UI\Control_Factory
 */
class Control_Factory_Test extends \Mundschenk\UI\Tests\TestCase {

	/**
	 * Test initialize.
	 *
	 * @covers ::initialize
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_initialize() {
		// Helper objects.
		$options      = m::mock( Options::class );
		$options_key  = 'my_options_key';
		$number_input = m::mock( 'overload:' . \Mundschenk\UI\Number_Input::class );
		$checkbox     = m::mock( 'overload:' . \Mundschenk\UI\Checkbox_Input::class );
		$select       = m::mock( 'overload:' . \Mundschenk\UI\Select::class );

		$defaults = [
			'foo'    => [
				'tab_id' => 'my-tab',
				'ui'     => \Mundschenk\UI\Number_Input::class,
			],
			'check1' => [
				'tab_id' => 'my-tab',
				'ui'     => \Mundschenk\UI\Checkbox_Input::class,
			],
			'check2' => [
				'tab_id'       => 'other-tab',
				'grouped_with' => 'check1',
				'ui'           => \Mundschenk\UI\Select::class,
			],
		];

		// Fake instances.
		$number_input->shouldReceive( 'create' )->once()->andReturn( $number_input );
		$checkbox->shouldReceive( 'create' )->once()->andReturn( $checkbox );
		$select->shouldReceive( 'create' )->once()->andReturn( $select );

		// Set up expectations.
		$checkbox->shouldReceive( 'add_grouped_control' )->once()->with( m::type( \Mundschenk\UI\Select::class ) );

		// Do it.
		$this->assertInternalType( 'array', Control_Factory::initialize( $defaults, $options, $options_key ) );
	}
}
