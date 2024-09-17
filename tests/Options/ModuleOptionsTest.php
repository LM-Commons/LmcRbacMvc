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

namespace LmcTest\Rbac\Mvc\Options;

use Lmc\Rbac\Exception\RuntimeException;
use Lmc\Rbac\Mvc\Identity\AuthenticationIdentityProvider;
use Lmc\Rbac\Mvc\Options\ModuleOptions;
use Lmc\Rbac\Mvc\Options\RedirectStrategyOptions;
use Lmc\Rbac\Mvc\Options\UnauthorizedStrategyOptions;
use LmcTest\Rbac\Mvc\Util\ServiceManagerFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Rbac\Mvc\Options\ModuleOptions
 */
class ModuleOptionsTest extends TestCase
{
    public function testAssertModuleDefaultOptions(): void
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = ServiceManagerFactory::getServiceManager()->get(ModuleOptions::class);

        $this->assertEquals('allow', $moduleOptions->getProtectionPolicy());
        $this->assertIsArray($moduleOptions->getGuards());
        $this->assertInstanceOf(UnauthorizedStrategyOptions::class, $moduleOptions->getUnauthorizedStrategy());
        $this->assertInstanceOf(RedirectStrategyOptions::class, $moduleOptions->getRedirectStrategy());
        $this->assertEquals(AuthenticationIdentityProvider::class, $moduleOptions->getIdentityProvider());
    }

    public function testSettersAndGetters(): void
    {
        $moduleOptions = new ModuleOptions([
            'identity_provider'     => 'foo',
            'guards'                => [],
            'protection_policy'     => 'deny',
            'unauthorized_strategy' => [
                'template' => 'error/unauthorized',
            ],
            'redirect_strategy'     => [
                'redirect_to_route_connected'    => 'home',
                'redirect_to_route_disconnected' => 'login',
            ],
            'guard_manager'         => [],
        ]);

        $this->assertEquals([], $moduleOptions->getGuards());
        $this->assertEquals('foo', $moduleOptions->getIdentityProvider());
        $this->assertEquals('deny', $moduleOptions->getProtectionPolicy());
        $this->assertInstanceOf(UnauthorizedStrategyOptions::class, $moduleOptions->getUnauthorizedStrategy());
        $this->assertInstanceOf(RedirectStrategyOptions::class, $moduleOptions->getRedirectStrategy());
        $this->assertEquals([], $moduleOptions->getGuardManager());
    }

    public function testThrowExceptionForInvalidProtectionPolicy(): void
    {
        $this->expectException(RuntimeException::class);

        $moduleOptions = new ModuleOptions();
        $moduleOptions->setProtectionPolicy('invalid');
    }
}
