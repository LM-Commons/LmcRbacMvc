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

use Lmc\Rbac\Mvc\Options\ModuleOptions;
use LmcTest\Rbac\Mvc\Util\ServiceManagerFactory;

/**
 * @covers \Lmc\Rbac\Mvc\Options\ModuleOptions
 */
class ModuleOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testAssertModuleDefaultOptions()
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = ServiceManagerFactory::getServiceManager()->get('Lmc\Rbac\Mvc\Options\ModuleOptions');

        $this->assertEquals('allow', $moduleOptions->getProtectionPolicy());
        $this->assertIsArray($moduleOptions->getGuards());
        $this->assertInstanceOf('Lmc\Rbac\Mvc\Options\UnauthorizedStrategyOptions', $moduleOptions->getUnauthorizedStrategy());
        $this->assertInstanceOf('Lmc\Rbac\Mvc\Options\RedirectStrategyOptions', $moduleOptions->getRedirectStrategy());
        $this->assertEquals('Lmc\Rbac\Mvc\Identity\AuthenticationIdentityProvider', $moduleOptions->getIdentityProvider());
    }

    public function testSettersAndGetters()
    {
        $moduleOptions = new ModuleOptions([
            'identity_provider' => 'foo',
            'guards'                => [],
            'protection_policy'     => 'deny',
            'unauthorized_strategy' => [
                'template' => 'error/unauthorized'
            ],
            'redirect_strategy' => [
                'redirect_to_route_connected'    => 'home',
                'redirect_to_route_disconnected' => 'login'
            ]
        ]);

        $this->assertEquals([], $moduleOptions->getGuards());
        $this->assertEquals('foo', $moduleOptions->getIdentityProvider());
        $this->assertEquals('deny', $moduleOptions->getProtectionPolicy());
        $this->assertInstanceOf('Lmc\Rbac\Mvc\Options\UnauthorizedStrategyOptions', $moduleOptions->getUnauthorizedStrategy());
        $this->assertInstanceOf('Lmc\Rbac\Mvc\Options\RedirectStrategyOptions', $moduleOptions->getRedirectStrategy());
    }

    public function testThrowExceptionForInvalidProtectionPolicy()
    {
        $this->expectException(\Lmc\Rbac\Exception\RuntimeException::class);

        $moduleOptions = new ModuleOptions();
        $moduleOptions->setProtectionPolicy('invalid');
    }
}
