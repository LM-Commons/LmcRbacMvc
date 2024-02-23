<?php
declare(strict_types=1);

namespace LmcRbacMvc\Role;

use Laminas\Permissions\Rbac\Role as LaminasRole;
use Laminas\Permissions\Rbac\RoleInterface;

class Role extends LaminasRole {
    
    /**
     * Get all child roles
     *
     * @return RoleInterface[]
     */
    public function getChildren(): array
    {
        if($this->children instanceof \Doctrine\ORM\PersistentCollection )
            return $this->children->toArray();
        else 
            return array_values($this->children);
    }
    
    /**
     * Get the parent roles.
     *
     * @return RoleInterface[]
     */
    public function getParents(): array
    {
        return $this->parents;
    }
    
    /**
     * {@inheritDoc}
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }
    
    /**
     * Check if a role is a descendant.
     */
    protected function hasDescendant(RoleInterface $role): bool
    {
        if(empty($this->children))
            $this->children = [];
        
        return parent::hasDescendant($role);
    }
}