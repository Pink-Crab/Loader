<?php

declare(strict_types=1);
/**
 * Used to regiter and unregister hooks.
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
 * @since 0.3.6
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Loader
 */


namespace PinkCrab\Loader;

use PinkCrab\Loader\{Hook,Hook_Removal};
use PinkCrab\Loader\Exceptions\Invalid_Hook_Callback_Exception;

class Hook_Manager {

	protected const TYPE_MAP = array(
		Hook::ACTION    => 'register_action',
		Hook::FILTER    => 'register_filter',
		Hook::AJAX      => 'register_ajax',
		Hook::SHORTCODE => 'register_shortocde',
		Hook::REMOVE    => 'register_remove',
	);


	/**
	 * Callback used to process a hook
	 *
	 * @param Hook $hook
	 * @return Hook
	 */
	public function process_hook( Hook $hook ): Hook {
		if ( $this->validate_context( $hook )
		&& array_key_exists( $hook->get_type(), self::TYPE_MAP )
		) {
			$method = self::TYPE_MAP[ $hook->get_type() ];
			return $this->{$method}( $hook );
		}

		return $hook;
	}

	/**
	 * Maps the hook as the parent if deferred.
	 * If deferred uses the current hook, to populate a second callback
	 * which is added via the deferred ones callback.
	 *
	 * Example
	 *
	 * add_action('deferred_hook', function(...$args) {
	 *     add_action('actual_hook', 'actual_callback'...);
	 * });
	 *
	 * @param Hook $hook
	 * @return Hook
	 */
	protected function map_deferred_hook( Hook $hook ): Hook {
		if ( $hook->is_deferred() === true
		&& $hook->get_deferred_on() !== null ) {
			// Construct the deffered hook, populated with currnent hook in callback.
			$deferred_on   = $hook->get_deferred_on();
			$deferred_hook = new Hook(
				$deferred_on['handle'],
				function ( ...$args ) use ( $hook ) {
					$this->process_hook( $hook );
				},
				$deferred_on['priority']
			);
			return $deferred_hook;
		}
		return $hook;
	}

	/**
	 * Validates if in admin only admin hooks are registtered and if on front, only front.
	 *
	 * @param Hook $hook
	 * @return bool
	 */
	protected function validate_context( Hook $hook ): bool {
		if ( $hook->is_admin() === true && $hook->is_front() === true ) {
			return true;
		} elseif ( is_admin() === true && $hook->is_admin() === true ) {
			return true;
		} elseif ( is_admin() === false && $hook->is_front() === true ) {
			return true;
		}
		return false;
	}

	/**
	 * Wraps the callback for all lazy calls, else just the passed
	 * callback.
	 *
	 * @param Hook $hook
	 * @return callable
	 * @throws Invalid_Hook_Callback_Exception
	 */
	protected function maybe_lazy_callback( Hook $hook ): callable {
		if ( ! \is_callable( $hook->get_callback() ) ) {
			throw Invalid_Hook_Callback_Exception::from_hook( $hook );
		}

		return $hook->is_lazy() === true
			? function( ...$args ) use ( $hook ) {
				return \call_user_func_array( $hook->get_callback(), $args );
			}
			: $hook->get_callback();
	}

	/**
	 * Registers action hook
	 *
	 * @param Hook $hook
	 * @return Hook
	 * @throws Invalid_Hook_Callback_Exception
	 */
	protected function register_action( Hook $hook ): Hook {
		if ( \is_callable( $hook->get_callback() ) ) {
			throw Invalid_Hook_Callback_Exception::from_hook( $hook );
		}

		add_action( $hook->get_handle(), $this->maybe_lazy_callback( $hook ), $hook->get_priority(), $hook->args_count() );
		$hook->registered();
		return $hook;
	}

	/**
	 * Registers a filter action
	 *
	 * @param Hook $hook
	 * @return Hook
	 * @throws Invalid_Hook_Callback_Exception
	 */
	protected function register_filter( Hook $hook ): Hook {
		if ( \is_callable( $hook->get_callback() ) ) {
			throw Invalid_Hook_Callback_Exception::from_hook( $hook );
		}

		add_filter( $hook->get_handle(), $this->maybe_lazy_callback( $hook ), $hook->get_priority(), $hook->args_count() );
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
		// Remove the hook.
		( new Hook_Removal(
			$hook->get_handle(),
			$hook->get_callback(),
			$hook->get_priority()
		) )->remove();

		$hook->registered();
		return $hook;
	}

	/**
	 * Register a standard wp_ajax action
	 *
	 * @param Hook $hook
	 * @return Hook
	 * @throws Invalid_Hook_Callback_Exception
	 */
	protected function register_ajax( Hook $hook ): Hook {
		if ( \is_callable( $hook->get_callback() ) ) {
			throw Invalid_Hook_Callback_Exception::from_hook( $hook );
		}
	}

	/**
	 * Registers a wp shortcode.
	 *
	 * @param Hook $hook
	 * @return Hook
	 * @throws Invalid_Hook_Callback_Exception
	 */
	protected function register_shortcode( Hook $hook ): Hook {
		if ( \is_callable( $hook->get_callback() ) ) {
			throw Invalid_Hook_Callback_Exception::from_hook( $hook );
		}
	}


}
