<?php
/*
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
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace LaminasRbac\Guard;

use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\AbstractPluginManager;
use LaminasRbac\Exception;
use LaminasRbac\Factory\ControllerGuardFactory;
use LaminasRbac\Factory\ControllerPermissionsGuardFactory;
use LaminasRbac\Factory\RouteGuardFactory;
use LaminasRbac\Factory\RoutePermissionsGuardFactory;

/**
 * Plugin manager to create guards
 *
 * @method GuardInterface get($name)
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @author  JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */
class GuardPluginManager extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $factories = [
        ControllerGuard::class            => ControllerGuardFactory::class,
        ControllerPermissionsGuard::class => ControllerPermissionsGuardFactory::class,
        RouteGuard::class                 => RouteGuardFactory::class,
        RoutePermissionsGuard::class      => RoutePermissionsGuardFactory::class,
    ];

    /**
     * {@inheritDoc}
     */
    public function validate($plugin)
    {
        if ($plugin instanceof GuardInterface) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf(
            'Guards must implement "LaminasRbac\Guard\GuardInterface", but "%s" was given',
            is_object($plugin) ? get_class($plugin) : gettype($plugin)
        ));
    }

    /**
     * {@inheritDoc}
     * @throws ContainerException
     */
    public function validatePlugin($plugin)
    {
        $this->validate($plugin);
    }

    /**
     * {@inheritDoc}
     */
    protected function canonicalizeName($name)
    {
        return $name;
    }
}
