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

use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;
use LmcRbacMvc\Guard\ControllerGuard;
use LmcRbacMvc\Guard\GuardInterface;
use LmcRbacMvc\Guard\RouteGuard;
use LmcRbacMvc\Guard\RoutePermissionsGuard;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @covers \LmcRbacMvc\Guard\AbstractGuard
 * @covers \LmcRbacMvc\Guard\RoutePermissionsGuard
 */
class RoutePermissionsGuardTest extends \PHPUnit\Framework\TestCase
{
    public function testAttachToRightEvent()
    {
        $eventManager = $this->createMock('Laminas\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())->method('attach')->with(RouteGuard::EVENT_NAME);

        $guard = new RoutePermissionsGuard($this->createMock('LmcRbacMvc\Service\AuthorizationService'));
        $guard->attach($eventManager);
    }

    /**
     * We want to ensure an order for guards
     */
    public function testAssertRoutePermissionsGuardPriority()
    {
        $this->assertLessThan(RouteGuard::EVENT_PRIORITY, RoutePermissionsGuard::EVENT_PRIORITY);
        $this->assertGreaterThan(ControllerGuard::EVENT_PRIORITY, RoutePermissionsGuard::EVENT_PRIORITY);
    }

    public static function rulesConversionProvider(): array
    {
        return [
            // Simple string to array conversion
            [
                'rules'    => [
                    'route' => 'permission1'
                ],
                'expected' => [
                    'route' => ['permission1']
                ]
            ],
            // Array to array
            [
                'rules'    => [
                    'route' => ['permission1', 'permission2']
                ],
                'expected' => [
                    'route' => ['permission1', 'permission2']
                ]
            ],
            // Traversable to array
            [
                'rules'    => [
                    'route' => new \ArrayIterator(['permission1', 'permission2'])
                ],
                'expected' => [
                    'route' => ['permission1', 'permission2']
                ]
            ],
            // Block a route for everyone
            [
                'rules'    => [
                    'route'
                ],
                'expected' => [
                    'route' => []
                ]
            ],
        ];
    }

    #[DataProvider('rulesConversionProvider')]
    public function testRulesConversions(array $rules, array $expected)
    {
        $roleService  = $this->createMock('LmcRbacMvc\Service\AuthorizationService');
        $routeGuard   = new RoutePermissionsGuard($roleService, $rules);
        $reflexionProperty = new \ReflectionProperty($routeGuard, 'rules');
        $this->assertEquals($expected, $reflexionProperty->getValue($routeGuard));
    }

    public static function routeDataProvider(): array
    {
        return [
            // Assert basic one-to-one mapping with both policies
            [
                'rules'               => ['adminRoute' => 'post.edit'],
                'matchedRouteName'    => 'adminRoute',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => ['adminRoute' => 'post.edit'],
                'matchedRouteName'    => 'adminRoute',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert that policy changes result for non-specified route guards
            [
                'rules'               => ['route' => 'post.edit'],
                'matchedRouteName'    => 'anotherRoute',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => ['route' => 'post.edit'],
                'matchedRouteName'    => 'anotherRoute',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert that composed route work for both policies
            [
                'rules'               => ['admin/dashboard' => 'post.edit'],
                'matchedRouteName'    => 'admin/dashboard',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => ['admin/dashboard' => 'post.edit'],
                'matchedRouteName'    => 'admin/dashboard',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert that wildcard route work for both policies
            [
                'rules'               => ['admin/*' => 'post.edit'],
                'matchedRouteName'    => 'admin/dashboard',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => ['admin/*' => 'post.edit'],
                'matchedRouteName'    => 'admin/dashboard',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert that wildcard route does match (or not depending on the policy) if rules is after matched route name
            [
                'rules'               => ['fooBar/*' => 'post.edit'],
                'matchedRouteName'    => 'admin/fooBar/baz',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => ['fooBar/*' => 'post.edit'],
                'matchedRouteName'    => 'admin/fooBar/baz',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert that it can grant access with multiple rules
            [
                'rules'               => [
                    'route1' => 'post.edit',
                    'route2' => 'post.edit'
                ],
                'matchedRouteName'    => 'route1',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    'route1' => 'post.edit',
                    'route2' => 'post.edit'
                ],
                'matchedRouteName'    => 'route2',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    'route1' => 'post.edit',
                    'route2' => 'post.edit'
                ],
                'matchedRouteName'    => 'route1',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            [
                'rules'               => [
                    'route1' => 'post.edit',
                    'route2' => 'post.edit'
                ],
                'matchedRouteName'    => 'route2',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert that it can grant/deny access with multiple rules based on the policy
            [
                'rules'               => [
                    'route1' => 'post.edit',
                    'route2' => 'post.edit'
                ],
                'matchedRouteName'    => 'route3',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    'route1' => 'post.edit',
                    'route2' => 'post.edit'
                ],
                'matchedRouteName'    => 'route3',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert it can deny access if the only permission does not have access
            [
                'rules'               => ['route' => 'post.edit'],
                'matchedRouteName'    => 'route',
                'identityPermissions' => [
                    ['post.edit', null, false],
                    ['post.read', null, true]
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => ['route' => 'post.edit'],
                'matchedRouteName'    => 'route',
                'identityPermissions' => [
                    ['post.edit', null, false],
                    ['post.read', null, true]
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert it can deny access if one of the permission does not have access
            [
                'rules'               => ['route' => ['post.edit', 'post.read']],
                'matchedRouteName'    => 'route',
                'identityPermissions' => [
                    ['post.edit', null, true],
                    ['post.read', null, true]
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => ['route' => ['post.edit', 'post.read']],
                'matchedRouteName'    => 'route',
                'identityPermissions' => [
                    ['post.edit', null, true],
                    ['post.read', null, false]
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => ['route' => ['post.edit', 'post.read']],
                'matchedRouteName'    => 'route',
                'identityPermissions' => [
                    ['post.edit', null, false],
                    ['post.read', null, true]
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            // Assert wildcard in permission
            [
                'rules'               => ['home' => '*'],
                'matchedRouteName'    => 'home',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => ['home' => '*'],
                'matchedRouteName'    => 'home',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert wildcard wins all
            [
                'rules'               => ['home' => ['*', 'post.edit']],
                'matchedRouteName'    => 'home',
                'identityPermissions' => [['post.edit', null, false]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => ['home' => ['*', 'post.edit']],
                'matchedRouteName'    => 'home',
                'identityPermissions' => [['post.edit', null, false]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            [
                'rules'               => ['route' => [
                    'permissions' => ['post.edit', 'post.read'],
                    'condition'   => GuardInterface::CONDITION_OR
                ]],
                'matchedRouteName'    => 'route',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            [
                'rules'               => ['route' => [
                    'permissions' => ['post.edit', 'post.read'],
                    'condition'   => GuardInterface::CONDITION_OR
                ]],
                'matchedRouteName'    => 'route',
                'identityPermissions' => [['post.edit', null, false], ['post.read', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            [
                'rules'               => ['route' => [
                    'permissions' => ['post.edit', 'post.read'],
                    'condition'   => GuardInterface::CONDITION_AND
                ]],
                'matchedRouteName'    => 'route',
                'identityPermissions' => [['post.edit', null, true], ['post.read', null, false]],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            [
                'rules'               => ['route' => [
                    'permissions' => ['post.edit', 'post.read'],
                    'condition'   => GuardInterface::CONDITION_AND
                ]],
                'matchedRouteName'    => 'route',
                'identityPermissions' => [['post.edit', null, true], ['post.read', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ]
        ];
    }

    #[DataProvider('routeDataProvider')]
    public function testRoutePermissionGranted(
        array $rules,
        $matchedRouteName,
        array $identityPermissions,
        $isGranted,
        $protectionPolicy
    ) {
        $routeMatch = $this->createRouteMatch();
        $routeMatch->setMatchedRouteName($matchedRouteName);

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatch);

        $authorizationService = $this->createMock('LmcRbacMvc\Service\AuthorizationServiceInterface');
        $authorizationService->expects($this->any())->method('isGranted')->willReturnMap($identityPermissions);

        $routeGuard = new RoutePermissionsGuard($authorizationService, $rules);
        $routeGuard->setProtectionPolicy($protectionPolicy);

        $this->assertEquals($isGranted, $routeGuard->isGranted($event));
    }

    public function testProperlyFillEventOnAuthorization()
    {
        $eventManager = $this->createMock('Laminas\EventManager\EventManagerInterface');

        $application = $this->createMock('Laminas\Mvc\Application');
        $application->expects($this->never())->method('getEventManager')->willReturn($eventManager);

        $routeMatch = $this->createRouteMatch();
        $routeMatch->setMatchedRouteName('adminRoute');

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatch);
        $event->setApplication($application);

        $authorizationService = $this->createMock('LmcRbacMvc\Service\AuthorizationServiceInterface');
        $authorizationService->expects($this->once())->method('isGranted')->with('post.edit')->willReturn(true);

        $routeGuard = new RoutePermissionsGuard($authorizationService, [
            'adminRoute' => 'post.edit'
        ]);
        $routeGuard->onResult($event);

        $this->assertEmpty($event->getError());
        $this->assertNull($event->getParam('exception'));
    }

    public function testProperlySetUnauthorizedAndTriggerEventOnUnauthorized()
    {
        $eventManager = $this->createMock('Laminas\EventManager\EventManager');

        $application = $this->createMock('Laminas\Mvc\Application');
        $application->expects($this->once())->method('getEventManager')->willReturn($eventManager);

        $routeMatch = $this->createRouteMatch();
        $routeMatch->setMatchedRouteName('adminRoute');

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatch);
        $event->setApplication($application);

        $eventManager->expects($this->once())->method('triggerEvent')->with($event);

        $authorizationService = $this->createMock('LmcRbacMvc\Service\AuthorizationServiceInterface');
        $authorizationService->expects($this->once())->method('isGranted')->with('post.edit')
            ->willReturn(false);

        $routeGuard = new RoutePermissionsGuard($authorizationService, [
            'adminRoute' => 'post.edit'
        ]);
        $routeGuard->onResult($event);

        $this->assertTrue($event->propagationIsStopped());
        $this->assertEquals(RouteGuard::GUARD_UNAUTHORIZED, $event->getError());
        $this->assertInstanceOf('LmcRbacMvc\Exception\UnauthorizedException', $event->getParam('exception'));
    }

    public function createRouteMatch(array $params = []): RouteMatch
    {
        return new RouteMatch($params);
    }
}
