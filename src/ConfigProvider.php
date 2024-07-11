<?php

namespace LmcRbacMvc;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'view_helpers' => $this->getViewHelperConfig(),
            'controller_plugins' => $this->getControllerPluginConfig(),
            'view_manager' => $this->getViewManagerConfig(),
            'lmc_rbac' => $this->getModuleConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                /* Factories that do not map to a class */
                'LmcRbacMvc\Guards' => \LmcRbacMvc\Guard\GuardsFactory::class,

                /* Factories that map to a class */
                \LmcRbacMvc\Assertion\AssertionPluginManager::class        => \LmcRbacMvc\Assertion\AssertionPluginManagerFactory::class,
                \LmcRbacMvc\Guard\GuardPluginManager::class                => \LmcRbacMvc\Guard\GuardPluginManagerFactory::class,
                \LmcRbacMvc\Identity\AuthenticationIdentityProvider::class => \LmcRbacMvc\Identity\AuthenticationIdentityProviderFactory::class,
                \LmcRbacMvc\Options\ModuleOptions::class                   => \LmcRbacMvc\Options\ModuleOptionsFactory::class,
                \LmcRbacMvc\Role\RoleProviderPluginManager::class          => \LmcRbacMvc\Role\RoleProviderPluginManagerFactory::class,
                \LmcRbacMvc\Service\AuthorizationService::class            => \LmcRbacMvc\Service\AuthorizationServiceFactory::class,
                \LmcRbacMvc\Service\RoleService::class                     => \LmcRbacMvc\Service\RoleServiceFactory::class,
                \LmcRbacMvc\View\Strategy\RedirectStrategy::class          => \LmcRbacMvc\View\Strategy\RedirectStrategyFactory::class,
                \LmcRbacMvc\View\Strategy\UnauthorizedStrategy::class      => \LmcRbacMvc\View\Strategy\UnauthorizedStrategyFactory::class,
            ],
        ];
    }

    public function getModuleConfig(): array
    {
        return [
            // Guard plugin manager
            'guard_manager' => [],

            // Role provider plugin manager
            'role_provider_manager' => [],

            // Assertion plugin manager
            'assertion_manager' => []
        ];
    }

    public function getControllerPluginConfig(): array
    {
        return [
            'factories' => [
                \LmcRbacMvc\Mvc\Controller\Plugin\IsGranted::class => \LmcRbacMvc\Mvc\Controller\Plugin\IsGrantedPluginFactory::class,
            ],
            'aliases' => [
                'isGranted' => \LmcRbacMvc\Mvc\Controller\Plugin\IsGranted::class,
            ],
        ];
    }

    public function getViewHelperConfig(): array
    {
        return [
            'factories' => [
                \LmcRbacMvc\View\Helper\IsGranted::class => \LmcRbacMvc\View\Helper\IsGrantedViewHelperFactory::class,
                \LmcRbacMvc\View\Helper\HasRole::class   => \LmcRbacMvc\View\Helper\HasRoleViewHelperFactory::class,
            ],
            'aliases' => [
                'isGranted' => \LmcRbacMvc\View\Helper\IsGranted::class,
                'hasRole'   => \LmcRbacMvc\View\Helper\HasRole::class,
            ],
        ];
    }

    public function getViewManagerConfig(): array
    {
        return [
            'template_map' => [
                'error/403'                             => __DIR__ . '/../view/error/403.phtml',
                'laminas-developer-tools/toolbar/lmc-rbac' => __DIR__ . '/../view/laminas-developer-tools/toolbar/lmc-rbac.phtml'
            ],
        ];
    }
}
