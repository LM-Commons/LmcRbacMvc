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

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Service\AuthorizationServiceDelegatorFactory;
use LmcRbacMvcTest\Initializer\AuthorizationAwareFake;
use LmcRbacMvcTest\Util\ServiceManagerFactory;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;

/**
 * @covers  \LmcRbacMvc\Service\AuthorizationServiceDelegatorFactory
 * @author  Jean-Marie Leroux <jmleroux.pro@gmail.com>
 * @license MIT License
 */
class AuthorizationServiceDelegatorTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;

    public function testDelegatorFactory()
    {
        $authServiceClassName = 'LmcRbacMvc\Service\AuthorizationService';
        $delegator            = new AuthorizationServiceDelegatorFactory();
        $serviceLocator       = $this->prophesize(ServiceLocatorInterface::class);
        $serviceLocator->willImplement(ContainerInterface::class);


        $authorizationService = $this->getMockBuilder('LmcRbacMvc\Service\AuthorizationService')
            ->disableOriginalConstructor()
            ->getMock();

        $callback = function () {
            return new AuthorizationAwareFake();
        };

        $serviceLocator->get($authServiceClassName)->willReturn($authorizationService)->shouldBeCalled();

        /** TODO replace this test */
        $decoratedInstance = $delegator->createDelegatorWithName($serviceLocator->reveal(), 'name', 'requestedName', $callback);

        $this->assertEquals($authorizationService, $decoratedInstance->getAuthorizationService());
    }

    public function testAuthorizationServiceIsNotInjectedWithoutDelegator()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        $serviceManager->setAllowOverride(true);
        $authorizationService = $this->getMockBuilder('LmcRbacMvc\Service\AuthorizationService')
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService(
            'LmcRbacMvc\Service\AuthorizationService',
            $authorizationService
        );
        $serviceManager->setAllowOverride(false);

        $serviceManager->setInvokableClass(
            'LmcRbacMvcTest\AuthorizationAware',
            'LmcRbacMvcTest\Initializer\AuthorizationAwareFake'
        );
        $decoratedInstance = $serviceManager->get('LmcRbacMvcTest\AuthorizationAware');
        $this->assertNull($decoratedInstance->getAuthorizationService());
    }

    public function testAuthorizationServiceIsInjectedWithDelegatorV3()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        if (! method_exists($serviceManager, 'build')) {
            $this->markTestSkipped('this test is only for zend-servicemanager v3');
        }

        $serviceManager->setAllowOverride(true);
//        $authorizationService = $this->getMock('LmcRbacMvc\Service\AuthorizationService', [], [], '', false);
        $authorizationService = $this->getMockBuilder('LmcRbacMvc\Service\AuthorizationService')
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService(
            'LmcRbacMvc\Service\AuthorizationService',
            $authorizationService
        );
        $serviceManager->setAllowOverride(false);

        $serviceManager->setInvokableClass(
            'LmcRbacMvcTest\AuthorizationAware',
            'LmcRbacMvcTest\Initializer\AuthorizationAwareFake'
        );

        $serviceManager->addDelegator(
            'LmcRbacMvcTest\Initializer\AuthorizationAwareFake',
            'LmcRbacMvc\Service\AuthorizationServiceDelegatorFactory'
        );

        $decoratedInstance = $serviceManager->get('LmcRbacMvcTest\AuthorizationAware');
        $this->assertEquals($authorizationService, $decoratedInstance->getAuthorizationService());
    }

    public function testDelegatorThrowExceptionWhenBadInterface()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        $serviceManager->setAllowOverride(true);
        $authorizationService = $this->getMockBuilder('LmcRbacMvc\Service\AuthorizationService')
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService(
            'LmcRbacMvc\Service\AuthorizationService',
            $authorizationService
        );
        $serviceManager->setAllowOverride(false);

        $serviceManager->setFactory(
            'LmcRbacTest\AuthorizationAware',
            function () {
                return new \StdClass();
            }
        );

        $serviceManager->addDelegator(
            'LmcRbacTest\AuthorizationAware',
            'LmcRbacMvc\Service\AuthorizationServiceDelegatorFactory'
        );

        $thrown = false;
        try {
            $serviceManager->get('LmcRbacTest\AuthorizationAware');
        } catch (\Exception $e) {
            $thrown = true;
            $this->assertStringEndsWith('The service LmcRbacTest\AuthorizationAware must implement AuthorizationServiceAwareInterface.', $e->getMessage());
            if ($e->getPrevious()) {
                $this->assertInstanceOf('LmcRbac\Exception\RuntimeException', $e->getPrevious());
            }
        }

        $this->assertTrue($thrown);
    }
}
