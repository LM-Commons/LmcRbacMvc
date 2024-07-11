<?php

namespace LmcRbacMvcTest\Assertion;

use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Assertion\AssertionPluginManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LmcRbacMvc\Assertion\AssertionPluginManager
 */
class AssertionPluginManagerTest extends TestCase
{
    public function testAssertionPluginManager()
    {
        // Simple test to check validation against AssertionInterface
        $serviceManager = new ServiceManager();
        $pluginManager = new AssertionPluginManager($serviceManager, []);
        $assertion = new \StdClass();
        $this->expectException(InvalidServiceException::class);
        $pluginManager->validate($assertion);
    }
}
