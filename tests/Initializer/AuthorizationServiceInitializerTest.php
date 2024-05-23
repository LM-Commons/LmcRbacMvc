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
namespace LmcRbacMvcTest\Initializer;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Initializer\AuthorizationServiceInitializer;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers  \LmcRbacMvc\Initializer\AuthorizationServiceInitializer
 * @author  Aeneas Rekkas
 * @license MIT License
 */
class AuthorizationServiceInitializerTest extends TestCase
{
    use ProphecyTrait;

    public function testInitializer()
    {
        $authServiceClassName = 'LmcRbacMvc\Service\AuthorizationService';
        $initializer          = new AuthorizationServiceInitializer();
        $instance             = new AuthorizationAwareFake();

        $authorizationService = $this->createMock('LmcRbacMvc\Service\AuthorizationService');

        $serviceManager = new ServiceManager();
        $serviceManager->setService(\LmcRbacMvc\Service\AuthorizationService::class, $authorizationService);

        $initializer($serviceManager, $instance);

        $this->assertEquals($authorizationService, $instance->getAuthorizationService());
    }
}
