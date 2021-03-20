<?php

declare(strict_types=1);

/**
 * Hook model
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

class Hook {

	/**  Hook type constants. */

	/** @var string */
	public const ACTION = 'action';
	/** @var string */
	public const FILTER = 'filter';
	/** @var string */
	public const AJAX = 'ajax';
	/** @var string */
	public const SHORTCODE = 'shortcode';
	/** @var string */
	public const REMOVE = 'remove';


	/**
	 * The hooks type
	 * @var string
	 * */
	protected $type = self::ACTION;

	/**
	 * The hooks handle
	 * @var string
	 * */
	protected $handle;

	/**
	 * The hooks callback
	 * @var callable
	 * */
	protected $callback;

	/**
	 * Hook priority (defualts to 10 as per WP Core)
	 * @var int
	 * */
	protected $priority = 10;

	/**
	 * Callback arg count (defualts to 1 as per WP Core)
	 * @var int
	 * */
	protected $args = 1;

	/**
	 * If ajax should be registered with priv
	 * @var bool
	 * */
	protected $ajax_private = true;

	/**
	 * If ajax should be registerd with nopriv
	 * @var bool
	 * */
	protected $ajax_public = true;

	/**
	 * Lazy evaluation off callback
	 * @var bool
	 * */
	protected $lazy = false;

	/**
	 * If this hook should has its creation deffered to another hook
	 * @var string|null
	 * */
	protected $deffered_on = null;

	/**
	 * Should this hook be loaded if is_admin === true
	 * @var bool
	 * */
	protected $is_admin = true;

	/**
	 * Should this hook be loaded if is_admin === false
	 * @var bool
	 * */
	protected $is_front = true;

	public function __construct(
		string $handle,
		callable $callback,
		int $priority = 10,
		int $args = 1
	) {
		$this->handle   = $handle;
		$this->callback = $callback;
		$this->priority = $priority;
		$this->args     = $args;
	}

	/**
	 * Get the value of type
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Set the value of type
	 *
	 * @param string $type
	 * @return self
	 */
	public function type( string $type ): self {
		$this->type = $type;
		return $this;
	}

	/**
	 * Get the hooks handle
	 * @return string
	 */
	public function get_handle(): string {
		return $this->handle;
	}

	/**
	 * Set the hooks handle
	 *
	 * @param string $handle  The hooks handle
	 * @return self
	 */
	public function handle( string $handle ): self {
		$this->handle = $handle;
		return $this;
	}

	/**
	 * Get the hooks callback
	 * @return callable
	 */
	public function get_callback(): callable {
		return $this->callback;
	}

	/**
	 * Set the hooks callback
	 *
	 * @param callable $callback  The hooks callback
	 * @return self
	 */
	public function callback( callable $callback ): self {
		$this->callback = $callback;
		return $this;
	}

	/**
	 * Get hook priority (defualts to 10 as per WP Core)
	 * @return int
	 */
	public function get_priority(): int {
		return $this->priority;
	}

	/**
	 * Set hook priority (defualts to 10 as per WP Core)
	 *
	 * @param int $priority  Hook priority (defualts to 10 as per WP Core)
	 * @return self
	 */
	public function priority( int $priority ): self {
		$this->priority = $priority;
		return $this;
	}

	/**
	 * Get callback arg count (defualts to 1 as per WP Core)
	 * @return int
	 */
	public function args_count(): int {
		return $this->args;
	}

	/**
	 * Set callback arg count (defualts to 1 as per WP Core)
	 *
	 * @param int $args  Callback arg count (defualts to 1 as per WP Core)
	 * @return self
	 */
	public function args( int $args ): self {
		$this->args = $args;
		return $this;
	}

	/**
	 * Get if ajax should be registered with priv
	 * @return bool
	 */
	public function is_ajax_private(): bool {
		return $this->ajax_private;
	}

	/**
	 * Set if ajax should be registered with priv
	 *
	 * @param bool $ajax_private  If ajax should be registered with priv
	 * @return self
	 */
	public function ajax_private( bool $ajax_private = true ): self {
		$this->ajax_private = $ajax_private;
		return $this;
	}

	/**
	 * Get if ajax should be registerd with nopriv
	 * @return bool
	 */
	public function is_ajax_public(): bool {
		return $this->ajax_public;
	}

	/**
	 * Set if ajax should be registerd with nopriv
	 *
	 * @param bool $ajax_public  If ajax should be registerd with nopriv
	 * @return self
	 */
	public function ajax_public( bool $ajax_public = true ): self {
		$this->ajax_public = $ajax_public;
		return $this;
	}

	/**
	 * Get lazy evaluation off callback
	 * @return bool
	 */
	public function is_lazy(): bool {
		return $this->lazy;
	}

	/**
	 * Set lazy evaluation off callback
	 *
	 * @param bool $lazy  Lazy evaluation off callback
	 * @return self
	 */
	public function lazy( bool $lazy = true ): self {
		$this->lazy = $lazy;
		return $this;
	}

	/**
	 * Get if this hook should has its creation deffered to another hook
	 * @return string|null
	 */
	public function get_deffered_on(): ?string {
		return $this->deffered_on;
	}

	/**
	 * Checks if the hook is to be deffered
	 * @return bool
	 */
	public function is_deffered(): bool {
		return $this->deffered_on !== null;
	}

	/**
	 * Set if this hook should has its creation deffered to another hook
	 *
	 * @param string|null $deffered_on  If this hook should has its creation deffered to another hook
	 * @return self
	 */
	public function deffered_on( $deffered_on ): self {
		$this->deffered_on = $deffered_on;
		return $this;
	}

	/**
	 * Get should this hook be loaded if is_admin === true
	 * @return bool
	 */
	public function is_admin(): bool {
		return $this->is_admin;
	}

	/**
	 * Set should this hook be loaded if is_admin === true
	 *
	 * @param bool $is_admin  Should this hook be loaded if is_admin === true
	 * @return self
	 */
	public function admin( bool $is_admin = true ): self {
		$this->is_admin = $is_admin;
		return $this;
	}

	/**
	 * Get should this hook be loaded if is_admin === false
	 * @return bool
	 */
	public function is_front(): bool {
		return $this->is_front;
	}

	/**
	 * Set should this hook be loaded if is_admin === false
	 *
	 * @param bool $is_front  Should this hook be loaded if is_admin === false
	 * @return self
	 */
	public function front( bool $is_front = true ): self {
		$this->is_front = $is_front;
		return $this;
	}
}

