<?php

namespace LmcRbacMvcTest;

use Lmc\Rbac\Mvc\ConfigProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Rbac\Mvc\ConfigProvider
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
