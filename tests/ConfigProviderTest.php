<?php

namespace LmcRbacMvcTest;

use LmcRbacMvc\ConfigProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LmcRbacMvc\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    public function testConfigProvider()
    {
        $provider = new ConfigProvider();
        $this->assertIsArray($provider());
        $this->assertArrayHasKey('dependencies', $provider());
        $this->assertArrayHasKey('factories', $provider->getDependencies());
        $this->assertArrayHasKey('controller_plugins', $provider());
        $this->assertArrayHasKey('view_manager', $provider());
        $this->assertArrayHasKey('lmc_rbac', $provider());
        $this->assertArrayHasKey('view_helpers', $provider());
    }
}
