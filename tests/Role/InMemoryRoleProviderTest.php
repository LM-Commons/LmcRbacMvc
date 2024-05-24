<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace LmcRbacMvcTest\Role;

use LmcRbacMvc\Role\InMemoryRoleProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LmcRbacMvc\Role\InMemoryRoleProvider
 */
class InMemoryRoleProviderTest extends TestCase
{
    public function testInMemoryProvider()
    {
        $inMemoryProvider = new InMemoryRoleProvider([
            'system' => [
                'permissions' => ['all'],
            ],
            'admin' => [
                'children'    => ['member'],
                'permissions' => ['delete']
            ],
            'member' => [
                'children'    => ['guest'],
                'permissions' => ['write']
            ],
            'guest'
        ]);

        $roles = $inMemoryProvider->getRoles(['admin', 'member', 'guest', 'system']);

        $this->assertCount(4, $roles);

        // Test admin role
        $adminRole = $roles[0];
        $this->assertInstanceOf('Laminas\Permissions\Rbac\RoleInterface', $adminRole);
        $this->assertEquals('admin', $adminRole->getName());
        $this->assertTrue($adminRole->hasPermission('delete'));

        // Test member role
        $memberRole = $roles[1];
        $this->assertInstanceOf('Laminas\Permissions\Rbac\RoleInterface', $memberRole);
        $this->assertEquals('member', $memberRole->getName());
        $this->assertTrue($memberRole->hasPermission('write'));
        $this->assertFalse($memberRole->hasPermission('delete'));

        // Test guest role
        $guestRole = $roles[2];
        $this->assertInstanceOf('Laminas\Permissions\Rbac\RoleInterface', $guestRole);
        $this->assertEquals('guest', $guestRole->getName());
        $this->assertFalse($guestRole->hasPermission('write'));
        $this->assertFalse($guestRole->hasPermission('delete'));

        // Test system role
        $systemRole = $roles[3];
        $this->assertInstanceOf('Laminas\Permissions\Rbac\RoleInterface', $systemRole);
        $this->assertEquals('system', $systemRole->getName());
        $this->assertTrue($systemRole->hasPermission('all'));
        $this->assertFalse($systemRole->hasPermission('write'));
        $this->assertFalse($systemRole->hasPermission('delete'));

        $this->assertSame($adminRole->getChildren()[0], $memberRole);
        $this->assertSame($memberRole->getChildren()[0], $guestRole);
    }
}
