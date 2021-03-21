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

use PinkCrab\Loader\Hook_Factory;
use PinkCrab\Loader\Hook_Collection;

class Hook_Loader
{

    protected $hooks;

    protected $hook_factory;

    final public function __construct()
    {
        $this->hook_factory = new Hook_Factory;
        $this->hooks = new Hook_Collection;
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
    public function action(string $handle, callable $callback, int $args = 1, int $priority = 10): Hook
    {
        $hook = $this->hook_factory->action($handle, $callback, $args, $priority);
        $this->hooks->push($hook);
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
    public function admin_action(string $handle, callable $callback, int $args = 1, int $priority = 10): Hook
    {
        $hook = $this->hook_factory->action($handle, $callback, $args, $priority, true, false);
        $this->hooks->push($hook);
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
    public function front_action(string $handle, callable $callback, int $args = 1, int $priority = 10): Hook
    {
        $hook = $this->hook_factory->action($handle, $callback, $args, $priority, false, true);
        $this->hooks->push($hook);
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
    public function filter(string $handle, callable $callback, int $args = 1, int $priority = 10): Hook
    {
        $hook = $this->hook_factory->filter($handle, $callback, $args, $priority);
        $this->hooks->push($hook);
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
    public function admin_filter(string $handle, callable $callback, int $args = 1, int $priority = 10): Hook
    {
        $hook = $this->hook_factory->filter($handle, $callback, $args, $priority, true, false);
        $this->hooks->push($hook);
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
    public function front_filter(string $handle, callable $callback, int $args = 1, int $priority = 10): Hook
    {
        $hook = $this->hook_factory->filter($handle, $callback, $args, $priority, false, true);
        $this->hooks->push($hook);
        return $hook;
    }
}
