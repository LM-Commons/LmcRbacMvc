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

namespace LaminasRbacTest\Guard;

use Laminas\ServiceManager\ServiceManager;
use LaminasRbac\Guard\GuardPluginManager;
use LaminasRbac\Options\ModuleOptions;

/**
 * @covers \LaminasRbac\Guard\GuardPluginManager
 */
class GuardPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function guardProvider()
    {
        return [
            [
                'LaminasRbac\Guard\RouteGuard',
                [
                    'admin/*' => 'foo'
                ]
            ],
            [
                'LaminasRbac\Guard\RoutePermissionsGuard',
                [
                    'post/delete' => 'post.delete'
                ]
            ],
            [
                'LaminasRbac\Guard\ControllerGuard',
                [
                    [
                        'controller' => 'Foo',
                        'actions'    => 'bar',
                        'roles'      => 'baz'
                    ]
                ]
            ],
            [
                'LaminasRbac\Guard\ControllerPermissionsGuard',
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
        $serviceManager->setService('LaminasRbac\Options\ModuleOptions', new ModuleOptions());
        $serviceManager->setService(
            'LaminasRbac\Service\RoleService',
            $this->getMock('LaminasRbac\Service\RoleService', [], [], '', false)
        );
        $serviceManager->setService(
            'LaminasRbac\Service\AuthorizationService',
            $this->getMock('LaminasRbac\Service\AuthorizationService', [], [], '', false)
        );

        $pluginManager = new GuardPluginManager($serviceManager);

        $guard = $pluginManager->get($type, $options);

        $this->assertInstanceOf($type, $guard);
    }

    public function testThrowExceptionForInvalidPlugin()
    {
        $this->setExpectedException('LaminasRbac\Exception\RuntimeException');

        $pluginManager = new GuardPluginManager(new ServiceManager());
        $pluginManager->get('stdClass');
    }
}
