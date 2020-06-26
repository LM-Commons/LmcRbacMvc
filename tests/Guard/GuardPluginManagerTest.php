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

namespace LmcRbacMvcTest\Guard;

use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Guard\GuardPluginManager;
use LmcRbacMvc\Options\ModuleOptions;

/**
 * @covers \LmcRbacMvc\Guard\GuardPluginManager
 */
class GuardPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function guardProvider()
    {
        return [
            [
                'LmcRbacMvc\Guard\RouteGuard',
                [
                    'admin/*' => 'foo'
                ]
            ],
            [
                'LmcRbacMvc\Guard\RoutePermissionsGuard',
                [
                    'post/delete' => 'post.delete'
                ]
            ],
            [
                'LmcRbacMvc\Guard\ControllerGuard',
                [
                    [
                        'controller' => 'Foo',
                        'actions'    => 'bar',
                        'roles'      => 'baz'
                    ]
                ]
            ],
            [
                'LmcRbacMvc\Guard\ControllerPermissionsGuard',
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

    /**
     * @dataProvider guardProvider
     */
    public function testCanCreateDefaultGuards($type, $options)
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', new ModuleOptions());
        $serviceManager->setService(
            'LmcRbacMvc\Service\RoleService',
            $this->getMock('LmcRbacMvc\Service\RoleService', [], [], '', false)
        );
        $serviceManager->setService(
            'LmcRbacMvc\Service\AuthorizationService',
            $this->getMock('LmcRbacMvc\Service\AuthorizationService', [], [], '', false)
        );

        $pluginManager = new GuardPluginManager($serviceManager);

        $guard = $pluginManager->get($type, $options);

        $this->assertInstanceOf($type, $guard);
    }

    public function testThrowExceptionForInvalidPlugin()
    {
        $this->setExpectedException('LmcRbacMvc\Exception\RuntimeException');

        $pluginManager = new GuardPluginManager(new ServiceManager());
        $pluginManager->get('stdClass');
    }
}
