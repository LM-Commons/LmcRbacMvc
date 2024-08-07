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

namespace LmcRbacMvc\Guard;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\MvcEvent;
use Lmc\Rbac\Exception;

/**
 * Abstract guard that hook on the MVC workflow
 *
 */
abstract class AbstractGuard implements GuardInterface
{
    use ListenerAggregateTrait;

    /**
     * Event priority
     */
    const EVENT_PRIORITY = -5;

    /**
     * MVC event to listen
     */
    const EVENT_NAME = MvcEvent::EVENT_ROUTE;

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = AbstractGuard::EVENT_PRIORITY): void
    {
        $this->listeners[] = $events->attach(static::EVENT_NAME, [$this, 'onResult'], $priority);
    }

    /**
     * @private
     * @param  MvcEvent $event
     * @return void
     */
    public function onResult(MvcEvent $event): void
    {
        if ($this->isGranted($event)) {
            return;
        }

        $event->setError(self::GUARD_UNAUTHORIZED);
        $event->setParam('exception', new Exception\UnauthorizedException(
            'You are not authorized to access this resource',
            403
        ));

        $application  = $event->getApplication();
        $eventManager = $application->getEventManager();

        $event->setName(MvcEvent::EVENT_DISPATCH_ERROR);
        $eventManager->triggerEvent($event);

        // just in case
        $event->stopPropagation(true);
    }
}
