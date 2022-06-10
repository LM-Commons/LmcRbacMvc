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

namespace LmcRbacMvcTest\Collector;

use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Identity\IdentityInterface;
use LmcRbacMvcTest\Asset\MockRoleWithPermissionTraversable;
use Rbac\Role\RoleInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Permissions\Rbac\Role;
use LmcRbacMvc\Collector\RbacCollector;
use LmcRbacMvc\Guard\GuardInterface;
use LmcRbacMvc\Options\ModuleOptions;
use LmcRbacMvc\Role\InMemoryRoleProvider;
use LmcRbacMvc\Service\RoleService;
use Rbac\Traversal\Strategy\RecursiveRoleIteratorStrategy;
use LmcRbacMvcTest\Asset\MockRoleWithPermissionMethod;
use LmcRbacMvcTest\Asset\MockRoleWithPermissionProperty;

/**
 * @covers \LmcRbacMvc\Collector\RbacCollector
 */
class RbacCollectorTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultGetterReturnValues()
    {
        $collector = new RbacCollector();

        $this->assertSame(-100, $collector->getPriority());
        $this->assertSame('lmc_rbac', $collector->getName());
    }

    public function testSerialize()
    {
        $collector  = new RbacCollector();
        $serialized = $collector->serialize();

        $this->assertIsString($serialized);

        $unserialized = unserialize($serialized);

        $this->assertSame([], $unserialized['guards']);
        $this->assertSame([], $unserialized['roles']);
        $this->assertSame([], $unserialized['options']);
    }

    public function testUnserialize()
    {
        $collector    = new RbacCollector();
        $unserialized = [
            'guards'      => ['foo' => 'bar'],
            'roles'       => ['foo' => 'bar'],
            'permissions' => ['foo' => 'bar'],
            'options'     => ['foo' => 'bar']
        ];
        $serialized   = serialize($unserialized);

        $collector->unserialize($serialized);

        $collection = $collector->getCollection();

        $this->assertIsArray($collection);
        $this->assertSame(['foo' => 'bar'], $collection['guards']);
        $this->assertSame(['foo' => 'bar'], $collection['roles']);
        $this->assertSame(['foo' => 'bar'], $collection['options']);
        $this->assertSame(['foo' => 'bar'], $collection['permissions']);
    }

    public function testUnserializeThrowsInvalidArgumentException()
    {
        $this->expectException('LmcRbacMvc\Exception\InvalidArgumentException');
        $collector    = new RbacCollector();
        $unserialized = 'not_an_array';
        $serialized   = serialize($unserialized);

        $collector->unserialize($serialized);
    }


    public function testCollectNothingIfNoApplicationIsSet()
    {
        $mvcEvent  = new MvcEvent();
        $collector = new RbacCollector();

        $this->assertNull($collector->collect($mvcEvent));
    }

    public function testCanCollect()
    {
        $dataToCollect = [
            'module_options' => [
                'guest_role'        => 'guest',
                'protection_policy' => GuardInterface::POLICY_ALLOW,
                'guards'            => [
                    'LmcRbacMvc\Guard\RouteGuard' => [
                        'admin*' => ['*']
                    ],
                    'LmcRbacMvc\Guard\ControllerGuard' => [
                        [
                            'controller' => 'Foo',
                            'roles'      => ['*']
                        ]
                    ]
                ]
            ],
            'role_config' => [
                'member' => [
                    'children'    => ['guest'],
                    'permissions' => ['write', 'delete']
                ],
                'guest' => [
                    'permissions' => ['read']
                ]
            ],
            'identity_role' => 'member'
        ];

        //$serviceManager = $this->getMockBuilder('Laminas\ServiceManager\ServiceLocatorInterface')->getMock();
        $serviceManager = new ServiceManager();
//        $serviceManager = $this->getMock('Laminas\ServiceManager\ServiceLocatorInterface');
        $application = $this->getMockBuilder('Laminas\Mvc\Application')
            ->disableOriginalConstructor()
            ->getMock();

//        $application = $this->getMock('Laminas\Mvc\Application', [], [], '', false);
        $application->expects($this->once())->method('getServiceManager')->will($this->returnValue($serviceManager));

        $mvcEvent = new MvcEvent();
        $mvcEvent->setApplication($application);

        $identity = $this->createMock(IdentityInterface::class);
//        $identity = $this->getMock('LmcRbacMvc\Identity\IdentityInterface');
        $identity->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue($dataToCollect['identity_role']));

//        $identityProvider = $this->getMock('LmcRbacMvc\Identity\IdentityProviderInterface');
        $identityProvider = $this->createMock(\LmcRbacMvc\Identity\IdentityProviderInterface::class);
        $identityProvider->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($identity));

        $roleService = new RoleService($identityProvider, new InMemoryRoleProvider($dataToCollect['role_config']), new RecursiveRoleIteratorStrategy());

        /*
        $serviceManager->expects($this->at(0))
                       ->method('get')
                       ->with('LmcRbacMvc\Service\RoleService')
                       ->will($this->returnValue($roleService));

        $serviceManager->expects($this->at(1))
                       ->method('get')
                       ->with('LmcRbacMvc\Options\ModuleOptions')
                       ->will($this->returnValue(new ModuleOptions($dataToCollect['module_options'])));
*/
        $serviceManager->setService('LmcRbacMvc\Service\RoleService', $roleService);
        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', new ModuleOptions($dataToCollect['module_options']));
        $collector = new RbacCollector();
        $collector->collect($mvcEvent);

        $collector->unserialize($collector->serialize());
        $collection = $collector->getCollection();

        $expectedCollection = [
            'options' => [
                'guest_role'        => 'guest',
                'protection_policy' => 'allow'
            ],
            'guards' => [
                'LmcRbacMvc\Guard\RouteGuard' => [
                    'admin*' => ['*']
                ],
                'LmcRbacMvc\Guard\ControllerGuard' => [
                    [
                        'controller' => 'Foo',
                        'roles'      => ['*']
                    ]
                ]
            ],
            'roles'  => [
                'member' => ['guest']
            ],
            'permissions' => [
                'member' => ['write', 'delete'],
                'guest'  => ['read']
            ]
        ];

        $this->assertEquals($expectedCollection, $collection);
    }

    /**
     * Tests the collectPermissions method when the role has a $permissions Property
     */
    public function testCollectPermissionsProperty()
    {
        $expectedCollection = [
            'guards' => [],
            'roles'  => ['role-with-permission-property'],
            'permissions' => [
                'role-with-permission-property' => ['permission-property-a', 'permission-property-b'],
            ],
            'options' => [
                'guest_role' => 'guest',
                'protection_policy' => GuardInterface::POLICY_ALLOW,
            ],
        ];

        $collection = $this->collectPermissionsPropertyTestBase(new MockRoleWithPermissionProperty());
        $this->assertEquals($expectedCollection, $collection);
    }

    /**
     * Tests the collectPermissions method when the role has a getPermissions() method
     */
    public function testCollectPermissionsMethod()
    {
        $expectedCollection = [
            'guards' => [],
            'roles'  => ['role-with-permission-method'],
            'permissions' => [
                'role-with-permission-method' => ['permission-method-a', 'permission-method-b'],
            ],
            'options' => [
                'guest_role' => 'guest',
                'protection_policy' => GuardInterface::POLICY_ALLOW,
            ],
        ];

        $collection = $this->collectPermissionsPropertyTestBase(new MockRoleWithPermissionMethod());
        $this->assertEquals($expectedCollection, $collection);
    }

    /**
     * Tests the collectPermissions method when the role implements Traversable
     */
    public function testCollectPermissionsTraversable()
    {
        $expectedCollection = [
            'guards' => [],
            'roles'  => ['role-with-permission-traversable'],
            'permissions' => [
                'role-with-permission-traversable' => ['permission-method-a', 'permission-method-b'],
            ],
            'options' => [
                'guest_role' => 'guest',
                'protection_policy' => GuardInterface::POLICY_ALLOW,
            ],
        ];

        $collection = $this->collectPermissionsPropertyTestBase(new MockRoleWithPermissionTraversable());
        $this->assertEquals($expectedCollection, $collection);
    }


    /**
     * Base method for the *collectPermissionProperty tests
     * @param RoleInterface $role
     * @return array|\string[]
     */
    private function collectPermissionsPropertyTestBase(RoleInterface $role)
    {
//        $serviceManager = $this->createMock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
        $serviceManager = new ServiceManager();

        $application = $this->getMockBuilder(\Laminas\Mvc\Application::class)
            ->disableOriginalConstructor()
            ->getMock();
        $application->expects($this->once())->method('getServiceManager')->will($this->returnValue($serviceManager));

        $mvcEvent = new MvcEvent();
        $mvcEvent->setApplication($application);

        $identity = $this->createMock(\LmcRbacMvc\Identity\IdentityInterface::class);
        $identity->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue([$role]));

        $identityProvider = $this->createMock(\LmcRbacMvc\Identity\IdentityProviderInterface::class);
        $identityProvider->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($identity));

        $roleProvider = $this->createMock(\LmcRbacMvc\Role\RoleProviderInterface::class);

        $roleService = new RoleService(
            $identityProvider,
            $roleProvider,
            new RecursiveRoleIteratorStrategy()
        );
        $serviceManager->setService('LmcRbacMvc\Service\RoleService', $roleService);
        /*
                $serviceManager->expects($this->at(0))
                    ->method('get')
                    ->with('LmcRbacMvc\Service\RoleService')
                    ->will($this->returnValue($roleService));
        */
        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', new ModuleOptions());
        /*
                $serviceManager->expects($this->at(1))
                    ->method('get')
                    ->with('LmcRbacMvc\Options\ModuleOptions')
                    ->will($this->returnValue(new ModuleOptions()));
        */
        $collector = new RbacCollector();
        $collector->collect($mvcEvent);

        $collector->unserialize($collector->serialize());
        return $collector->getCollection();
    }
}
