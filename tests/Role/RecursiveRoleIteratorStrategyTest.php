<?php

namespace LmcTest\Rbac\Mvc\Role;

use Lmc\Rbac\Mvc\Role\RecursiveRoleIterator;
use Lmc\Rbac\Mvc\Role\RecursiveRoleIteratorStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass('Lmc\Rbac\Mvc\Role\RecursiveRoleIteratorStrategy')]
class RecursiveRoleIteratorStrategyTest extends TestCase
{
    public function testGetRecursiveRoleIterator(): void
    {
        $roles = [];

        $strategy = new RecursiveRoleIteratorStrategy();
        $iterator = $strategy->getRolesIterator($roles);
        $this->assertInstanceOf(\Traversable::class, $iterator);
    }
}
