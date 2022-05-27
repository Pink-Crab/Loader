<?php

declare(strict_types=1);

/**
 * Hook_Collection test
 *
 * @since 1.0.1
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Loader
 */

namespace PinkCrab\Loader\Tests;

use PHPUnit\Framework\TestCase;
use PinkCrab\Loader\{Hook,Hook_Collection};

class Test_Hook_Collection extends TestCase {

	/** @testdox When a Hook is pushed to a Hook_Collection is should be included in the collected */
	public function test_push(): void {
		$collection = new Hook_Collection;
		$collection->push( new Hook( 'action', 'is_string' ) );
		$this->assertCount( 1, $collection );
	}

	/** @testdox When being registered, the functionality will be applied to all Hooks */
	public function test_can_register_hooks(): void {
		$collection = new Hook_Collection;
		$collection->push( new Hook( 'action', 'is_string' ) );

		$this->expectOutputString( 'action' );
		$collection->register(
			function( $hook ) {
				print( $hook->get_handle() );
			}
		);
	}

	/** @testdox Hooks added to the collection can be removed one at a time as they were added */
	public function test_can_pop_hook_from_collection(): void {
		$data       = new Hook( 'action', 'is_string' );
		$collection = new Hook_Collection;
		$collection->push( $data );

		$this->assertSame( $data, $collection->pop() );
	}

	/** @testdox Attempting to get the last element and none of are set, it should return null.  */
	public function test_returns_null_if_pop_with_empty_collection(): void {
		$collection = new Hook_Collection;
		$this->assertNull( $collection->pop() );
	}

		/** @testdox It should be possible to filter the hooks, before they are registered. */
	public function test_can_filter_hooks_prior_to_registration(): void {
		$inital_hook       = new Hook( 'action', 'is_string' );
		$collection = new Hook_Collection;
		$collection->push( $inital_hook );
		
		add_filter( Hook_Collection::REGISTER_HOOKS, function( $hooks ) {
			// Change the first hooks handle.
			$hooks[0]->handle('changed_via_filter');

			// Add a custom hook.
			$hooks[] = new Hook( 'added_via_filter', 'is_string' );

			return $hooks;
		} );

		// Register hooks and keep a log.
		$registered_hooks = [];
		$collection->register(function($hook) use(&$registered_hooks) {
			$registered_hooks[] = $hook;
		});

		$this->assertCount(2, $registered_hooks);
		$this->assertEquals('changed_via_filter', $registered_hooks[0]->get_handle());
		$this->assertEquals('added_via_filter', $registered_hooks[1]->get_handle());
		$this->assertSame($inital_hook, $registered_hooks[0]);
	}
}
