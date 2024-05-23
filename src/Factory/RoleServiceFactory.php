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

namespace LmcRbacMvc\Factory;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Exception\RuntimeException;
use LmcRbacMvc\Identity\IdentityProviderInterface;
use LmcRbacMvc\Options\ModuleOptions;
use LmcRbacMvc\Role\RoleProviderInterface;
use LmcRbacMvc\Role\RoleProviderPluginManager;
use LmcRbacMvc\Service\RoleService;
use LmcRbacMvc\Role\RecursiveRoleIteratorStrategy;
use LmcRbacMvc\Role\TraversalStrategyInterface;

/**
 * Factory to create the role service
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @license MIT
 */
class RoleServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RoleService
    {
        /* @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        /* @var IdentityProviderInterface $identityProvider */
        $identityProvider = $container->get($moduleOptions->getIdentityProvider());

        $roleProviderConfig = $moduleOptions->getRoleProvider();

        if (empty($roleProviderConfig)) {
            throw new ServiceNotCreatedException('No role provider has been set for LmcRbacMvc');
        }

        /* @var RoleProviderPluginManager $pluginManager */
        $pluginManager = $container->get(RoleProviderPluginManager::class);

        reset($roleProviderConfig);
        $roleProvider = $pluginManager->get(key($roleProviderConfig), current($roleProviderConfig));

        /* @var TraversalStrategyInterface $traversalStrategy */
        $traversalStrategy = new RecursiveRoleIteratorStrategy();//$container->get(Rbac::class)->getTraversalStrategy();

        $roleService = new RoleService($identityProvider, $roleProvider, $traversalStrategy);
        $roleService->setGuestRole($moduleOptions->getGuestRole());

        return $roleService;
    }
}
