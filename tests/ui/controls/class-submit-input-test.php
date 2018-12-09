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

use Mundschenk\UI\Controls\Submit_Input;
use Mundschenk\UI\Controls\Input;
use Mundschenk\Data_Storage\Options;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use Mockery as m;

/**
 * Mundschenk\UI\Controls\Submit_Input unit test.
 *
 * @coversDefaultClass \Mundschenk\UI\Controls\Submit_Input
 * @usesDefaultClass \Mundschenk\UI\Controls\Submit_Input
 *
 * @uses ::__construct
 * @uses \Mundschenk\UI\Controls\Input::__construct
 * @uses \Mundschenk\UI\Abstract_Control::__construct
 * @uses \Mundschenk\UI\Abstract_Control::prepare_args
 */
class Submit_Input_Test extends \Mundschenk\UI\Tests\TestCase {

	/**
	 * Test fixture.
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * Test fixture.
	 *
	 * @var \Mundschenk\UI\Controls\Submit_Input
	 */
	protected $input;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() { // @codingStandardsIgnoreLine
		parent::setUp();

		Functions\when( 'wp_parse_args' )->alias( 'array_merge' );

		// Mock Mundschenk\Data_Storage\Options instance.
		$this->options = m::mock( Options::class )
			->shouldReceive( 'get' )->andReturn( false )->byDefault()
			->shouldReceive( 'set' )->andReturn( false )->byDefault()
			->getMock();

		$this->input = m::mock( Submit_Input::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$args = [
			'tab_id'       => 'my_tab_id',
			'section'      => 'my_section',
			'default'      => 'my_default',
			'short'        => 'my_short',
			'label'        => 'my_label',
			'help_text'    => 'my_help_text',
			'inline_help'  => false,
			'attributes'   => [ 'foo' => 'bar' ],
			'button_class' => 'my_class',
		];

		$this->input->shouldReceive( 'prepare_args' )->once()->with( $args, [ 'button_class' ] )->andReturn( $args );

		$this->invokeMethod( $this->input, '__construct', [ $this->options, 'options_key', 'my_id', $args ], Submit_Input::class );
	}

	/**
	 * Test constructor.
	 *
	 * @covers ::__construct
	 *
	 * @uses \Mundschenk\UI\Controls\Input::__construct
	 */
	public function test_constructor() {
		$input = m::mock( Submit_Input::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$args = [
			'tab_id'       => 'my_tab_id',
			'section'      => 'my_section',
			'default'      => 'my_default',
			'short'        => 'my_short',
			'label'        => 'my_label',
			'help_text'    => 'my_help_text',
			'inline_help'  => false,
			'attributes'   => [ 'foo' => 'bar' ],
			'button_class' => 'my_class',
		];

		$input->shouldReceive( 'prepare_args' )->once()->with( $args, [ 'button_class' ] )->andReturn( $args );

		$this->invokeMethod( $input, '__construct', [ $this->options, 'options_key', 'my_id', $args ], Submit_Input::class );

		$this->assertSame( 'submit', $this->getValue( $input, 'input_type', Input::class ) );
		$this->assertAttributeSame( 'my_class', 'button_class', $input );
	}

	/**
	 * Tests get_id_and_class_markup.
	 *
	 * @covers ::get_id_and_class_markup
	 *
	 * @uses \Mundschenk\UI\Abstract_Control::get_inner_html_attributes
	 * @uses \Mundschenk\UI\Abstract_Control::get_id_and_class_markup
	 */
	public function test_get_id_and_class_markup() {
		Functions\expect( 'esc_attr' )->once()->with( 'my_id' )->andReturn( 'my_escaped_id' );
		Functions\expect( 'esc_attr' )->once()->with( 'my_class' )->andReturn( 'my_escaped_class' );

		$this->input->shouldReceive( 'get_id' )->once()->andReturn( 'my_id' );
		$this->input->shouldReceive( 'get_html_attributes' )->once()->andReturn( 'foo="bar"' );

		$this->assertSame( 'id="my_escaped_id" name="my_escaped_id" foo="bar" class="my_escaped_class"', $this->invokeMethod( $this->input, 'get_id_and_class_markup' ) );
	}

	/**
	 * Tests get_value.
	 *
	 * @covers ::get_value
	 */
	public function test_get_value() {
		$this->assertSame( 'my_label', $this->input->get_value() );
	}
}
