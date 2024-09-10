---
title: Getting Started
sidebar_label: Getting Started
sidebar_position: 1
---

LmcRbacMvc is a companion component that extends the functionality of LmcRbac to provide Role-based Access Control 
(RBAC) for Laminas MVC applications.

LmcRbacMvc provides additional features on top of LmcRbac that are suitable for a Laminas MVC application:

- Guards that acts like a firewall allowing access to routes, controllers and actions to authorized users.
- Strategies to execute when unauthorized access occurs such as redirection and error responses
- Extensions to LmcRbac Authorization service such as controller and view plugins

:::tip[Important Note:]
 
If you are migrating from v3, there are breaking changes to take into account. See the [Upgrading](Upgrading/upgrade.md) section for details.
:::

## Extending LmcRbac

LmcRbacMvc extends the functionality of LmcRbac to support Laminas MVC applications.

It is highly recommended to first go through the concepts and usage of LmcRbac before using the functionalities of 
LmcRbacMvc. 

<!--
## How can I integrate LmcRbacMvc into my application?

LmcRbacMvc offers multiple ways to protect your application:

* Using **Guards**: these classes act as "firewalls" that block access to routes and/or controllers. Guards are usually
  configured using PHP arrays, and are executed early in the MVC dispatch process. Typically this happens right after
  the route has been matched.
* Using **AuthorizationService**: a complementary method is to use the `AuthorizationService` class and inject it into your
  service classes to protect them from unwanted access.

While it is advised to use both methods to make your application even more secure, this is completely optional and you
can choose either of them independently.

To find out about how you can easily make your existing application more secure, please refer to the following section:

* [Cookbook: A real world example](cookbook.md#a-real-world-application)
-->

## Requirements

- PHP 8.1 or higher
- LmcRbac v2 (installed by default)

### Optional requirements

- [LmcRbacMvcDeveloperTools](https://github.com/LM-Commons/LmcRbacMvcDeveloperTools): a companion component to Laminas Developer Tools to collect and display LmcRbcMvc data

## Installation

Install the module using Composer:

```sh
$ composer require lm-commons/lmc-rbac-mvc:^4.0
```

You will be prompted by the `laminas-component-installer` plugin to inject LM-Commons\LmcRbacMvc.

:::note[Manual installation]

Enable the module by adding `Lmc\Rbac\Mvc` key to your `application.config.php` or `modules.config.php` file.
:::

Customize the module by copy-pasting the `lmc_rbac.global.php.dist` file to your `config/autoload` folder.

:::warning
LmcRbac and LmcRbacMvc share the same config key `'lmc_rbac'`. Be careful when creating configuration files to avoid
overriding configuration. 

It is recommended that have one configuration file containing both LmcRbac and LmcRbacMvc configuration. This makes it 
easier to keep all configuration in one place.
:::

## Quick Start

Before you start configuring LmcRbacMvc, you must set up LmcRbac first. Please follow the [instructions](https://lm-commons.github.io/LmcRbac/docs/gettingstarted) in LmcRbac 
documentation.

### Specifying an identity provider

By default, LmcRbacMvc internally uses the `Laminas\Authentication\AuthenticationService` service key to retrieve the user (logged or
not). Therefore, you must implement and register this service in your application by providing a factory on your configuration. 

For example, in `module.config.php` file:

```php
return [
    'service_manager' => [
        'factories' => [
	        \Laminas\Authentication\AuthenticationService::class => function($container) {
	            // Create your authentication service!
	        }
	    ]
    ]
];
```

The identity given by `Laminas\Authentication\AuthenticationService` must implement `Lmc\Rbac\Identity\IdentityInterface`.

:::warning
Note that the default identity provided with Laminas does not implement this interface.
:::

:::tip
If you are also using the [LmcUser](https://github.com/lm-commons/lmcuser) package, then the `Laminas\Authentication\AuthenticationService` will be
provided for you and there is no need to implement your own.

:::warning
LmcUser's default User entity does not implement the `IdentityInteface` that is required
by LmcRbac. 
:::

LmcRbacMvc is flexible enough to use something other than the built-in `AuthenticationService`, by specifying custom
identity providers. For more information, refer to the [Create a custom identity provider](Guides/identity-providers.md) 
section.

## Adding a guard

A guard allows your application to block access to routes and/or controllers using a simple syntax. For instance, this configuration
grants access to any route that begins with `admin` (or is exactly `admin`) to the `admin` role only:

```php
return [
    'lmc_rbac' => [
        'guards' => [
	        'Lmc\Rbac\Mvc\Guard\RouteGuard' => [
                'admin*' => ['admin']
	        ]
        ]
    ]
];
```

LmcRbacMvc has several built-in guards, and you can also register your own guards. For more information, refer
[to this section](guards.md#built-in-guards).

## Registering a strategy

When a guard blocks access to a route/controller, or if you throw the `Lmc\Rbac\Mvc\Exception\UnauthorizedException`
exception in your service, LmcRbacMvc automatically performs some logic for you depending on the view strategy used.

For instance, if you want LmcRbacMvc to automatically redirect all unauthorized requests to the "login" route,
add
the following code in the `onBootstrap` method of your `Module.php` class:

```php
public function onBootstrap(MvcEvent $event)
{
    $app = $event->getApplication();
    $sm = $app->getServiceManager();
    $em = $app->getEventManager();
    
    $listener = $sm->get(\Lmc\Rbac\Mvc\View\Strategy\RedirectStrategy::class);
    $listener->attach($em);
}
```

or add the listener to 'listeners' config key in a configuration file:

```php
return [
    // other configs...
    
    'listeners' => [
        \Lmc\Rbac\Mvc\View\Strategy\RedirectStrategy::class
    ],
];
```


By default, `RedirectStrategy` redirects all unauthorized requests to a route named "login" when the user is not connected
and to a route named "home" when the user is connected. This is entirely configurable.

:::warning
For flexibility purposes, LmcRbacMvc **does not** register any strategy for you by default!
:::

For more information about built-in strategies, refer [to this section](strategies.md#built-in-strategies)
in the [Strategies](strategies.md) section.

## Using the authorization service

With LmcRbac and LmcRbacMvc properly configured, you can inject the authorization service into any class and use it to 
check if the current identity is granted to do something.

The LmcRbacMvc Authorization Service is a wrapper to the LmcRbac Authorization Service. 

The difference is in the `isGranted` method:

```php
public function isGranted(string $permission, mixed $context = null): bool;
```
where the service will get the identity from the identity provider and there is no need to get it separately.

The authorization service can be retrieved from the container using the key `Lmc\Rbac\Mvc\Service\AuthorizationServiceInterface`.
Once injected, you can use it as follows:

```php
use Lmc\Rbac\Mvc\Exception\UnauthorizedException;

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
