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

use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;
use Lmc\Rbac\Mvc\Guard\AbstractGuard;
use Lmc\Rbac\Mvc\Guard\ControllerGuard;
use Lmc\Rbac\Mvc\Guard\GuardInterface;
use Lmc\Rbac\Mvc\Service\RoleService;
use Lmc\Rbac\Mvc\Role\RecursiveRoleIteratorStrategy;
use Lmc\Rbac\Role\InMemoryRoleProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractGuard::class)]
#[CoversClass(ControllerGuard::class)]
class ControllerGuardTest extends TestCase
{
    public function testAttachToRightEvent()
    {
        $guard = new ControllerGuard($this->getMockBuilder('Lmc\Rbac\Mvc\Service\RoleService')->disableOriginalConstructor()->getMock());

        $eventManager = $this->createMock('Laminas\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->with(ControllerGuard::EVENT_NAME);

        $guard->attach($eventManager);
    }

    public static function rulesConversionProvider(): array
    {
        return [
            // Without actions
            [
                'rules' => [
                    [
                        'controller' => 'MyController',
                        'roles'      => 'role1'
                    ],
                    [
                        'controller' => 'MyController2',
                        'roles'      => ['role2', 'role3']
                    ],
                    new \ArrayIterator([
                        'controller' => 'MyController3',
                        'roles'      => new \ArrayIterator(['role4'])
                    ])
                ],
                'expected' => [
                    'mycontroller'  => [0 => ['role1']],
                    'mycontroller2' => [0 => ['role2', 'role3']],
                    'mycontroller3' => [0 => ['role4']]
                ]
            ],

            // With one action
            [
                'rules' => [
                    [
                        'controller' => 'MyController',
                        'actions'    => 'DELETE',
                        'roles'      => 'role1'
                    ],
                    [
                        'controller' => 'MyController2',
                        'actions'    => ['delete'],
                        'roles'      => 'role2'
                    ],
                    new \ArrayIterator([
                        'controller' => 'MyController3',
                        'actions'    => new \ArrayIterator(['DELETE']),
                        'roles'      => new \ArrayIterator(['role3'])
                    ])
                ],
                'expected' => [
                    'mycontroller'  => [
                        'delete' => ['role1']
                    ],
                    'mycontroller2'  => [
                        'delete' => ['role2']
                    ],
                    'mycontroller3'  => [
                        'delete' => ['role3']
                    ],
                ]
            ],

            // With multiple actions
            [
                'rules' => [
                    [
                        'controller' => 'MyController',
                        'actions'    => ['EDIT', 'delete'],
                        'roles'      => 'role1'
                    ],
                    new \ArrayIterator([
                        'controller' => 'MyController2',
                        'actions'    => new \ArrayIterator(['edit', 'DELETE']),
                        'roles'      => new \ArrayIterator(['role2'])
                    ])
                ],
                'expected' => [
                    'mycontroller'  => [
                        'edit'   => ['role1'],
                        'delete' => ['role1']
                    ],
                    'mycontroller2'  => [
                        'edit'   => ['role2'],
                        'delete' => ['role2']
                    ]
                ]
            ],

            // Test that that if a rule is set globally to the controller, it does not override any
            // action specific rule that may have been specified before
            [
                'rules' => [
                    [
                        'controller' => 'MyController',
                        'actions'    => ['edit'],
                        'roles'      => 'role1'
                    ],
                    [
                        'controller' => 'MyController',
                        'roles'      => 'role2'
                    ]
                ],
                'expected' => [
                    'mycontroller'  => [
                        'edit' => ['role1'],
                        0      => ['role2']
                    ]
                ]
            ]
        ];
    }

    #[DataProvider('rulesConversionProvider')]
    public function testRulesConversions(array $rules, array $expected)
    {
        $roleService     = $this->getMockBuilder('Lmc\Rbac\Mvc\Service\RoleService')->disableOriginalConstructor()->getMock();
        $controllerGuard = new ControllerGuard($roleService, $rules);

        $reflProperty = new \ReflectionProperty($controllerGuard, 'rules');
        $reflProperty->setAccessible(true);

        $this->assertEquals($expected, $reflProperty->getValue($controllerGuard));
    }

    public static function controllerDataProvider(): array
    {
        return [
            // Test simple guard with both policies
            [
                'rules' => [
                    [
                        'controller' => 'BlogController',
                        'roles'      => 'admin'
                    ]
                ],
                'controller'   => 'BlogController',
                'action'       => 'edit',
                'rolesConfig'  => [
                    'admin'
                ],
                'identityRole' => ['admin'],
                'isGranted'    => true,
                'policy'       => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules' => [
                    [
                        'controller' => 'BlogController',
                        'roles'      => 'admin'
                    ]
                ],
                'controller'   => 'BlogController',
                'action'       => 'edit',
                'rolesConfig'  => [
                    'admin'
                ],
                'identityRole' => ['admin'],
                'isGranted'    => true,
                'policy'       => GuardInterface::POLICY_DENY
            ],

            // Test with multiple rules
            [
                'rules' => [
                    [
                        'controller' => 'BlogController',
                        'actions'    => 'read',
                        'roles'      => 'admin'
                    ],
                    [
                        'controller' => 'BlogController',
                        'actions'    => 'edit',
                        'roles'      => 'admin'
                    ]
                ],
                'controller'   => 'BlogController',
                'action'       => 'edit',
                'rolesConfig'  => [
                    'admin'
                ],
                'identityRole' => ['admin'],
                'isGranted'    => true,
                'policy'       => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules' => [
                    [
                        'controller' => 'BlogController',
                        'actions'    => 'read',
                        'roles'      => 'admin'
                    ],
                    [
                        'controller' => 'BlogController',
                        'actions'    => 'edit',
                        'roles'      => 'admin'
                    ]
                ],
                'controller'   => 'BlogController',
                'action'       => 'edit',
                'rolesConfig'  => [
                    'admin'
                ],
                'identityRole' => ['admin'],
                'isGranted'    => true,
                'policy'       => GuardInterface::POLICY_DENY
            ],

            // Assert that policy can deny unspecified rules
            [
                'rules' => [
                    [
                        'controller' => 'BlogController',
                        'roles'      => 'member'
                    ],
                ],
                'controller'   => 'CommentController',
                'action'       => 'edit',
                'rolesConfig'  => [
                    'member'
                ],
                'identityRole' => ['member'],
                'isGranted'    => true,
                'policy'       => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules' => [
                    [
                        'controller' => 'BlogController',
                        'roles'      => 'member'
                    ],
                ],
                'controller'   => 'CommentController',
                'action'       => 'edit',
                'rolesConfig'  => [
                    'member'
                ],
                'identityRole' => ['member'],
                'isGranted'    => false,
                'policy'       => GuardInterface::POLICY_DENY
            ],

            // Test assert policy can deny other actions from controller when only one is specified
            [
                'rules' => [
                    [
                        'controller' => 'BlogController',
                        'actions'    => 'edit',
                        'roles'      => 'member'
                    ],
                ],
                'controller'   => 'BlogController',
                'action'       => 'read',
                'rolesConfig'  => [
                    'member'
                ],
                'identityRole' => ['member'],
                'isGranted'    => true,
                'policy'       => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules' => [
                    [
                        'controller' => 'BlogController',
                        'actions'    => 'edit',
                        'roles'      => 'member'
                    ],
                ],
                'controller'   => 'BlogController',
                'action'       => 'read',
                'rolesConfig'  => [
                    'member'
                ],
                'identityRole' => ['member'],
                'isGranted'    => false,
                'policy'       => GuardInterface::POLICY_DENY
            ],

            // Assert it can uses parent-children relationship
            [
                'rules' => [
                    [
                        'controller' => 'IndexController',
                        'actions'    => 'index',
                        'roles'      => 'guest'
                    ]
                ],
                'controller'   => 'IndexController',
                'action'       => 'index',
                'rolesConfig'  => [
                    'admin' => [
                        'children' => ['guest']
                    ],
                    'guest'
                ],
                'identityRole' => ['admin'],
                'isGranted'    => true,
                'policy'       => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules' => [
                    [
                        'controller' => 'IndexController',
                        'actions'    => 'index',
                        'roles'      => 'guest'
                    ]
                ],
                'controller'   => 'IndexController',
                'action'       => 'index',
                'rolesConfig'  => [
                    'admin' => [
                        'children' => ['guest']
                    ],
                    'guest'
                ],
                'identityRole' => ['admin'],
                'isGranted'    => true,
                'policy'       => GuardInterface::POLICY_DENY
            ],

            // Assert wildcard in roles
            [
                'rules' => [
                    [
                        'controller' => 'IndexController',
                        'roles'      => '*'
                    ]
                ],
                'controller'   => 'IndexController',
                'action'       => 'index',
                'rolesConfig'  => [
                    'admin'
                ],
                'identityRole' => ['admin'],
                'isGranted'    => true,
                'policy'       => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'            => [
                    [
                        'controller' => 'IndexController',
                        'roles'      => '*'
                    ]
                ],
                'controller'   => 'IndexController',
                'action'       => 'index',
                'rolesConfig'  => [
                    'admin'
                ],
                'identityRole' => ['admin'],
                'isGranted'    => true,
                'policy'       => GuardInterface::POLICY_DENY
            ],
        ];
    }

    #[DataProvider('controllerDataProvider')]
    public function testControllerGranted(
        array $rules,
        $controller,
        $action,
        array $rolesConfig,
        $identityRole,
        $isGranted,
        $policy
    ) {
        $event      = new MvcEvent();
        $routeMatch = $this->createRouteMatch([
            'controller' => $controller,
            'action' => $action,
        ]);

        $event->setRouteMatch($routeMatch);

        $identity = $this->createMock('Lmc\Rbac\Identity\IdentityInterface');
        $identity->expects($this->any())->method('getRoles')->willReturn($identityRole);

        $identityProvider = $this->createMock('Lmc\Rbac\Mvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())->method('getIdentity')->willReturn($identity);

        $roleProvider = new InMemoryRoleProvider($rolesConfig);
        $roleService  = new RoleService(
            $identityProvider,
            new \Lmc\Rbac\Service\RoleService($roleProvider, ''),
            new RecursiveRoleIteratorStrategy());

        $controllerGuard = new ControllerGuard($roleService, $rules);
        $controllerGuard->setProtectionPolicy($policy);

        $this->assertEquals($isGranted, $controllerGuard->isGranted($event));
    }

    public function testProperlyFillEventOnAuthorization()
    {
        $event      = new MvcEvent();
        $routeMatch = $this->createRouteMatch([
            'controller' => 'MyController',
            'action' => 'edit',
        ]);

        $application  = $this->getMockBuilder('Laminas\Mvc\Application')->disableOriginalConstructor()->getMock();
        $eventManager = $this->createMock('Laminas\EventManager\EventManagerInterface');

        $application->expects($this->never())->method('getEventManager')->willReturn($eventManager);

        $event->setRouteMatch($routeMatch);
        $event->setApplication($application);

        $identity = $this->createMock('Lmc\Rbac\Identity\IdentityInterface');
        $identity->expects($this->any())->method('getRoles')->willReturn(['member']);

        $identityProvider = $this->createMock('Lmc\Rbac\Mvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())->method('getIdentity')->willReturn($identity);

        $roleProvider = new InMemoryRoleProvider([
            'member'
        ]);

        $roleService = new RoleService(
            $identityProvider,
            new \Lmc\Rbac\Service\RoleService($roleProvider, ''),
            new RecursiveRoleIteratorStrategy());

        $routeGuard = new ControllerGuard($roleService, [[
            'controller' => 'MyController',
            'actions'    => 'edit',
            'roles'      => 'member'
        ]]);

        $routeGuard->onResult($event);

        $this->assertEmpty($event->getError());
        $this->assertNull($event->getParam('exception'));
    }

    public function testProperlySetUnauthorizedAndTriggerEventOnUnauthorized()
    {
        $event      = new MvcEvent();
        $routeMatch = $this->createRouteMatch([
            'controller' => 'MyController',
            'action' => 'delete',
        ]);

        $application  = $this->getMockBuilder('Laminas\Mvc\Application')->disableOriginalConstructor()->getMock();
        $eventManager = $this->createMock('Laminas\EventManager\EventManager');

        $application->expects($this->once())->method('getEventManager')->willReturn($eventManager);

        $eventManager->expects($this->once())->method('triggerEvent')->with($event);

        $routeMatch->setParam('controller', 'MyController');
        $routeMatch->setParam('action', 'delete');

        $event->setRouteMatch($routeMatch);
        $event->setApplication($application);

        $identity = $this->createMock('Lmc\Rbac\Identity\IdentityInterface');
        $identity->expects($this->any())->method('getRoles')->willReturn(['member']);

        $identityProvider = $this->createMock('Lmc\Rbac\Mvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())->method('getIdentity')->willReturn($identity);

        $roleProvider = new InMemoryRoleProvider([
            'member'
        ]);

        $roleService = new RoleService(
            $identityProvider,
            new \Lmc\Rbac\Service\RoleService($roleProvider, ''),
            new RecursiveRoleIteratorStrategy());

        $routeGuard = new ControllerGuard($roleService, [[
            'controller' => 'MyController',
            'actions'    => 'edit',
            'roles'      => 'member'
        ]]);

        $routeGuard->onResult($event);

        $this->assertTrue($event->propagationIsStopped());
        $this->assertEquals(ControllerGuard::GUARD_UNAUTHORIZED, $event->getError());
        $this->assertInstanceOf('Lmc\Rbac\Mvc\Exception\UnauthorizedException', $event->getParam('exception'));
    }

    public function createRouteMatch(array $params = [])
    {
        return new RouteMatch($params);
    }
}
