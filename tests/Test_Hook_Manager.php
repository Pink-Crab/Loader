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
use InvalidArgumentException;
use PinkCrab\Loader\Hook_Removal;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Loader\Tests\Fixtures\Hook_Manager_Object_Mock;

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

	/** @testdox When a deferred hook is processed, a new hook should be created using the defined hook handle and priority. Its callback should be set to run add_action, add_filter with the itnital data, on the deferred hook call. */
	public function test_can_map_deferred_hook() {
		$inital_hook = ( new Hook( 'inital_hook', 'is_string' ) )
			->deferred_hook( 'deferred_hook' );

		$manager = new Hook_Manager_Object_Mock();
		$manager->process_hook( $inital_hook );

		// Check deferred hook added and grab details
		$this->assertArrayHasKey( 'deferred_hook', $manager->_hooks['actions'] );
		$deferred_hook_details = $manager->_hooks['actions']['deferred_hook'];

		// Get callback details
		$reflected_callback = new ReflectionFunction( $deferred_hook_details['callback'] );
		$reflected_use_args = $reflected_callback->getStaticVariables();
		$this->assertSame( $inital_hook, $reflected_use_args['hook'] );
	}
}
