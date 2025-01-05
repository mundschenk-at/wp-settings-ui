<?php
/**
 *  This file is part of PHP-Typography.
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
 *  @package mundschenk-at/php-typography/tests
 *  @license http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Mundschenk\UI\Tests;

use Brain\Monkey;

/**
 * Abstract base class for \PHP_Typography\* unit tests.
 */
abstract class TestCase extends \Mundschenk\PHPUnit_Cross_Version\TestCase {

	/**
	 * Call protected/private method of a class.
	 *
	 * @param object        $instance    Instantiated object that we will run method on.
	 * @param string        $method_name Method name to call.
	 * @param mixed[]       $parameters  Array of parameters to pass into method.
	 * @param ?class-string $classname   Optional. The class to use for accessing private properties.
	 *
	 * @return mixed Method return.
	 */
	protected function invokeMethod( $instance, $method_name, array $parameters = [], $classname = null ) {
		if ( empty( $classname ) ) {
			$classname = \get_class( $instance );
		}

		$reflection = new \ReflectionClass( $classname );
		$method     = $reflection->getMethod( $method_name );
		$method->setAccessible( true );

		return $method->invokeArgs( $instance, $parameters );
	}

	/**
	 * Call protected/private method of a class.
	 *
	 * @param class-string $classname   A class that we will run the method on.
	 * @param string       $method_name Method name to call.
	 * @param mixed[]      $parameters  Array of parameters to pass into method.
	 *
	 * @return mixed                    Method return value.
	 */
	protected function invokeStaticMethod( $classname, $method_name, array $parameters = [] ) {
		$reflection = new \ReflectionClass( $classname );
		$method     = $reflection->getMethod( $method_name );
		$method->setAccessible( true );

		return $method->invokeArgs( null, $parameters );
	}


	/**
	 * Sets the value of a private/protected property of a class.
	 *
	 * @param class-string $classname     A class whose property we will access.
	 * @param string       $property_name Property to set.
	 * @param mixed|null   $value         The new value.
	 */
	protected function setStaticValue( $classname, $property_name, $value ): void {
		$reflection = new \ReflectionClass( $classname );
		$property   = $reflection->getProperty( $property_name );
		$property->setAccessible( true );
		$property->setValue( null, $value );
	}

	/**
	 * Sets the value of a private/protected property of a class.
	 *
	 * @param object        $instance      Instantiated object that we will run method on.
	 * @param string        $property_name Property to set.
	 * @param mixed|null    $value         The new value.
	 * @param ?class-string $classname     Optional. The class to use for accessing private properties.
	 */
	protected function setValue( $instance, $property_name, $value, $classname = null ): void {
		if ( empty( $classname ) ) {
			$classname = \get_class( $instance );
		}

		$reflection = new \ReflectionClass( $classname );
		$property   = $reflection->getProperty( $property_name );
		$property->setAccessible( true );
		$property->setValue( $instance, $value );
	}

	/**
	 * Retrieves the value of a private/protected property of a class.
	 *
	 * @param class-string $classname     A class whose property we will access.
	 * @param string       $property_name Property to set.
	 *
	 * @return mixed
	 */
	protected function getStaticValue( $classname, $property_name ) {
		$reflection = new \ReflectionClass( $classname );
		$property   = $reflection->getProperty( $property_name );
		$property->setAccessible( true );

		return $property->getValue();
	}

	/**
	 * Retrieves the value of a private/protected property of a class.
	 *
	 * @param object        $instance      Instantiated object that we will run method on.
	 * @param string        $property_name Property to set.
	 * @param ?class-string $classname     Optional. The class to use for accessing private properties.
	 *
	 * @return mixed
	 */
	protected function getValue( $instance, $property_name, $classname = null ) {
		if ( empty( $classname ) ) {
			$classname = \get_class( $instance );
		}

		$reflection = new \ReflectionClass( $classname );
		$property   = $reflection->getProperty( $property_name );
		$property->setAccessible( true );

		return $property->getValue( $instance );
	}

	/**
	 * Reports an error identified by $message if $attribute in $instance does not have the $key.
	 *
	 * @param string $key       The array key.
	 * @param string $attribute The attribute name.
	 * @param object $instance  The object.
	 * @param string $message   Optional. Default ''.
	 */
	protected function assertAttributeArrayHasKey( $key, $attribute, $instance, $message = '' ): void {
		$ref  = new \ReflectionClass( \get_class( $instance ) );
		$prop = $ref->getProperty( $attribute );
		$prop->setAccessible( true );

		$this->assertArrayHasKey( $key, $prop->getValue( $instance ), $message );
	}

	/**
	 * Reports an error identified by $message if $attribute in $instance does have the $key.
	 *
	 * @param string $key       The array key.
	 * @param string $attribute The attribute name.
	 * @param object $instance  The object.
	 * @param string $message   Optional. Default ''.
	 */
	protected function assertAttributeArrayNotHasKey( $key, $attribute, $instance, $message = '' ): void {
		$ref  = new \ReflectionClass( \get_class( $instance ) );
		$prop = $ref->getProperty( $attribute );
		$prop->setAccessible( true );

		$this->assertArrayNotHasKey( $key, $prop->getValue( $instance ), $message );
	}
}
