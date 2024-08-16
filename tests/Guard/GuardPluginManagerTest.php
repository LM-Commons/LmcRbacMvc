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

namespace LmcTest\Rbac\Mvc\Guard;

use Laminas\ServiceManager\ServiceManager;
use Lmc\Rbac\Mvc\Guard\GuardPluginManager;
use Lmc\Rbac\Mvc\Options\ModuleOptions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @covers \Lmc\Rbac\Mvc\Guard\GuardPluginManager
 */
class GuardPluginManagerTest extends \PHPUnit\Framework\TestCase
{
    public static function guardProvider(): array
    {
        return [
            [
                'Lmc\Rbac\Mvc\Guard\RouteGuard',
                [
                    'admin/*' => 'foo'
                ]
            ],
            [
                'Lmc\Rbac\Mvc\Guard\RoutePermissionsGuard',
                [
                    'post/delete' => 'post.delete'
                ]
            ],
            [
                'Lmc\Rbac\Mvc\Guard\ControllerGuard',
                [
                    [
                        'controller' => 'Foo',
                        'actions'    => 'bar',
                        'roles'      => 'baz'
                    ]
                ]
            ],
            [
                'Lmc\Rbac\Mvc\Guard\ControllerPermissionsGuard',
                [
                    [
                        'controller'  => 'Foo',
                        'actions'     => 'bar',
                        'permissions' => 'baz'
                    ]
                ]
            ],
        ];
    }

    #[DataProvider('guardProvider')]
    public function testCanCreateDefaultGuards($type, $options)
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Lmc\Rbac\Mvc\Options\ModuleOptions', new ModuleOptions());
        $serviceManager->setService(
            'Lmc\Rbac\Mvc\Service\RoleService',
            $this->createMock('Lmc\Rbac\Mvc\Service\RoleService')
        );
        $serviceManager->setService(
            'Lmc\Rbac\Mvc\Service\AuthorizationService',
            $this->createMock('Lmc\Rbac\Mvc\Service\AuthorizationService')
        );

        $pluginManager = new GuardPluginManager($serviceManager);

        $guard = $pluginManager->get($type, $options);

        $this->assertInstanceOf($type, $guard);
    }

    public function testThrowExceptionForInvalidPlugin()
    {
        $this->expectException('Lmc\Rbac\Exception\RuntimeException');

        $pluginManager = new GuardPluginManager(new ServiceManager());
        $pluginManager->setService('foo', new \stdClass());
    }
}
