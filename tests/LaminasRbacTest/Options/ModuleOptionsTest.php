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

namespace LaminasRbacTest;

use LaminasRbac\Options\ModuleOptions;
use LaminasRbacTest\Util\ServiceManagerFactory;

/**
 * @covers \LaminasRbac\Options\ModuleOptions
 */
class ModuleOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertModuleDefaultOptions()
    {
        /** @var \LaminasRbac\Options\ModuleOptions $moduleOptions */
        $moduleOptions = ServiceManagerFactory::getServiceManager()->get('LaminasRbac\Options\ModuleOptions');

        $this->assertEquals('LaminasRbac\Identity\AuthenticationIdentityProvider', $moduleOptions->getIdentityProvider());
        $this->assertEquals('guest', $moduleOptions->getGuestRole());
        $this->assertEquals('allow', $moduleOptions->getProtectionPolicy());
        $this->assertInternalType('array', $moduleOptions->getGuards());
        $this->assertInternalType('array', $moduleOptions->getRoleProvider());
        $this->assertInternalType('array', $moduleOptions->getAssertionMap());
        $this->assertInstanceOf('LaminasRbac\Options\UnauthorizedStrategyOptions', $moduleOptions->getUnauthorizedStrategy());
        $this->assertInstanceOf('LaminasRbac\Options\RedirectStrategyOptions', $moduleOptions->getRedirectStrategy());
    }

    public function testSettersAndGetters()
    {
        $moduleOptions = new ModuleOptions([
            'identity_provider'     => 'IdentityProvider',
            'guest_role'            => 'unknown',
            'guards'                => [],
            'protection_policy'     => 'deny',
            'role_provider'         => [],
            'assertion_map'            => [
                'foo' => 'bar'
            ],
            'unauthorized_strategy' => [
                'template' => 'error/unauthorized'
            ],
            'redirect_strategy' => [
                'redirect_to_route_connected'    => 'home',
                'redirect_to_route_disconnected' => 'login'
            ]
        ]);

        $this->assertEquals('IdentityProvider', $moduleOptions->getIdentityProvider());
        $this->assertEquals('unknown', $moduleOptions->getGuestRole());
        $this->assertEquals([], $moduleOptions->getGuards());
        $this->assertEquals('deny', $moduleOptions->getProtectionPolicy());
        $this->assertEquals([], $moduleOptions->getRoleProvider());
        $this->assertEquals(['foo' => 'bar'], $moduleOptions->getAssertionMap());
        $this->assertInstanceOf('LaminasRbac\Options\UnauthorizedStrategyOptions', $moduleOptions->getUnauthorizedStrategy());
        $this->assertInstanceOf('LaminasRbac\Options\RedirectStrategyOptions', $moduleOptions->getRedirectStrategy());
    }

    public function testThrowExceptionForInvalidProtectionPolicy()
    {
        $this->setExpectedException('LaminasRbac\Exception\RuntimeException');

        $moduleOptions = new ModuleOptions();
        $moduleOptions->setProtectionPolicy('invalid');
    }

    public function testThrowExceptionIfMoreThanOneRoleProviderIsSet()
    {
        $this->setExpectedException('LaminasRbac\Exception\RuntimeException');

        $moduleOptions = new ModuleOptions();
        $moduleOptions->setRoleProvider(['foo', 'bar']);
    }
}
