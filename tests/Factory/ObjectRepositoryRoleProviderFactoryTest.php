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

namespace LmcRbacMvcTest\Factory;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\ServiceManager;
use LmcRbac\Exception\RuntimeException;
use LmcRbacMvc\Role\RoleProviderPluginManager;

/**
 * @covers \LmcRbacMvc\Factory\ObjectRepositoryRoleProviderFactory
 */
class ObjectRepositoryRoleProviderFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactoryUsingObjectRepository()
    {
        $serviceManager = new ServiceManager();
        $pluginManager  = new RoleProviderPluginManager($serviceManager);

        $options = [
            'role_name_property' => 'name',
            'object_repository'  => 'RoleObjectRepository'
        ];

        $serviceManager->setService('RoleObjectRepository', $this->createMock('Doctrine\Persistence\ObjectRepository'));

        $roleProvider = $pluginManager->get('LmcRbacMvc\Role\ObjectRepositoryRoleProvider', $options);
        $this->assertInstanceOf('LmcRbacMvc\Role\ObjectRepositoryRoleProvider', $roleProvider);
    }

    public function testFactoryUsingObjectManager()
    {
        $serviceManager = new ServiceManager();
        $pluginManager  = new RoleProviderPluginManager($serviceManager);

        $options = [
            'role_name_property' => 'name',
            'object_manager'     => 'ObjectManager',
            'class_name'         => 'Role'
        ];

        $objectManager = $this->createMock('Doctrine\Persistence\ObjectManager');
        $objectManager->expects($this->once())
                      ->method('getRepository')
                      ->with($options['class_name'])
                      ->will($this->returnValue($this->createMock('Doctrine\Persistence\ObjectRepository')));

        $serviceManager->setService('ObjectManager', $objectManager);

        $roleProvider = $pluginManager->get('LmcRbacMvc\Role\ObjectRepositoryRoleProvider', $options);
        $this->assertInstanceOf('LmcRbacMvc\Role\ObjectRepositoryRoleProvider', $roleProvider);
    }

    /**
     * This is required due to the fact that the ServiceManager catches ALL exceptions and throws it's own...
     */
    public function testThrowExceptionIfNoRoleNamePropertyIsSet()
    {
        try {
            $serviceManager = new ServiceManager();
            $pluginManager  = new RoleProviderPluginManager($serviceManager);

            $pluginManager->get('LmcRbacMvc\Role\ObjectRepositoryRoleProvider', []);
        } catch (ServiceNotCreatedException $smException) {
            while ($e = $smException->getPrevious()) {
                if ($e instanceof RuntimeException) {
                    $this->assertInstanceOf(RuntimeException::class, $e);
                    return true;
                }
            }
        }

        $this->fail(
            'LmcRbacMvc\Factory\ObjectRepositoryRoleProviderFactory::createService() :: '
            .'LmcRbac\Exception\RuntimeException was not found in the previous Exceptions'
        );
    }

    /**
     * This is required due to the fact that the ServiceManager catches ALL exceptions and throws it's own...
     */
    public function testThrowExceptionIfNoObjectManagerNorObjectRepositoryIsSet()
    {
        try {
            $serviceManager = new ServiceManager();
            $pluginManager  = new RoleProviderPluginManager($serviceManager);

            $pluginManager->get('LmcRbacMvc\Role\ObjectRepositoryRoleProvider', [
                'role_name_property' => 'name'
            ]);
        } catch (ServiceNotCreatedException $smException) {
            while ($e = $smException->getPrevious()) {
                if ($e instanceof RuntimeException) {
                    $this->assertInstanceOf(RuntimeException::class, $e);
                    return true;
                }
            }
        }

        $this->fail(
             'LmcRbacMvc\Factory\ObjectRepositoryRoleProviderFactory::createService() :: '
            .'LmcRbac\Exception\RuntimeException was not found in the previous Exceptions'
        );
    }
}
