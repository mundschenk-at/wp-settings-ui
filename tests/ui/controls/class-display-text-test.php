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

use Mundschenk\UI\Controls\Display_Text;
use Mundschenk\UI\Controls\Input;
use Mundschenk\Data_Storage\Options;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use Mockery as m;

/**
 * Mundschenk\UI\Controls\Display_Text unit test.
 *
 * @coversDefaultClass \Mundschenk\UI\Controls\Display_Text
 * @usesDefaultClass \Mundschenk\UI\Controls\Display_Text
 *
 * @uses ::__construct
 * @uses \Mundschenk\UI\Abstract_Control::__construct
 * @uses \Mundschenk\UI\Abstract_Control::prepare_args
 */
class Display_Text_Test extends \Mundschenk\UI\Tests\TestCase {

	/**
	 * Test fixture.
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * Test fixture.
	 *
	 * @var \Mundschenk\UI\Controls\Display_Text
	 */
	protected $display_text;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() { // @codingStandardsIgnoreLine
		parent::setUp();

		Functions\expect( 'wp_parse_args' )->atLeast()->once()->andReturnUsing(
			function( $array1, $array2 ) {
				return \array_merge( $array2, $array1 );
			}
		);

		// Mock Mundschenk\Data_Storage\Options instance.
		$this->options = m::mock( Options::class )
			->shouldReceive( 'get' )->andReturn( false )->byDefault()
			->shouldReceive( 'set' )->andReturn( false )->byDefault()
			->getMock();

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
			'elements'         => [
				'<foo>Foo</foo>',
				'<bar/>',
			],
		];

		$this->display_text = m::mock( Display_Text::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$this->invokeMethod( $this->display_text, '__construct', [ $this->options, 'options_key', 'my_id', $args ], Display_Text::class );
	}

	/**
	 * Test constructor.
	 *
	 * @covers ::__construct
	 *
	 * @uses \Mundschenk\UI\Controls\Input::__construct
	 */
	public function test_constructor() {
		$display_text = m::mock( Display_Text::class )
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
			'elements'         => [],
		];

		$this->invokeMethod( $display_text, '__construct', [ $this->options, 'options_key', 'my_id', $args ], Display_Text::class );

		$this->assertInstanceOf( Display_Text::class, $display_text );
	}

	/**
	 * Tests get_element_markup.
	 *
	 * @covers ::get_element_markup
	 */
	public function test_get_element_markup() {
		Functions\expect( 'wp_kses' )->once()->with( '<foo>Foo</foo><bar/>', m::type( 'array' ) )->andReturn( 'escaped_value' );

		$this->assertSame( 'escaped_value', $this->invokeMethod( $this->display_text, 'get_element_markup' ) );
	}

	/**
	 * Tests get_value.
	 *
	 * @covers ::get_value
	 */
	public function test_get_value() {
		$this->assertSame( '', $this->display_text->get_value() );
	}

	/**
	 * Tests create.
	 *
	 * @covers ::create
	 *
	 * @uses \Mundschenk\UI\Abstract_Control::prepare_args
	 */
	public function test_create() {
		$args = [
			'tab_id'        => 'foo',
			'elements'      => [],
		];

		$this->assertInstanceOf( Display_Text::class, Display_Text::create( $this->options, 'my_options', 'my_control_id', $args ) );
	}
}
