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

namespace Lmc\Rbac\Mvc\Service;

/**
 * Makes a class AuthorizationService aware
 *
 * @author  Aeneas Rekkas
 * @license MIT License
 */
trait AuthorizationServiceAwareTrait
{
    /**
     * The AuthorizationService
     *
     * @var AuthorizationServiceInterface|null
     */
    protected ?AuthorizationServiceInterface $authorizationService = null;

    /**
     * Set the AuthorizationService
     *
     * @param AuthorizationServiceInterface $authorizationService
     * @return void
     */
    public function setAuthorizationService(AuthorizationServiceInterface $authorizationService): void
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * Return the AuthorizationService
     *
     * @return AuthorizationServiceInterface|null
     */
    public function getAuthorizationService(): ?AuthorizationServiceInterface
    {
        return $this->authorizationService;
    }
}
