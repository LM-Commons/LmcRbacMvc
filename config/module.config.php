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
            'LmcRbac\Guards' => \LmcRbac\Factory\GuardsFactory::class,

            /* Factories that map to a class */
            \Rbac\Rbac::class                                           => \LmcRbac\Factory\RbacFactory::class,
            \LmcRbac\Assertion\AssertionPluginManager::class        => \LmcRbac\Factory\AssertionPluginManagerFactory::class,
            \LmcRbac\Collector\RbacCollector::class                 => \Laminas\ServiceManager\Factory\InvokableFactory::class,
            \LmcRbac\Guard\GuardPluginManager::class                => \LmcRbac\Factory\GuardPluginManagerFactory::class,
            \LmcRbac\Identity\AuthenticationIdentityProvider::class => \LmcRbac\Factory\AuthenticationIdentityProviderFactory::class,
            \LmcRbac\Options\ModuleOptions::class                   => \LmcRbac\Factory\ModuleOptionsFactory::class,
            \LmcRbac\Role\RoleProviderPluginManager::class          => \LmcRbac\Factory\RoleProviderPluginManagerFactory::class,
            \LmcRbac\Service\AuthorizationService::class            => \LmcRbac\Factory\AuthorizationServiceFactory::class,
            \LmcRbac\Service\RoleService::class                     => \LmcRbac\Factory\RoleServiceFactory::class,
            \LmcRbac\View\Strategy\RedirectStrategy::class          => \LmcRbac\Factory\RedirectStrategyFactory::class,
            \LmcRbac\View\Strategy\UnauthorizedStrategy::class      => \LmcRbac\Factory\UnauthorizedStrategyFactory::class,
        ],
    ],

    'view_helpers' => [
        'factories' => [
            \LmcRbac\View\Helper\IsGranted::class => \LmcRbac\Factory\IsGrantedViewHelperFactory::class,
            \LmcRbac\View\Helper\HasRole::class   => \LmcRbac\Factory\HasRoleViewHelperFactory::class,
        ],
        'aliases' => [
            'isGranted' => \LmcRbac\View\Helper\IsGranted::class,
            'hasRole'   => \LmcRbac\View\Helper\HasRole::class,
        ]
    ],

    'controller_plugins' => [
        'factories' => [
            \LmcRbac\Mvc\Controller\Plugin\IsGranted::class => \LmcRbac\Factory\IsGrantedPluginFactory::class,
        ],
        'aliases' => [
            'isGranted' => \LmcRbac\Mvc\Controller\Plugin\IsGranted::class,
        ]
    ],

    'view_manager' => [
        'template_map' => [
            'error/403'                             => __DIR__ . '/../view/error/403.phtml',
            'laminas-developer-tools/toolbar/lmc-rbac' => __DIR__ . '/../view/laminas-developer-tools/toolbar/lmc-rbac.phtml'
        ]
    ],

    'laminas-developer-tools' => [
        'profiler' => [
            'collectors' => [
                'lmc_rbac' => \LmcRbac\Collector\RbacCollector::class,
            ],
        ],
        'toolbar' => [
            'entries' => [
                'lmc_rbac' => 'laminas-developer-tools/toolbar/lmc-rbac',
            ],
        ],
    ],

    'lmc_rbac' => [
        // Guard plugin manager
        'guard_manager' => [],

        // Role provider plugin manager
        'role_provider_manager' => [],

        // Assertion plugin manager
        'assertion_manager' => []
    ]
];
