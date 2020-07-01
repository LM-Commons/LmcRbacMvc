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

namespace LmcRbacMvcTest\Service;

use Rbac\Rbac;
use Rbac\Traversal\Strategy\RecursiveRoleIteratorStrategy;
use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Role\InMemoryRoleProvider;
use LmcRbacMvc\Service\AuthorizationService;
use LmcRbacMvc\Service\RoleService;
use LmcRbacMvcTest\Asset\SimpleAssertion;
use LmcRbacMvc\Assertion\AssertionPluginManager;

/**
 * @covers \LmcRbacMvc\Service\AuthorizationService
 */
class AuthorizationServiceTest extends \PHPUnit\Framework\TestCase
{
    public function grantedProvider()
    {
        return [
            // Simple is granted
            [
                'guest',
                'read',
                null,
                true
            ],

            // Simple is allowed from parent
            [
                'member',
                'read',
                null,
                true
            ],

            // Simple is refused
            [
                'guest',
                'write',
                null,
                false
            ],

            // Simple is refused from parent
            [
                'guest',
                'delete',
                null,
                false
            ],

            // Simple is refused from assertion map
            [
                'admin',
                'delete',
                false,
                false,
                [
                    'delete' => 'LmcRbacMvcTest\Asset\SimpleAssertion'
                ]
            ],

            // Simple is accepted from assertion map
            [
                'admin',
                'delete',
                true,
                true,
                [
                    'delete' => 'LmcRbacMvcTest\Asset\SimpleAssertion'
                ]
            ],

            // Simple is refused from no role
            [
                [],
                'read',
                null,
                false
            ],
        ];
    }

    /**
     * @dataProvider grantedProvider
     */
    public function testGranted($role, $permission, $context, $isGranted, $assertions = [])
    {
        $roleConfig = [
            'admin'  => [
                'children'    => ['member'],
                'permissions' => ['delete']
            ],
            'member' => [
                'children'    => ['guest'],
                'permissions' => ['write']
            ],
            'guest'  => [
                'permissions' => ['read']
            ]
        ];

        $assertionPluginConfig = [
            'invokables' => [
                'LmcRbacMvcTest\Asset\SimpleAssertion' => 'LmcRbacMvcTest\Asset\SimpleAssertion'
            ]
        ];

        $identity = $this->createMock('LmcRbacMvc\Identity\IdentityInterface');
        $identity->expects($this->once())->method('getRoles')->will($this->returnValue((array) $role));

        $identityProvider = $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($identity));

        $rbac                   = new Rbac(new RecursiveRoleIteratorStrategy());
        $roleService            = new RoleService(
            $identityProvider,
            new InMemoryRoleProvider($roleConfig),
            $rbac->getTraversalStrategy()
        );
        $assertionPluginManager = new AssertionPluginManager(new ServiceManager(), $assertionPluginConfig);
        $authorizationService   = new AuthorizationService($rbac, $roleService, $assertionPluginManager);

        $authorizationService->setAssertions($assertions);

        $this->assertEquals($isGranted, $authorizationService->isGranted($permission, $context));
    }

    public function testDoNotCallAssertionIfThePermissionIsNotGranted()
    {
        $role = $this->createMock('Rbac\Role\RoleInterface');
        $rbac = $this->createMock('Rbac\Rbac');

        $roleService = $this->createMock('LmcRbacMvc\Service\RoleService');
        $roleService->expects($this->once())->method('getIdentityRoles')->will($this->returnValue([$role]));

        $assertionPluginManager = $this->createMock('LmcRbacMvc\Assertion\AssertionPluginManager');
        $assertionPluginManager->expects($this->never())->method('get');

        $authorizationService = new AuthorizationService($rbac, $roleService, $assertionPluginManager);

        $this->assertFalse($authorizationService->isGranted('foo'));
    }

    public function testThrowExceptionForInvalidAssertion()
    {
        $role = $this->createMock('Rbac\Role\RoleInterface');
        $rbac = $this->createMock('Rbac\Rbac');

        $rbac->expects($this->once())->method('isGranted')->will($this->returnValue(true));

        $roleService = $this->createMock('LmcRbacMvc\Service\RoleService');
        $roleService->expects($this->once())->method('getIdentityRoles')->will($this->returnValue([$role]));

        $assertionPluginManager = $this->createMock('LmcRbacMvc\Assertion\AssertionPluginManager');
        $authorizationService   = new AuthorizationService($rbac, $roleService, $assertionPluginManager);

        $this->expectException('LmcRbacMvc\Exception\InvalidArgumentException');

        $authorizationService->setAssertion('foo', new \stdClass());
        $authorizationService->isGranted('foo');
    }

    public function testDynamicAssertions()
    {
        $role = $this->createMock('Rbac\Role\RoleInterface');
        $rbac = $this->createMock('Rbac\Rbac');

        $rbac->expects($this->exactly(2))->method('isGranted')->will($this->returnValue(true));

        $roleService = $this->createMock('LmcRbacMvc\Service\RoleService');
        $roleService->expects($this->exactly(2))->method('getIdentityRoles')->will($this->returnValue([$role]));

        $assertionPluginManager = $this->createMock('LmcRbacMvc\Assertion\AssertionPluginManager');
        $authorizationService   = new AuthorizationService($rbac, $roleService, $assertionPluginManager);

        // Using a callable
        $called = false;

        $authorizationService->setAssertion(
            'foo',
            function (AuthorizationService $injectedService) use ($authorizationService, &$called) {
                $this->assertSame($injectedService, $authorizationService);

                $called = true;

                return false;
            }
        );

        $this->assertFalse($authorizationService->isGranted('foo'));
        $this->assertTrue($called);

        // Using an assertion object
        $assertion = new SimpleAssertion();
        $authorizationService->setAssertion('foo', $assertion);

        $this->assertFalse($authorizationService->isGranted('foo', false));
        $this->assertTrue($assertion->getCalled());
    }

    public function testAssertionMap()
    {
        $rbac                   = $this->createMock('Rbac\Rbac');
        $roleService            = $this->createMock('LmcRbacMvc\Service\RoleService');
        $assertionPluginManager = $this->createMock('LmcRbacMvc\Assertion\AssertionPluginManager');
        $authorizationService   = new AuthorizationService($rbac, $roleService, $assertionPluginManager);

        $authorizationService->setAssertions(['foo' => 'bar', 'bar' => 'foo']);

        $this->assertTrue($authorizationService->hasAssertion('foo'));
        $this->assertTrue($authorizationService->hasAssertion('bar'));

        $authorizationService->setAssertion('bar', null);

        $this->assertFalse($authorizationService->hasAssertion('bar'));
    }

    /**
     * @covers LmcRbacMvc\Service\AuthorizationService::getIdentity
     */
    public function testGetIdentity()
    {
        $rbac             = $this->createMock('Rbac\Rbac');
        $identity         = $this->createMock('LmcRbacMvc\Identity\IdentityInterface');
        $roleService      = $this->createMock('LmcRbacMvc\Service\RoleService');
        $assertionManager = $this->createMock('LmcRbacMvc\Assertion\AssertionPluginManager');
        $authorization    = new AuthorizationService($rbac, $roleService, $assertionManager);

        $roleService->expects($this->once())->method('getIdentity')->will($this->returnValue($identity));

        $this->assertSame($authorization->getIdentity(), $identity);
    }
}
