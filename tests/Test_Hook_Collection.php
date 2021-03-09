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
use PinkCrab\Loader\Hook_Collection;

class Test_Hook_Collection extends TestCase {

	public function test_push(): void {
		$collection = new Hook_Collection;
		$collection->push( array( 'some data' ) );
		$this->assertCount( 1, $collection );
	}

	public function test_can_register_hooks(): void {
		$collection = new Hook_Collection;
		$collection->push(
			array(
				'string'     => 'some output',
				'registered' => false,
			)
		);

		$this->expectOutputString( 'some output' );
		$collection->register(
			function( $hook ) {
				print( $hook['string'] );
			}
		);
	}

	public function test_can_pop_hook_from_collection(): void {
		$data       = array( 'data' );
		$collection = new Hook_Collection;
		$collection->push( array( 'some data' ) );
		$collection->push( $data );

		$this->assertSame( $data, $collection->pop() );
	}
}
