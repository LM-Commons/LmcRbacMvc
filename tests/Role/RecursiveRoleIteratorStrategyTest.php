<?php

namespace LmcRbacMvcTest\Role;

use LmcRbacMvc\Role\RecursiveRoleIterator;
use LmcRbacMvc\Role\RecursiveRoleIteratorStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass('LmcRbacMvc\Role\RecursiveRoleIteratorStrategy')]
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
