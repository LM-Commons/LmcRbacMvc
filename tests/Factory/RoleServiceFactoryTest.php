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
use LmcRbacMvc\Factory\RoleServiceFactory;
use LmcRbacMvc\Options\ModuleOptions;
use LmcRbacMvc\Role\RoleProviderPluginManager;

/**
 * @covers \LmcRbacMvc\Factory\RoleServiceFactory
 */
class RoleServiceFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $options = new ModuleOptions([
            'identity_provider'    => 'LmcRbacMvc\Identity\AuthenticationProvider',
            'guest_role'           => 'guest',
            'role_provider'        => [
                'LmcRbacMvc\Role\InMemoryRoleProvider' => [
                    'foo'
                ]
            ]
        ]);

        $traversalStrategy = $this->createMock('LmcRbacMvc\Role\RecursiveRoleIteratorStrategy');
        $roleProvider = $this->createMock('\LmcRbacMvc\Role\RoleProviderInterface');

        $rbac = $this->createMock('Laminas\Permissions\Rbac\Rbac');
//         $rbac->expects($this->once())
//             ->method('getTraversalStrategy')
//             ->will($this->returnValue(
//                 $traversalStrategy
//             ));

        $pluginManager = $this->createMock('\LmcRbacMvc\Role\RoleProviderPluginManager');
        $pluginManager->expects($this->once())
            ->method('get')
            ->with('LmcRbacMvc\Role\InMemoryRoleProvider', ['foo'])
            ->will($this->returnValue(
                $roleProvider
            ));

        $serviceManager = new ServiceManager();
        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', $options);
        $serviceManager->setService('Rbac\Rbac', $rbac);
        $serviceManager->setService('LmcRbacMvc\Role\RoleProviderPluginManager', $pluginManager);
        $serviceManager->setService('LmcRbacMvc\Identity\AuthenticationProvider', $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface'));

        $factory = new RoleServiceFactory();
        $roleService = $factory($serviceManager, 'LmcRbacMvc\Service\RoleService');

        $this->assertInstanceOf('LmcRbacMvc\Service\RoleService', $roleService);
        $this->assertEquals('guest', $roleService->getGuestRole());
        //$this->assertAttributeSame($traversalStrategy, 'traversalStrategy', $roleService);

    }

    /** TODO what are we testing here? */
    public function testIfRoleArrayPointerBeyondArrayEnd()
    {
        $options = new ModuleOptions([
            'identity_provider'    => 'LmcRbacMvc\Identity\AuthenticationProvider',
            'guest_role'           => 'guest',
            'role_provider'        => [
                'LmcRbacMvc\Role\InMemoryRoleProvider' => [
                    'foo'
                ]
            ]
        ]);

        // Simulate if array pointer beyond end of array. E.g after 'while(next($roleProvider)) { //do }'
        $roleProvider = $options->getRoleProvider();
        next($roleProvider);
        $options->setRoleProvider($roleProvider);

        $traversalStrategy = $this->createMock('LmcRbacMvc\Role\RecursiveRoleIteratorStrategy');
        $roleProvider = $this->createMock('\LmcRbacMvc\Role\RoleProviderInterface');

        $rbac = $this->createMock('Laminas\Permissions\Rbac\Rbac');

        $pluginManager = $this->createMock('\LmcRbacMvc\Role\RoleProviderPluginManager');
        $pluginManager->expects($this->once())
            ->method('get')
            ->with('LmcRbacMvc\Role\InMemoryRoleProvider', ['foo'])
            ->will($this->returnValue(
                $roleProvider
            ));

        $serviceManager = new ServiceManager();
        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', $options);
        $serviceManager->setService('Rbac\Rbac', $rbac);
        $serviceManager->setService('LmcRbacMvc\Role\RoleProviderPluginManager', $pluginManager);
        $serviceManager->setService('LmcRbacMvc\Identity\AuthenticationProvider', $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface'));

        /* TODO what are we testing here? */
        $factory = new RoleServiceFactory();
        $factory($serviceManager, '');
    }

    public function testThrowExceptionIfNoRoleProvider()
    {
        $this->expectException(\Laminas\ServiceManager\Exception\ServiceNotCreatedException::class);

        $options = new ModuleOptions([
            'identity_provider' => 'LmcRbacMvc\Identity\AuthenticationProvider',
            'guest_role'        => 'guest',
            'role_provider'     => []
        ]);

        $serviceManager = new ServiceManager();
        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', $options);
        $serviceManager->setService(
            'LmcRbacMvc\Identity\AuthenticationProvider',
            $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface')
        );

        /** TODO what are we testing here since there are no assertion? */
        $factory     = new RoleServiceFactory();
        $factory($serviceManager, '');
    }
}
