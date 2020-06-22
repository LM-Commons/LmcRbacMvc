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

namespace LaminasRbacTest\Factory;

use Laminas\ServiceManager\ServiceManager;
use LaminasRbac\Factory\AuthorizationServiceFactory;
use LaminasRbac\Options\ModuleOptions;

/**
 * @covers \LaminasRbac\Factory\AuthorizationServiceFactory
 */
class AuthorizationServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager();

        $serviceManager->setService('Rbac\Rbac', $this->getMock('Rbac\Rbac', [], [], '', false));

        $serviceManager->setService(
            'LaminasRbac\Service\RoleService',
            $this->getMock('LaminasRbac\Service\RoleService', [], [], '', false)
        );
        $serviceManager->setService(
            'LaminasRbac\Assertion\AssertionPluginManager',
            $this->getMock('LaminasRbac\Assertion\AssertionPluginManager', [], [], '', false)
        );
        $serviceManager->setService(
            'LaminasRbac\Options\ModuleOptions',
            new ModuleOptions([])
        );

        $factory              = new AuthorizationServiceFactory();
        $authorizationService = $factory->createService($serviceManager);

        $this->assertInstanceOf('LaminasRbac\Service\AuthorizationService', $authorizationService);
    }
}
