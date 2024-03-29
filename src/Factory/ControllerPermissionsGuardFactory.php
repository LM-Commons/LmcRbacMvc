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

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Guard\ControllerPermissionsGuard;
use LmcRbacMvc\Options\ModuleOptions;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Create a controller guard for checking permissions
 *
 * @author  JM Lerouxw <jmleroux.pro@gmail.com>
 * @license MIT
 */
class ControllerPermissionsGuardFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $options = [])
    {
        $this->setCreationOptions($options);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param ContainerInterface $container
     * @param string $resolvedName
     * @param array|null $options
     * @return ControllerPermissionsGuard
     */
    public function __invoke(ContainerInterface $container, $resolvedName, array $options = null)
    {
        if (null === $options) {
            $options = [];
        }

        /* @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get('LmcRbacMvc\Options\ModuleOptions');

        /* @var AuthorizationService $authorizationService */
        $authorizationService = $container->get('LmcRbacMvc\Service\AuthorizationService');

        $guard = new ControllerPermissionsGuard($authorizationService, $options);
        $guard->setProtectionPolicy($moduleOptions->getProtectionPolicy());

        return $guard;
    }

    /**
     * @param AbstractPluginManager|ServiceLocatorInterface $serviceLocator
     * @return ControllerPermissionsGuard
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), ControllerPermissionsGuard::class, $this->options);
    }
}
