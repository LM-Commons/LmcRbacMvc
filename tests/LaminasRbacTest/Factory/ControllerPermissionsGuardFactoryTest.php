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
use LaminasRbac\Factory\ControllerPermissionsGuardFactory;
use LaminasRbac\Guard\GuardInterface;
use LaminasRbac\Guard\GuardPluginManager;
use LaminasRbac\Options\ModuleOptions;

/**
 * @covers \LaminasRbac\Factory\ControllerPermissionsGuardFactory
 */
class ControllerPermissionsGuardFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager();

        if (method_exists($serviceManager, 'build')) {
            $this->markTestSkipped('this test is only vor zend-servicemanager v2');
        }

        $creationOptions = [
            'route' => 'permission'
        ];

        $options = new ModuleOptions([
            'identity_provider' => 'LaminasRbac\Identity\AuthenticationProvider',
            'guards'            => [
                'LaminasRbac\Guard\ControllerPermissionsGuard' => $creationOptions
            ],
            'protection_policy' => GuardInterface::POLICY_ALLOW,
        ]);

        $serviceManager->setService('LaminasRbac\Options\ModuleOptions', $options);
        $serviceManager->setService(
            'LaminasRbac\Service\AuthorizationService',
            $this->getMock('LaminasRbac\Service\AuthorizationService', [], [], '', false)
        );

        $pluginManager = new GuardPluginManager($serviceManager);

        $factory    = new ControllerPermissionsGuardFactory();
        $guard = $factory->createService($pluginManager);

        $this->assertInstanceOf('LaminasRbac\Guard\ControllerPermissionsGuard', $guard);
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $guard->getProtectionPolicy());
    }

    public function testFactoryV3()
    {
        $serviceManager = new ServiceManager();

        if (!method_exists($serviceManager, 'build')) {
            $this->markTestSkipped('this test is only vor zend-servicemanager v3');
        }

        $creationOptions = [
            'route' => 'permission'
        ];

        $options = new ModuleOptions([
            'identity_provider' => 'LaminasRbac\Identity\AuthenticationProvider',
            'guards'            => [
                'LaminasRbac\Guard\ControllerPermissionsGuard' => $creationOptions
            ],
            'protection_policy' => GuardInterface::POLICY_ALLOW,
        ]);

        $serviceManager->setService('LaminasRbac\Options\ModuleOptions', $options);
        $serviceManager->setService(
            'LaminasRbac\Service\AuthorizationService',
            $this->getMock('LaminasRbac\Service\AuthorizationService', [], [], '', false)
        );

        $factory    = new ControllerPermissionsGuardFactory();
        $guard = $factory($serviceManager, GuardPluginManager::class);

        $this->assertInstanceOf('LaminasRbac\Guard\ControllerPermissionsGuard', $guard);
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $guard->getProtectionPolicy());
    }
}
