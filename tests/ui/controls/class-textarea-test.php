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

namespace Mundschenk\UI\Controls\Tests;

use Mundschenk\UI\Controls\Textarea;
use Mundschenk\UI\Controls\Input;
use Mundschenk\Data_Storage\Options;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use Mockery as m;

/**
 * Mundschenk\UI\Controls\Textarea unit test.
 *
 * @coversDefaultClass \Mundschenk\UI\Controls\Textarea
 * @usesDefaultClass \Mundschenk\UI\Controls\Textarea
 *
 * @uses ::__construct
 * @uses \Mundschenk\UI\Abstract_Control::__construct
 * @uses \Mundschenk\UI\Abstract_Control::prepare_args
 */
class Textarea_Test extends \Mundschenk\UI\Tests\TestCase {

	/**
	 * Test fixture.
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * Test fixture.
	 *
	 * @var \Mundschenk\UI\Controls\Textarea
	 */
	protected $textarea;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() { // @codingStandardsIgnoreLine
		parent::setUp();

		// Mock Mundschenk\Data_Storage\Options instance.
		$this->options = m::mock( Options::class )
			->shouldReceive( 'get' )->andReturn( false )->byDefault()
			->shouldReceive( 'set' )->andReturn( false )->byDefault()
			->getMock();

		$this->textarea = m::mock( Textarea::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$args = [
			'tab_id'           => 'my_tab_id',
			'section'          => 'my_section',
			'default'          => 'my_default',
			'short'            => 'my_short',
			'label'            => 'my_label',
			'help_text'        => 'my_help_text',
			'inline_help'      => false,
			'attributes'       => [ 'foo' => 'bar' ],
			'outer_attributes' => [ 'foo' => 'bar' ],
			'settings_args'    => [],
		];

		$this->textarea->shouldReceive( 'prepare_args' )->once()->with( $args, [ 'tab_id', 'default' ] )->andReturn( $args );

		$this->invokeMethod( $this->textarea, '__construct', [ $this->options, 'options_key', 'my_id', $args ], Textarea::class );
	}

	/**
	 * Test constructor.
	 *
	 * @covers ::__construct
	 *
	 * @uses \Mundschenk\UI\Controls\Input::__construct
	 */
	public function test_constructor() {
		$textarea = m::mock( Textarea::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$args = [
			'tab_id'           => 'my_tab_id',
			'section'          => 'my_section',
			'default'          => 'my_default',
			'short'            => 'my_short',
			'label'            => 'my_label',
			'help_text'        => 'my_help_text',
			'inline_help'      => false,
			'attributes'       => [ 'foo' => 'bar' ],
			'outer_attributes' => [ 'foo' => 'bar' ],
			'settings_args'    => [],
		];

		$textarea->shouldReceive( 'prepare_args' )->once()->with( $args, [ 'tab_id', 'default' ] )->andReturn( $args );

		$this->invokeMethod( $textarea, '__construct', [ $this->options, 'options_key', 'my_id', $args ], Textarea::class );

		$this->assertInstanceOf( Textarea::class, $textarea );
	}

	/**
	 * Tests get_element_markup.
	 *
	 * @covers ::get_element_markup
	 */
	public function test_get_element_markup() {
		Functions\expect( 'esc_textarea' )->once()->with( 'value' )->andReturn( 'escaped_value' );
		$this->textarea->shouldReceive( 'get_value' )->once()->andReturn( 'value' );
		$this->textarea->shouldReceive( 'get_id_and_class_markup' )->once()->andReturn( 'id="foo"' );

		$this->assertSame( '<textarea class="large-text" id="foo">escaped_value</textarea>', $this->invokeMethod( $this->textarea, 'get_element_markup' ) );
	}

	/**
	 * Tests create.
	 *
	 * @covers ::create
	 *
	 * @uses \Mundschenk\UI\Abstract_Control::prepare_args
	 */
	public function test_create() {
		Functions\expect( 'wp_parse_args' )->twice()->andReturnUsing(
			function( $array1, $array2 ) {
				return \array_merge( $array2, $array1 );
			}
		);

		$args = [
			'tab_id'        => 'foo',
			'default'       => 'bar',
		];
		$this->assertInstanceOf( Textarea::class, Textarea::create( $this->options, 'my_options', 'my_control_id', $args ) );
	}
}
