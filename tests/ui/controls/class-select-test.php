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

use Mundschenk\UI\Controls\Select;
use Mundschenk\Data_Storage\Options;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use Mockery as m;

/**
 * Mundschenk\UI\Controls\Select unit test.
 *
 * @coversDefaultClass \Mundschenk\UI\Controls\Select
 * @usesDefaultClass \Mundschenk\UI\Controls\Select
 *
 * @uses ::__construct
 * @uses \Mundschenk\UI\Abstract_Control::__construct
 * @uses \Mundschenk\UI\Abstract_Control::prepare_args
 */
class Select_Test extends \Mundschenk\UI\Tests\TestCase {

	/**
	 * Test fixture.
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * Test fixture.
	 *
	 * @var \Mundschenk\UI\Controls\Select
	 */
	protected $select;

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

		$this->select = m::mock( Select::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$args = [
			'tab_id'            => 'my_tab_id',
			'section'           => 'my_section',
			'default'           => 'my_default',
			'short'             => 'my_short',
			'label'             => 'my_label',
			'help_text'         => 'my_help_text',
			'attributes'        => [ 'foo' => 'bar' ],
			'outer_attributes'  => [ 'foo' => 'bar' ],
			'inline_help'       => false,
			'option_values'     => [ 'option', 'values', 'three' ],
			'settings_args'     => [ 'my' => 'settings_arg' ],
			'sanitize_callback' => 'my_sanitize_function',
		];

		$this->select->shouldReceive( 'prepare_args' )->once()->with( $args, [ 'tab_id', 'default', 'option_values' ] )->andReturn( $args );

		$this->invokeMethod( $this->select, '__construct', [ $this->options, 'options_key', 'my_id', $args ], Select::class );
	}

	/**
	 * Test constructor.
	 *
	 * @covers ::__construct
	 *
	 * @uses \Mundschenk\UI\Controls\Select::__construct
	 */
	public function test_constructor() {
		$select = m::mock( Select::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$args = [
			'tab_id'            => 'my_tab_id',
			'section'           => 'my_section',
			'default'           => 'my_default',
			'short'             => 'my_short',
			'label'             => 'my_label',
			'help_text'         => 'my_help_text',
			'inline_help'       => false,
			'attributes'        => [ 'foo' => 'bar' ],
			'outer_attributes'  => [ 'foo' => 'bar' ],
			'option_values'     => [ 'option', 'values' ],
			'settings_args'     => [ 'my' => 'settings_arg' ],
			'sanitize_callback' => 'my_sanitize_function',
		];

		$select->shouldReceive( 'prepare_args' )->once()->with( $args, [ 'tab_id', 'default', 'option_values' ] )->andReturn( $args );

		$this->invokeMethod( $select, '__construct', [ $this->options, 'options_key', 'my_id', $args ], Select::class );

		$this->assertSame( [ 'option', 'values' ], $this->getValue( $select, 'option_values', Select::class ) );
	}

	/**
	 * Tests set_option_values.
	 *
	 * @covers ::set_option_values
	 */
	public function test_set_option_values() {
		$option_values = [
			'my',
			'option',
			'values',
		];

		$this->select->set_option_values( $option_values );

		$this->assertSame( $option_values, $this->getValue( $this->select, 'option_values', Select::class ) );
	}

	/**
	 * Tests get_value.
	 *
	 * @covers ::get_value
	 */
	public function test_get_value() {
		$this->options->shouldReceive( 'get' )->once()->with( 'options_key' )->andReturn(
			[
				'foo'   => 'bar',
				'my_id' => 2,
			]
		);

		$this->assertSame( 2, $this->invokeMethod( $this->select, 'get_value' ) );
	}

	/**
	 * Tests get_value when value is not in options.
	 *
	 * @covers ::get_value
	 */
	public function test_get_value_unsuccessful() {
		$this->options->shouldReceive( 'get' )->once()->with( 'options_key' )->andReturn(
			[
				'foo'   => 'bar',
				'my_id' => 'foobar',
			]
		);

		$this->assertNull( $this->invokeMethod( $this->select, 'get_value' ) );
	}

	/**
	 * Tests get_element_markup.
	 *
	 * @covers ::get_element_markup
	 */
	public function test_get_element_markup() {
		$option_count = count( $this->getValue( $this->select, 'option_values', Select::class ) );

		Functions\expect( 'esc_html' )->times( $option_count )->with( m::type( 'string' ) )->andReturn( 'DISPLAY' );
		Functions\expect( 'selected' )->times( $option_count )->with( 'value', m::type( 'int' ), false )->andReturn( 'SELECTED' );
		Functions\expect( 'esc_attr' )->times( $option_count )->with( m::type( 'int' ) )->andReturn( 'VALUE' );

		$this->select->shouldReceive( 'get_value' )->once()->andReturn( 'value' );
		$this->select->shouldReceive( 'get_id_and_class_markup' )->once()->andReturn( 'ID_AND_CLASS' );

		$this->assertRegExp( "#<select ID_AND_CLASS>(<option value=\"VALUE\" SELECTED>DISPLAY</option>){{$option_count}}</select>#", $this->invokeMethod( $this->select, 'get_element_markup' ) );
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
			'option_values' => [ 1, 2 ],
		];
		$this->assertInstanceOf( Select::class, Select::create( $this->options, 'my_options', 'my_control_id', $args ) );
	}
}
