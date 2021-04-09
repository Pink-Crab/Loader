<?php

declare(strict_types=1);

/**
 * Tests for the Loader alias.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Loader
 */

namespace PinkCrab\Loader\Tests;

use PinkCrab\Loader\Loader;
use PHPUnit\Framework\TestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;

class Test_Loader extends TestCase {

	/** @testdox It should be possible to use the old Loader class as an alias for the Hook_Loader */
	public function test_loader_extends_hook_loader() {
		$loader = new Loader();
		$loader->action( 'loader', 'is_string' );
		$loader->filter( 'loader', 'is_string' );
		$loader->ajax( 'loader', 'is_string' );
		$loader->remove( 'loader', 'is_string' );
		$this->assertCount( 4, Objects::get_property( $loader, 'hooks' ) );
	}

	/** @testdox It should be possible to use the static "boot()" method to construct a valid instance of a Loader. */
	public function test_use_boot_static_on_loader() {
		$loader = Loader::boot();
		$this->assertInstanceOf( Loader::class, $loader );
		$this->assertInstanceOf( Hook_Loader::class, $loader );
	}
}
