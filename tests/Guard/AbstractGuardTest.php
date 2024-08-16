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

namespace LmcTest\Rbac\Mvc\Guard;

use Laminas\EventManager\EventManager;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use LmcTest\Rbac\Mvc\Asset\DummyGuard;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \Lmc\Rbac\Mvc\Guard\AbstractGuard
 * @covers \Lmc\Rbac\Mvc\Guard\ControllerGuard
 */
class AbstractGuardTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;

    public function testDoesNotLimitDispatchErrorEventToOnlyOneListener()
    {
        $eventManager = new EventManager();
        $application = $this->prophesize(Application::class);
        $application->getEventManager()->willReturn($eventManager);

        $event = new MvcEvent();
        $event->setApplication($application->reveal());

        $guard = new DummyGuard();
        $guard->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $event) {
            $event->setParam('first-listener', true);
        });
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $event) {
            $event->setParam('second-listener', true);
        });

        // attach listener with lower priority than DummyGuard
        $eventManager->attach(MvcEvent::EVENT_ROUTE, function (MvcEvent $event) {
            $this->fail('should not be called, because guard should stop propagation');
        }, DummyGuard::EVENT_PRIORITY - 1);

        $event->setName(MvcEvent::EVENT_ROUTE);
        $eventManager->triggerEvent($event);

        $this->assertTrue($event->getParam('first-listener'));
        $this->assertTrue($event->getParam('second-listener'));
        $this->assertTrue($event->propagationIsStopped());
    }
}
