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
use Lmc\Rbac\Mvc\Guard\GuardsFactory;
use Lmc\Rbac\Mvc\Guard\GuardPluginManager;
use Lmc\Rbac\Mvc\Options\ModuleOptions;

/**
 * @covers \Lmc\Rbac\Mvc\Guard\GuardsFactory
 */
class GuardsFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $moduleOptions = new ModuleOptions([
            'guards' => [
                'Lmc\Rbac\Mvc\Guard\RouteGuard' => [
                    'admin/*' => 'role1'
                ],
                'Lmc\Rbac\Mvc\Guard\RoutePermissionsGuard' => [
                    'admin/post' => 'post.manage'
                ],
                'Lmc\Rbac\Mvc\Guard\ControllerGuard' => [[
                    'controller' => 'MyController',
                    'actions'    => ['index', 'edit'],
                    'roles'      => ['role']
                ]],
                'Lmc\Rbac\Mvc\Guard\ControllerPermissionsGuard' => [[
                    'controller'  => 'PostController',
                    'actions'     => ['index', 'edit'],
                    'permissions' => ['post.read']
                ]]
            ]
        ]);

        $serviceManager = new ServiceManager();
        $pluginManager = new GuardPluginManager($serviceManager);

        $serviceManager->setService('Lmc\Rbac\Mvc\Options\ModuleOptions', $moduleOptions);
        $serviceManager->setService('Lmc\Rbac\Mvc\Guard\GuardPluginManager', $pluginManager);
        $serviceManager->setService(
            'Lmc\Rbac\Mvc\Service\RoleService',
            $this->getMockBuilder('Lmc\Rbac\Mvc\Service\RoleService')->disableOriginalConstructor()->getMock()
        );
        $serviceManager->setService(
            'Lmc\Rbac\Mvc\Service\AuthorizationService',
            $this->getMockBuilder('Lmc\Rbac\Mvc\Service\AuthorizationServiceInterface')->disableOriginalConstructor()->getMock()
        );

        $factory = new GuardsFactory();
        $guards  = $factory($serviceManager, '');

        $this->assertIsArray($guards);

        $this->assertCount(4, $guards);
        $this->assertInstanceOf('Lmc\Rbac\Mvc\Guard\RouteGuard', $guards[0]);
        $this->assertInstanceOf('Lmc\Rbac\Mvc\Guard\RoutePermissionsGuard', $guards[1]);
        $this->assertInstanceOf('Lmc\Rbac\Mvc\Guard\ControllerGuard', $guards[2]);
        $this->assertInstanceOf('Lmc\Rbac\Mvc\Guard\ControllerPermissionsGuard', $guards[3]);
    }

    public function testReturnArrayIfNoConfig()
    {
        $moduleOptions = new ModuleOptions([
            'guards' => []
        ]);

        $serviceManager = new ServiceManager();
        $pluginManager = new GuardPluginManager($serviceManager);

        $serviceManager->setService('Lmc\Rbac\Mvc\Options\ModuleOptions', $moduleOptions);

        $factory = new GuardsFactory();
        $guards  = $factory($serviceManager, GuardsFactory::class);

        $this->assertIsArray($guards);

        $this->assertEmpty($guards);
    }
}
