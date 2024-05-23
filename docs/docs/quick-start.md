---
sidebar_position: 3
---

# Quick Start

In this section, you will learn:

* How to set up the module
* How to specify an identity provider
* How to add a simple role provider

Before starting the quick start, make sure you have properly installed the module by following the instructions in
the README file.

## Specifying an identity provider

By default, LmcRbacMvc internally uses the `Laminas\Authentication\AuthenticationService` service key to retrieve the user (logged or
not). Therefore, you must implement and register this service in your application by adding these lines in your `module.config.php` file:

```php
return [
    'service_manager' => [
        'factories' => [
	        'Laminas\Authentication\AuthenticationService' => function($sm) {
	            // Create your authentication service!
	        }
	    ]
    ]
];
```
:::tip
If you are also using the [LmcUser](https://github.com/lm-commons/lmcuser) package, then the `Laminas\Authentication\AuthenticationService` will be provided for you and there is no need to implement your own.
:::

The identity given by `Laminas\Authentication\AuthenticationService` must implement `LmcRbacMvc\Identity\IdentityInterface`.
:::warning
Note that the default identity provided with Laminas does not implement this interface, neither does the LmcUser suite.
:::

LmcRbacMvc is flexible enough to use something other than the built-in `AuthenticationService`, by specifying custom
identity providers. For more information, refer [to this section](role-providers.md#identity-providers).

## Adding a guard

A guard allows your application to block access to routes and/or controllers using a simple syntax. For instance, this configuration
grants access to any route that begins with `admin` (or is exactly `admin`) to the `admin` role only:

```php
return [
    'lmc_rbac' => [
        'guards' => [
	        'LmcRbacMvc\Guard\RouteGuard' => [
                'admin*' => ['admin']
	        ]
        ]
    ]
];
```

LmcRbacMvc has several built-in guards, and you can also register your own guards. For more information, refer
[to this section](guards.md#built-in-guards).

## Adding a role provider

RBAC model is based on roles. Therefore, for LmcRbacMvc to work properly, it must be aware of all the roles that are
used inside your application.

This configuration creates an *admin* role that has a child role called *member*. The *admin* role automatically
inherits the *member* permissions.

```php
return [
    'lmc_rbac' => [
        'role_provider' => [
	        'LmcRbacMvc\Role\InMemoryRoleProvider' => [
	            'admin' => [
	                'children'    => ['member'],
	                'permissions' => ['delete']
	            ],
		        'member' => [
		            'permissions' => ['edit']
		        ]
	        ]
	    ]
    ]
];
```

In this example, the *admin* role has two permissions: `delete` and `edit` (because it inherits the permissions from
its child), while the *member* role only has the `edit` permission.

LmcRbacMvc has several built-in role providers, and you can also register your own role providers. For more information,
refer [to this section](role-providers.md#built-in-role-providers).

## Registering a strategy

When a guard blocks access to a route/controller, or if you throw the `LmcRbacMvc\Exception\UnauthorizedException`
exception in your service, LmcRbacMvc automatically performs some logic for you depending on the view strategy used.

For instance, if you want LmcRbacMvc to automatically redirect all unauthorized requests to the "login" route, add
the following code in the `onBootstrap` method of your `Module.php` class:

```php
public function onBootstrap(MvcEvent $e)
{
    $app = $e->getApplication();
    $sm = $app->getServiceManager();
    $em = $app->getEventManager();
    
    $listener = $sm->get(\LmcRbacMvc\View\Strategy\RedirectStrategy::class);
    $listener->attach($em);
}
```

By default, `RedirectStrategy` redirects all unauthorized requests to a route named "login" when the user is not connected
and to a route named "home" when the user is connected. This is, of course, entirely configurable.

> For flexibility purposes, LmcRbacMvc **does not** register any strategy for you by default!

For more information about built-in strategies, refer [to this section](strategies.md#built-in-strategies).
[to this section](strategies.md)

## Using the authorization service

Now that LmcRbacMvc is properly configured, you can inject the authorization service into any class and use it to check
if the current identity is granted to do something.

The authorization service is registered inside the service manager using the following key: `LmcRbacMvc\Service\AuthorizationService`.
Once injected, you can use it as follows:

```php
use LmcRbacMvc\Exception\UnauthorizedException;

class ActionController extends  \Laminas\Mvc\Controller\AbstractActionController {
public function delete()
{
    if (!$this->authorizationService->isGranted('delete')) {
        throw new UnauthorizedException();
    }

    // Delete the post
}
}
```
