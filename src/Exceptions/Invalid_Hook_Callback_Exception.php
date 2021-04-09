<?php

declare(strict_types=1);
/**
 * Invaild callback hook exception.
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

namespace PinkCrab\Loader\Exceptions;

use Exception;
use PinkCrab\Loader\Hook;

class Invalid_Hook_Callback_Exception extends Exception {

	/**
	 * Throw invlaid callback exception.
	 *
	 * @param \PinkCrab\Loader\Hook $hook
	 * @return Invalid_Hook_Callback_Exception
	 */
	public static function from_hook( Hook $hook ): Invalid_Hook_Callback_Exception {
		$message = sprintf(
			'%s hook was called with an invalid callback. Only %s hooks may use malformed callback arrays',
			$hook->get_type(),
			Hook::REMOVE
		);
		return new Invalid_Hook_Callback_Exception( $message );
	}
}
