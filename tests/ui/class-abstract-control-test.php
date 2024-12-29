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

namespace Mundschenk\UI\Tests;

use Mundschenk\UI\Abstract_Control;
use Mundschenk\Data_Storage\Options;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use org\bovigo\vfs\vfsStream;

use Mockery as m;

/**
 * Mundschenk\UI\Abstract_Control unit test.
 *
 * @coversDefaultClass \Mundschenk\UI\Abstract_Control
 * @usesDefaultClass \Mundschenk\UI\Abstract_Control
 *
 * @uses ::__construct
 */
class Abstract_Control_Test extends \Mundschenk\UI\Tests\TestCase {

	/**
	 * Test fixture.
	 *
	 * @var Options&m\MockInterface
	 */
	protected Options $options;

	/**
	 * Test fixture.
	 *
	 * @var Abstract_Control&m\MockInterface
	 */
	protected Abstract_Control $control;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function set_up() {
		parent::set_up();

		// Set up virtual filesystem.
		$filesystem = [
			'plugin' => [
				'partials' => [
					'control.php' => 'CONTROL',
				],
			],
		];
		vfsStream::setup( 'root', null, $filesystem );
		set_include_path( 'vfs://root/' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_set_include_path

		// Mock Mundschenk\Data_Storage\Options instance.
		$this->options = m::mock( Options::class ) // @phpstan-ignore method.notFound
			->shouldReceive( 'get' )->andReturn( false )->byDefault()
			->shouldReceive( 'set' )->andReturn( false )->byDefault()
			->getMock();

		$this->control = m::mock( Abstract_Control::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$this->invokeMethod( $this->control, '__construct', [ $this->options, 'options_key', 'id', 'tab_id', 'section', 'default', 'short', 'label', 'help_text', true, [], [], [ 'my' => 'settings_arg' ] ], Abstract_Control::class );
	}

	/**
	 * Test constructor.
	 *
	 * @covers ::__construct
	 */
	public function test_constructor(): void {
		$control = m::mock( Abstract_Control::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$params = [
			$this->options,
			'options_key',
			'id',
			'tab_id',
			'section',
			'default',
			'short',
			'label',
			'help_text',
			true,
			[ 'foo' => 'bar' ],
			[ 'bar' => 'foo' ],
			[ 'test' => 'value' ],
		];

		$this->invokeMethod( $control, '__construct', $params, Abstract_Control::class );

		$this->assert_attribute_same( 'id', 'id', $control );
		$this->assert_attribute_same( 'tab_id', 'tab_id', $control );
		$this->assert_attribute_same( 'section', 'section', $control );
		$this->assert_attribute_same( 'default', 'default', $control );
		$this->assert_attribute_same( 'short', 'short', $control );
		$this->assert_attribute_same( 'label', 'label', $control );
		$this->assert_attribute_same( 'help_text', 'help_text', $control );
		$this->assert_attribute_same( true, 'inline_help', $control );
		$this->assert_attribute_same( [ 'foo' => 'bar' ], 'attributes', $control );
		$this->assert_attribute_same( [ 'bar' => 'foo' ], 'outer_attributes', $control );
		$this->assert_attribute_same( [ 'test' => 'value' ], 'settings_args', $control );
	}

	/**
	 * Test prepare_args.
	 *
	 * @covers ::prepare_args
	 */
	public function test_prepare_args(): void {
		$input = [
			'foo'    => 'bar',
			'tab_id' => 'my_tab',
		];

		$expected = [
			'foo'               => 'bar',
			'tab_id'            => 'my_tab',
			'section'           => 'my_tab',
			'short'             => null,
			'label'             => null,
			'help_text'         => null,
			'inline_help'       => false,
			'attributes'        => [],
			'outer_attributes'  => [],
			'settings_args'     => [],
			'sanitize_callback' => null,
		];

		Functions\expect( 'wp_parse_args' )->twice()->andReturnUsing(
			static function ( $array1, $array2 ) {
				return \array_merge( $array2, $array1 );
			}
		);

		$result = $this->invokeMethod( $this->control, 'prepare_args', [ $input, [] ] );
		\ksort( $expected );
		\ksort( $result );

		$this->assertSame( $expected, $result );
	}

	/**
	 * Test get_value.
	 *
	 * @covers ::get_value
	 */
	public function test_get_value(): void {
		$this->options->shouldReceive( 'get' )->once()->with( 'options_key' )->andReturn( [ 'foo' => 'bar' ] );
		$this->setValue( $this->control, 'id', 'foo' );

		$this->assertSame( 'bar', $this->control->get_value() );
	}

	/**
	 * Test get_value with empty options key.
	 *
	 * @covers ::get_value
	 */
	public function test_get_value_empty_options_key(): void {
		$this->setValue( $this->control, 'options_key', '', Abstract_Control::class );
		$this->setValue( $this->control, 'id', 'foo', Abstract_Control::class );

		$this->options->shouldReceive( 'get' )->once()->with( 'foo' )->andReturn( 'bar' );

		$this->assertSame( 'bar', $this->control->get_value() );
	}

	/**
	 * Test render_element.
	 *
	 * @covers ::render_element
	 */
	public function test_render_element(): void {
		$this->control->shouldReceive( 'get_element_markup' )->once()->andReturn( '<foo>' );
		$this->expectOutputString( '<foo>' );

		$this->invokeMethod( $this->control, 'render_element' );
	}

	/**
	 * Test get_inner_html_attributes.
	 *
	 * @covers ::get_html_attributes
	 * @covers ::get_html_attributes
	 *
	 * @uses ::get_inner_html_attributes
	 */
	public function test_get_inner_html_attributes(): void {
		$attributes = [
			'foo' => 'bar',
			'rel' => 'self',
		];
		$this->setValue( $this->control, 'attributes', $attributes );

		Functions\expect( 'esc_attr' )->times( count( $attributes ) * 2 )->andReturnUsing(
			static function ( $input ) {
				return $input;
			}
		);

		$this->assertSame( 'foo="bar" rel="self" ', $this->invokeMethod( $this->control, 'get_inner_html_attributes' ) );
	}

	/**
	 * Test get_default.
	 *
	 * @covers ::get_default
	 */
	public function test_get_default(): void {
		$this->assertSame( 'default', $this->control->get_default() );
	}

	/**
	 * Test get_id.
	 *
	 * @covers ::get_id
	 */
	public function test_get_id(): void {
		$this->options->shouldReceive( 'get_name' )->once()->with( 'options_key' )->andReturn( 'typo_configuration' );

		$this->assertSame( 'typo_configuration[id]', $this->control->get_id() );
	}

	/**
	 * Test get_id.
	 *
	 * @covers ::get_id
	 */
	public function test_get_id_empty_options_iey(): void {
		$this->setValue( $this->control, 'options_key', '', Abstract_Control::class );

		$this->options->shouldReceive( 'get_name' )->once()->with( 'id' )->andReturn( 'foobar' );

		$this->assertSame( 'foobar', $this->control->get_id() );
	}

	/**
	 * Test get_id_and_class_markup.
	 *
	 * @covers ::get_id_and_class_markup
	 *
	 * @uses ::get_inner_html_attributes
	 */
	public function test_get_id_and_class_markup(): void {
		Functions\expect( 'esc_attr' )->once()->with( 'foo[bar]' )->andReturn( 'foo[bar]' );

		$this->control->shouldReceive( 'get_id' )->once()->andReturn( 'foo[bar]' );
		$this->control->shouldReceive( 'get_html_attributes' )->once()->andReturn( 'foo="bar"' );

		$this->assertSame( 'id="foo[bar]" name="foo[bar]" foo="bar" aria-describedby="foo[bar]-description"', $this->invokeMethod( $this->control, 'get_id_and_class_markup' ) );
	}

	/**
	 * Test label_has_placeholder.
	 *
	 * @covers ::label_has_placeholder
	 */
	public function test_label_has_placeholder(): void {
		$this->setValue( $this->control, 'label', 'My label' );
		$this->assertFalse( $this->invokeMethod( $this->control, 'label_has_placeholder' ) );

		$this->setValue( $this->control, 'label', 'My %1$s label' );
		$this->assertTrue( $this->invokeMethod( $this->control, 'label_has_placeholder' ) );
	}

	/**
	 * Test has_inline_help.
	 *
	 * @covers ::has_inline_help
	 */
	public function test_has_inline_help(): void {
		$this->assertTrue( $this->invokeMethod( $this->control, 'has_inline_help' ) );

		$this->setValue( $this->control, 'help_text', false );
		$this->assertFalse( $this->invokeMethod( $this->control, 'has_inline_help' ) );
	}

	/**
	 * Test get_label.
	 *
	 * @covers ::get_label
	 */
	public function test_get_label_with_placeholder(): void {
		$this->setValue( $this->control, 'label', 'My %1$s label' );

		$this->control->shouldReceive( 'label_has_placeholder' )->once()->andReturn( true );
		$this->control->shouldReceive( 'get_element_markup' )->once()->andReturn( '<element>' );

		$this->assertSame( 'My <element> label', $this->invokeMethod( $this->control, 'get_label' ) );
	}

	/**
	 * Test get_label.
	 *
	 * @covers ::get_label
	 */
	public function test_get_label_no_placeholder(): void {
		$this->control->shouldReceive( 'label_has_placeholder' )->once()->andReturn( false );
		$this->control->shouldReceive( 'get_element_markup' )->never();

		$this->assertSame( 'label', $this->invokeMethod( $this->control, 'get_label' ) );
	}

	/**
	 * Test register.
	 *
	 * @covers ::register
	 */
	public function test_register(): void {
		$this->control->shouldReceive( 'get_id' )->once()->andReturn( 'id' );
		Functions\expect( 'add_settings_field' )->once()->with( 'id', 'short', [ $this->control, 'render' ], 'option_group_tab_id', 'section', [ 'my' => 'settings_arg' ] );

		$this->assertNull( $this->control->register( 'option_group_' ) );
	}

	/**
	 * Test add_grouped_control.
	 *
	 * @covers ::add_grouped_control
	 *
	 * @uses ::group_with
	 */
	public function test_add_grouped_control(): void {
		/**
		 * Control mock.
		 *
		 * @var Abstract_Control&m\MockInterface $second_control
		 */
		$second_control = m::mock( Abstract_Control::class )
			->shouldAllowMockingProtectedMethods()
			->makePartial();

		$this->control->add_grouped_control( $second_control );

		$this->assert_attribute_same( $this->control, 'grouped_with', $second_control );
		$this->assert_attribute_contains( $second_control, 'grouped_controls', $this->control );
	}

	/**
	 * Test add_grouped_control.
	 *
	 * @covers ::add_grouped_control
	 */
	public function test_add_grouped_control_failure(): void {

		$this->control->add_grouped_control( $this->control );

		$this->assertNotSame( $this->get_value( $this->control, 'grouped_with' ), $this->control );
		$this->assert_attribute_not_contains( $this->control, 'grouped_controls', $this->control );
	}

	/**
	 * Test render.
	 *
	 * @covers ::render
	 */
	public function test_render(): void {
		$this->setValue( $this->control, 'base_path', 'plugin' );

		$this->expectOutputString( 'CONTROL' );

		$this->control->render();
	}
}
