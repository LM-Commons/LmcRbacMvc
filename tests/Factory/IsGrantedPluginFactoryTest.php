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

namespace LmcRbacTest\Factory;

use Laminas\Mvc\Controller\PluginManager;
use Laminas\ServiceManager\ServiceManager;
use LmcRbac\Factory\IsGrantedPluginFactory;

/**
 * @covers \LmcRbac\Factory\IsGrantedPluginFactory
 */
class IsGrantedPluginFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Dependency on zend-servicemanager v2 removed
     */
    /*
    public function testFactory()
    {
        $serviceManager = new ServiceManager();

        if (method_exists($serviceManager, 'build')) {
            $this->markTestSkipped('this test is only vor zend-servicemanager v2');
        }

        $pluginManager  = new PluginManager($serviceManager);

        $serviceManager->setService(
            'LmcRbac\Service\AuthorizationService',
            $this->getMock('LmcRbac\Service\AuthorizationServiceInterface')
        );

        $factory   = new IsGrantedPluginFactory();
        $isGranted = $factory->createService($pluginManager);

        $this->assertInstanceOf('LmcRbac\Mvc\Controller\Plugin\IsGranted', $isGranted);
    }
    */
    public function testFactoryV3()
    {
        $serviceManager = new ServiceManager();

        if (! method_exists($serviceManager, 'build')) {
            $this->markTestSkipped('this test is only vor zend-servicemanager v3');
        }
        $serviceManager->setService(
            'LmcRbac\Service\AuthorizationService',
            $this->getMock('LmcRbac\Service\AuthorizationServiceInterface')
        );

        $factory   = new IsGrantedPluginFactory();
        $isGranted = $factory($serviceManager, 'LmcRbac\Mvc\Controller\Plugin\IsGranted');

        $this->assertInstanceOf('LmcRbac\Mvc\Controller\Plugin\IsGranted', $isGranted);
    }
}
