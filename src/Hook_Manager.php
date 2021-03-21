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

use PinkCrab\Loader\Hook;

class Hook_Manager
{

    /**
     * Callback used to process a hook
     *
     * @param Hook $hook
     * @return Hook
     */
    public function process_hook(Hook $hook): Hook
    {
        # code...
    }

    /** 
     * Registers action hook
     * 
     * @param Hook $hook
     * @return Hook
     */
    protected function reigster_action(Hook $hook): Hook
    {
        # code...
    }

    /**
     * Registers a filter action
     *
     * @param Hook $hook
     * @return Hook
     */
    protected function register_filter(Hook $hook): Hook
    {
        # code...
    }

    /**
     * Removes a hook using hook_removal
     *
     * @param Hook $hook
     * @return Hook
     */
    protected function register_remove(Hook $hook): Hook
    {
        # code...
    }

    /**
     * Register a standard wp_ajax action
     *
     * @param Hook $hook
     * @return Hook
     */
    protected function register_ajax(Hook $hook): Hook
    {
        # code...
    }

    /**
     * Registers a wp shortcode.
     *
     * @param Hook $hook
     * @return Hook
     */
    protected function register_shortcode(Hook $hook): Hook
    {
        # code...
    }

    /**
     * Returns the callable use for a deferred action call.
     *
     * @param Hook $hook
     * @return callable
     */
    protected function create_deferal_callback(Hook $hook): callable
    {
        return function (...$args) use ($hook) {
            add_action($hook->get_handle(), $hook->get_callback(), $hook->args_count(), $hook->get_priority());
        };
    }
}
