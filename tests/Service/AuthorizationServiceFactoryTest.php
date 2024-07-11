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
use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Service\AuthorizationServiceFactory;
use LmcRbacMvc\Options\ModuleOptions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LmcRbacMvc\Service\AuthorizationServiceFactory
 */
class AuthorizationServiceFactoryTest extends TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager();

        $serviceManager->setService('rbac', new Rbac());

        $serviceManager->setService(
            'LmcRbacMvc\Service\RoleService',
            $this->getMockBuilder('LmcRbacMvc\Service\RoleService')->disableOriginalConstructor()->getMock()
        );
        $serviceManager->setService(
            'LmcRbacMvc\Assertion\AssertionPluginManager',
            $this->getMockBuilder('LmcRbacMvc\Assertion\AssertionPluginManager')->disableOriginalConstructor()->getMock()

        );
        $serviceManager->setService(
            'LmcRbacMvc\Options\ModuleOptions',
            new ModuleOptions([])
        );

        $factory              = new AuthorizationServiceFactory();
        $authorizationService = $factory($serviceManager, 'LmcRbacMvc\Service\AuthorizationService');

        $this->assertInstanceOf(\LmcRbacMvc\Service\AuthorizationService::class, $authorizationService);
    }
}
