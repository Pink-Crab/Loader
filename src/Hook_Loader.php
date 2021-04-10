<?php

declare(strict_types=1);
/**
 * Hook loader
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @since 1.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Loader
 */

namespace PinkCrab\Loader;

use PinkCrab\Loader\{Hook,Hook_Factory,Hook_Collection,Hook_Manager};

class Hook_Loader {


	/**
	 * Internal array holding the Hooks.
	 *
	 * @var Hook_Collection
	 */
	protected $hooks;

	/**
	 * The factory used to populate Hooks
	 *
	 * @var Hook_Factory
	 */
	protected $hook_factory;

	final public function __construct() {
		$this->hook_factory = new Hook_Factory();
		$this->hooks        = new Hook_Collection();
	}

	/**
	 * Adds an action hook globally
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @param integer $args
	 * @param integer $priority
	 * @return Hook
	 */
	public function action( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook {
		$hook = $this->hook_factory->action( $handle, $callback, $args, $priority );
		$this->hooks->push( $hook );
		return $hook;
	}

	/**
	 * Adds an admin only action
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @param integer $args
	 * @param integer $priority
	 * @return Hook
	 */
	public function admin_action( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook {
		$hook = $this->hook_factory->action( $handle, $callback, $args, $priority, true, false );
		$this->hooks->push( $hook );
		return $hook;
	}

	/**
	 * Adds a frontend only action
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @param integer $args
	 * @param integer $priority
	 * @return Hook
	 */
	public function front_action( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook {
		$hook = $this->hook_factory->action( $handle, $callback, $args, $priority, false, true );
		$this->hooks->push( $hook );
		return $hook;
	}

	/**
	 * Adds an filter hook globally
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @param integer $args
	 * @param integer $priority
	 * @return Hook
	 */
	public function filter( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook {
		$hook = $this->hook_factory->filter( $handle, $callback, $args, $priority );
		$this->hooks->push( $hook );
		return $hook;
	}

	/**
	 * Adds an admin only filter
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @param integer $args
	 * @param integer $priority
	 * @return Hook
	 */
	public function admin_filter( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook {
		$hook = $this->hook_factory->filter( $handle, $callback, $args, $priority, true, false );
		$this->hooks->push( $hook );
		return $hook;
	}

	/**
	 * Adds a frontend only filter
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @param integer $args
	 * @param integer $priority
	 * @return Hook
	 */
	public function front_filter( string $handle, callable $callback, int $args = 1, int $priority = 10 ): Hook {
		$hook = $this->hook_factory->filter( $handle, $callback, $args, $priority, false, true );
		$this->hooks->push( $hook );
		return $hook;
	}

	/**
	 * Adds a remove hook (either action or filter)
	 *
	 * @param string $handle
	 * @param callable|array{0:string,1:string} $callback
	 * @param integer $priority
	 * @return Hook
	 */
	public function remove( string $handle, $callback, int $priority = 10 ): Hook {
		$hook = $this->hook_factory->remove( $handle, $callback, $priority );
		$this->hooks->push( $hook );
		return $hook;
	}

	/**
	 * Adds a remove action hook
	 * A more verbose alias for Hook_Loader::remove()
	 *
	 * @param string $handle
	 * @param callable|array{0:string,1:string} $callback
	 * @param integer $priority
	 * @return Hook
	 */
	public function remove_action( string $handle, $callback, int $priority = 10 ): Hook {
		return $this->remove( $handle, $callback, $priority );
	}

	/**
	 * Adds a remove filter hook
	 * A more verbose alias for Hook_Loader::remove()
	 *
	 * @param string $handle
	 * @param callable|array{0:string,1:string} $callback
	 * @param integer $priority
	 * @return Hook
	 */
	public function remove_filter( string $handle, $callback, int $priority = 10 ): Hook {
		return $this->remove( $handle, $callback, $priority );
	}

	/**
	 * Adds an ajax hook.
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @param boolean $public
	 * @param boolean $private
	 * @return Hook
	 */
	public function ajax( string $handle, callable $callback, bool $public = true, bool $private = true ): Hook {
		$hook = $this->hook_factory->ajax( $handle, $callback, $public, $private );
		$this->hooks->push( $hook );
		return $hook;
	}


	/**
	 * Add a short code
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @return Hook
	 */
	public function shortcode( string $handle, callable $callback ): Hook {
		$hook = $this->hook_factory->shortcode( $handle, $callback );
		$this->hooks->push( $hook );
		return $hook;
	}

	/**
	 * Processes the hooks, either with the internal or custom hook manager.
	 *
	 * @param Hook_Manager|null $hook_manager
	 * @return void
	 */
	public function register_hooks( ?Hook_Manager $hook_manager = null ): void {

		if ( $hook_manager === null ) {
			$hook_manager = new Hook_Manager();
		}

		$this->hooks->register(
			function( Hook $hook ) use ( $hook_manager ) {
				$hook_manager->process_hook( $hook );
			}
		);
	}

}
