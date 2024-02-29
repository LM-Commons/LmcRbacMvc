<?php

declare(strict_types=1);

namespace LmcRbacMvc\Rbac;

use Laminas\Permissions\Rbac\Rbac as LaminasRbac;
use Laminas\Permissions\Rbac\RoleInterface;
use Laminas\Permissions\Rbac\AssertionInterface;
use Laminas\Permissions\Rbac\Exception;

/**
 * @deprecated
 */
class Rbac extends LaminasRbac{
    /**
     * Return all the roles
     *
     * @return RoleInterface[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Determines if access is granted by checking the role and child roles for permission.
     *
     * @param RoleInterface|string $role
     * @param null|AssertionInterface|Callable $assertion
     * @throws Exception\InvalidArgumentException If the role is not found.
     * @throws Exception\InvalidArgumentException If the assertion is an invalid type.
     */
    public function isGranted($role, string $permission, $assertion = null): bool
    {
        if ( is_array($role) && $role[0] instanceof RoleInterface) {
            $role = $role[0];
        }
        if (! $this->hasRole($role)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'No role with name "%s" could be found',
                is_object($role) ? $role->getName() : $role
                ));
        }
        
        if (is_string($role)) {
            $role = $this->getRole($role);
        }
        
        $result = $role->hasPermission($permission);
        if (false === $result || null === $assertion) {
            return $result;
        }
        
        if (
            ! $assertion instanceof AssertionInterface
            && ! is_callable($assertion)
            ) {
                throw new Exception\InvalidArgumentException(
                    'Assertions must be a Callable or an instance of Laminas\Permissions\Rbac\AssertionInterface'
                    );
            }
            
            if ($assertion instanceof AssertionInterface) {
                return $result && $assertion->assert($this, $role, $permission);
            }
            
            // Callable assertion provided.
            return $result && $assertion($this, $role, $permission);
    }
    
    /**
     * Is a role registered?
     *
     * @param  RoleInterface|string $role
     */
    public function hasRole($role): bool
    {
        if(empty($this->roles)){
            $this->addRole($role);
        }

        return parent::hasRole($role);
    }

}
