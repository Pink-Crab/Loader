<?php

declare(strict_types=1);
/**
 * Mock Hook_Manager for logging all add_action, add_filter and Hook_Removal() isntances.
 * Overwrites the reigster_action(), reigster_filter() & register_remove() method
 * relies on the Hook_Managers other functionality.
 *
 * @since 1.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Loader\Tests\Fixtures;

use PinkCrab\Loader\{Hook,Hook_Manager};

class Hook_Manager_Object_Mock extends Hook_Manager {

	/** Holds all the hooks which have been registered. */
	public $_hooks = array(
		'actions' => array(),
		'filters' => array(),
		'remove'  => array(),
	);

	/** Adds and action to the internal set. */
	public function _add_action( $handle, $callback, $priority = 10, $args = 1 ) {
		$this->_hooks['actions'][ $handle ] = array(
			'handle'   => $handle,
			'callback' => $callback,
			'priority' => $priority,
			'args'     => $args,
		);
	}

	/** Adds and filter to the internal set. */
	public function _add_filter( $handle, $callback, $priority = 10, $args = 1 ) {
		$this->_hooks['filters'][ $handle ] = array(
			'handle'   => $handle,
			'callback' => $callback,
			'priority' => $priority,
			'args'     => $args,
		);
	}

	/** Adds a remove hook record to the internal set. */
	public function _remove_hook( $handle, $callback, $priority = 10 ) {
		$this->_hooks['remove'][ $handle ] = array(
			'handle'   => $handle,
			'callback' => $callback,
			'priority' => $priority,
		);
	}

	/** Clears the internal state. */
	public function _clear(): void {
		$this->_hooks = array(
			'actions' => array(),
			'filters' => array(),
			'remove'  => array(),
		);
	}

	/**
	 * Registers action hook
	 *
	 * @param Hook $hook
	 * @return Hook
	 */
	protected function register_action( Hook $hook ): Hook {
		$this->_add_action( $hook->get_handle(), $hook->get_callback(), $hook->get_priority(), $hook->args_count() );
		$hook->registered();
		return $hook;
	}

	/**
	 * Registers a filter action
	 *
	 * @param Hook $hook
	 * @return Hook
	 */
	protected function register_filter( Hook $hook ): Hook {
		$this->_add_filter( $hook->get_handle(), $hook->get_callback(), $hook->get_priority(), $hook->args_count() );
		$hook->registered();
		return $hook;
	}

	/**
	 * Removes a hook using hook_removal
	 *
	 * @param Hook $hook
	 * @return Hook
	 */
	protected function register_remove( Hook $hook ): Hook {
		$this->_remove_hook( $hook->get_handle(), $hook->get_callback(), $hook->get_priority() );
		$hook->registered();
		return $hook;
	}

}
