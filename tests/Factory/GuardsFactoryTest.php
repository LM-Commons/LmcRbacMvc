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

namespace LmcRbacTest\Factory;

use Laminas\ServiceManager\ServiceManager;
use LmcRbac\Factory\GuardsFactory;
use LmcRbac\Guard\GuardPluginManager;
use LmcRbac\Options\ModuleOptions;

/**
 * @covers \LmcRbac\Factory\GuardsFactory
 */
class GuardsFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $moduleOptions = new ModuleOptions([
            'guards' => [
                'LmcRbac\Guard\RouteGuard' => [
                    'admin/*' => 'role1'
                ],
                'LmcRbac\Guard\RoutePermissionsGuard' => [
                    'admin/post' => 'post.manage'
                ],
                'LmcRbac\Guard\ControllerGuard' => [[
                    'controller' => 'MyController',
                    'actions'    => ['index', 'edit'],
                    'roles'      => ['role']
                ]],
                'LmcRbac\Guard\ControllerPermissionsGuard' => [[
                    'controller'  => 'PostController',
                    'actions'     => ['index', 'edit'],
                    'permissions' => ['post.read']
                ]]
            ]
        ]);

        $serviceManager = new ServiceManager();
        $pluginManager = new GuardPluginManager($serviceManager);

        $serviceManager->setService('LmcRbac\Options\ModuleOptions', $moduleOptions);
        $serviceManager->setService('LmcRbac\Guard\GuardPluginManager', $pluginManager);
        $serviceManager->setService(
            'LmcRbac\Service\RoleService',
            $this->getMock('LmcRbac\Service\RoleService', [], [], '', false)
        );
        $serviceManager->setService(
            'LmcRbac\Service\AuthorizationService',
            $this->getMock('LmcRbac\Service\AuthorizationServiceInterface', [], [], '', false)
        );

        $factory = new GuardsFactory();
        $guards  = $factory->createService($serviceManager);

        $this->assertInternalType('array', $guards);

        $this->assertCount(4, $guards);
        $this->assertInstanceOf('LmcRbac\Guard\RouteGuard', $guards[0]);
        $this->assertInstanceOf('LmcRbac\Guard\RoutePermissionsGuard', $guards[1]);
        $this->assertInstanceOf('LmcRbac\Guard\ControllerGuard', $guards[2]);
        $this->assertInstanceOf('LmcRbac\Guard\ControllerPermissionsGuard', $guards[3]);
    }

    public function testReturnArrayIfNoConfig()
    {
        $moduleOptions = new ModuleOptions([
            'guards' => []
        ]);

        $serviceManager = new ServiceManager();
        $pluginManager = new GuardPluginManager($serviceManager);

        $serviceManager->setService('LmcRbac\Options\ModuleOptions', $moduleOptions);

        $factory = new GuardsFactory();
        $guards  = $factory->createService($serviceManager);

        $this->assertInternalType('array', $guards);

        $this->assertEmpty($guards);
    }
}
