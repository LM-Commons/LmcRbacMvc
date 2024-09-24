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
use Lmc\Rbac\Mvc\Guard\ControllerPermissionsGuard;
use Lmc\Rbac\Mvc\Guard\GuardInterface;
use Lmc\Rbac\Role\InMemoryRoleProvider;
use Lmc\Rbac\Mvc\Service\RoleService;
use Lmc\Rbac\Mvc\Role\RecursiveRoleIteratorStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractGuard::class)]
#[CoversClass(ControllerPermissionsGuard::class)]
class ControllerPermissionsGuardTest extends TestCase
{
    private function getMockAuthorizationService()
    {
        $authorizationService = $this->createMock('Lmc\Rbac\Mvc\Service\AuthorizationService');

        return $authorizationService;
    }

    public function testAttachToRightEvent()
    {
        $guard = new ControllerPermissionsGuard($this->getMockAuthorizationService());

        $eventManager = $this->createMock('Laminas\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())->method('attach')->with(ControllerGuard::EVENT_NAME);

        $guard->attach($eventManager);
    }

    public static function rulesConversionProvider(): array
    {
        return [
            // Without actions
            [
                'rules'    => [
                    [
                        'controller'  => 'MyController',
                        'permissions' => 'post.manage'
                    ],
                    [
                        'controller'  => 'MyController2',
                        'permissions' => ['post.update', 'post.delete']
                    ],
                    new \ArrayIterator([
                        'controller'  => 'MyController3',
                        'permissions' => new \ArrayIterator(['post.manage'])
                    ])
                ],
                'expected' => [
                    'mycontroller'  => [0 => ['post.manage']],
                    'mycontroller2' => [0 => ['post.update', 'post.delete']],
                    'mycontroller3' => [0 => ['post.manage']]
                ]
            ],
            // With one action
            [
                'rules'    => [
                    [
                        'controller'  => 'MyController',
                        'actions'     => 'DELETE',
                        'permissions' => 'permission1'
                    ],
                    [
                        'controller'  => 'MyController2',
                        'actions'     => ['delete'],
                        'permissions' => 'permission2'
                    ],
                    new \ArrayIterator([
                        'controller'  => 'MyController3',
                        'actions'     => new \ArrayIterator(['DELETE']),
                        'permissions' => new \ArrayIterator(['permission3'])
                    ])
                ],
                'expected' => [
                    'mycontroller'  => [
                        'delete' => ['permission1']
                    ],
                    'mycontroller2' => [
                        'delete' => ['permission2']
                    ],
                    'mycontroller3' => [
                        'delete' => ['permission3']
                    ],
                ]
            ],
            // With multiple actions
            [
                'rules'    => [
                    [
                        'controller'  => 'MyController',
                        'actions'     => ['EDIT', 'delete'],
                        'permissions' => 'permission1'
                    ],
                    new \ArrayIterator([
                        'controller'  => 'MyController2',
                        'actions'     => new \ArrayIterator(['edit', 'DELETE']),
                        'permissions' => new \ArrayIterator(['permission2'])
                    ])
                ],
                'expected' => [
                    'mycontroller'  => [
                        'edit'   => ['permission1'],
                        'delete' => ['permission1']
                    ],
                    'mycontroller2' => [
                        'edit'   => ['permission2'],
                        'delete' => ['permission2']
                    ]
                ]
            ],
            // Test that that if a rule is set globally to the controller, it does not override any
            // action specific rule that may have been specified before
            [
                'rules'    => [
                    [
                        'controller'  => 'MyController',
                        'actions'     => ['edit'],
                        'permissions' => 'permission1'
                    ],
                    [
                        'controller'  => 'MyController',
                        'permissions' => 'permission2'
                    ]
                ],
                'expected' => [
                    'mycontroller' => [
                        'edit' => ['permission1'],
                        0      => ['permission2']
                    ]
                ]
            ]
        ];
    }

    #[DataProvider('rulesConversionProvider')]
    public function testRulesConversions(array $rules, array $expected)
    {
        $controllerGuard = new ControllerPermissionsGuard($this->getMockAuthorizationService(), $rules);

        $reflProperty = new \ReflectionProperty($controllerGuard, 'rules');

        $this->assertEquals($expected, $reflProperty->getValue($controllerGuard));
    }

    public static function controllerDataProvider(): array
    {
        return [
            // Test simple guard with both policies
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'permissions' => 'post.edit'
                    ]
                ],
                'controller'          => 'BlogController',
                'action'              => 'edit',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'permissions' => 'post.edit'
                    ]
                ],
                'controller'          => 'BlogController',
                'action'              => 'edit',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Test with multiple rules
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'read',
                        'permissions' => 'post.read'
                    ],
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'edit',
                        'permissions' => 'post.edit'
                    ]
                ],
                'controller'          => 'BlogController',
                'action'              => 'edit',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'read',
                        'permissions' => 'post.read'
                    ],
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'edit',
                        'permissions' => 'post.edit'
                    ]
                ],
                'controller'          => 'BlogController',
                'action'              => 'edit',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Test with multiple permissions. All must be authorized.
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'admin',
                        'permissions' => ['post.update', 'post.delete'],
                    ],
                ],
                'controller'          => 'BlogController',
                'action'              => 'admin',
                'identityPermissions' => [
                    ['post.update', null, true],
                    ['post.delete', null, true],
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'admin',
                        'permissions' => ['post.update', 'post.delete'],
                    ],
                ],
                'controller'          => 'BlogController',
                'action'              => 'admin',
                'identityPermissions' => [
                    ['post.update', null, false],
                    ['post.delete', null, true],
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'admin',
                        'permissions' => ['post.update', 'post.delete'],
                    ],
                ],
                'controller'          => 'BlogController',
                'action'              => 'admin',
                'identityPermissions' => [
                    ['post.update', null, true],
                    ['post.delete', null, false],
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert that policy can deny unspecified rules
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'permissions' => 'post.edit'
                    ],
                ],
                'controller'          => 'CommentController',
                'action'              => 'edit',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'permissions' => 'post.edit'
                    ],
                ],
                'controller'          => 'CommentController',
                'action'              => 'edit',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Test assert policy can deny other actions from controller when only one is specified
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'edit',
                        'permissions' => 'post.edit'
                    ],
                ],
                'controller'          => 'BlogController',
                'action'              => 'read',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'edit',
                        'permissions' => 'post.edit'
                    ],
                ],
                'controller'          => 'BlogController',
                'action'              => 'read',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert wildcard in permissions
            [
                'rules'               => [
                    [
                        'controller'  => 'IndexController',
                        'permissions' => '*'
                    ]
                ],
                'controller'          => 'IndexController',
                'action'              => 'index',
                'identityPermissions' => [['post.edit', null, false]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'IndexController',
                        'permissions' => '*'
                    ]
                ],
                'controller'          => 'IndexController',
                'action'              => 'index',
                'identityPermissions' => [['post.edit', null, false]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
        ];
    }

    #[DataProvider('controllerDataProvider')]
    public function testControllerGranted(
        array $rules,
        $controller,
        $action,
        $identityPermissions,
        $isGranted,
        $policy
    ) {
        $routeMatch = $this->createRouteMatch([
            'controller' => $controller,
            'action' => $action,
        ]);

        $authorizationService = $this->getMockAuthorizationService();
        $authorizationService->expects($this->any())->method('isGranted')->willReturnMap($identityPermissions);

        $controllerGuard = new ControllerPermissionsGuard($authorizationService, $rules);
        $controllerGuard->setProtectionPolicy($policy);

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatch);

        $this->assertEquals($isGranted, $controllerGuard->isGranted($event));
    }

    public function testProperlyFillEventOnAuthorization()
    {
        $event      = new MvcEvent();
        $routeMatch = $this->createRouteMatch([
            'controller' => 'MyController',
            'action' => 'edit',
        ]);

        $application = $this->createMock('Laminas\Mvc\Application');
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

        $routeGuard = new ControllerGuard($roleService, [
            [
                'controller' => 'MyController',
                'actions'    => 'edit',
                'roles'      => 'member'
            ]
        ]);

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

        $application  = $this->createMock('Laminas\Mvc\Application');
        $eventManager = $this->createMock('Laminas\EventManager\EventManager');

        $application->expects($this->once())->method('getEventManager')->willReturn($eventManager);

        $eventManager->expects($this->once())->method('triggerEvent')->with($event);

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

        $routeGuard = new ControllerGuard($roleService, [
            [
                'controller' => 'MyController',
                'actions'    => 'edit',
                'roles'      => 'member'
            ]
        ]);

        $routeGuard->onResult($event);

        $this->assertTrue($event->propagationIsStopped());
        $this->assertEquals(ControllerGuard::GUARD_UNAUTHORIZED, $event->getError());
        $this->assertInstanceOf('Lmc\Rbac\Mvc\Exception\UnauthorizedException', $event->getParam('exception'));
    }

    public function createRouteMatch(array $params = [])
    {
        return new RouteMatch($params);
        /*
        $class = class_exists(V2RouteMatch::class) ? V2RouteMatch::class : RouteMatch::class;
        return new $class($params);
        */
    }
}
