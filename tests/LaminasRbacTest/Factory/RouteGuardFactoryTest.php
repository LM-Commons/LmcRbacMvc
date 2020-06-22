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

namespace LaminasRbacTest\Factory;

use Laminas\ServiceManager\ServiceManager;
use LaminasRbac\Factory\RouteGuardFactory;
use LaminasRbac\Guard\GuardInterface;
use LaminasRbac\Guard\GuardPluginManager;
use LaminasRbac\Options\ModuleOptions;

/**
 * @covers \LaminasRbac\Factory\RouteGuardFactory
 */
class RouteGuardFactoryTest extends \PHPUnit_Framework_TestCase
{
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
            'identity_provider' => 'LaminasRbac\Identity\AuthenticationProvider',
            'guards'            => [
                'LaminasRbac\Guard\RouteGuard' => $creationOptions
            ],
            'protection_policy' => GuardInterface::POLICY_ALLOW,
        ]);

        $serviceManager->setService('LaminasRbac\Options\ModuleOptions', $options);
        $serviceManager->setService(
            'LaminasRbac\Service\RoleService',
            $this->getMock('LaminasRbac\Service\RoleService', [], [], '', false)
        );

        $pluginManager = new GuardPluginManager($serviceManager);

        $factory    = new RouteGuardFactory();
        $routeGuard = $factory->createService($pluginManager);

        $this->assertInstanceOf('LaminasRbac\Guard\RouteGuard', $routeGuard);
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $routeGuard->getProtectionPolicy());
    }

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
            'identity_provider' => 'LaminasRbac\Identity\AuthenticationProvider',
            'guards'            => [
                'LaminasRbac\Guard\RouteGuard' => $creationOptions
            ],
            'protection_policy' => GuardInterface::POLICY_ALLOW,
        ]);

        $serviceManager->setService('LaminasRbac\Options\ModuleOptions', $options);
        $serviceManager->setService(
            'LaminasRbac\Service\RoleService',
            $this->getMock('LaminasRbac\Service\RoleService', [], [], '', false)
        );

        $factory    = new RouteGuardFactory();
        $routeGuard = $factory($serviceManager, 'LaminasRbac\Guard\RouteGuard');

        $this->assertInstanceOf('LaminasRbac\Guard\RouteGuard', $routeGuard);
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $routeGuard->getProtectionPolicy());
    }
}
