<?php

declare(strict_types=1);

/**
 * Hook_Factory tests.
 *
 * @since 0.3.6
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Loader
 */

namespace PinkCrab\Loader\Tests;

use WP_UnitTestCase;
use PinkCrab\Loader\{Hook,Hook_Factory};
use PinkCrab\Loader\Tests\Fixtures\Hooks_Via_Static;

class Test_Hook_Factory extends WP_UnitTestCase
{
    /** @var Hook_Factory */
    protected $hook_factory;

    public function setup(): void
    {
        $this->hook_factory = new Hook_Factory;
    }

    /** @testdox A hook can be created by passing in the handle, callback, args, priority and its loading preference for admin and front */
    public function test_create_action_hook_for_front_and_admin()
    {
        $admin = $this->hook_factory->action('init', 'is_string', 1, 1, true, false);
        $this->assertTrue($admin->is_admin());
        $this->assertFalse($admin->is_front());

        $front = $this->hook_factory->action('init', 'is_callable', 1, 1, false, true);
        $this->assertFalse($front->is_admin());
        $this->assertTrue($front->is_front());

        $global = $this->hook_factory->action('init', 'is_int', 1, 1, true, true);
        $this->assertTrue($global->is_admin());
        $this->assertTrue($global->is_front());

        $default = $this->hook_factory->action('init', 'is_int', 1, 1);
        $this->assertTrue($default->is_admin());
        $this->assertTrue($default->is_front());
    }

    /** @testdox An action hook can be created by passing in as little as just the hook and callback */
    public function test_create_action_hook()
    {
        $hook_default = $this->hook_factory->action('init', 'is_int');
        $this->assertEquals(Hook::ACTION, $hook_default->get_type());
        $this->assertEquals('init', $hook_default->get_handle());
        $this->assertEquals('is_int', $hook_default->get_callback());
        $this->assertEquals(10, $hook_default->get_priority());
        $this->assertEquals(1, $hook_default->args_count());
        $this->assertTrue($hook_default->is_admin());
        $this->assertTrue($hook_default->is_front());

        $hook = $this->hook_factory->action('init', 'is_string', 2, 20);
        $this->assertEquals(Hook::ACTION, $hook->get_type());
        $this->assertEquals('init', $hook->get_handle());
        $this->assertEquals('is_string', $hook->get_callback());
        $this->assertEquals(20, $hook->get_priority());
        $this->assertEquals(2, $hook->args_count());
        $this->assertTrue($hook->is_admin());
        $this->assertTrue($hook->is_front());
    }

    /** @testdox A filter can be created by passing in the handle, callback, args, priority and its loading preference for admin and front */
    public function test_create_filter_hook_for_front_and_admin()
    {
        $admin = $this->hook_factory->filter('init', 'is_string', 1, 1, true, false);
        $this->assertTrue($admin->is_admin());
        $this->assertFalse($admin->is_front());

        $front = $this->hook_factory->filter('init', 'is_callable', 1, 1, false, true);
        $this->assertFalse($front->is_admin());
        $this->assertTrue($front->is_front());

        $global = $this->hook_factory->filter('init', 'is_int', 1, 1, true, true);
        $this->assertTrue($global->is_admin());
        $this->assertTrue($global->is_front());

        $default = $this->hook_factory->filter('init', 'is_int', 1, 1);
        $this->assertTrue($default->is_admin());
        $this->assertTrue($default->is_front());
    }

    /** @testdox An file hook can be created by passing in as little as just the hook and callback */
    public function test_create_filter_hook()
    {
        $hook_default = $this->hook_factory->filter('init', 'is_int');
        $this->assertEquals(Hook::FILTER, $hook_default->get_type());
        $this->assertEquals('init', $hook_default->get_handle());
        $this->assertEquals('is_int', $hook_default->get_callback());
        $this->assertEquals(10, $hook_default->get_priority());
        $this->assertEquals(1, $hook_default->args_count());
        $this->assertTrue($hook_default->is_admin());
        $this->assertTrue($hook_default->is_front());

        $hook = $this->hook_factory->filter('the_content', 'is_bool', 2, 99999);
        $this->assertEquals(Hook::FILTER, $hook->get_type());
        $this->assertEquals('the_content', $hook->get_handle());
        $this->assertEquals('is_bool', $hook->get_callback());
        $this->assertEquals(99999, $hook->get_priority());
        $this->assertEquals(2, $hook->args_count());
        $this->assertTrue($hook->is_admin());
        $this->assertTrue($hook->is_front());
    }
    /** @testdox When called the factory should return a Remove Hook, Hook which can be used for front and or admin hooks. */
    public function test_create_removal_hook_for_front_and_admin()
    {
        $default = $this->hook_factory->remove('init', 'is_int', 1);
        $this->assertTrue($default->is_admin());
        $this->assertTrue($default->is_front());
    }

    /** @testdox An action hook can be created by passing in as little as just the hook and callback */
    public function test_create_removal_hook()
    {
        $hook_default = $this->hook_factory->remove('init', 'is_int');
        $this->assertEquals(Hook::REMOVE, $hook_default->get_type());
        $this->assertEquals('init', $hook_default->get_handle());
        $this->assertEquals('is_int', $hook_default->get_callback());
        $this->assertEquals(10, $hook_default->get_priority());
        $this->assertTrue($hook_default->is_admin());
        $this->assertTrue($hook_default->is_front());

        $hook = $this->hook_factory->remove(
            'some_action',
            array(Hooks_Via_Static::class, 'filter_callback_static'),
            50
        );
        $this->assertEquals(Hook::REMOVE, $hook->get_type());
        $this->assertEquals('some_action', $hook->get_handle());
        $this->assertEquals(array(Hooks_Via_Static::class, 'filter_callback_static'), $hook->get_callback());
        $this->assertEquals(50, $hook->get_priority());
        $this->assertTrue($hook->is_admin());
        $this->assertTrue($hook->is_front());
    }
}
