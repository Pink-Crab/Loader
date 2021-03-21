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

use PinkCrab\Loader\Hook;
use PHPUnit\Framework\TestCase;
use PinkCrab\Loader\Hook_Collection;

class Test_Hook_Collection extends TestCase {

	public function test_push(): void {
		$collection = new Hook_Collection;
		$collection->push( new Hook('action', 'is_string') );
		$this->assertCount( 1, $collection );
	}

	public function test_can_register_hooks(): void {
		$collection = new Hook_Collection;
		$collection->push(new Hook('action', 'is_string'));

		$this->expectOutputString( 'action' );
		$collection->register(
			function( $hook ) {
				print( $hook->get_handle() );
			}
		);
	}

	public function test_can_pop_hook_from_collection(): void {
		$data       = new Hook('action', 'is_string');
		$collection = new Hook_Collection;
		$collection->push( $data );

		$this->assertSame( $data, $collection->pop() );
	}
}
