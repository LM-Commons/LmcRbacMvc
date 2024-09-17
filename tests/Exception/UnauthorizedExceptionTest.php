<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mvc\Exception;

use Lmc\Rbac\Mvc\Exception\UnauthorizedException;
use PHPUnit\Framework\TestCase;

class UnauthorizedExceptionTest extends TestCase
{
    public function testUnauthorizedException(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('You are not authorized to access this resource');
        throw new UnauthorizedException();
    }

    public function testUnauthorizedExceptionMessage(): void
    {
        $this->expectExceptionMessage('foo');
        throw new UnauthorizedException('foo');
    }
}
