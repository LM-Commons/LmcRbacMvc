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

namespace LmcRbacMvc\Service;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Lmc\Rbac\Service\AuthorizationServiceInterface as BaseAuthorizationService;
use Psr\Container\ContainerInterface;

/**
 * Factory to create the authorization service
 *
 */
class AuthorizationServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthorizationService
    {
        if (!$container->has(BaseAuthorizationService::class)) {
            throw new ServiceNotCreatedException('The service \Lmc\Rbac\Service\AuthorizationServiceInterface is not defined. Make sure that LmcRbac is configured correctly.');
        }
        /* @var RoleService $roleService */
        $roleService = $container->get(RoleService::class);

        /** @var BaseAuthorizationService $baseAuthorizationService */
        $baseAuthorizationService = $container->get(BaseAuthorizationService::class);

        return new AuthorizationService($roleService, $baseAuthorizationService);
    }
}
