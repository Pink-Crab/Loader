<?php

declare(strict_types=1);

/**
 * Hook_Factory tests.
 *
 * @since 1.0.1
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Loader
 */

namespace PinkCrab\Loader\Tests;

use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Loader\Hook;
use PinkCrab\Loader\Hook_Collection;
use PinkCrab\Loader\Hook_Factory;
use PinkCrab\Loader\Hook_Loader;
use WP_UnitTestCase;
use PinkCrab\FunctionConstructors\Arrays as Arr;
use PinkCrab\FunctionConstructors\GeneralFunctions as F;


class Test_Hook_Loader extends WP_UnitTestCase
{

    /** @testdox A user should be able to create an instance of the loader and have the Collection and Factory pre popualted. */
    public function test_can_be_contrustucted_with_internal_resources(): void
    {
        $loader = new Hook_Loader;
        $this->assertInstanceOf(Hook_Collection::class, Objects::get_property($loader, 'hooks'));
        $this->assertInstanceOf(Hook_Factory::class, Objects::get_property($loader, 'hook_factory'));
    }

    public function test_add_actions(): void
    {
        $loader = new Hook_Loader;
        $loader->action('global_action', 'is_string');
        $loader->admin_action('admin_action', 'is_string');
        $loader->front_action('front_action', 'is_string');

        // Extract Collections intenral array.
        $hooks = Objects::get_property($loader, 'hooks')->export();

        $this->assertCount(1, Arr\filterCount(function($e){
            return $e->get_handle() === 'global_action';
        })($hooks));

        $this->assertCount(1, Arr\filterCount(function($e){
            return $e->get_handle() === 'admin_action';
        })($hooks));

        $this->assertCount(1, Arr\filterCount(function($e){
            return $e->get_handle() === 'front_action';
        })($hooks));
    }
}
