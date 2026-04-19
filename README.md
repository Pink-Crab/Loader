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

## Registering Hooks

All registration methods return the created `Hook` object. Nothing is actually bound to WordPress until you call `$loader->register_hooks()` — until then hooks are just staged in the collection, which makes them easy to filter or inspect.

### action()

```
Hook_Loader::action( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook
```

Registers an action on both admin and front-end contexts. Equivalent to `add_action($handle, $callback, $priority, $args)` when flushed.

```php
$loader->action( 'init', 'my_init_callback' );
$loader->action( 'save_post', [ $saver, 'handle' ], 2, 20 );
```

### admin_action()

```
Hook_Loader::admin_action( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook
```

Same as `action()` but only registers when the request is inside `wp-admin` (checked at flush time via `is_admin()`).

```php
$loader->admin_action( 'admin_menu', [ $menu, 'register' ] );
```

### front_action()

```
Hook_Loader::front_action( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook
```

Same as `action()` but only registers on the front-end (when `is_admin()` is false).

```php
$loader->front_action( 'wp_enqueue_scripts', [ $assets, 'enqueue' ] );
```

### filter()

```
Hook_Loader::filter( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook
```

Registers a filter on both admin and front-end contexts. The callback must return its first argument (the value being filtered).

```php
$loader->filter( 'the_content', 'my_content_filter' );
$loader->filter( 'wp_nav_menu_items', [ $menu, 'append' ], 2, 50 );
```

### admin_filter()

```
Hook_Loader::admin_filter( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook
```

Same as `filter()` but only registers inside `wp-admin`.

```php
$loader->admin_filter( 'post_row_actions', [ $rows, 'add_action' ], 2 );
```

### front_filter()

```
Hook_Loader::front_filter( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook
```

Same as `filter()` but only registers on the front-end.

```php
$loader->front_filter( 'body_class', [ $body, 'classes' ], 1, 20 );
```

### Flushing everything

```php
$loader = new Hook_Loader();

$loader->action(       'init',                'my_init_callback' );
$loader->admin_action( 'admin_menu',          'my_admin_menu_setup' );
$loader->front_action( 'wp_enqueue_scripts',  'my_front_assets' );
$loader->filter(       'the_content',         'my_content_filter' );
$loader->admin_filter( 'post_row_actions',    'my_admin_row_actions', 2 );
$loader->front_filter( 'body_class',          'my_body_classes', 1, 20 );

// Commit everything to $wp_filter / $wp_actions.
$loader->register_hooks();
```

### Use with a class

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

## Removing Hooks

WordPress's native `remove_action()` / `remove_filter()` require the *same* callable you passed to `add_action()`. That's easy for global functions and static methods, but breaks for hooks added against class instances — you need the original `$instance` to call `remove_action('foo', [$instance, 'method'])`, and that instance is often gone or inaccessible.

The remove methods on `Hook_Loader` accept either a live callable *or* a `[class-name, method-name]` array (both strings). Internally, `Hook_Removal` walks `$wp_filter` and matches on class name and method name — no instance required.

### remove()

```
Hook_Loader::remove( string $handle, $callback, int $priority = 10 ): Hook
```

Removes a hook from either the action or filter registry. Accepts a callable, `[ClassName::class, 'method']`, or `[$instance, 'method']`.

```php
$loader->remove( 'init', [ Some_Other_Plugin_Action::class, 'boot' ], 10 );
```

### remove_action()

```
Hook_Loader::remove_action( string $handle, $callback, int $priority = 10 ): Hook
```

Alias for `remove()` that reads more explicitly at the call-site when you know you're targeting an action.

```php
$loader->remove_action( 'save_post', 'someone_elses_saver', 10 );
```

### remove_filter()

```
Hook_Loader::remove_filter( string $handle, $callback, int $priority = 10 ): Hook
```

Alias for `remove()` that reads more explicitly at the call-site when you know you're targeting a filter.

```php
$loader->remove_filter( 'the_content', [ Some_Other_Plugin_Filter::class, 'wrap' ], 10 );
```

## Shortcodes

### shortcode()

```
Hook_Loader::shortcode( string $handle, callable $callback ): Hook
```

Registers a shortcode. Runs `add_shortcode()` when `register_hooks()` fires.

```php
$loader->shortcode( 'my_shortcode', function ( array $atts ): string {
    return esc_html( $atts['text'] ?? '' );
} );

// Somewhere later:
do_shortcode( "[my_shortcode text='hello']" );
```

## Ajax

### ajax()

```
Hook_Loader::ajax( string $handle, callable $callback, bool $public_ajax = true, bool $private_ajax = true ): Hook
```

WordPress splits AJAX into two actions: `wp_ajax_<handle>` (authenticated users) and `wp_ajax_nopriv_<handle>` (anonymous users). `Hook_Loader::ajax()` registers either or both from a single call. `$public_ajax` controls the `wp_ajax_nopriv_*` registration; `$private_ajax` controls the `wp_ajax_*` registration.

```php
$loader->ajax( 'my_action', 'my_callback', true,  true  );  // logged in AND logged out
$loader->ajax( 'my_action', 'my_callback', true,  false );  // logged out only  ($private_ajax=false)
$loader->ajax( 'my_action', 'my_callback', false, true  );  // logged in only   ($public_ajax=false)
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
