# Hook_Loader

The PinkCrab Hook Hook_Loader.


![alt text](https://img.shields.io/badge/Current_Version-1.1.2-yellow.svg?style=flat " ") 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)
![](https://github.com/Pink-Crab/Loader/workflows/GitHub_CI/badge.svg " ")
[![codecov](https://codecov.io/gh/Pink-Crab/Loader/branch/master/graph/badge.svg?token=94DFTAVAAI)](https://codecov.io/gh/Pink-Crab/Loader)

For more details please visit our docs.
https://app.gitbook.com/@glynn-quelch/s/pinkcrab/

## Version ##

**Release 1.1.2**

> Since v1.0.0 we have made some changes to have this all works under the hood, we have changed from Loader to Hook_Loader as the main class, but Loader has been left in as pollyfill for older versions.

## Why? ##

WordPress and especially WooCommerce is built around hooks and if you have to register a lot of them, it can be hard to keep track of them all and what is currently being registered with WP. 

The PinkCrab Hook_Loader gives a more manageable way of registering and removing hook call, shortcode and ajax calls.


## Setup ##

```bash 
$ composer require pinkcrab/hook-loader

``` 

Once you have the hook loader installed it just case of putting it to use. As this is all held as a class, you can pass the instance as depenedecnies to any class you wish to give access to the loader.

## Registering Hooks (actions & filters)
> **Hook_Loader::action(string $hook, callable $method, int $priority=10, int $args = 1): void**

> **Hook_Loader::admin_action(string $hook, callable $method, int $priority=10, int $args = 1): void**

> **Hook_Loader::front_action(string $hook, callable $method, int $priority=10, int $args = 1): void**

> **Hook_Loader::filter(string $hook, callable $method, int $priority=10, int $args = 1): void**

> **Hook_Loader::admin_filter(string $hook, callable $method, int $priority=10, int $args = 1): void**

> **Hook_Loader::front_filter(string $hook, callable $method, int $priority=10, int $args = 1): void**

```php
$loader = new Hook_Loader();

// Add actions
$loader->action('some_action', 'my_callback'); // Registered front and admin
$loader->front_action('some_action', 'my_callback'); // Frontend only
$loader->admin_action('some_action', 'my_callback'); // Admin only

// Filters
$loader->filter('some_filter', 'strtolower'); // Registered front and admin
$loader->front_filter('some_filter', 'strtolower'); // Frontend only
$loader->admin_filter('some_filter', 'strtolower'); // Admin only

// Remove hooks
$loader->remove('some_action', 'someone_else_callback', 10); 
$loader->remove_filter('some_action', 'someone_else_callback', 10); // Does the same as remove()
$loader->remove_action('some_action', 'someone_else_callback', 10); // Does the same as remove()

// Ajax and Shortcode.
$loader->shortcode('my_shortcode', 'shortcode_callback');
$loader->ajax('my_action', 'my_callback', true, true);

// Once all have been added, just process with 
$loader->register_hooks();

```

### Use with a class.

``` php
class SomeAction{
	/**	
	 * Register all hooks for this class
	 * @param Hook_Loader $loader
	 * @return void
	 */
	public function hooks(Hook_Loader $loader){
		$loader->action('action_handle', [$this, 'some_method')], 20);
	}

	// The callback
	public function some_method(){
		print 'I WAS CALLED';
	}
}

// In your code, just pass the loader to this class.
$loader = new Hook_Loader();
$some_action = new SomeAction();

// Add all the some_action hooks to loader and register them.
$some_action->hooks($loader);
$loader->register_hooks();

```

## Hook Removal (actions & filters)
> **Hook_Loader::remove(string $hook, callable $method, int $priority=10): void**

> **Hook_Loader::remove_filter(string $hook, callable $method, int $priority=10): void**

> **Hook_Loader::remove_hook(string $hook, callable $method, int $priority=10): void**

While remove_action() and remove_filter() are prefectly suitable 90% of the time, it can be tricky to unset hooks with have been added as isntance to classes, you can not recall the same instance. Out Hook_Removal class will manually remove all hooks based on the class name (instance or static use). Allowing for the removal of hooks created by other plugins. 

Even if the hook was added via a class instance, you only need to use the class name to add the method used. This allows the avoidance of having to recreate an instance of the class and potentially rerunning other hooks and setup routines.

``` php
// The above hook can be removed using

// Just the full class name (doesnt run autload)
$loader->remove('action_handle', [SomeAction::class, 'some_method'], 20);

// Or as a new instance
$loader->remove('action_handle', [new SomeAction(), 'some_method'], 20);
```

## Shortcodes
> **Hook_Loader::shortcode(string $hook, callable $method): void**

You can easily add shortcodes using the loader, not only that you ensure they come with fully populated objects behind them

``` php
// Simple example
$loader->shortcode(
	'testShortCode',
	function( $atts ) {
		echo $atts['text'];
	}
);

// Called with shortcode as normal (either in php or wp-admin text input) 
do_shortcode( "[testShortCode text='yes']" ); // yes

// Part of a class
class ShortCode {

	protected $some_service;

	public function __construct(Some_Service $some_service){
		$this->some_service = $some_service;
	}

	public function register(Hook_Loader $loader){
		$loader->shortcode('my_shortcode', [$this, ['render_shortcode']]);
	}

	public function render_shortcode(array $args){
		print $this->some_service->do_something($args['something']);
	}
}

```
## Ajax
> **Hook_Loader::ajax(string $hook, callable $method, bool $public, bool $private): void**

If you want to register ajax calls, it requires 2 hook calls. This soon gets messy if you are setting up multiple calls. The Hook_Loader can handle registering either or both of this with a single declaration.

```php

$loader->ajax('my_action', 'my_callback', true, true); // For both logged in and out users.
$loader->ajax('my_action', 'my_callback', false, true); // For only logged in users.
$loader->ajax('my_action', 'my_callback', true, false); // For only logged out users.
```
As with the other examples this can be used as part of class to create self contained ajax calls. While this can be done manually, the Registerables package comes with a very useful Ajax abstract class which can be used.


## Testing ##

To run the full suite (as run via GH CLI)

```bash
	composer all
```

### PHP Unit ###

If you would like to run the tests for this package, please ensure you add your database details into the test/wp-config.php file before running phpunit.

```bash 
$ composer test

``` 

Run with coverage report (/coverage-report)
```bash 
$ composer coverage
```

### PHP Stan ###

The module comes with a pollyfill for all WP Functions, allowing for the testing of all core files. The current config omits the Dice file as this is not ours. To run the suite call.

```bash 
$ vendor/bin/phpstan analyse src/ -l8 

``` 
```bash 
$ composer analyse
```

### PHPCS ###

You can run the codebase thorough PHPCS by calling.
```bash 
$ composer sniff
```

## License ##

### MIT License ###

http://www.opensource.org/licenses/mit-license.html  

## Change Log ##
* 1.1.2 - Loader::class has now been marked as deprecated
* 1.1.1 - Typo on register_hooks() (spelt at regster_hooks)
* 1.1.0 - All internal functionality moved over, still has the same ex
* 1.0.2 - Fixed incorrect docblock on Hook_Loader_Collection::pop() and adding missing readme entries for shortcode and ajax.
* 1.0.1 - Added pop() and count() to the hook collection. Not used really from outside, only in tests.
* 1.0.0 - Moved from Plugin Core package. Moved the internal collection to there own Object away from PC Collection.
