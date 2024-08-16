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

namespace LmcTest\Rbac\Mvc\View\Strategy;

use Lmc\Rbac\Mvc\View\Strategy\UnauthorizedStrategyFactory;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;

/**
 * @covers \Lmc\Rbac\Mvc\View\Strategy\UnauthorizedStrategyFactory
 */
class UnauthorizedStrategyFactoryTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;

    public function testFactory()
    {
        $unauthorizedStrategyOptions = $this->createMock('Lmc\Rbac\Mvc\Options\UnauthorizedStrategyOptions');

        $moduleOptionsMock = $this->createMock('Lmc\Rbac\Mvc\Options\ModuleOptions');
        $moduleOptionsMock->expects($this->once())
                          ->method('getUnauthorizedStrategy')
                          ->will($this->returnValue($unauthorizedStrategyOptions));

        $serviceLocatorMock = $this->prophesize('Laminas\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->willImplement(ContainerInterface::class);
        $serviceLocatorMock->get('Lmc\Rbac\Mvc\Options\ModuleOptions')->willReturn($moduleOptionsMock)->shouldBeCalled();

        $factory              = new UnauthorizedStrategyFactory();
        $unauthorizedStrategy = $factory($serviceLocatorMock->reveal(),'Lmc\Rbac\Mvc\View\Strategy\UnauthorizedStrategy');

        $this->assertInstanceOf('Lmc\Rbac\Mvc\View\Strategy\UnauthorizedStrategy', $unauthorizedStrategy);
    }
}
