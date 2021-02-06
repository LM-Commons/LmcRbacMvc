<?php


namespace LmcRbacMvcTest\Asset;

use Rbac\Role\RoleInterface;


class MockRoleWithPermissionTraversable implements RoleInterface
{
    public function getPermissions()
    {
        return new \ArrayObject(['permission-method-a', 'permission-method-b']);
    }

    public function getName()
    {
        return 'role-with-permission-traversable';
    }
    public function hasPermission($permission)
    {
        return false;
    }

}
