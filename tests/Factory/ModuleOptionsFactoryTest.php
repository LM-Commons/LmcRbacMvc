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

namespace LmcRbacMvcTest\Factory;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Factory\ModuleOptionsFactory;

/**
 * @covers \LmcRbacMvc\Factory\ModuleOptionsFactory
 */
class ModuleOptionsFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $config = ['lmc_rbac' => []];

        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', $config);

        $factory = new ModuleOptionsFactory();
        $options = $factory($serviceManager, 'LmcRbacMvc\Options\ModuleOptions' );

        $this->assertInstanceOf('LmcRbacMvc\Options\ModuleOptions', $options);
    }

    public function testFactoryNotCreatedException()
    {
        $config = [];

        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', $config);

        $this->expectException(ServiceNotCreatedException::class);
        $factory = new ModuleOptionsFactory();
        $options = $factory($serviceManager, 'LmcRbacMvc\Options\ModuleOptions' );
    }
}
