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

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Factory\RedirectStrategyFactory;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \LmcRbacMvc\Factory\RedirectStrategyFactory
 */
class RedirectStrategyFactoryTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;

    public function testFactory()
    {
        $redirectStrategyOptions = $this->createMock('LmcRbacMvc\Options\RedirectStrategyOptions');

        $moduleOptionsMock = $this->createMock('LmcRbacMvc\Options\ModuleOptions');
        $moduleOptionsMock->expects($this->once())
                          ->method('getRedirectStrategy')
                          ->will($this->returnValue($redirectStrategyOptions));

        $authenticationServiceMock = $this->createMock('Laminas\Authentication\AuthenticationService');

        $serviceLocatorMock = $this->prophesize(ServiceLocatorInterface::class);
        $serviceLocatorMock->willImplement(ContainerInterface::class);
        $serviceLocatorMock->get('LmcRbacMvc\Options\ModuleOptions')
                           ->willReturn($moduleOptionsMock)
                           ->shouldBeCalled();
        $serviceLocatorMock->get('Laminas\Authentication\AuthenticationService')
                           ->willReturn($authenticationServiceMock)
                           ->shouldBeCalled();

        $factory          = new RedirectStrategyFactory();
        $redirectStrategy = $factory($serviceLocatorMock->reveal(), 'LmcRbacMvc\View\Strategy\RedirectStrategy');

        $this->assertInstanceOf('LmcRbacMvc\View\Strategy\RedirectStrategy', $redirectStrategy);
    }
}
