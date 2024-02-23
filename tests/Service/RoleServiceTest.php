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

use LmcRbacMvc\Identity\IdentityInterface;
use LmcRbacMvc\Identity\IdentityProviderInterface;
use LmcRbacMvc\Role\InMemoryRoleProvider;
use LmcRbacMvc\Role\RoleProviderInterface;
use LmcRbacMvc\Service\RoleService;
use PHPUnit\Framework\TestCase;
use Laminas\Permissions\Rbac\RoleInterface;
use LmcRbacMvc\Role\RecursiveRoleIteratorStrategy;
use LmcRbacMvc\Role\TraversalStrategyInterface;

/**
 * @covers \LmcRbacMvc\Service\RoleService
 */
class RoleServiceTest extends TestCase
{
    public function roleProvider()
    {
        return [
            // No identity role
            [
                'rolesConfig' => [],
                'identityRoles' => [],
                'rolesToCheck' => [
                    'member'
                ],
                'doesMatch' => false
            ],

            // Simple
            [
                'rolesConfig' => [
                    'member' => [
                        'children' => ['guest']
                    ],
                    'guest'
                ],
                'identityRoles' => [
                    'guest'
                ],
                'rolesToCheck' => [
                    'member'
                ],
                'doesMatch' => false
            ],
            [
                'rolesConfig' => [
                    'member' => [
                        'children' => ['guest']
                    ],
                    'guest'
                ],
                'identityRoles' => [
                    'member'
                ],
                'rolesToCheck' => [
                    'member'
                ],
                'doesMatch' => true
            ],

            // Complex role inheritance
            [
                'rolesConfig' => [
                    'admin' => [
                        'children' => ['moderator']
                    ],
                    'moderator' => [
                        'children' => ['member']
                    ],
                    'member' => [
                        'children' => ['guest']
                    ],
                    'guest'
                ],
                'identityRoles' => [
                    'member',
                    'moderator'
                ],
                'rolesToCheck' => [
                    'admin'
                ],
                'doesMatch' => false
            ],
            [
                'rolesConfig' => [
                    'admin' => [
                        'children' => ['moderator']
                    ],
                    'moderator' => [
                        'children' => ['member']
                    ],
                    'member' => [
                        'children' => ['guest']
                    ],
                    'guest'
                ],
                'identityRoles' => [
                    'member',
                    'admin'
                ],
                'rolesToCheck' => [
                    'moderator'
                ],
                'doesMatch' => true
            ],

            // Complex role inheritance and multiple check
            [
                'rolesConfig' => [
                    'sysadmin' => [
                        'children' => ['siteadmin', 'admin']
                    ],
                    'siteadmin',
                    'admin' => [
                        'children' => ['moderator']
                    ],
                    'moderator' => [
                        'children' => ['member']
                    ],
                    'member' => [
                        'children' => ['guest']
                    ],
                    'guest'
                ],
                'identityRoles' => [
                    'member',
                    'moderator'
                ],
                'rolesToCheck' => [
                    'admin',
                    'sysadmin'
                ],
                'doesMatch' => false
            ],
            [
                'rolesConfig' => [
                    'sysadmin' => [
                        'children' => ['siteadmin', 'admin']
                    ],
                    'siteadmin',
                    'admin' => [
                        'children' => ['moderator']
                    ],
                    'moderator' => [
                        'children' => ['member']
                    ],
                    'member' => [
                        'children' => ['guest']
                    ],
                    'guest'
                ],
                'identityRoles' => [
                    'moderator',
                    'admin'
                ],
                'rolesToCheck' => [
                    'sysadmin',
                    'siteadmin',
                    'member'
                ],
                'doesMatch' => true
            ]
        ];
    }

    /**
     * @dataProvider roleProvider
     */
    public function testMatchIdentityRoles(array $rolesConfig, array $identityRoles, array $rolesToCheck, $doesMatch)
    {
        $identity = $this->createMock('LmcRbacMvc\Identity\IdentityInterface');
        $identity->expects($this->once())->method('getRoles')->will($this->returnValue($identityRoles));

        $identityProvider = $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())
                         ->method('getIdentity')
                         ->will($this->returnValue($identity));

        $roleService = new RoleService($identityProvider, new InMemoryRoleProvider($rolesConfig), new RecursiveRoleIteratorStrategy());

        $this->assertEquals($doesMatch, $roleService->matchIdentityRoles($rolesToCheck));
    }

    public function testReturnGuestRoleIfNoIdentityIsFound()
    {
        $identityProvider = $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())
                         ->method('getIdentity')
                         ->will($this->returnValue(null));

        $roleService = new RoleService(
            $identityProvider,
            new InMemoryRoleProvider([]),
            $this->createMock('LmcRbacMvc\Role\TraversalStrategyInterface')
        );

        $roleService->setGuestRole('guest');

        $result = $roleService->getIdentityRoles();

        $this->assertEquals('guest', $roleService->getGuestRole());
        $this->assertCount(1, $result);
        $this->assertInstanceOf('Laminas\Permissions\Rbac\RoleInterface', $result[0]);
        $this->assertEquals('guest', $result[0]->getName());
    }

    public function testSetIdentityProvider()
    {
        $identityProvider = $this->createMock(IdentityProviderInterface::class);
        $identityProvider->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue('test'));
        $roleService = new RoleService(
            $this->createMock(IdentityProviderInterface::class),
            new InMemoryRoleProvider([]),
            $this->createMock(TraversalStrategyInterface::class)
        );
        $roleService->setIdentityProvider($identityProvider);
        $this->assertEquals('test', $roleService->getIdentity());
    }

    public function testSetRoleProvider()
    {
        $role = $this->createMock(RoleInterface::class);
        $identity = $this->createMock(IdentityInterface::class);
        $identity->expects($this->once())->method('getRoles')->will($this->returnValue(new \ArrayObject([$role])));

        $identityProvider = $this->createMock(IdentityProviderInterface::class);
        $identityProvider->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($identity));
        $roleProvider = new InMemoryRoleProvider([
            'member' => [
                'children' => ['guest'],
            ],
            'guest'
        ]);
        $roleService = new RoleService(
            $identityProvider,
            $roleProvider,
            new RecursiveRoleIteratorStrategy()
        );
        $roleService->setRoleProvider($roleProvider);
        $roles = $roleService->getIdentityRoles();
        $this->assertEquals($role, $roles[0]);
    }

    public function testConvertRolesTraversable()
    {
        $identity = $this->createMock(IdentityInterface::class);
        $identity->expects($this->once())->method('getRoles')->will($this->returnValue(['guest']));

        $identityProvider = $this->createMock(IdentityProviderInterface::class);
        $identityProvider->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($identity));
        $roleService = new RoleService(
            $identityProvider,
            $this->createMock(RoleProviderInterface::class),
            new RecursiveRoleIteratorStrategy()
        );
        $roleProvider = new InMemoryRoleProvider([
            'member' => [
                'children' => ['guest']
            ],
            'guest'
        ]);
        $roleService->setRoleProvider($roleProvider);
        $this->assertEquals(false, $roleService->matchIdentityRoles(['member']));
    }


    public function testThrowExceptionIfIdentityIsWrongType()
    {
        $this->expectException('LmcRbacMvc\Exception\RuntimeException');
        $this->expectExceptionMessage('LmcRbacMvc expects your identity to implement LmcRbacMvc\Identity\IdentityInterface, "stdClass" given');

        $identityProvider = $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())
                         ->method('getIdentity')
                         ->will($this->returnValue(new \stdClass()));

        $roleService = new RoleService(
            $identityProvider,
            $this->createMock('LmcRbacMvc\Role\RoleProviderInterface'),
            $this->createMock('LmcRbacMvc\Role\TraversalStrategyInterface')
        );

        $roleService->getIdentityRoles();
    }
}
