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

use Lmc\Rbac\Identity\IdentityInterface;
use Lmc\Rbac\Permission\PermissionInterface;
use Lmc\Rbac\Service\AuthorizationServiceInterface as BaseAuthorizationServiceInterface;

/**
 * Authorization service is a simple service that internally uses Rbac to check if identity is
 * granted a permission
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @license MIT
 */
class AuthorizationService implements AuthorizationServiceInterface
{
    /**
     * @var RoleService
     */
    protected RoleService $roleService;

    /**
     * @var BaseAuthorizationServiceInterface
     */
    protected BaseAuthorizationServiceInterface $baseAuthorizationService;

    /**
     * Constructor
     *
     * @param RoleService $roleService
     * @param BaseAuthorizationServiceInterface $baseAuthorizationService
     */
    public function __construct(RoleService $roleService, BaseAuthorizationServiceInterface $baseAuthorizationService)
    {
        $this->roleService            = $roleService;
        $this->baseAuthorizationService = $baseAuthorizationService;
    }

    /**
     * Get the current identity from the role service
     *
     * @return IdentityInterface|null
     */
    public function getIdentity(): ?IdentityInterface
    {
        return $this->roleService->getIdentity();
    }

    /**
     * Check if the permission is granted to the current identity
     *
     * @param string $permission
     * @param mixed|null $context
     * @return bool
     */
    public function isGranted(string $permission, mixed $context = null): bool
    {
        return $this->baseAuthorizationService->isGranted($this->getIdentity(), $permission, $context);
    }

    public function getAssertions(): array
    {
        return $this->baseAuthorizationService->getAssertions();
    }

    public function getAssertion(PermissionInterface|string $permission): \Lmc\Rbac\Assertion\AssertionInterface|callable|string|null
    {
        return $this->baseAuthorizationService->getAssertion($permission);
    }

    public function setAssertions(array $assertions, bool $merge = false): void
    {
        $this->baseAuthorizationService->setAssertions($assertions, $merge);
    }

    public function setAssertion(PermissionInterface|string $permission, callable|\Lmc\Rbac\Assertion\AssertionInterface|string $assertion): void
    {
        $this->baseAuthorizationService->setAssertion($permission, $assertion);
    }

    public function hasAssertion(PermissionInterface|string $permission): bool
    {
        return $this->baseAuthorizationService->hasAssertion($permission);
    }
}
