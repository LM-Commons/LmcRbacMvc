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

namespace LmcRbacMvc\Options;

use Laminas\Stdlib\AbstractOptions;
use LmcRbacMvc\Exception;
use LmcRbacMvc\Guard\GuardInterface;

/**
 * Options for ZfcRbac module
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @license MIT
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * Key of the identity provider used to retrieve the identity
     */
    protected string $identityProvider = 'LmcRbacMvc\Identity\AuthenticationIdentityProvider';

    /**
     * Guest role (used when no identity is found)
     */
    protected string $guestRole = 'guest';

    /**
     * Guards
     */
    protected array $guards = [];

    /**
     * Assertion map
     */
    protected array $assertionMap = [];

    /**
     * Protection policy for guards (can be "deny" or "allow")
     */
    protected string $protectionPolicy = GuardInterface::POLICY_ALLOW;

    /**
     * A configuration for role provider
     */
    protected array $roleProvider = [];

    /**
     * Options for the unauthorized strategy
     */
    protected ?UnauthorizedStrategyOptions $unauthorizedStrategy = null;

    /**
     * Options for the redirect strategy
     */
    protected ?RedirectStrategyOptions $redirectStrategy = null;

    /**
     * Constructor
     *
     * {@inheritDoc}
     */
    public function __construct($options = null)
    {
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

    /**
     * Set the key of the identity provider used to retrieve the identity
     *
     * @param string $identityProvider
     * @return void
     */
    public function setIdentityProvider(string $identityProvider): void
    {
        $this->identityProvider = $identityProvider;
    }

    /**
     * Get the key of the identity provider used to retrieve the identity
     *
     * @return string
     */
    public function getIdentityProvider(): string
    {
        return $this->identityProvider;
    }

    /**
     * Set the assertions options
     *
     * @param array $assertionMap
     * @return void
     */
    public function setAssertionMap(array $assertionMap): void
    {
        $this->assertionMap = $assertionMap;
    }

    /**
     * Get the assertions options
     *
     * @return array
     */
    public function getAssertionMap(): array
    {
        return $this->assertionMap;
    }

    /**
     * Set the guest role (used when no identity is found)
     *
     * @param string $guestRole
     * @return void
     */
    public function setGuestRole(string $guestRole): void
    {
        $this->guestRole = $guestRole;
    }

    /**
     * Get the guest role (used when no identity is found)
     *
     * @return string
     */
    public function getGuestRole(): string
    {
        return $this->guestRole;
    }

    /**
     * Set the guards options
     *
     * @param  array $guards
     * @return void
     */
    public function setGuards(array $guards): void
    {
        $this->guards = $guards;
    }

    /**
     * Get the guards options
     *
     * @return array
     */
    public function getGuards(): array
    {
        return $this->guards;
    }

    /**
     * Set the protection policy for guards
     *
     * @param string $protectionPolicy
     * @return void
     *@throws Exception\RuntimeException
     */
    public function setProtectionPolicy(string $protectionPolicy): void
    {
        if ($protectionPolicy !== GuardInterface::POLICY_ALLOW && $protectionPolicy !== GuardInterface::POLICY_DENY) {
            throw new Exception\RuntimeException(sprintf(
                'An invalid protection policy was set. Can only be "deny" or "allow", "%s" given',
                $protectionPolicy
            ));
        }

        $this->protectionPolicy = (string) $protectionPolicy;
    }

    /**
     * Get the protection policy for guards
     *
     * @return string
     */
    public function getProtectionPolicy(): string
    {
        return $this->protectionPolicy;
    }

    /**
     * Set the configuration for the role provider
     *
     * @param  array $roleProvider
     * @throws Exception\RuntimeException
     */
    public function setRoleProvider(array $roleProvider): void
    {
        if (count($roleProvider) > 1) {
            throw new Exception\RuntimeException(
                'You can only have one role provider'
            );
        }

        $this->roleProvider = $roleProvider;
    }

    /**
     * Get the configuration for the role provider
     *
     * @return array
     */
    public function getRoleProvider(): array
    {
        return $this->roleProvider;
    }

    /**
     * Set the unauthorized strategy options
     *
     * @param array $unauthorizedStrategy
     */
    public function setUnauthorizedStrategy(array $unauthorizedStrategy): void
    {
        $this->unauthorizedStrategy = new UnauthorizedStrategyOptions($unauthorizedStrategy);
    }

    /**
     * Get the unauthorized strategy options
     *
     * @return UnauthorizedStrategyOptions|null
     */
    public function getUnauthorizedStrategy(): ?UnauthorizedStrategyOptions
    {
        if (null === $this->unauthorizedStrategy) {
            $this->unauthorizedStrategy = new UnauthorizedStrategyOptions();
        }

        return $this->unauthorizedStrategy;
    }

    /**
     * Set the redirect strategy options
     *
     * @param array $redirectStrategy
     */
    public function setRedirectStrategy(array $redirectStrategy): void
    {
        $this->redirectStrategy = new RedirectStrategyOptions($redirectStrategy);
    }

    /**
     * Get the redirect strategy options
     *
     * @return RedirectStrategyOptions|null
     */
    public function getRedirectStrategy(): ?RedirectStrategyOptions
    {
        if (null === $this->redirectStrategy) {
            $this->redirectStrategy = new RedirectStrategyOptions();
        }

        return $this->redirectStrategy;
    }
}
