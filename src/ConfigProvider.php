<?php

namespace Lmc\Rbac\Mvc;

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
                'Lmc\Rbac\Mvc\Guards' => \Lmc\Rbac\Mvc\Guard\GuardsFactory::class,

                /* Factories that map to a class */
                \Lmc\Rbac\Mvc\Guard\GuardPluginManager::class                => \Lmc\Rbac\Mvc\Guard\GuardPluginManagerFactory::class,
                \Lmc\Rbac\Mvc\Identity\AuthenticationIdentityProvider::class => \Lmc\Rbac\Mvc\Identity\AuthenticationIdentityProviderFactory::class,
                \Lmc\Rbac\Mvc\Options\ModuleOptions::class                   => \Lmc\Rbac\Mvc\Options\ModuleOptionsFactory::class,
                \Lmc\Rbac\Mvc\Service\AuthorizationService::class            => \Lmc\Rbac\Mvc\Service\AuthorizationServiceFactory::class,
                \Lmc\Rbac\Mvc\Service\RoleService::class                     => \Lmc\Rbac\Mvc\Service\RoleServiceFactory::class,
                \Lmc\Rbac\Mvc\View\Strategy\RedirectStrategy::class          => \Lmc\Rbac\Mvc\View\Strategy\RedirectStrategyFactory::class,
                \Lmc\Rbac\Mvc\View\Strategy\UnauthorizedStrategy::class      => \Lmc\Rbac\Mvc\View\Strategy\UnauthorizedStrategyFactory::class,
            ],
        ];
    }

    public function getModuleConfig(): array
    {
        return [
            // Guard plugin manager
            'guard_manager' => [],
        ];
    }

    public function getControllerPluginConfig(): array
    {
        return [
            'factories' => [
                \Lmc\Rbac\Mvc\Mvc\Controller\Plugin\IsGranted::class => \Lmc\Rbac\Mvc\Mvc\Controller\Plugin\IsGrantedPluginFactory::class,
            ],
            'aliases' => [
                'isGranted' => \Lmc\Rbac\Mvc\Mvc\Controller\Plugin\IsGranted::class,
            ],
        ];
    }

    public function getViewHelperConfig(): array
    {
        return [
            'factories' => [
                \Lmc\Rbac\Mvc\View\Helper\IsGranted::class => \Lmc\Rbac\Mvc\View\Helper\IsGrantedViewHelperFactory::class,
                \Lmc\Rbac\Mvc\View\Helper\HasRole::class   => \Lmc\Rbac\Mvc\View\Helper\HasRoleViewHelperFactory::class,
            ],
            'aliases' => [
                'isGranted' => \Lmc\Rbac\Mvc\View\Helper\IsGranted::class,
                'hasRole'   => \Lmc\Rbac\Mvc\View\Helper\HasRole::class,
            ],
        ];
    }

    public function getViewManagerConfig(): array
    {
        return [
            'template_map' => [
                'error/403'                             => __DIR__ . '/../view/error/403.phtml',
            ],
        ];
    }
}
