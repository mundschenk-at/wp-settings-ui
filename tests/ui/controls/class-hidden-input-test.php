<?php
/**
 *  This file is part of WordPress Settings UI.
 *
 *  Copyright 2017-2024 Peter Putzer.
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

namespace Mundschenk\UI\Controls\Tests;

use Mundschenk\UI\Controls\Hidden_Input;
use Mundschenk\UI\Controls\Input;
use Mundschenk\Data_Storage\Options;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use Mockery as m;

/**
 * Mundschenk\UI\Controls\Hidden_Input unit test.
 *
 * @coversDefaultClass \Mundschenk\UI\Controls\Hidden_Input
 * @usesDefaultClass \Mundschenk\UI\Controls\Hidden_Input
 */
class Hidden_Input_Test extends \Mundschenk\UI\Tests\TestCase {

	/**
	 * Test fixture.
	 *
	 * @var Options&m\MockInterface
	 */
	protected Options $options;

	/**
	 * Test fixture.
	 *
	 * @var Hidden_Input&m\MockInterface
	 */
	protected Hidden_Input $input;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function set_up() {
		parent::set_up();

		Functions\when( 'wp_parse_args' )->alias( 'array_merge' );

		// Mock Mundschenk\Data_Storage\Options instance.
		$this->options = m::mock( Options::class ) // @phpstan-ignore method.notFound
			->shouldReceive( 'get' )->andReturn( false )->byDefault()
			->shouldReceive( 'set' )->andReturn( false )->byDefault()
			->getMock();
	}

	/**
	 * Test constructor.
	 *
	 * @covers ::__construct
	 *
	 * @uses \Mundschenk\UI\Controls\Input::__construct
	 * @uses \Mundschenk\UI\Abstract_Control::__construct
	 * @uses \Mundschenk\UI\Abstract_Control::prepare_args
	 */
	public function test_constructor(): void {
		$input = m::mock( Hidden_Input::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$args = [
			'tab_id'      => 'my_tab_id',
			'section'     => 'my_section',
			'default'     => 'my_default',
			'short'       => 'my_short',
			'label'       => 'my_label',
			'help_text'   => 'my_help_text',
			'inline_help' => false,
			'attributes'  => [ 'foo' => 'bar' ],
		];

		$this->invokeMethod( $input, '__construct', [ $this->options, 'options_key', 'my_id', $args ], Hidden_Input::class );

		$this->assertSame( 'hidden', $this->getValue( $input, 'input_type', Input::class ) );
	}
}
