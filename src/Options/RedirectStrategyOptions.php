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

namespace Lmc\Rbac\Mvc\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Redirect strategy options
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @license MIT
 */
class RedirectStrategyOptions extends AbstractOptions
{
    /**
     * Should the user be redirected when connected and not authorized
     */
    protected bool $redirectWhenConnected = true;

    /**
     * The name of the route to redirect when a user is connected and not authorized
     */
    protected string $redirectToRouteConnected = 'home';

    /**
     * The name of the route to redirect when a user is disconnected and not authorized
     */
    protected string $redirectToRouteDisconnected = 'login';

    /**
     * Should the previous URI should be appended as a query param?
     */
    protected bool $appendPreviousUri = true;

    /**
     * If appendPreviousUri is enabled, key to use in query params that hold the previous URI
     */
    protected string $previousUriQueryKey = 'redirectTo';

    /**
     * @param bool $redirectWhenConnected
     * @return void
     */
    public function setRedirectWhenConnected(bool $redirectWhenConnected): void
    {
        $this->redirectWhenConnected = $redirectWhenConnected;
    }

    /**
     * @return bool
     */
    public function getRedirectWhenConnected(): bool
    {
        return $this->redirectWhenConnected;
    }

    /**
     * @param string $redirectToRouteConnected
     * @return void
     */
    public function setRedirectToRouteConnected(string $redirectToRouteConnected): void
    {
        $this->redirectToRouteConnected = $redirectToRouteConnected;
    }

    /**
     * @return string
     */
    public function getRedirectToRouteConnected(): string
    {
        return $this->redirectToRouteConnected;
    }

    /**
     * @param string $redirectToRouteDisconnected
     * @return void
     */
    public function setRedirectToRouteDisconnected(string $redirectToRouteDisconnected): void
    {
        $this->redirectToRouteDisconnected = $redirectToRouteDisconnected;
    }

    /**
     * @return string
     */
    public function getRedirectToRouteDisconnected(): string
    {
        return $this->redirectToRouteDisconnected;
    }

    /**
     * @param boolean $appendPreviousUri
     */
    public function setAppendPreviousUri(bool $appendPreviousUri): void
    {
        $this->appendPreviousUri = $appendPreviousUri;
    }

    /**
     * @return boolean
     */
    public function getAppendPreviousUri(): bool
    {
        return $this->appendPreviousUri;
    }

    /**
     * @param string $previousUriQueryKey
     */
    public function setPreviousUriQueryKey(string $previousUriQueryKey): void
    {
        $this->previousUriQueryKey = $previousUriQueryKey;
    }

    /**
     * @return string
     */
    public function getPreviousUriQueryKey(): string
    {
        return $this->previousUriQueryKey;
    }
}
