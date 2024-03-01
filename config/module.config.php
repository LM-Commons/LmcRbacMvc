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
            'LmcRbacMvc\Guards' => \LmcRbacMvc\Factory\GuardsFactory::class,

            /* Factories that map to a class */
            \LmcRbacMvc\Assertion\AssertionPluginManager::class        => \LmcRbacMvc\Factory\AssertionPluginManagerFactory::class,
            // TODO Remove RbacCollector once it is moved to a separate library
            \LmcRbacMvc\Collector\RbacCollector::class                 => \Laminas\ServiceManager\Factory\InvokableFactory::class,
            \LmcRbacMvc\Guard\GuardPluginManager::class                => \LmcRbacMvc\Factory\GuardPluginManagerFactory::class,
            \LmcRbacMvc\Identity\AuthenticationIdentityProvider::class => \LmcRbacMvc\Factory\AuthenticationIdentityProviderFactory::class,
            \LmcRbacMvc\Options\ModuleOptions::class                   => \LmcRbacMvc\Factory\ModuleOptionsFactory::class,
            \LmcRbacMvc\Role\RoleProviderPluginManager::class          => \LmcRbacMvc\Factory\RoleProviderPluginManagerFactory::class,
            \LmcRbacMvc\Service\AuthorizationService::class            => \LmcRbacMvc\Factory\AuthorizationServiceFactory::class,
            \LmcRbacMvc\Service\RoleService::class                     => \LmcRbacMvc\Factory\RoleServiceFactory::class,
            \LmcRbacMvc\View\Strategy\RedirectStrategy::class          => \LmcRbacMvc\Factory\RedirectStrategyFactory::class,
            \LmcRbacMvc\View\Strategy\UnauthorizedStrategy::class      => \LmcRbacMvc\Factory\UnauthorizedStrategyFactory::class,
        ],
    ],

    'view_helpers' => [
        'factories' => [
            \LmcRbacMvc\View\Helper\IsGranted::class => \LmcRbacMvc\Factory\IsGrantedViewHelperFactory::class,
            \LmcRbacMvc\View\Helper\HasRole::class   => \LmcRbacMvc\Factory\HasRoleViewHelperFactory::class,
        ],
        'aliases' => [
            'isGranted' => \LmcRbacMvc\View\Helper\IsGranted::class,
            'hasRole'   => \LmcRbacMvc\View\Helper\HasRole::class,
        ]
    ],

    'controller_plugins' => [
        'factories' => [
            \LmcRbacMvc\Mvc\Controller\Plugin\IsGranted::class => \LmcRbacMvc\Factory\IsGrantedPluginFactory::class,
        ],
        'aliases' => [
            'isGranted' => \LmcRbacMvc\Mvc\Controller\Plugin\IsGranted::class,
        ]
    ],

    'view_manager' => [
        'template_map' => [
            'error/403'                             => __DIR__ . '/../view/error/403.phtml',
            'laminas-developer-tools/toolbar/lmc-rbac' => __DIR__ . '/../view/laminas-developer-tools/toolbar/lmc-rbac.phtml'
        ]
    ],

    /*
     * Developer tools are now provided by the companion module LmcRbacMvcDevTools
     * You can still use the config below but you are encouraged to use the new module
     *
    'laminas-developer-tools' => [
        'profiler' => [
            'collectors' => [
                'lmc_rbac' => \LmcRbacMvc\Collector\RbacCollector::class,
            ],
        ],
        'toolbar' => [
            'entries' => [
                'lmc_rbac' => 'laminas-developer-tools/toolbar/lmc-rbac',
            ],
        ],
    ],
     */

    'lmc_rbac' => [
        // Guard plugin manager
        'guard_manager' => [],

        // Role provider plugin manager
        'role_provider_manager' => [],

        // Assertion plugin manager
        'assertion_manager' => []
    ]
];
