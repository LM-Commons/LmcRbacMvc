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

namespace LaminasRbacTest\View\Helper;

use LaminasRbac\View\Helper\IsGranted;
use LaminasRbacTest\Util\ServiceManagerFactory;

/**
 * @covers \LaminasRbac\View\Helper\IsGranted
 */
class IsGrantedTest extends \PHPUnit_Framework_TestCase
{
    public function testHelperIsRegistered()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $config = $serviceManager->get('Config');
        $this->assertArrayHasKey('view_helpers', $config);
        $viewHelpersConfig = $config['view_helpers'];
        $this->assertEquals('LaminasRbac\View\Helper\IsGranted', $viewHelpersConfig['aliases']['isGranted']);
        $this->assertEquals(
            'LaminasRbac\Factory\IsGrantedViewHelperFactory',
            $viewHelpersConfig['factories']['LaminasRbac\View\Helper\IsGranted']
        );
    }

    public function testCallAuthorizationService()
    {
        $authorizationService = $this->getMock('LaminasRbac\Service\AuthorizationServiceInterface');

        $authorizationService->expects($this->once())
                             ->method('isGranted')
                             ->with('edit')
                             ->will($this->returnValue(true));

        $helper = new IsGranted($authorizationService);

        $this->assertTrue($helper('edit'));
    }
}
