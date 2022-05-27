<?php

declare(strict_types=1);
/**
 * The hook loader.
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
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Loader
 */

namespace PinkCrab\Loader;

use Countable;
use PinkCrab\Loader\Hook;

class Hook_Collection implements Countable {

	/**
	 * Filter used to filter the Hooks.
	 */
	const REGISTER_HOOKS = 'pinkcrab/loader/register_hooks';

	/**
	 * Holds the hooks.
	 *
	 * @var Hook[]
	 */
	protected $hooks = array();

	/**
	 * Pushes an item to the collection.
	 *
	 * @param Hook $hook
	 * @return self
	 */
	public function push( Hook $hook ): self {
		$this->hooks[] = $hook;
		return $this;
	}

	/**
	 * Applies a function to all items in the collection.
	 *
	 * @param callable(Hook):void $function
	 * @return void
	 */
	public function register( callable $function ): void {
		// Filter the hooks, to add/amend filters.
		$hooks = \apply_filters( self::REGISTER_HOOKS, $this->hooks );

		foreach ( $hooks as $key => $hook ) {
			$function( $hook );

			// If array key doesnt exist, then add to end.
			if ( ! \array_key_exists( $key, $this->hooks ) ) {
				$key                 = ( array_key_last( $hooks ) + 1 );
				$this->hooks[ $key ] = $hook;
			}

			$this->hooks[ $key ]->registered( true );
		}
	}

	/**
	 * Get count of hooks registered.
	 *
	 * @return int
	 */
	public function count(): int {
		return count( $this->hooks );
	}

	/**
	 * Pop the last hook registered.
	 *
	 * @return Hook|null
	 */
	public function pop(): ?Hook {
		if ( $this->count() !== 0 ) {
			return array_pop( $this->hooks );
		}
		return null;
	}

	/**
	 * Exports the internal hook array, as an array.
	 *
	 * @since 1.1.0
	 * @return Hook[]
	 */
	public function export(): array {
		return $this->hooks;
	}
}
