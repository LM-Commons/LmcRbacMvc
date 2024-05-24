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

namespace LmcRbacMvcTest\Role;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Doctrine\Persistence\ObjectManager;
use LmcRbacMvc\Role\RecursiveRoleIterator;
use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Role\ObjectRepositoryRoleProvider;
use LmcRbacMvcTest\Asset\FlatRole;
use LmcRbacMvcTest\Asset\HierarchicalRole;
use LmcRbacMvcTest\Asset\Role;
use LmcRbacMvcTest\Asset\Permission;
use LmcRbacMvcTest\Util\ServiceManagerFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @covers \LmcRbacMvc\Role\ObjectRepositoryRoleProvider
 */
class ObjectRepositoryRoleProviderTest extends \PHPUnit\Framework\TestCase
{
    protected ServiceManager $serviceManager;

    public static function roleProvider(): array
    {
        return [
            'one-role-flat' => [
                'rolesConfig' => [
                    'admin',
                ],
                'rolesToCheck' => ['admin'],
            ],
            '2-roles-flat' => [
                'rolesConfig' => [
                    'admin',
                    'member',
                ],
                'rolesToCheck' => ['admin', 'member', ]
            ],
        ];
    }

    /**
     * @dataProvider roleProvider
     */
    public function testObjectRepositoryProviderForFlatRole( array $rolesConfig, array $rolesToCheck)
    {
        $this->serviceManager = ServiceManagerFactory::getServiceManager();
        $objectManager        = $this->getObjectManager();

        // Let's add the roles
        foreach ($rolesConfig as $name => $roleConfig) {
            if (is_array($roleConfig)) {
                $role = new Role($name);
                if (isset($roleConfig['permissions'])) {
                    foreach ($roleConfig['permissions'] as $permission) {
                        $role->addPermission($permission);
                    }
                }
            } else {
                $role = new Role($roleConfig);
            }
            $objectManager->persist($role);
        }
        $objectManager->flush();

        $objectRepository = $objectManager->getRepository('LmcRbacMvcTest\Asset\Role');
        $objectRepositoryRoleProvider = new ObjectRepositoryRoleProvider($objectRepository, 'name');

        // get the roles
        $roles = $objectRepositoryRoleProvider->getRoles($rolesToCheck);

        $this->assertIsArray($roles);
        $this->assertCount(count($rolesToCheck), $roles);

        $i = 0;
        foreach ($roles as $role) {
            $this->assertInstanceOf('Laminas\Permissions\Rbac\RoleInterface', $role);
            $this->assertEquals($rolesToCheck[$i], $role->getName());
            $i++;
        }
    }

    public function testObjectRepositoryProviderForFlatRoleWithPermissions()
    {
        $this->serviceManager = ServiceManagerFactory::getServiceManager();
        $objectManager        = $this->getObjectManager();

        // Create permission
//        $permission = new Permission('manage');
//        $objectManager->persist($permission);
//        $objectManager->flush();

        // Let's add some roles
        $adminRole = new Role('admin');
        $adminRole->addPermission('manage');
        $adminRole->addPermission('write');
        $adminRole->addPermission('read');
        $objectManager->persist($adminRole);

        $objectManager->flush();

        $objectRepository = $objectManager->getRepository('LmcRbacMvcTest\Asset\Role');

        $objectRepositoryRoleProvider = new ObjectRepositoryRoleProvider($objectRepository, 'name');

        // Get only the role
        $roles = $objectRepositoryRoleProvider->getRoles(['admin']);

        $this->assertCount(1, $roles);
        $this->assertIsArray($roles);

        $this->assertInstanceOf('Laminas\Permissions\Rbac\RoleInterface', $roles[0]);
        $this->assertEquals('admin', $roles[0]->getName());
        $this->assertTrue($roles[0]->hasPermission('manage') );
        $this->assertTrue($roles[0]->hasPermission('read') );
        $this->assertTrue($roles[0]->hasPermission('write') );
        $this->assertFalse($roles[0]->hasPermission('foo') );
    }

    public function testObjectRepositoryProviderForHierarchicalRole()
    {
        $this->serviceManager = ServiceManagerFactory::getServiceManager();
        $objectManager        = $this->getObjectManager();

        // Let's add some roles
//        $guestRole = new HierarchicalRole('guest');
        $guestRole = new Role('guest');
        $objectManager->persist($guestRole);

//        $memberRole = new HierarchicalRole('member');
        $memberRole = new Role('member');
        $memberRole->addChild($guestRole);
        $objectManager->persist($memberRole);

//        $adminRole = new HierarchicalRole('admin');
        $adminRole = new Role('admin');
        $adminRole->addChild($memberRole);
        $objectManager->persist($adminRole);

        $objectManager->flush();

//        $objectRepository = $objectManager->getRepository('LmcRbacMvcTest\Asset\HierarchicalRole');
        $objectRepository = $objectManager->getRepository('LmcRbacMvcTest\Asset\Role');

        $objectRepositoryRoleProvider = new ObjectRepositoryRoleProvider($objectRepository, 'name');

        // Get only the admin role
        $roles = $objectRepositoryRoleProvider->getRoles(['admin']);

        $this->assertCount(1, $roles);
        $this->assertIsArray($roles);

        $this->assertInstanceOf('Laminas\Permissions\Rbac\RoleInterface', $roles[0]);
        $this->assertEquals('admin', $roles[0]->getName());

        $iteratorIterator = new \RecursiveIteratorIterator(
            new RecursiveRoleIterator($roles[0]->getChildren()),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $childRolesString = '';

        foreach ($iteratorIterator as $childRole) {
            $this->assertInstanceOf('Laminas\Permissions\Rbac\RoleInterface', $childRole);
            $childRolesString .= $childRole->getName();
        }

        $this->assertEquals('memberguest', $childRolesString);
    }

    public function testRoleCacheOnConsecutiveCalls()
    {
        $objectRepository = $this->createMock('Doctrine\ORM\EntityRepository');
        $memberRole       = new Role('member');
        $provider         = new ObjectRepositoryRoleProvider($objectRepository, 'name');
        $result           = [$memberRole];

        $objectRepository->expects($this->once())->method('findBy')->will($this->returnValue($result));

        $this->assertEquals($result, $provider->getRoles(['member']));
        $this->assertEquals($result, $provider->getRoles(['member']));
    }

    public function testClearRoleCache()
    {
        $objectRepository = $this->createMock('Doctrine\ORM\EntityRepository');
        $memberRole       = new Role('member');
        $provider         = new ObjectRepositoryRoleProvider($objectRepository, 'name');
        $result           = [$memberRole];

        $objectRepository->expects($this->exactly(2))->method('findBy')->will($this->returnValue($result));

        $this->assertEquals($result, $provider->getRoles(['member']));
        $provider->clearRoleCache();
        $this->assertEquals($result, $provider->getRoles(['member']));
    }

    public function testThrowExceptionIfAskedRoleIsNotFound()
    {
        $this->serviceManager = ServiceManagerFactory::getServiceManager();

        $objectManager                = $this->getObjectManager();
        $objectRepository             = $objectManager->getRepository('LmcRbacMvcTest\Asset\Role');
        $objectRepositoryRoleProvider = new ObjectRepositoryRoleProvider($objectRepository, 'name');

        $this->expectException(
            \LmcRbac\Exception\RoleNotFoundException::class,
            'Some roles were asked but could not be loaded from database: guest, admin'
        );

        $objectRepositoryRoleProvider->getRoles(['guest', 'admin']);
    }

    /**
     * @return EntityManager|ObjectManager
     * @throws ToolsException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getObjectManager(): \Doctrine\ORM\EntityManager|ObjectManager
    {
        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $this->serviceManager->get('Doctrine\\ORM\\EntityManager');
        $schemaTool    = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        return $entityManager;
    }
}
