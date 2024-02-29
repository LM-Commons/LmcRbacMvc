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

namespace LmcRbacMvcTest\Factory;

use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Factory\GuardsFactory;
use LmcRbacMvc\Guard\GuardPluginManager;
use LmcRbacMvc\Options\ModuleOptions;

/**
 * @covers \LmcRbacMvc\Factory\GuardsFactory
 */
class GuardsFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $moduleOptions = new ModuleOptions([
            'guards' => [
                'LmcRbacMvc\Guard\RouteGuard' => [
                    'admin/*' => 'role1'
                ],
                'LmcRbacMvc\Guard\RoutePermissionsGuard' => [
                    'admin/post' => 'post.manage'
                ],
                'LmcRbacMvc\Guard\ControllerGuard' => [[
                    'controller' => 'MyController',
                    'actions'    => ['index', 'edit'],
                    'roles'      => ['role']
                ]],
                'LmcRbacMvc\Guard\ControllerPermissionsGuard' => [[
                    'controller'  => 'PostController',
                    'actions'     => ['index', 'edit'],
                    'permissions' => ['post.read']
                ]]
            ]
        ]);

        $serviceManager = new ServiceManager();
        $pluginManager = new GuardPluginManager($serviceManager);

        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', $moduleOptions);
        $serviceManager->setService('LmcRbacMvc\Guard\GuardPluginManager', $pluginManager);
        $serviceManager->setService(
            'LmcRbacMvc\Service\RoleService',
            $this->getMockBuilder('LmcRbacMvc\Service\RoleService')->disableOriginalConstructor()->getMock()
        );
        $serviceManager->setService(
            'LmcRbacMvc\Service\AuthorizationService',
            $this->getMockBuilder('LmcRbacMvc\Service\AuthorizationServiceInterface')->disableOriginalConstructor()->getMock()
        );

        $factory = new GuardsFactory();
        $guards  = $factory($serviceManager, '');

        $this->assertIsArray($guards);

        $this->assertCount(4, $guards);
        $this->assertInstanceOf('LmcRbacMvc\Guard\RouteGuard', $guards[0]);
        $this->assertInstanceOf('LmcRbacMvc\Guard\RoutePermissionsGuard', $guards[1]);
        $this->assertInstanceOf('LmcRbacMvc\Guard\ControllerGuard', $guards[2]);
        $this->assertInstanceOf('LmcRbacMvc\Guard\ControllerPermissionsGuard', $guards[3]);
    }

    public function testReturnArrayIfNoConfig()
    {
        $moduleOptions = new ModuleOptions([
            'guards' => []
        ]);

        $serviceManager = new ServiceManager();
        $pluginManager = new GuardPluginManager($serviceManager);

        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', $moduleOptions);

        $factory = new GuardsFactory();
        $guards  = $factory($serviceManager, GuardsFactory::class);

        $this->assertIsArray($guards);

        $this->assertEmpty($guards);
    }
}
