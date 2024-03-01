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

use Laminas\Permissions\Rbac\Rbac;
use Laminas\Permissions\Rbac\Role;
use LmcRbacMvc\Role\RecursiveRoleIteratorStrategy;
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
    public static function grantedProvider(): array
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

        $identity = $this->createMock(\LmcRbacMvc\Identity\IdentityInterface::class);
        $identity->expects($this->once())->method('getRoles')->willReturn((array) $role);

        $identityProvider = $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())
            ->method('getIdentity')
            ->willReturn($identity);

        $roleService            = new RoleService(
            $identityProvider,
            new InMemoryRoleProvider($roleConfig),
            new RecursiveRoleIteratorStrategy()
        );
        
        $assertionPluginManager = new AssertionPluginManager(new ServiceManager(), $assertionPluginConfig);
        $authorizationService   = new AuthorizationService($roleService, $assertionPluginManager);

        $authorizationService->setAssertions($assertions);

        $this->assertEquals($isGranted, $authorizationService->isGranted($permission, $context));
    }

    public function testDoNotCallAssertionIfThePermissionIsNotGranted()
    {
        $role = $this->createMock('Laminas\Permissions\Rbac\RoleInterface');

        $roleService = $this->createMock('LmcRbacMvc\Service\RoleService');
        $roleService->expects($this->once())->method('getIdentityRoles')->willReturn([$role]);

        $assertionPluginManager = $this->createMock('LmcRbacMvc\Assertion\AssertionPluginManager');
        $assertionPluginManager->expects($this->never())->method('get');

        $authorizationService = new AuthorizationService($roleService, $assertionPluginManager);

        $this->assertFalse($authorizationService->isGranted('foo'));
    }

    /** this test is no longer needed because of type checking is now enforced*/
    /** @deprecated  */
    public function testThrowExceptionForInvalidAssertion()
    {
        $roleService = $this->createMock('LmcRbacMvc\Service\RoleService');

        $assertionPluginManager = $this->createMock('LmcRbacMvc\Assertion\AssertionPluginManager');
        $authorizationService   = new AuthorizationService($roleService, $assertionPluginManager);

        $this->expectException(\TypeError::class);

        $authorizationService->setAssertion('foo', new \stdClass());
    }

    public function testDynamicAssertions()
    {
        $role = new Role('user');
        $role->addPermission('foo');

        $roleService = $this->createMock('LmcRbacMvc\Service\RoleService');
        $roleService->expects($this->exactly(2))->method('getIdentityRoles')->willReturn([$role]);

        $assertionPluginManager = $this->createMock('LmcRbacMvc\Assertion\AssertionPluginManager');
        $authorizationService   = new AuthorizationService($roleService, $assertionPluginManager);

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

    /**
     * @covers \LmcRbacMvc\Service\AuthorizationService::hasAssertion
     * @covers \LmcRbacMvc\Service\AuthorizationService::setAssertion
     */
    public function testAssertionMap()
    {
        $roleService            = $this->createMock('LmcRbacMvc\Service\RoleService');
        $assertionPluginManager = $this->createMock('LmcRbacMvc\Assertion\AssertionPluginManager');
        $authorizationService   = new AuthorizationService($roleService, $assertionPluginManager);

        $authorizationService->setAssertions(['foo' => 'bar', 'bar' => 'foo']);

        $this->assertTrue($authorizationService->hasAssertion('foo'));
        $this->assertTrue($authorizationService->hasAssertion('bar'));
        $this->assertFalse($authorizationService->hasAssertion('test'));
    }

    /**
     * @covers \LmcRbacMvc\Service\AuthorizationService::getIdentity
     */
    public function testGetIdentity()
    {
        $identity         = $this->createMock('LmcRbacMvc\Identity\IdentityInterface');
        $roleService      = $this->createMock('LmcRbacMvc\Service\RoleService');
        $assertionManager = $this->createMock('LmcRbacMvc\Assertion\AssertionPluginManager');
        $authorization    = new AuthorizationService($roleService, $assertionManager);

        $roleService->expects($this->once())->method('getIdentity')->willReturn($identity);

        $this->assertSame($authorization->getIdentity(), $identity);
    }
}
