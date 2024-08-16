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

namespace Lmc\Rbac\Mvc\Guard;

use Laminas\ServiceManager\AbstractPluginManager;
use Lmc\Rbac\Exception;

/**
 * Plugin manager to create guards
 *
 * @method GuardInterface get($name)
 *
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
    public function validate($instance): void
    {
        if ($instance instanceof GuardInterface) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf(
            'Guards must implement "Lmc\Rbac\Mvc\Guard\GuardInterface", but "%s" was given',
            is_object($instance) ? get_class($instance) : gettype($instance)
        ));
    }
}
