<?php

namespace LmcRbacMvcTest\Role;

use LmcRbacMvcTest\Asset\Permission;
use Laminas\Permissions\Rbac\Role;
use PHPUnit\Framework\TestCase;

class PermissionInterfaceTest extends TestCase
{
    public function testPermissionInterfaceReturnsString()
    {
        $role = new Role('test');
        $permission = new Permission('foo');
        $role->addPermission($permission);
        $this->assertTrue($role->hasPermission('foo'));
    }
}
