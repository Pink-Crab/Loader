<?php

declare(strict_types=1);

/**
 * Hook_Removal tests.
 *
 * @since 0.3.6
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Loader
 */

namespace PinkCrab\Loader\Tests;

use WP_UnitTestCase;
use InvalidArgumentException;
use PinkCrab\Loader\Hook_Removal;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Loader\Tests\Fixtures\{Hooks_Via_Static,Hooks_Via_Instance};

class Test_Hook_Removal extends WP_UnitTestCase {

	/** @testdox Callback which is a static methods on classes, can be used to remove an action, by passing the correct class and method name (like a regular callable). */
	public function test_can_remove_static_action() {
		// Register action.
		( new Hooks_Via_Static() )->register_action();

		$response = ( new Hook_Removal(
			Hooks_Via_Static::ACTION_HANDLE,
			array( Hooks_Via_Static::class, 'action_callback_static' )
		) )->remove();

		$this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter'][ Hooks_Via_Static::ACTION_HANDLE ]->callbacks[10] );
	}

	/** @testdox Callback which is a static methods on classes, can be used to remove a filter, by passing the correct class and method name (like a regular callable). */
	public function test_can_remove_static_filter() {
		// Register action.
		( new Hooks_Via_Static() )->register_filter();

		$response = ( new Hook_Removal(
			Hooks_Via_Static::FILTER_HANDLE,
			array( Hooks_Via_Static::class, 'filter_callback_static' )
		) )->remove();

		$this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter'][ Hooks_Via_Static::FILTER_HANDLE ]->callbacks[10] );
	}

	/** @testdox Callback which is a method on an instaniated classes, can be used to remove an action, by passing the same class instance and method name (like a regular callable). */
	public function test_can_remove_instanced_action() {
		// Register action.
		( new Hooks_Via_Instance() )->register_action();

		$response = ( new Hook_Removal(
			Hooks_Via_Instance::ACTION_HANDLE,
			array( new Hooks_Via_Instance(), 'action_callback_instance' )
		) )->remove();

		$this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter'][ Hooks_Via_Instance::ACTION_HANDLE ]->callbacks[10] );
	}

	/** @testdox Callback which is a method on an instaniated classes, can be used to remove an filter, by passing the same class instance and method name (like a regular callable). */
	public function test_can_remove_instanced_filter() {
		// Register action.
		( new Hooks_Via_Instance() )->register_filter();

		$response = ( new Hook_Removal(
			Hooks_Via_Instance::FILTER_HANDLE,
			array( new Hooks_Via_Instance(), 'filter_callback_instance' )
		) )->remove();

		$this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter'][ Hooks_Via_Instance::FILTER_HANDLE ]->callbacks[10] );
	}

	/** @testdox Callback which is a globally registere function can be removed by passing its name (like a regular callable). */
	public function test_can_remove_global_functon() {
		add_action( 'test_global_function', 'pc_tests_noop' );
		$response = ( new Hook_Removal( 'test_global_function', 'pc_tests_noop' ) )
			->remove();

		$this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter']['test_global_function']->callbacks[10] );
	}

	/** @testdox Callbacks which are anonymous functions should fail due to anonymous functions/class being ignored.*/
	public function test_returns_false_for_closures(): void {
		add_action(
			'clousre_hook',
			function() {
				echo 'THIS CAN NOT BE REMOVED';
			}
		);
		$this->assertFalse(
			( new Hook_Removal(
				'clousre_hook',
				function() {
					echo 'THIS CAN NOT BE REMOVED';
				}
			) )->remove()
		);
	}

	/** @testdox Any none valid callback value is passed which can either be a callable or an array containing a class name/isntance of method, will not be processed(removed)  */
	public function test_retruns_empty_class_array_if_not_an_array() {
		$hook_remover = new Hook_Removal( 'fake_handle', array( new Hooks_Via_Instance(), 'action_callback_instance' ) );

		// Mock a none valid class.
		Objects::set_property( $hook_remover, 'callback', 'NOT ARRAY' );
		$callback_as_array = Objects::invoke_method( $hook_remover, 'get_callback_as_array' );
		$this->assertEmpty( $callback_as_array['class'] );
		$this->assertEmpty( $callback_as_array['method'] );
	}

	/**
	 * Tests just the name of a class whose hook was registered as an instance
	 * can be removed.
	 *
	 * @return void
	 */
	public function test_can_use_name_for_instanced_hook() {
		// Register action.
		( new Hooks_Via_Instance() )->register_filter();

		$response = ( new Hook_Removal(
			Hooks_Via_Instance::FILTER_HANDLE,
			array( Hooks_Via_Instance::class, 'filter_callback_instance' )
		) )->remove();

		$this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter'][ Hooks_Via_Instance::FILTER_HANDLE ]->callbacks[10] );
	}

	/** @testdox Should none callable or array be passed as the callback, the system should hault with an error */
	public function test_exception_thrown_for_invalid_callback_type() {
		$this->expectException( InvalidArgumentException::class );
		new Hook_Removal(
			Hooks_Via_Instance::FILTER_HANDLE,
			12.45
		);
	}

	/** @testdox Should an array with more than 2 elements be passed, the system should hault with an error */
	public function test_exception_thrown_for_invalid_callback_array_too_long() {
		$this->expectException( InvalidArgumentException::class );
		new Hook_Removal(
			Hooks_Via_Instance::FILTER_HANDLE,
			array( Hooks_Via_Instance::class, 'filter_callback_instance', 'too', 'many' )
		);
	}

	/** @testdox Should a class (name or instance) be passed as the first value of a callback array, the system should hault with an error */
	public function test_exception_thrown_for_invalid_callback_class() {
		$this->expectException( InvalidArgumentException::class );
		new Hook_Removal(
			Hooks_Via_Instance::FILTER_HANDLE,
			array( 'Class_Thats_Not', 'function' )
		);
	}

	/** @testdox Should a method be passed as the 2nd value of the callback array, which doesnt exist on the class. The system should hault with an error */
	public function test_exception_thrown_for_invalid_callback_method() {
		$this->expectException( InvalidArgumentException::class );
		new Hook_Removal(
			Hooks_Via_Instance::FILTER_HANDLE,
			array( Hooks_Via_Instance::class, 'fake_method' )
		);
	}
}
