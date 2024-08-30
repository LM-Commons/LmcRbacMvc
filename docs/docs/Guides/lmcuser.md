---
title: Using LmcRbacMvc and LmcUser
sidebar_label: Using LmcRbacMvc and LmcUser
sidebar_position: 3
---

To use the authentication service from LmcUser, there are a few actions to take:

1. Set up a user entity that supports roles
2. Set up the Laminas authentication service to use LmcUser
3. Set up guards
4. Optionally, set up a redirect strategy

## Set up the user entity

The default user entity class in LmcUser does not implement the `Lmc\Rbac\Identity\IdentityInterface`. You will need
to define a user identity class that does by, for example, extending the default user class:

```php
<?php

class CustomUser extends \LmcUser\Entity\User implements \Lmc\Rbac\Identity\IdentityInterface
{
    private array $roles = ['user'];  // let's make 'user' the default role

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;
        return $this;
    }
}
````

and then set LmcUser to use this entity:

```php
return [
    'lmc_user' => [
        'user_entity_class' => CustomUser::class,
    ]
];
```
:::note
This example does not cover modifying the database tables and mappers to fetch/hydrate roles into the user object.
:::

## Set up the Laminas authentication service to use LmcUser

You need to set up Laminas Authentication to use the Authentication service from LmcUser.

Add the following alias in your `application.config.php`:

```php
return [
    'service_manager' => [
        'aliases' => [
            'Laminas\Authentication\AuthenticationService' => 'lmcuser_auth_service'
        ]
    ]
];
```

## Set up guards

The login and register pages should be only accessible from 'guest' users and all other pages should be accessible from 
logged in users.

Add the LmcUser routes to your `guards`:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            \Lmc\Rbac\Mvc\Guard\RouteGuard::class => [
                'lmcuser/login'    => ['guest'],
                'lmcuser/register' => ['guest'], // required if registration is enabled
                'lmcuser*'         => ['user'] // includes logout, changepassword and changeemail
            ]
        ]
    ]
];
```

## Set up a redirect strategy

If you would like for your application to redirect users to the login page when an unauthorized access is detected, then
you need to setup the `RedirectStrategy`:

In your application.config.php:
```php

return [
    'listeners' => [
        \Lmc\Rbac\Mvc\View\Strategy\RedirectStrategy::class,
    ],
];
```

In your LmcRbac config file

```php
return [
    'lmc_rbac' => [
        'redirect_strategy' => [
            'redirect_when_connected'        => true,
            'redirect_to_route_connected'    => 'home',
            'redirect_to_route_disconnected' => 'lmcuser/login',
            'append_previous_uri'            => true,
            'previous_uri_query_key'         => 'redirectTo'
        ],
    ]
];
```

