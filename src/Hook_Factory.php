<?php

declare(strict_types=1);
/**
 * Primary hook manager, used to register/unregister all hooks
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

use PinkCrab\Loader\Hook;

class Hook_Factory {


	/**
	 * Creates an action.
	 *
	 * @param string $handle
	 * @param callable $callable
	 * @param integer $args
	 * @param integer $priroty
	 * @param boolean $is_admin
	 * @param boolean $is_public
	 * @return Hook
	 */
	public function action(
		string $handle,
		callable $callable,
		int $args = 1,
		int $priroty = 10,
		bool $is_admin = true,
		bool $is_public = true
	): Hook {
		$hook = new Hook( $handle, $callable, $priroty, $args );
		$hook->type( Hook::ACTION );
		$hook->admin( $is_admin );
		$hook->front( $is_public );
		return $hook;
	}

	/**
	 * Creates a filter.
	 *
	 * @param string $handle
	 * @param callable $callable
	 * @param integer $args
	 * @param integer $priroty
	 * @param boolean $is_admin
	 * @param boolean $is_public
	 * @return Hook
	 */
	public function filter(
		string $handle,
		callable $callable,
		int $args = 1,
		int $priroty = 10,
		bool $is_admin = true,
		bool $is_public = true
	): Hook {
		$hook = new Hook( $handle, $callable, $priroty, $args );
		$hook->type( Hook::FILTER );
		$hook->admin( $is_admin );
		$hook->front( $is_public );
		return $hook;
	}

	/**
	 * Removes a hook
	 *
	 * @param string $handle
	 * @param callable|array{0:string,1:string} $callable
	 * @param integer $priroty
	 * @return Hook
	 */
	public function remove(
		string $handle,
		$callable,
		int $priroty = 10
	): Hook {
		$hook = new Hook( $handle, $callable, $priroty );
		$hook->type( Hook::REMOVE );
		return $hook;
	}

	/**
	 * Creates a shortcode.
	 *
	 * @param string $handle
	 * @param callable $callable
	 * @return Hook
	 */
	public function shortcode(
		string $handle,
		callable $callable
	): Hook {
		$hook = new Hook( $handle, $callable );
		$hook->type( Hook::SHORTCODE );
		return $hook;
	}

	/**
	 * Creates a shortcode.
	 *
	 * @param string $handle
	 * @param callable $callable
	 * @param boolean $private
	 * @param boolean $public
	 * @return Hook
	 */
	public function ajax(
		string $handle,
		callable $callable,
		bool $public = true,
		bool $private = true
	): Hook {
		$hook = new Hook( $handle, $callable );
		$hook->type( Hook::AJAX );
		$hook->ajax_private( $private );
		$hook->ajax_public( $public );
		return $hook;
	}
}
