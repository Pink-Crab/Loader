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
	 * @param callable $callback
	 * @param integer $args
	 * @param integer $priroty
	 * @param boolean $is_admin
	 * @param boolean $is_public
	 * @return Hook
	 */
	public function action(
		string $handle,
		callable $callback,
		int $args = 1,
		int $priroty = 10,
		bool $is_admin = true,
		bool $is_public = true
	): Hook {
		$hook = new Hook( $handle, $callback, $priroty, $args );
		$hook->type( Hook::ACTION );
		$hook->admin( $is_admin );
		$hook->front( $is_public );
		return $hook;
	}

	/**
	 * Creates a filter.
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @param integer $args
	 * @param integer $priroty
	 * @param boolean $is_admin
	 * @param boolean $is_public
	 * @return Hook
	 */
	public function filter(
		string $handle,
		callable $callback,
		int $args = 1,
		int $priroty = 10,
		bool $is_admin = true,
		bool $is_public = true
	): Hook {
		$hook = new Hook( $handle, $callback, $priroty, $args );
		$hook->type( Hook::FILTER );
		$hook->admin( $is_admin );
		$hook->front( $is_public );
		return $hook;
	}

	/**
	 * Removes a hook
	 *
	 * @param string $handle
	 * @param callable|array{0:string,1:string} $callback
	 * @param integer $priroty
	 * @return Hook
	 */
	public function remove(
		string $handle,
		$callback,
		int $priroty = 10
	): Hook {
		$hook = new Hook( $handle, $callback, $priroty );
		$hook->type( Hook::REMOVE );
		return $hook;
	}

	/**
	 * Creates a shortcode.
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @return Hook
	 */
	public function shortcode(
		string $handle,
		callable $callback
	): Hook {
		$hook = new Hook( $handle, $callback );
		$hook->type( Hook::SHORTCODE );
		return $hook;
	}

	/**
	 * Creates a shortcode.
	 *
	 * @param string $handle
	 * @param callable $callback
	 * @param boolean $public_ajax
	 * @param boolean $private_ajax
	 * @return Hook
	 */
	public function ajax(
		string $handle,
		callable $callback,
		bool $public_ajax = true,
		bool $private_ajax = true
	): Hook {
		$hook = new Hook( $handle, $callback );
		$hook->type( Hook::AJAX );
		$hook->ajax_private( $private_ajax );
		$hook->ajax_public( $public_ajax );
		return $hook;
	}
}
