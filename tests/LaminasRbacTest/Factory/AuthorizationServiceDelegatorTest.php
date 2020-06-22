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

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LaminasRbac\Factory\AuthorizationServiceDelegatorFactory;
use LaminasRbacTest\Initializer\AuthorizationAwareFake;
use LaminasRbacTest\Util\ServiceManagerFactory;

/**
 * @covers  \LaminasRbac\Factory\AuthorizationServiceDelegatorFactory
 * @author  Jean-Marie Leroux <jmleroux.pro@gmail.com>
 * @license MIT License
 */
class AuthorizationServiceDelegatorTest extends \PHPUnit_Framework_TestCase
{
    public function testDelegatorFactory()
    {
        $authServiceClassName = 'LaminasRbac\Service\AuthorizationService';
        $delegator            = new AuthorizationServiceDelegatorFactory();
        $serviceLocator       = $this->prophesize(ServiceLocatorInterface::class);
        $serviceLocator->willImplement(ContainerInterface::class);

        $authorizationService = $this->getMock('LaminasRbac\Service\AuthorizationService', [], [], '', false);

        $callback = function () {
            return new AuthorizationAwareFake();
        };

        $serviceLocator->get($authServiceClassName)->willReturn($authorizationService)->shouldBeCalled();

        $decoratedInstance = $delegator->createDelegatorWithName($serviceLocator->reveal(), 'name', 'requestedName', $callback);

        $this->assertEquals($authorizationService, $decoratedInstance->getAuthorizationService());
    }

    public function testAuthorizationServiceIsNotInjectedWithoutDelegator()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        $serviceManager->setAllowOverride(true);
        $authorizationService = $this->getMock('LaminasRbac\Service\AuthorizationService', [], [], '', false);
        $serviceManager->setService(
            'LaminasRbac\Service\AuthorizationService',
            $authorizationService
        );
        $serviceManager->setAllowOverride(false);

        $serviceManager->setInvokableClass(
            'LaminasRbacTest\AuthorizationAware',
            'LaminasRbacTest\Initializer\AuthorizationAwareFake'
        );
        $decoratedInstance = $serviceManager->get('LaminasRbacTest\AuthorizationAware');
        $this->assertNull($decoratedInstance->getAuthorizationService());
    }

    public function testAuthorizationServiceIsInjectedWithDelegator()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        if (method_exists($serviceManager, 'build')) {
            $this->markTestSkipped('this test is only vor zend-servicemanager v2');
        }

        $serviceManager->setAllowOverride(true);
        $authorizationService = $this->getMock('LaminasRbac\Service\AuthorizationService', [], [], '', false);
        $serviceManager->setService(
            'LaminasRbac\Service\AuthorizationService',
            $authorizationService
        );
        $serviceManager->setAllowOverride(false);

        $serviceManager->setInvokableClass(
            'LaminasRbacTest\AuthorizationAware',
            'LaminasRbacTest\Initializer\AuthorizationAwareFake'
        );

        $serviceManager->addDelegator(
            'LaminasRbacTest\AuthorizationAware',
            'LaminasRbac\Factory\AuthorizationServiceDelegatorFactory'
        );

        $decoratedInstance = $serviceManager->get('LaminasRbacTest\AuthorizationAware');
        $this->assertEquals($authorizationService, $decoratedInstance->getAuthorizationService());
    }

    public function testAuthorizationServiceIsInjectedWithDelegatorV3()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        if (! method_exists($serviceManager, 'build')) {
            $this->markTestSkipped('this test is only for zend-servicemanager v3');
        }

        $serviceManager->setAllowOverride(true);
        $authorizationService = $this->getMock('LaminasRbac\Service\AuthorizationService', [], [], '', false);
        $serviceManager->setService(
            'LaminasRbac\Service\AuthorizationService',
            $authorizationService
        );
        $serviceManager->setAllowOverride(false);

        $serviceManager->setInvokableClass(
            'LaminasRbacTest\AuthorizationAware',
            'LaminasRbacTest\Initializer\AuthorizationAwareFake'
        );

        $serviceManager->addDelegator(
            'LaminasRbacTest\Initializer\AuthorizationAwareFake',
            'LaminasRbac\Factory\AuthorizationServiceDelegatorFactory'
        );

        $decoratedInstance = $serviceManager->get('LaminasRbacTest\AuthorizationAware');
        $this->assertEquals($authorizationService, $decoratedInstance->getAuthorizationService());
    }

    public function testDelegatorThrowExceptionWhenBadInterface()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        $serviceManager->setAllowOverride(true);
        $authorizationService = $this->getMock('LaminasRbac\Service\AuthorizationService', [], [], '', false);
        $serviceManager->setService(
            'LaminasRbac\Service\AuthorizationService',
            $authorizationService
        );
        $serviceManager->setAllowOverride(false);

        $serviceManager->setFactory(
            'LaminasRbacTest\AuthorizationAware',
            function () {
                return new \StdClass();
            }
        );

        $serviceManager->addDelegator(
            'LaminasRbacTest\AuthorizationAware',
            'LaminasRbac\Factory\AuthorizationServiceDelegatorFactory'
        );

        $thrown = false;
        try {
            $serviceManager->get('LaminasRbacTest\AuthorizationAware');
        } catch (\Exception $e) {
            $thrown = true;
            $this->assertStringEndsWith('The service LaminasRbacTest\AuthorizationAware must implement AuthorizationServiceAwareInterface.', $e->getMessage());
            if ($e->getPrevious()) {
                $this->assertInstanceOf('LaminasRbac\Exception\RuntimeException', $e->getPrevious());
            }
        }

        $this->assertTrue($thrown);
    }
}
