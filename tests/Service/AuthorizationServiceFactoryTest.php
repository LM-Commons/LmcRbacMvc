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

use Laminas\Permissions\Rbac\Rbac;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\ServiceManager;
use Lmc\Rbac\Mvc\Service\AuthorizationServiceFactory;
use Lmc\Rbac\Mvc\Options\ModuleOptions;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Rbac\Mvc\Service\AuthorizationServiceFactory
 */
class AuthorizationServiceFactoryTest extends TestCase
{
    /**
     * Test the default case
     */
    public function testFactory()
    {
        $container = $this->createMock('Psr\Container\ContainerInterface');
        $container->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($className) {
                return match($className) {
                    'Lmc\Rbac\Mvc\Service\RoleService' => $this->createMock('Lmc\Rbac\Mvc\Service\RoleService'),
                    'Lmc\Rbac\Service\AuthorizationServiceInterface' => $this->createMock('Lmc\Rbac\Service\AuthorizationService'),
                };
            }
        );

        $container->expects($this->once())
            ->method('has')
            ->with('Lmc\Rbac\Service\AuthorizationServiceInterface')
            ->willReturn(true);

        $factory              = new AuthorizationServiceFactory();
        $authorizationService = $factory($container, 'Lmc\Rbac\Mvc\Service\AuthorizationService');
    }

    /**
     * Test the case where Lmc\Rbac\Service\Authorization service is not ser
     */
    public function testMissingBaseAuthorizationService()
    {
        $container = $this->createMock('Psr\Container\ContainerInterface');
        $container->expects($this->never())
            ->method('get');

        $container->expects($this->once())
            ->method('has')
            ->with('Lmc\Rbac\Service\AuthorizationServiceInterface')
            ->willReturn(false);

        $this->expectException(ServiceNotCreatedException::class);

        $factory              = new AuthorizationServiceFactory();
        $authorizationService = $factory($container, 'Lmc\Rbac\Mvc\Service\AuthorizationService');
    }
}
