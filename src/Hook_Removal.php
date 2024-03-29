<?php

declare(strict_types=1);
/**
 * Hook unloader, used to remove hooks registered by other plugins and themes.
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

use Closure;
use InvalidArgumentException;

class Hook_Removal {


	/**
	 * Filter/Action handle
	 *
	 * @var string
	 */
	protected $handle;

	/**
	 * Registered Callback
	 *
	 * @var callable|array{0:string,1:string}
	 */
	protected $callback;

	/**
	 * Priority of registered hook
	 *
	 * @var int
	 */
	protected $priority = 10;

	/**
	 * Global of all hooks registered
	 *
	 * @var array<string, \WP_Hook>
	 */
	protected $registered_hooks;

	/**
	 * @param string $handle
	 * @param callable|array{0:string,1:string} $callback
	 * @param int $priority
	 */
	public function __construct( string $handle, $callback, int $priority = 10 ) {
		if ( ! $this->validate_callback( $callback ) ) {
			throw new InvalidArgumentException( 'Callback must be a valid callable or array<string|object, string> representing a valid callback.' );
		}
		$this->handle           = $handle;
		$this->callback         = $callback;
		$this->priority         = $priority;
		$this->registered_hooks = $GLOBALS['wp_filter'];
	}

	/**
	 * Validate the callback passed.
	 *
	 * @param mixed $callback
	 * @return bool
	 */
	protected function validate_callback( $callback ): bool {
		if ( \is_callable( $callback ) ) {
			return true;
		}

		// If faux callable with class-string
		if ( \is_array( $callback )
		&& \count( $callback ) === 2
		&& \is_string( $callback[0] )
		&& \class_exists( $callback[0], false )
		&& \is_string( $callback[1] )
		&& \method_exists( $callback[0], $callback[1] )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Removes the registered hook.
	 *
	 * @return bool
	 */
	public function remove(): bool {
		if ( ! $this->has_hook() || $this->is_callback_closure() ) {
			return false;
		}

		$removed = false;

		foreach ( $this->registered_hooks() as $key => $registered_callback ) {
			// Is class.
			if (
				\is_array( $registered_callback['function'] )
				&& \count( $registered_callback['function'] ) === 2
				&& $this->matching_class_callback( $registered_callback )
			) {
				unset( $this->registered_hooks[ $this->handle ]->callbacks[ $this->priority ][ $key ] );
				$removed = true;
			}

			// Is global function
			if (
				\is_string( $registered_callback['function'] )
				&& $this->matching_function_callback( $registered_callback )
			) {
				unset( $this->registered_hooks[ $this->handle ]->callbacks[ $this->priority ][ $key ] );
				$removed = true;
			}
		}

		return $removed;
	}

	/**
	 * Checks if a registered callback matches a defined gloabl function.
	 *
	 * @param array{function:(string|array{0:(string|object), 1:string}), accepted_args:int} $registered_callback
	 * @return bool
	 */
	protected function matching_function_callback( $registered_callback ): bool {
		return \is_string( $this->callback )
		&& \is_string( $registered_callback['function'] )
		&& \strcmp( $registered_callback['function'], $this->callback ) === 0;
	}

	/**
	 * Checks if a registered callback matches defined class.
	 *
	 * Checks the class based on its name, not the instance.
	 *
	 * @param array{function:(string|array{0:(string|object), 1:string}), accepted_args:int} $registered_callback
	 * @return bool
	 */
	protected function matching_class_callback( $registered_callback ): bool {
		$registered_class = \is_object( $registered_callback['function'][0] )
			? \get_class( $registered_callback['function'][0] )
			: $registered_callback['function'][0];

		$callback_class = $this->get_callback_as_array();

		return \class_exists( $callback_class['class'] )
			&& \strcmp( $registered_class, $callback_class['class'] ) === 0
			&& \strcmp( $registered_callback['function'][1], $callback_class['method'] ) === 0;
	}

	/**
	 * All registered hooks on the defined handle and priority.
	 *
	 * @return array<string|int, array{function:(string|array{0:(string|object), 1:string}), accepted_args:int}>
	 */
	protected function registered_hooks(): array {
		return \array_filter(
			$this->registered_hooks[ $this->handle ]->callbacks[ $this->priority ],
			function ( array $callback ) {
				return \array_key_exists( 'function', $callback );
			}
		);
	}

	/**
	 * Returns the current callback object as an array of strings
	 *
	 * @return array{'class':string, method:string}
	 */
	protected function get_callback_as_array(): array {
		if ( ! \is_array( $this->callback ) ) {
			return array(
				'class'  => '',
				'method' => '',
			);
		}

		/** @var array{0:(string|object), 1:string} */
		$callback = $this->callback;

		return array(
			'class'  => \is_object( $callback[0] ) ? \get_class( $callback[0] ) : $callback[0],
			'method' => $callback[1],
		);
	}

	/**
	 * Validates the callback.
	 *
	 * @return bool
	 */
	protected function is_callback_closure(): bool {
		return $this->callback instanceof Closure;
	}

	/**
	 * Ensure hooks have been registered to passed hook and priority.
	 *
	 * @return bool
	 */
	protected function has_hook(): bool {
		return \array_key_exists( $this->handle, $this->registered_hooks )
			&& \array_key_exists( $this->priority, $this->registered_hooks[ $this->handle ]->callbacks );
	}
}
