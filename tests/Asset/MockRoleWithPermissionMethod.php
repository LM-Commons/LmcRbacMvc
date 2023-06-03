<?php

namespace LmcRbacMvcTest\Asset;


use Laminas\Permissions\Rbac\RoleInterface;

class MockRoleWithPermissionMethod implements RoleInterface
{
    public function getPermissions()
    {
        return ['permission-method-a', 'permission-method-b'];
    }

    public function getName() : string
    {
        return 'role-with-permission-method';
    }
    public function hasPermission($permission) : bool
    {
        return false;
    }
    
    /**
     * Add permission to the role.
     */
    public function addPermission(string $name): void{ return;}
    
    /**
     * Add a child.
     */
    public function addChild(RoleInterface $child): void{return;}
    
    /**
     * Get the children roles.
     *
     * @return RoleInterface[]
     */
    public function getChildren(): iterable{return [];}
    
    /**
     * Add a parent.
     */
    public function addParent(RoleInterface $parent): void{return;}
    
    /**
     * Get the parent roles.
     *
     * @return RoleInterface[]
     */
    public function getParents(): iterable{return [];}
    
    public function hasChildren() : bool
    {
        return false;
    }
}
