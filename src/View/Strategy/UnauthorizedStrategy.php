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

namespace LmcRbacMvc\View\Strategy;

use Laminas\Http\Response as HttpResponse;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;
use Lmc\Rbac\Exception\UnauthorizedExceptionInterface;
use LmcRbacMvc\Guard\GuardInterface;
use LmcRbacMvc\Options\UnauthorizedStrategyOptions;

/**
 * This strategy renders a specific template when a user is unauthorized
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @license MIT
 */
class UnauthorizedStrategy extends AbstractStrategy
{
    protected UnauthorizedStrategyOptions $options;

    /**
     * Constructor
     *
     * @param UnauthorizedStrategyOptions $options
     */
    public function __construct(UnauthorizedStrategyOptions $options)
    {
        $this->options = $options;
    }

    /**
     * @private
     * @param  MvcEvent $event
     * @return void
     */
    public function onError(MvcEvent $event): void
    {
        // Do nothing if no error or if response is not HTTP response
        if (!($event->getParam('exception') instanceof UnauthorizedExceptionInterface)
            || ($event->getResult() instanceof HttpResponse)
            || !($event->getResponse() instanceof HttpResponse)
        ) {
            return;
        }

        $model = new ViewModel();
        $model->setTemplate($this->options->getTemplate());

        switch ($event->getError()) {
            case GuardInterface::GUARD_UNAUTHORIZED:
                $model->setVariable('error', GuardInterface::GUARD_UNAUTHORIZED);
                break;

            default:
                break;
        }

        $response = $event->getResponse() ?: new HttpResponse();
        $response->setStatusCode(403);

        $event->setResponse($response);
        $event->setResult($model);
    }
}
