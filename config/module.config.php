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

return [
    'service_manager' => [
        'factories' => [
            /* Factories that do not map to a class */
            'LaminasRbac\Guards' => \LaminasRbac\Factory\GuardsFactory::class,

            /* Factories that map to a class */
            \Rbac\Rbac::class                                           => \LaminasRbac\Factory\RbacFactory::class,
            \LaminasRbac\Assertion\AssertionPluginManager::class        => \LaminasRbac\Factory\AssertionPluginManagerFactory::class,
            \LaminasRbac\Collector\RbacCollector::class                 => \Laminas\ServiceManager\Factory\InvokableFactory::class,
            \LaminasRbac\Guard\GuardPluginManager::class                => \LaminasRbac\Factory\GuardPluginManagerFactory::class,
            \LaminasRbac\Identity\AuthenticationIdentityProvider::class => \LaminasRbac\Factory\AuthenticationIdentityProviderFactory::class,
            \LaminasRbac\Options\ModuleOptions::class                   => \LaminasRbac\Factory\ModuleOptionsFactory::class,
            \LaminasRbac\Role\RoleProviderPluginManager::class          => \LaminasRbac\Factory\RoleProviderPluginManagerFactory::class,
            \LaminasRbac\Service\AuthorizationService::class            => \LaminasRbac\Factory\AuthorizationServiceFactory::class,
            \LaminasRbac\Service\RoleService::class                     => \LaminasRbac\Factory\RoleServiceFactory::class,
            \LaminasRbac\View\Strategy\RedirectStrategy::class          => \LaminasRbac\Factory\RedirectStrategyFactory::class,
            \LaminasRbac\View\Strategy\UnauthorizedStrategy::class      => \LaminasRbac\Factory\UnauthorizedStrategyFactory::class,
        ],
    ],

    'view_helpers' => [
        'factories' => [
            \LaminasRbac\View\Helper\IsGranted::class => \LaminasRbac\Factory\IsGrantedViewHelperFactory::class,
            \LaminasRbac\View\Helper\HasRole::class   => \LaminasRbac\Factory\HasRoleViewHelperFactory::class,
        ],
        'aliases' => [
            'isGranted' => \LaminasRbac\View\Helper\IsGranted::class,
            'hasRole'   => \LaminasRbac\View\Helper\HasRole::class,
        ]
    ],

    'controller_plugins' => [
        'factories' => [
            \LaminasRbac\Mvc\Controller\Plugin\IsGranted::class => \LaminasRbac\Factory\IsGrantedPluginFactory::class,
        ],
        'aliases' => [
            'isGranted' => \LaminasRbac\Mvc\Controller\Plugin\IsGranted::class,
        ]
    ],

    'view_manager' => [
        'template_map' => [
            'error/403'                             => __DIR__ . '/../view/error/403.phtml',
            'laminas-developer-tools/toolbar/laminas-rbac' => __DIR__ . '/../view/laminas-developer-tools/toolbar/laminas-rbac.phtml'
        ]
    ],

    'laminas-developer-tools' => [
        'profiler' => [
            'collectors' => [
                'laminas_rbac' => \LaminasRbac\Collector\RbacCollector::class,
            ],
        ],
        'toolbar' => [
            'entries' => [
                'laminas_rbac' => 'laminas-developer-tools/toolbar/zfc-rbac',
            ],
        ],
    ],

    'laminas_rbac' => [
        // Guard plugin manager
        'guard_manager' => [],

        // Role provider plugin manager
        'role_provider_manager' => [],

        // Assertion plugin manager
        'assertion_manager' => []
    ]
];
