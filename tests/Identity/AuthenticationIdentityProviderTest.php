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

namespace LmcTest\Rbac\Mvc\Identity;

use Laminas\Authentication\AuthenticationService;
use Lmc\Rbac\Mvc\Identity\AuthenticationIdentityProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Rbac\Mvc\Identity\AuthenticationIdentityProvider
 */
class AuthenticationIdentityProviderTest extends TestCase
{
    /**
     * @var AuthenticationIdentityProvider
     */
    protected AuthenticationIdentityProvider $identityProvider;

    /**
     * @var AuthenticationService|MockObject
     */
    protected AuthenticationService|MockObject $authenticationService;

    public function setUp() :void
    {
        $this->authenticationService = $this->createMock('Laminas\Authentication\AuthenticationService');
        $this->identityProvider = new AuthenticationIdentityProvider($this->authenticationService);
    }

    public function testCanReturnIdentity()
    {
        $identity = $this->createMock('Lmc\Rbac\Identity\IdentityInterface');

        $this->authenticationService->expects($this->once())
                                    ->method('getIdentity')
                                    ->willReturn($identity);

        $this->assertSame($identity, $this->identityProvider->getIdentity());
    }
}
