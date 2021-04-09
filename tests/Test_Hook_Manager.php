<?php

declare(strict_types=1);

/**
 * Hook Manger unit tests.
 * Uses Mock Manager
 *
 * @since 1.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Loader
 */

namespace PinkCrab\Loader\Tests;

use WP_UnitTestCase;
use ReflectionFunction;
use PinkCrab\Loader\Hook;
use Automattic\Jetpack\Constants;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Loader\Tests\Fixtures\Hook_Manager_Object_Mock;
use PinkCrab\Loader\Exceptions\Invalid_Hook_Callback_Exception;

class Test_Hook_Manager extends WP_UnitTestCase {

	/** @testdox When a hook is passed to the manager, it should be directed to the correct method for registration. */
	public function test_hooks_are_processed_by_correct_register_method() {
		// Create all hook types
		$action = ( new Hook( 'action', 'is_string' ) )->type( Hook::ACTION );
		$filter = ( new Hook( 'filter', 'is_bool' ) )->type( Hook::FILTER );
		$remove = ( new Hook( 'remove', 'is_int' ) )->type( Hook::REMOVE );

		$manager = new Hook_Manager_Object_Mock();

		// Process them.
		$manager->process_hook( $action );
		$manager->process_hook( $filter );
		$manager->process_hook( $remove );

		$this->assertCount( 1, $manager->_hooks['actions'] );
		$this->assertEquals( 'is_string', $manager->_hooks['actions']['action']['callback'] );

		$this->assertCount( 1, $manager->_hooks['filters'] );
		$this->assertEquals( 'is_bool', $manager->_hooks['filters']['filter']['callback'] );

		$this->assertCount( 1, $manager->_hooks['remove'] );
		$this->assertEquals( 'is_int', $manager->_hooks['remove']['remove']['callback'] );
	}

	/** @testdox When an invalid hook type is passed, it should not be processed. */
	public function test_invalid_hook_types_are_not_processed(): void {
		$invalid = ( new Hook( 'remove', 'is_int' ) )->type( 'INVALID' );
		$manager = new Hook_Manager_Object_Mock();
		$invalid = $manager->process_hook( $invalid );
		$this->assertFalse( $invalid->is_registered() );
	}

	public function FunctionName(Type $var = null)
	{
		# code...
	}
}
