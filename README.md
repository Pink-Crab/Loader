# Hook_Loader

An object-based WordPress hook loader. Register actions, filters, AJAX endpoints and shortcodes against `Hook_Loader`, then call `register_hooks()` to commit them to WordPress. Supports admin-only, front-only, and removal of hooks (including hooks registered against class instances you no longer hold).

[![Latest Stable Version](https://poser.pugx.org/pinkcrab/hook-loader/v)](https://packagist.org/packages/pinkcrab/hook-loader)
[![Total Downloads](https://poser.pugx.org/pinkcrab/hook-loader/downloads)](https://packagist.org/packages/pinkcrab/hook-loader)
[![License](https://poser.pugx.org/pinkcrab/hook-loader/license)](https://packagist.org/packages/pinkcrab/hook-loader)
[![PHP Version Require](https://poser.pugx.org/pinkcrab/hook-loader/require/php)](https://packagist.org/packages/pinkcrab/hook-loader)
![GitHub contributors](https://img.shields.io/github/contributors/Pink-Crab/Loader?label=Contributors)
![GitHub issues](https://img.shields.io/github/issues-raw/Pink-Crab/Loader)

[![WP 6.6 [PHP8.0-8.4] Tests](https://github.com/Pink-Crab/Loader/actions/workflows/WP_6_6.yaml/badge.svg)](https://github.com/Pink-Crab/Loader/actions/workflows/WP_6_6.yaml)
[![WP 6.7 [PHP8.0-8.4] Tests](https://github.com/Pink-Crab/Loader/actions/workflows/WP_6_7.yaml/badge.svg)](https://github.com/Pink-Crab/Loader/actions/workflows/WP_6_7.yaml)
[![WP 6.8 [PHP8.0-8.4] Tests](https://github.com/Pink-Crab/Loader/actions/workflows/WP_6_8.yaml/badge.svg)](https://github.com/Pink-Crab/Loader/actions/workflows/WP_6_8.yaml)
[![WP 6.9 [PHP8.0-8.4] Tests](https://github.com/Pink-Crab/Loader/actions/workflows/WP_6_9.yaml/badge.svg)](https://github.com/Pink-Crab/Loader/actions/workflows/WP_6_9.yaml)

[![codecov](https://codecov.io/gh/Pink-Crab/Loader/branch/master/graph/badge.svg?token=94DFTAVAAI)](https://codecov.io/gh/Pink-Crab/Loader)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pink-Crab/Loader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pink-Crab/Loader/?branch=master)

For more details please visit the docs site: https://perique.info/lib/Hook_Loader.html

## Why?

WordPress — and especially WooCommerce — is built around hooks. Wiring up actions and filters with `add_action()` / `add_filter()` scattered across classes gets hard to reason about quickly: there's no single place to see what's registered, admin-only vs front-only conditions end up duplicated, and removing hooks added by class instances is a known pain.

`Hook_Loader` gives you a single object to declare everything against. You stage your hooks, then flush them to WordPress in one call (`register_hooks()`), which means your class constructors stay side-effect-free and your test harness can inspect the staged hooks before they reach `$wp_filter`.

## Install

```bash
composer require pinkcrab/hook-loader
```

Then include the Composer autoloader in your project:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

All registration methods return the created `Hook` object. Nothing binds to WordPress until you call `$loader->register_hooks()` — until then hooks are just staged in the collection.

## Methods (Registration)

### action
**action( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook**
> @param string $handle Hook handle to register against.  
> @param callable $callback Hook callback.  
> @param int $args Number of arguments passed to the callback. Default 1.  
> @param int $priority Priority the hook fires at. Default 10.  
> @return \PinkCrab\Loader\Hook  

Registers an action on both admin and front-end contexts. Equivalent to `add_action($handle, $callback, $priority, $args)` when flushed.

*Example*
```php
$loader->action( 'init', 'my_init_callback' );
$loader->action( 'save_post', [ $saver, 'handle' ], 2, 20 );
```

### admin_action
**admin_action( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook**
> @param string $handle Hook handle to register against.  
> @param callable $callback Hook callback.  
> @param int $args Number of arguments passed to the callback. Default 1.  
> @param int $priority Priority the hook fires at. Default 10.  
> @return \PinkCrab\Loader\Hook  

Same as `action()` but only registers when the request is inside `wp-admin` (checked at flush time via `is_admin()`).

*Example*
```php
$loader->admin_action( 'admin_menu', [ $menu, 'register' ] );
```

### front_action
**front_action( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook**
> @param string $handle Hook handle to register against.  
> @param callable $callback Hook callback.  
> @param int $args Number of arguments passed to the callback. Default 1.  
> @param int $priority Priority the hook fires at. Default 10.  
> @return \PinkCrab\Loader\Hook  

Same as `action()` but only registers on the front-end (when `is_admin()` is false).

*Example*
```php
$loader->front_action( 'wp_enqueue_scripts', [ $assets, 'enqueue' ] );
```

### filter
**filter( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook**
> @param string $handle Hook handle to register against.  
> @param callable $callback Filter callback; must return the first argument.  
> @param int $args Number of arguments passed to the callback. Default 1.  
> @param int $priority Priority the hook fires at. Default 10.  
> @return \PinkCrab\Loader\Hook  

Registers a filter on both admin and front-end contexts.

*Example*
```php
$loader->filter( 'the_content', 'my_content_filter' );
$loader->filter( 'wp_nav_menu_items', [ $menu, 'append' ], 2, 50 );
```

### admin_filter
**admin_filter( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook**
> @param string $handle Hook handle to register against.  
> @param callable $callback Filter callback; must return the first argument.  
> @param int $args Number of arguments passed to the callback. Default 1.  
> @param int $priority Priority the hook fires at. Default 10.  
> @return \PinkCrab\Loader\Hook  

Same as `filter()` but only registers inside `wp-admin`.

*Example*
```php
$loader->admin_filter( 'post_row_actions', [ $rows, 'add_action' ], 2 );
```

### front_filter
**front_filter( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook**
> @param string $handle Hook handle to register against.  
> @param callable $callback Filter callback; must return the first argument.  
> @param int $args Number of arguments passed to the callback. Default 1.  
> @param int $priority Priority the hook fires at. Default 10.  
> @return \PinkCrab\Loader\Hook  

Same as `filter()` but only registers on the front-end.

*Example*
```php
$loader->front_filter( 'body_class', [ $body, 'classes' ], 1, 20 );
```

## Methods (Removal)

### remove
**remove( string $handle, $callback, int $priority = 10 ): Hook**
> @param string $handle Hook handle to remove from.  
> @param callable|array{0:string,1:string} $callback Callable, or `[class-name, method-name]` array (both strings).  
> @param int $priority Priority the target hook was registered at. Default 10.  
> @return \PinkCrab\Loader\Hook  

WordPress's native `remove_action()` / `remove_filter()` require the *same* callable you passed to `add_action()`. That breaks for hooks added against class instances — you need the original `$instance` and that's often gone or inaccessible. `Hook_Removal` walks `$wp_filter` and matches on class name + method name instead, so a `[class-name, method-name]` array is enough.

*Example*
```php
// Match by class name only — no need to reconstruct an instance.
$loader->remove( 'init', [ Some_Other_Plugin_Action::class, 'boot' ], 10 );

// Works equivalently with an instance, if you have one.
$loader->remove( 'init', [ $instance, 'boot' ], 10 );

// Plain callables also work.
$loader->remove( 'init', 'some_global_function', 10 );
```

### remove_action
**remove_action( string $handle, $callback, int $priority = 10 ): Hook**
> @param string $handle Hook handle to remove from.  
> @param callable|array{0:string,1:string} $callback Callable, or `[class-name, method-name]` array.  
> @param int $priority Priority the target hook was registered at. Default 10.  
> @return \PinkCrab\Loader\Hook  

Alias for `remove()` that signals intent at the call-site when you're removing an action. Identical runtime behaviour — WordPress stores actions and filters in the same `$wp_filter` registry.

*Example*
```php
// Global function added via add_action() elsewhere.
$loader->remove_action( 'save_post', 'someone_elses_saver', 10 );

// Instance-method action registered by a third-party plugin — match by class name.
$loader->remove_action(
    'init',
    [ \Other_Plugin\Bootstrap::class, 'register' ],
    10
);

// Or pass a live instance if you happen to hold one.
$loader->remove_action( 'init', [ $existing_instance, 'register' ], 10 );
```

### remove_filter
**remove_filter( string $handle, $callback, int $priority = 10 ): Hook**
> @param string $handle Hook handle to remove from.  
> @param callable|array{0:string,1:string} $callback Callable, or `[class-name, method-name]` array.  
> @param int $priority Priority the target hook was registered at. Default 10.  
> @return \PinkCrab\Loader\Hook  

Alias for `remove()` that signals intent at the call-site when you're removing a filter. Identical runtime behaviour to `remove()` / `remove_action()`.

*Example*
```php
// Unregister a filter that another plugin added by class.
$loader->remove_filter(
    'the_content',
    [ \Third_Party\Content_Filter::class, 'wrap' ],
    10
);

// Unregister a WP core filter callback by name.
$loader->remove_filter( 'the_content', 'wpautop', 10 );

// Swap a third-party filter for your own at the same priority:
$loader->remove_filter( 'the_title', [ \Other_Plugin\Titles::class, 'prefix' ], 20 );
$loader->filter(        'the_title', [ $this, 'prefix' ], 1, 20 );
$loader->register_hooks();
```

## Methods (Shortcodes & Ajax)

### shortcode
**shortcode( string $handle, callable $callback ): Hook**
> @param string $handle Shortcode tag.  
> @param callable $callback Shortcode callback. Receives the attributes array and must return a string.  
> @return \PinkCrab\Loader\Hook  

Registers a shortcode. Runs `add_shortcode()` when `register_hooks()` fires.

*Example*
```php
$loader->shortcode( 'my_shortcode', function ( array $atts ): string {
    return esc_html( $atts['text'] ?? '' );
} );

// Somewhere later:
do_shortcode( "[my_shortcode text='hello']" );
```

### ajax
**ajax( string $handle, callable $callback, bool $public_ajax = true, bool $private_ajax = true ): Hook**
> @param string $handle Ajax action handle (without the `wp_ajax_` / `wp_ajax_nopriv_` prefix).  
> @param callable $callback Ajax handler callback.  
> @param bool $public_ajax Register against `wp_ajax_nopriv_<handle>` for anonymous users. Default true.  
> @param bool $private_ajax Register against `wp_ajax_<handle>` for authenticated users. Default true.  
> @return \PinkCrab\Loader\Hook  

WordPress splits AJAX into two actions: `wp_ajax_<handle>` (authenticated users) and `wp_ajax_nopriv_<handle>` (anonymous users). `Hook_Loader::ajax()` registers either or both from a single call.

*Example*
```php
$loader->ajax( 'my_action', 'my_callback', true,  true  );  // logged in AND logged out
$loader->ajax( 'my_action', 'my_callback', true,  false );  // logged out only  ($private_ajax=false)
$loader->ajax( 'my_action', 'my_callback', false, true  );  // logged in only   ($public_ajax=false)
```

## Methods (Lifecycle)

### register_hooks
**register_hooks(): void**
> @return void  

Flushes every staged hook to WordPress. Call once, after all registrations have been declared. Before this is called nothing is bound to `$wp_filter` / `$wp_actions`.

*Example*
```php
$loader = new Hook_Loader();
// ...register hooks...
$loader->register_hooks();
```

## Use with a class

Because hooks are staged (not fired immediately), your class constructors stay clean. Expose a `hooks()` method that accepts the loader and records what the class wants registered; the composition root (your plugin bootstrap) flushes them:

```php
class Some_Action {
    public function hooks( Hook_Loader $loader ): void {
        $loader->action( 'init', [ $this, 'boot' ] );
        $loader->front_filter( 'the_content', [ $this, 'wrap_content' ], 1, 20 );
    }

    public function boot(): void {
        // side-effecty init
    }

    public function wrap_content( string $content ): string {
        return '<div class="mine">' . $content . '</div>';
    }
}

$loader      = new Hook_Loader();
$some_action = new Some_Action();
$some_action->hooks( $loader );
$loader->register_hooks();
```

## Filtering hooks before registration

The hook collection is passed through a filter before it hits WordPress, letting other code mutate, add, or strip hooks at flush time. Use the `Hook_Collection::REGISTER_HOOKS` constant (or its literal string value `pinkcrab/loader/register_hooks`):

```php
add_filter( Hook_Collection::REGISTER_HOOKS, function ( $hooks ) {
    // Inspect, mutate, or replace the staged hooks.
    return $hooks;
} );
```

## Tested Against

* PHP 8.0, 8.1, 8.2, 8.3 & 8.4
* WP 6.6, 6.7, 6.8 & 6.9
* MySQL 8.4

## License

### MIT License

http://www.opensource.org/licenses/mit-license.html

## Change Log

* 1.3.0 - Drop PHP 7.x, require PHP 8.0+. Modernise the tooling chain (PHPStan 2.x at level 9, PHPUnit 8|9, WPCS 3.x). Replace the single GitHub_CI workflow with the WP 6.6–6.9 matrix (PHP 8.0–8.4, `mysql:8.4`) using `codecov/codecov-action@v4`. Suppress the WP 6.8 `wp_is_block_theme` early-call notice in `tests/wp-config.php`. Remove `object-calisthenics/phpcs-calisthenics-rules` from dev-deps. **BC break:** `Hook_Loader::ajax()` and `Hook_Factory::ajax()` parameters `$public` / `$private` renamed to `$public_ajax` / `$private_ajax` (positional callers unaffected; named-argument callers need to update). Reserved-keyword parameter names removed throughout (`$callable` / `$function` → `$callback`).
* 1.2.0 - Updated testing dependencies and support for php8, added in the ability to filter hooks prior to registration.
* 1.1.2 - Loader::class has now been marked as deprecated
* 1.1.1 - Typo on register_hooks() (spelt at regster_hooks)
* 1.1.0 - All internal functionality moved over, still has the same ex
* 1.0.2 - Fixed incorrect docblock on Hook_Loader_Collection::pop() and adding missing readme entries for shortcode and ajax.
* 1.0.1 - Added pop() and count() to the hook collection. Not used really from outside, only in tests.
* 1.0.0 - Moved from Plugin Core package. Moved the internal collection to there own Object away from PC Collection.
