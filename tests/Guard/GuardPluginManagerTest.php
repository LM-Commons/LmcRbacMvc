<?php

declare(strict_types=1);

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

namespace LmcTest\Rbac\Mvc\Guard;

use Laminas\ServiceManager\ServiceManager;
use Lmc\Rbac\Exception\RuntimeException;
use Lmc\Rbac\Mvc\Guard\ControllerGuard;
use Lmc\Rbac\Mvc\Guard\ControllerPermissionsGuard;
use Lmc\Rbac\Mvc\Guard\GuardPluginManager;
use Lmc\Rbac\Mvc\Guard\RouteGuard;
use Lmc\Rbac\Mvc\Guard\RoutePermissionsGuard;
use Lmc\Rbac\Mvc\Options\ModuleOptions;
use Lmc\Rbac\Mvc\Service\AuthorizationService;
use Lmc\Rbac\Mvc\Service\RoleService;
use LmcTest\Rbac\Mvc\Asset\TestGuard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \Lmc\Rbac\Mvc\Guard\GuardPluginManager
 */
class GuardPluginManagerTest extends TestCase
{
    public static function guardProvider(): array
    {
        return [
            [
                RouteGuard::class,
                [
                    'admin/*' => 'foo',
                ],
            ],
            [
                RoutePermissionsGuard::class,
                [
                    'post/delete' => 'post.delete',
                ],
            ],
            [
                ControllerGuard::class,
                [
                    [
                        'controller' => 'Foo',
                        'actions'    => 'bar',
                        'roles'      => 'baz',
                    ],
                ],
            ],
            [
                ControllerPermissionsGuard::class,
                [
                    [
                        'controller'  => 'Foo',
                        'actions'     => 'bar',
                        'permissions' => 'baz',
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('guardProvider')]
    public function testCanCreateDefaultGuards(string $type, array $options): void
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(ModuleOptions::class, new ModuleOptions());
        $serviceManager->setService(
            RoleService::class,
            $this->createMock(RoleService::class)
        );
        $serviceManager->setService(
            AuthorizationService::class,
            $this->createMock(AuthorizationService::class)
        );

        $pluginManager = new GuardPluginManager($serviceManager);

        $guard = $pluginManager->get($type, $options);

        $this->assertInstanceOf($type, $guard);
    }

    public function testThrowExceptionForInvalidPlugin(): void
    {
        $this->expectException(RuntimeException::class);

        $pluginManager = new GuardPluginManager(new ServiceManager());
        $pluginManager->setService('foo', new stdClass());
    }

    public function testCanCreateNewGuard(): void
    {
        $moduleOptions  = new ModuleOptions([
            'guards'        => [
                TestGuard::class => [],
            ],
            'guard_manager' => [
                'factories' => [
                    TestGuard::class => function () {
                        return new TestGuard();
                    },
                ],
            ],
        ]);
        $serviceManager = new ServiceManager();
        $serviceManager->setService(ModuleOptions::class, $moduleOptions);
        $serviceManager->setService(
            RoleService::class,
            $this->createMock(RoleService::class)
        );
        $serviceManager->setService(
            AuthorizationService::class,
            $this->createMock(AuthorizationService::class)
        );

        $pluginManager = new GuardPluginManager($serviceManager, $moduleOptions->getGuardManager());

        $guard = $pluginManager->get(TestGuard::class);

        $this->assertInstanceOf(TestGuard::class, $guard);
    }
}
