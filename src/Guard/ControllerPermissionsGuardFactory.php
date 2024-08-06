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

namespace LmcRbacMvc\Guard;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
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
    protected array $options = [];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setCreationOptions($options);
    }

    /**
     * @param array $options
     */
    public function setCreationOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ControllerPermissionsGuard
    {
        if (null === $options) {
            $options = [];
        }

        /* @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        /* @var AuthorizationService $authorizationService */
        $authorizationService = $container->get(AuthorizationService::class);

        $guard = new ControllerPermissionsGuard($authorizationService, $options);
        $guard->setProtectionPolicy($moduleOptions->getProtectionPolicy());

        return $guard;
    }
}