# Loader

The PinkCrab Hook Loader.

![alt text](https://img.shields.io/badge/Current_Version-1.0.0-yellow.svg?style=flat " ") 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)

![](https://github.com/Pink-Crab/Loader/workflows/GitHub_CI/badge.svg " ")
[![codecov](https://codecov.io/gh/Pink-Crab/Loader/branch/master/graph/badge.svg?token=94DFTAVAAI)](https://codecov.io/gh/Pink-Crab/Loader)

For more details please visit our docs.
https://app.gitbook.com/@glynn-quelch/s/pinkcrab/

## Version ##

**Release 1.0.0**

## Why? ##

WordPress and especially WooCommerce is built around hooks and if you have to register a lot of them, it can be hard to keep track of them all and what is currently being registered with WP. 

The PinkCrab Loader gives a more manageable way of registering and removing hook call, shortcode and ajax calls.

## Setup ##

```bash 
$ composer require pinkcrab/hook-loader

``` 

Once you have the hook loader installed it just case of putting it to use. As this is all held as a class, you can pass the instance as depenedecnies to any class you wish to give access to the loader.

```php
$loader = new Loader();

// Add actions
$loader->action('some_action', 'my_callback');
$loader->front_action('some_action', 'my_callback');
$loader->admin_action('some_action', 'my_callback');

// Filters
$loader->filter('some_filter', 'strtolower');
$loader->front_filter('some_filter', 'strtolower');
$loader->admin_filter('some_filter', 'strtolower');

// Remove hooks
$loader->remove('some_action', 'someone_else_callback', 10);
$loader->remove_filter('some_action', 'someone_else_callback', 10);
$loader->remove_action('some_action', 'someone_else_callback', 10);

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
	 * @param Loader $loader
	 * @return void
	 */
	public function hooks(Loader $loader){
		$loader->action('action_handle', [$this, 'some_method')]);
	}

	// The callback
	public function some_method(){
		print 'I WAS CALLED';
	}
}

// In your code, just pass the loader to this class.
$loader = new Loader();
$some_action = new SomeAction();

// Add all the some_action hooks to loader and register them.
$some_action->hooks($loader);
$loader->register_hooks();

```

## Hook Removal

While remove_action() and remove_filter() are prefectly suitable 90% of the time, it can be tricky to unset hooks with have been added as isntance to classes, you can not recall the same instance. Out Hook_Removal class will manually remove all hooks based on the class name (instance or static use). Allowing for the removal of hooks created by other plugins. 

You will need to create an instance of the object to pass to the removal tool. If other plugin developers have hooks loaded on __construct, the removal tool, will remove them.

```php
$loader->remove('action_handle', [new SomeAction, 'some_method']);
```

IF HOWEVER creating a new instnace of the class causes side effects, you can use reflection to generate an instance without constructing the class.
(this may be added to automatically do this at a later date.)

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

1.0.0 - Moved from Plugin Core package. Moved the internal collection to there own Object away from PC Collection.