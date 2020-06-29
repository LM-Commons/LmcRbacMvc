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
use LmcRbacMvc\Factory\RouteGuardFactory;
use LmcRbacMvc\Guard\GuardInterface;
use LmcRbacMvc\Guard\GuardPluginManager;
use LmcRbacMvc\Options\ModuleOptions;

/**
 * @covers \LmcRbacMvc\Factory\RouteGuardFactory
 */
class RouteGuardFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Dependency on zend-servicemanager v2 removed
     */
    /*
    public function testFactory()
    {
        $serviceManager = new ServiceManager();

        if (method_exists($serviceManager, 'build')) {
            $this->markTestSkipped('this test is only vor zend-servicemanager v2');
        }

        $creationOptions = [
            'route' => 'role'
        ];

        $options = new ModuleOptions([
            'identity_provider' => 'LmcRbacMvc\Identity\AuthenticationProvider',
            'guards'            => [
                'LmcRbacMvc\Guard\RouteGuard' => $creationOptions
            ],
            'protection_policy' => GuardInterface::POLICY_ALLOW,
        ]);

        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', $options);
        $serviceManager->setService(
            'LmcRbacMvc\Service\RoleService',
            $this->getMock('LmcRbacMvc\Service\RoleService', [], [], '', false)
        );

        $pluginManager = new GuardPluginManager($serviceManager);

        $factory    = new RouteGuardFactory();
        $routeGuard = $factory->createService($pluginManager);

        $this->assertInstanceOf('LmcRbacMvc\Guard\RouteGuard', $routeGuard);
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $routeGuard->getProtectionPolicy());
    }
    */

    public function testFactoryV3()
    {
        $serviceManager = new ServiceManager();

        if (!method_exists($serviceManager, 'build')) {
            $this->markTestSkipped('this test is only vor zend-servicemanager v3');
        }

        $creationOptions = [
            'route' => 'role'
        ];

        $options = new ModuleOptions([
            'identity_provider' => 'LmcRbacMvc\Identity\AuthenticationProvider',
            'guards'            => [
                'LmcRbacMvc\Guard\RouteGuard' => $creationOptions
            ],
            'protection_policy' => GuardInterface::POLICY_ALLOW,
        ]);

        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', $options);
        $serviceManager->setService(
            'LmcRbacMvc\Service\RoleService',
            $this->getMockBuilder('LmcRbacMvc\Service\RoleService')->disableOriginalConstructor()->getMock()
        );

        $factory    = new RouteGuardFactory();
        $routeGuard = $factory($serviceManager, 'LmcRbacMvc\Guard\RouteGuard');

        $this->assertInstanceOf('LmcRbacMvc\Guard\RouteGuard', $routeGuard);
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $routeGuard->getProtectionPolicy());
    }
}
