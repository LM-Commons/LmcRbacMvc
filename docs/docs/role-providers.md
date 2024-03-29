---
sidebar_position: 4
---
# Role providers


In this section, you will learn:

* What are role providers
* What are identity providers
* How to use and configure built-in providers
* How to create custom role providers

## What are role providers?

A role provider is an object that returns a list of roles. Each role provider must implement the
`LmcRbacMvc\Role\RoleProviderInterface` interface. The only required method is `getRoles`, and must return an array
of `Rbac\Role\RoleInterface` objects.

Roles can come from one of many sources: in memory, from a file, from a database... However, please note that 
you can specify only one role provider per application. The reason is that having multiple role providers makes
the workflow harder and can lead to security problems that are very hard to spot.

## Identity providers?

Identity providers return the current identity. Most of the time, this means the logged in user. LmcRbacMvc comes with a
default identity provider (`LmcRbacMvc\Identity\AuthenticationIdentityProvider`) that uses the
`Laminas\Authentication\AuthenticationService` service.

### Create your own identity provider

If you want to implement your own identity provider, create a new class that implements
`LmcRbacMvc\Identity\IdentityProviderInterface` class. Then, change the `identity_provider` option in LmcRbacMvc config,
as shown below:

```php
return [
    'lmc_rbac' => [
        'identity_provider' => 'MyCustomIdentityProvider'
    ]
];
```

The identity provider is automatically pulled from the service manager.

## Built-in role providers

LmcRbacMvc comes with two built-in role providers: `InMemoryRoleProvider` and `ObjectRepositoryRoleProvider`. A role
provider must be added to the `role_provider` subkey:

```php
return [
    'lmc_rbac' => [
        'role_provider' => [
            // Role provider config here!
        ]
    ]
];
```

### InMemoryRoleProvider

This provider is ideal for small/medium sites with few roles/permissions. All the data is specified in a simple
PHP file, so you never hit a database.

Here is an example of the format you need to use:

```php
return [
    'lmc_rbac' => [
        'role_provider' => [
            'LmcRbacMvc\Role\InMemoryRoleProvider' => [
                'admin' => [
                    'children'    => ['member'],
                    'permissions' => ['article.delete']
                ],
                'member' => [
                    'children'    => ['guest'],
                    'permissions' => ['article.edit', 'article.archive']
                ],
                'guest' => [
                    'permissions' => ['article.read']
                ]
            ]
        ]
    ]
];
```

The `children` and `permissions` subkeys are entirely optional. Internally, the `InMemoryRoleProvider` creates
either a `Rbac\Role\Role` object if the role does not have any children, or a `Rbac\Role\HierarchicalRole` if
the role has at least one child.

If you are more confident with flat RBAC, the previous config can be re-written to remove any inheritence between roles:

```php
return [
    'lmc_rbac' => [
        'role_provider' => [
            'LmcRbacMvc\Role\InMemoryRoleProvider' => [
                'admin' => [
                    'permissions' => [
                        'article.delete',
                        'article.edit',
                        'article.archive',
                        'article.read'
                    ]
                ],
                'member' => [
                    'permissions' => [
                        'article.edit',
                        'article.archive',
                        'article.read'
                    ]
                ],
                'guest' => [
                    'permissions' => ['article.read']
                ]
            ]
        ]
    ]
];
```

### ObjectRepositoryRoleProvider

This provider fetches roles from the database using `Doctrine\Common\Persistence\ObjectRepository` interface.

You can configure this provider by giving an object repository service name that is fetched from the service manager
using the `object_repository` key:

```php
return [
    'lmc_rbac' => [
        'role_provider' => [
            'LmcRbacMvc\Role\ObjectRepositoryRoleProvider' => [
                'object_repository'  => 'App\Repository\RoleRepository',
                'role_name_property' => 'name'
            ]
        ]
    ]
];
```

Or you can specify the `object_manager` and `class_name` options:

```php
return [
    'lmc_rbac' => [
        'role_provider' => [
            'LmcRbacMvc\Role\ObjectRepositoryRoleProvider' => [
                'object_manager'     => 'doctrine.entitymanager.orm_default',
                'class_name'         => 'App\Entity\Role',
                'role_name_property' => 'name'
            ]
        ]
    ]
];
```

In both cases, you need to specify the `role_name_property` value, which is the name of the entity's property
that holds the actual role name. This is used internally to only load the identity roles, instead of loading
the whole table every time.

Please note that your entity fetched from the table MUST implement the `Rbac\Role\RoleInterface` interface.

## Creating custom role providers

To create a custom role provider, you first need to create a class that implements the `LmcRbacMvc\Role\RoleProviderInterface`
interface.

Then, you need to add it to the role provider manager:

```php
return [
    'lmc_rbac' => [
        'role_provider_manager' => [
            'factories' => [
                'Application\Role\CustomRoleProvider' => 'Application\Factory\CustomRoleProviderFactory'
            ]
        ]    
    ]
];
```

You can now use it like any other role provider:

```php
return [
    'lmc_rbac' => [
        'role_provider' => [
            'Application\Role\CustomRoleProvider' => [
                // Options
            ]
        ]
    ]
];
```

