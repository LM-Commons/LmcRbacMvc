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

namespace LmcRbacMvcTest\Service;

use LmcRbacMvc\Service\AuthorizationService;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass('\LmcRbacMvc\Service\AuthorizationService')]
class AuthorizationServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testGetIdentity()
    {
        $identity         = $this->createMock('LmcRbacMvc\Identity\IdentityInterface');
        $roleService      = $this->createMock('LmcRbacMvc\Service\RoleService');
        $roleService->expects($this->once())->method('getIdentity')->willReturn($identity);
        $authorizationService    = new AuthorizationService(
            $roleService,
            $this->createMock('\Lmc\Rbac\Service\AuthorizationServiceInterface')
        );
        $this->assertSame($authorizationService->getIdentity(), $identity);
    }

    public function testAssertionSettersGetters(): void
    {
        $baseAuthorizationService = $this->createMock('\Lmc\Rbac\Service\AuthorizationServiceInterface');
        $authorizationService = new AuthorizationService(
            $this->createMock('LmcRbacMvc\Service\RoleService'),
            $baseAuthorizationService
        );

        $baseAuthorizationService->expects($this->once())->method('hasAssertion');
        $baseAuthorizationService->expects($this->once())->method('getAssertion');
        $baseAuthorizationService->expects($this->once())->method('getAssertions');
        $baseAuthorizationService->expects($this->once())->method('setAssertion');
        $baseAuthorizationService->expects($this->once())->method('setAssertions');

        $authorizationService->hasAssertion('foo');
        $authorizationService->getAssertion('foo');
        $authorizationService->getAssertions();
        $authorizationService->setAssertions([]);
        $authorizationService->setAssertion('foo', 'bar');
    }

    public function testIsGranted(): void
    {
        $identity         = $this->createMock('Lmc\Rbac\Identity\IdentityInterface');
        $roleService      = $this->createMock('LmcRbacMvc\Service\RoleService');
        $roleService->expects($this->once())->method('getIdentity')->willReturn($identity);
        $baseAuthorizationService = $this->createMock('\Lmc\Rbac\Service\AuthorizationServiceInterface');
        $baseAuthorizationService->expects($this->once())->method('isGranted')->with($identity);

        $authorizationService = new AuthorizationService($roleService, $baseAuthorizationService);
        $authorizationService->isGranted('foo');
    }
}
