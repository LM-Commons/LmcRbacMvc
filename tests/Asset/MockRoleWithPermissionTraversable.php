<?php


namespace LmcRbacMvcTest\Asset;

use Laminas\Permissions\Rbac\RoleInterface;


class MockRoleWithPermissionTraversable implements RoleInterface
{
    public function getPermissions()
    {
        return new \ArrayObject(['permission-method-a', 'permission-method-b']);
    }

    public function getName():string
    {
        return 'role-with-permission-traversable';
    }
    public function hasPermission($permission):bool
    {
        return false;
    }

    public function addPermission(string $name): void{
        return;
    }
    
    /**
     * Add a child.
     */
    public function addChild(RoleInterface $child): void{
        return;
    }
    
    /**
     * Get the children roles.
     *
     * @return RoleInterface[]
     */
    public function getChildren(): iterable{
        return [];
    }
    
    /**
     * Add a parent.
     */
    public function addParent(RoleInterface $parent): void{
        return;
    }
    
    /**
     * Get the parent roles.
     *
     * @return RoleInterface[]
     */
    public function getParents(): iterable{
        return [];
    }
    
    /**
     * Get the children roles.
     *
     * @return RoleInterface[]
     */
    public function hasChildren(): iterable{
        return [];
    }
}
