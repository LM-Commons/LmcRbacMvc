<?php

namespace LmcRbacMvcTest\Role;

use Lmc\Rbac\Role\Role;
use Lmc\Rbac\Role\RoleInterface;
use LmcRbacMvc\Role\RecursiveRoleIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RecursiveRoleIterator::class)]
class RecursiveRoleIteratorTest extends TestCase
{
    public function testIsValid()
    {
        $roles = [
            new Role('foo')
        ];
        $roleIterator = new RecursiveRoleIterator($roles);
        foreach ($roleIterator as $role) {
            $this->assertInstanceOf(RoleInterface::class, $role);
        }
    }

    public function testWithChildren()
    {
        $parent = new Role('foo');
        $child  = new Role('bar');
        $parent->addChild($child);
        $roles = [$parent];
        $roleIterator = new \RecursiveIteratorIterator(new RecursiveRoleIterator($roles), \RecursiveIteratorIterator::SELF_FIRST);
        $count = 0;
        foreach ($roleIterator as $role) {
            $this->assertInstanceOf(RoleInterface::class, $role);
            $count++;
        }
        $this->assertEquals(2, $count);
    }

    public function testWithInvalidChildren()
    {
        $parent = new Role('foo');
        $child  = new Role('bar');
        $parent->addChild($child);
        $roles = [$parent];
        $roleIterator = new \RecursiveIteratorIterator(new RecursiveRoleIterator($roles), \RecursiveIteratorIterator::SELF_FIRST);
        $count = 0;
        foreach ($roleIterator as $role) {
            $this->assertInstanceOf(RoleInterface::class, $role);
            $count++;
        }
        $this->assertEquals(2, $count);
    }

    public function testWithInvalidItems()
    {
        $roles = [new Role('foo'), new \stdClass()];
        $roleIterator = new \RecursiveIteratorIterator(new RecursiveRoleIterator($roles), \RecursiveIteratorIterator::SELF_FIRST);
        $count = 0;
        foreach ($roleIterator as $role) {
            $this->assertInstanceOf(RoleInterface::class, $role);
            $count++;
        }
        $this->assertEquals(1, $count);
    }
}
