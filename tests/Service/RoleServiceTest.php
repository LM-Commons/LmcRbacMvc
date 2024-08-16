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

use Lmc\Rbac\Role\Role;
use LmcRbacMvc\Identity\IdentityInterface;
use LmcRbacMvc\Identity\IdentityProviderInterface;
use LmcRbacMvc\Role\InMemoryRoleProvider;
use LmcRbacMvc\Role\RoleProviderInterface;
use LmcRbacMvc\Service\RoleService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use LmcRbacMvc\Role\RecursiveRoleIteratorStrategy;
use LmcRbacMvc\Role\TraversalStrategyInterface;

/**
 * @covers \LmcRbacMvc\Service\RoleService
 */
class RoleServiceTest extends TestCase
{
    public static function roleProvider(): array
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
            ],
            // With Role objects
            [
                'rolesConfig' => [
                    'member',
                    'guest'
                ],
                'identityRoles' => [
                    'member'
                ],
                'rolesToCheck' => [
                    new Role('member'),
                ],
                'doesMatch' => true
            ]
        ];
    }

    #[DataProvider('roleProvider')]
    public function testMatchIdentityRoles(array $rolesConfig, array $identityRoles, array $rolesToCheck, $doesMatch)
    {
        $identity = $this->createMock('Lmc\Rbac\Identity\IdentityInterface');
        $identity->expects($this->any())->method('getRoles')->willReturn($identityRoles);

        $identityProvider = $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())
                         ->method('getIdentity')
                         ->willReturn($identity);

        $roleProvider = new \Lmc\Rbac\Role\InMemoryRoleProvider($rolesConfig);
        $baseRoleService = new \Lmc\Rbac\Service\RoleService($roleProvider, 'guest');

        $roleService = new RoleService($identityProvider, $baseRoleService, new RecursiveRoleIteratorStrategy());

        $this->assertEquals($doesMatch, $roleService->matchIdentityRoles($rolesToCheck));
    }

    public function testSetIdentityProvider()
    {
        $identityProvider = $this->createMock(IdentityProviderInterface::class);
        $identityProvider->expects($this->any())
            ->method('getIdentity')
            ->willReturn(null);
        $roleService = new RoleService(
            $this->createMock(IdentityProviderInterface::class),
            $this->createMock(\Lmc\Rbac\Service\RoleService::class),
            $this->createMock(TraversalStrategyInterface::class)
        );
        $roleService->setIdentityProvider($identityProvider);
        $this->assertNull($roleService->getIdentity());
    }

    public function testGetRoleService(): void
    {
        $baseRoleService = $this->createMock(\Lmc\Rbac\Service\RoleService::class);
        $roleService = new RoleService(
            $this->createMock(IdentityProviderInterface::class),
            $baseRoleService,
            $this->createMock(TraversalStrategyInterface::class),
        );
        $this->assertEquals($baseRoleService, $roleService->getRoleService());
    }
}
