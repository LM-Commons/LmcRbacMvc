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

namespace LmcTest\Rbac\Mvc\Service;

use Laminas\ServiceManager\ServiceManager;
use Lmc\Rbac\Mvc\Service\RoleServiceFactory;
use Lmc\Rbac\Mvc\Options\ModuleOptions;

/**
 * @covers \Lmc\Rbac\Mvc\Service\RoleServiceFactory
 */
class RoleServiceFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $options = new ModuleOptions([
            'identity_provider'    => 'Lmc\Rbac\Mvc\Identity\AuthenticationProvider',
        ]);

        $identityProvider = $this->createMock('Lmc\Rbac\Mvc\Identity\AuthenticationIdentityProvider');

        $baseRoleService = $this->createMock('Lmc\Rbac\Service\RoleServiceInterface');

        $container = $this->createMock('Psr\Container\ContainerInterface');

        $container->expects($this->exactly(3))
            ->method('get')
            ->willReturnCallback(function ($name) use ($options, $identityProvider, $baseRoleService) {
                return match ($name) {
                    ModuleOptions::class => $options,
                    $options->getIdentityProvider() => $identityProvider,
                    'Lmc\Rbac\Service\RoleServiceInterface' => $baseRoleService,
                };
            });

        $factory = new RoleServiceFactory();
        $roleService = $factory($container, 'Lmc\Rbac\Mvc\Service\RoleService');
    }
}
