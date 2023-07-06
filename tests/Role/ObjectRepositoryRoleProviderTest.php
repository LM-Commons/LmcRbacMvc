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

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Doctrine\Persistence\ObjectManager;
use LmcRbacMvc\Role\RecursiveRoleIterator;
use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Role\ObjectRepositoryRoleProvider;
use LmcRbacMvcTest\Asset\FlatRole;
use LmcRbacMvcTest\Asset\HierarchicalRole;
use LmcRbacMvcTest\Util\ServiceManagerFactory;

/**
 * @covers \LmcRbacMvc\Role\ObjectRepositoryRoleProvider
 */
class ObjectRepositoryRoleProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function testObjectRepositoryProviderForFlatRole()
    {
        $this->serviceManager = ServiceManagerFactory::getServiceManager();
        $objectManager        = $this->getObjectManager();

        // Let's add some roles
        $adminRole = new FlatRole('admin');
        $objectManager->persist($adminRole);

        $memberRole = new FlatRole('member');
        $objectManager->persist($memberRole);

        $objectManager->flush();

        $objectRepository = $objectManager->getRepository('LmcRbacMvcTest\Asset\FlatRole');

        $objectRepositoryRoleProvider = new ObjectRepositoryRoleProvider($objectRepository, 'name');

        // Get only the admin role
        $roles = $objectRepositoryRoleProvider->getRoles(['admin']);

        $this->assertCount(1, $roles);
        $this->assertIsArray($roles);

        $this->assertInstanceOf('Laminas\Permissions\Rbac\RoleInterface', $roles[0]);
        $this->assertEquals('admin', $roles[0]->getName());
    }

    public function testObjectRepositoryProviderForHierarchicalRole()
    {
        $this->serviceManager = ServiceManagerFactory::getServiceManager();
        $objectManager        = $this->getObjectManager();

        // Let's add some roles
        $guestRole = new HierarchicalRole('guest');
        $objectManager->persist($guestRole);

        $memberRole = new HierarchicalRole('member');
        $memberRole->addChild($guestRole);
        $objectManager->persist($memberRole);

        $adminRole = new HierarchicalRole('admin');
        $adminRole->addChild($memberRole);
        $objectManager->persist($adminRole);

        $objectManager->flush();

        $objectRepository = $objectManager->getRepository('LmcRbacMvcTest\Asset\HierarchicalRole');

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
        $memberRole       = new FlatRole('member');
        $provider         = new ObjectRepositoryRoleProvider($objectRepository, 'name');
        $result           = [$memberRole];

        $objectRepository->expects($this->once())->method('findBy')->will($this->returnValue($result));

        $this->assertEquals($result, $provider->getRoles(['member']));
        $this->assertEquals($result, $provider->getRoles(['member']));
    }

    public function testClearRoleCache()
    {
        $objectRepository = $this->createMock('Doctrine\ORM\EntityRepository');
        $memberRole       = new FlatRole('member');
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
        $objectRepository             = $objectManager->getRepository('LmcRbacMvcTest\Asset\FlatRole');
        $objectRepositoryRoleProvider = new ObjectRepositoryRoleProvider($objectRepository, 'name');

        $this->expectException(
            \LmcRbacMvc\Exception\RoleNotFoundException::class,
            'Some roles were asked but could not be loaded from database: guest, admin'
        );

        $objectRepositoryRoleProvider->getRoles(['guest', 'admin']);
    }

    /**
     * @return ObjectManager
     * @throws ToolsException
     */
    private function getObjectManager()
    {
        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $this->serviceManager->get('Doctrine\\ORM\\EntityManager');
        $schemaTool    = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        return $entityManager;
    }
}
