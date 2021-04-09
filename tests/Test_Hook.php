<?php

declare(strict_types=1);

/**
 * Hook Model tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Loader
 */

namespace PinkCrab\Loader\Tests;

use PinkCrab\Loader\Hook;
use PHPUnit\Framework\TestCase;

class Test_Hook extends TestCase {

	public function test_can_create_base_model_with_constructor() {
		$hook = new Hook( 'my_action', 'is_int', 999, 2 );
		$this->assertEquals( Hook::ACTION, $hook->get_type() );
		$this->assertEquals( 'my_action', $hook->get_handle() );
		$this->assertEquals( 'is_int', $hook->get_callback() );
		$this->assertEquals( 999, $hook->get_priority() );
		$this->assertEquals( 2, $hook->args_count() );

		// Check defaults.
		$hook_default = new Hook( 'my_filter', 'is_string' );
		$this->assertEquals( Hook::ACTION, $hook_default->get_type() );
		$this->assertEquals( 'my_filter', $hook_default->get_handle() );
		$this->assertEquals( 'is_string', $hook_default->get_callback() );
		$this->assertEquals( 10, $hook_default->get_priority() );
		$this->assertEquals( 1, $hook_default->args_count() );
	}

	/** @testdox Can set a hook with a type and that type setter is fluent */
	public function test_can_set_type(): void {
		$hook = new Hook( 'my_filter', 'is_string' );
		$this->assertEquals( Hook::ACTION, $hook->get_type() );

		$hook_filter = $hook->type( HOOK::FILTER );
		$this->assertEquals( Hook::FILTER, $hook_filter->get_type() );

		$hook_ajax = $hook_filter->type( HOOK::AJAX );
		$this->assertEquals( Hook::AJAX, $hook_ajax->get_type() );

		$hook_shortcode = $hook_ajax->type( HOOK::SHORTCODE );
		$this->assertEquals( Hook::SHORTCODE, $hook_shortcode->get_type() );

		$hook_action = $hook_shortcode->type( HOOK::ACTION );
		$this->assertEquals( Hook::ACTION, $hook_action->get_type() );

		$hook_remove = $hook_action->type( HOOK::REMOVE );
		$this->assertEquals( Hook::REMOVE, $hook_remove->get_type() );
	}

	/** @testdox Can modify and get the hooks handle */
	public function test_handle_methods(): void {
		$hook = new Hook( 'empty', 'is_string' );
		$hook->handle( 'test' );
		$this->assertEquals( 'test', $hook->get_handle() );
	}

	/** @testdox Can modify and get the hooks callback */
	public function test_callback_methods(): void {
		$hook = new Hook( 'foo', 'is_bool' );
		$hook->callback( 'is_string' );
		$this->assertEquals( 'is_string', $hook->get_callback() );
	}

	/** @testdox Can modify and get the hooks priority */
	public function test_priority_methods(): void {
		$hook = new Hook( 'foo', 'is_bool', 42 );
		$hook->priority( 420 );
		$this->assertEquals( 420, $hook->get_priority() );
	}

	/** @testdox Can modify and get the hooks args count */
	public function test_args_methods(): void {
		$hook = new Hook( 'foo', 'is_bool', 42, 1 );
		$hook->args( 2 );
		$this->assertEquals( 2, $hook->args_count() );
	}

	/** @testdox Can set and recall if the defined ajax hook is private */
	public function test_ajax_private_methods(): void {
		$hook = new Hook( 'foo', 'is_bool' );
		$hook->ajax_private( false );
		$this->assertFalse( $hook->is_ajax_private() );
		$hook->ajax_private();
		$this->assertTrue( $hook->is_ajax_private() );
	}

    /** @testdox Can set and recall if the defined ajax hook is public */
	public function test_ajax_public_methods(): void {
		$hook = new Hook( 'foo', 'is_bool' );
		$hook->ajax_public( false );
		$this->assertFalse( $hook->is_ajax_public() );
		$hook->ajax_public();
		$this->assertTrue( $hook->is_ajax_public() );
	}

    /** @testdox Can toggle the hook being called on the backend */
	public function test_admin_methods(): void {
		$hook = new Hook( 'foo', 'is_bool' );
		$hook->admin( false );
		$this->assertFalse( $hook->is_admin() );
		$hook->admin();
		$this->assertTrue( $hook->is_admin() );
	}

    /** @testdox Can toggle the hook being called on the frontend */
	public function test_front_methods(): void {
		$hook = new Hook( 'foo', 'is_bool' );
		$hook->front( false );
		$this->assertFalse( $hook->is_front() );
		$hook->front();
		$this->assertTrue( $hook->is_front() );
	}

    /** @testdox Can toggle if the hook has been registered */
	public function test_registered_methods(): void {
		$hook = new Hook( 'foo', 'is_bool' );
		$hook->registered( false );
		$this->assertFalse( $hook->is_registered() );
		$hook->registered();
		$this->assertTrue( $hook->is_registered() );
	}
}
