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

	/** @var string */
	protected $type = self::ACTION;
	/** @var string */
	protected $handle;
	/** @var callable */
	protected $callback;
	/** @var int */
	protected $priority = 10;
	/** @var int */
	protected $args = 1;
	/** @var bool */
	protected $ajax_private = true;
	/** @var bool */
	protected $ajax_public = true;
	/** @var bool */
	protected $lazy = false;
	/** @var string|null */
	protected $deffered_on = null;
	/** @var bool */
	protected $is_admin = true;
	/** @var bool */
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
}

