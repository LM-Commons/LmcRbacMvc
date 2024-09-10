---
sidebar_position: 2
title: Guards
---
In this section, you will learn:

* What guards are
* How to use and configure built-in guards
* How to create custom guards

## What are guards and when should you use them?

Guards are listeners that are registered on a specific event of
the MVC workflow. They allow your application to quickly mark a request as unauthorized.

Here is a simple workflow without guards:

![Laminas Framework workflow without guards](images/workflow-without-guards.png?raw=true)

And here is a simple workflow with a route guard:

![Laminas Framework workflow with guards](images/workflow-with-guards.png?raw=true)

RouteGuard and ControllerGuard are not aware of permissions but rather only think about "roles". For
instance, you may want to refuse access to each routes that begin by "admin/*" to all users that do not have the
"admin" role.

If you want to protect a route for a set of permissions, you must use RoutePermissionsGuard. For instance,
you may want to grant access to a route "post/delete" only to roles having the "delete" permission.
Note that in a RBAC system, a permission is linked to a role, not to a user.

Albeit simple to use, guards should not be the only protection in your application, and you should always
protect your services as well. The reason is that your business logic should be handled by your service. Protecting a given
route or controller does not mean that the service cannot be access from elsewhere (another action for instance).

### Protection policy

By default, when a guard is added, it will perform a check only on the specified guard rules. Any route or controller
that is not specified in the rules will be "granted" by default. Therefore, the default is a "blacklist"
mechanism.

However, you may want a more restrictive approach (also called "whitelist"). In this mode, once a guard is added,
anything that is not explicitly added will be refused by default.

For instance, let's say you have two routes: "index" and "login". If you specify a route guard rule to allow "index"
route to "member" role, your "login" route will become defacto unauthorized to anyone, unless you add a new rule for
allowing the route "login" to "member" role.

You can change it in LmcRbacMvc config, as follows:

```php
use LmcRbacMvc\Guard\GuardInterface;

return [
    'lmc_rbac' => [
        'protection_policy' => GuardInterface::POLICY_DENY
    ]
];
```

> NOTE: this policy will block ANY route/controller (so it will also block any console routes or controllers). The
deny policy is much more secure, but it needs much more configuration to work with.

## Built-in guards

LmcRbacMvc comes with four guards, in order of priority :

* RouteGuard : protect a set of routes based on the identity roles
* RoutePermissionsGuard : protect a set of routes based on roles permissions
* ControllerGuard : protect a controllers and/or actions based on the identity roles
* ControllerPermissionsGuard : protect a controllers and/or actions based on roles permissions

All guards must be added in the `guards` subkey:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            // Guards config here!
        ]
    ]
];
```

Because of the way Laminas Framework handles config, you can without problem define some rules in one module, and
more rules in another module. All the rules will be automatically merged.

> For your mental health, I recommend you to use either the route guard OR the controller guard, but not both. If
you decide to use both conjointly, I recommend you to set the protection policy to "allow" (otherwise, you will
need to define rules for every routes AND every controller, which can become quite frustrating!).

Please note that if your application uses both route and controller guards, route guards are always executed
**before** controller guards (they have a higher priority).

### RouteGuard

> The RouteGuard listens to the `MvcEvent::EVENT_ROUTE` event with a priority of -5.

The RouteGuard allows your application to protect a route or a hierarchy of routes. You must provide an array of "key" => "value",
where the key is a route pattern and the value is an array of role names:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RouteGuard' => [
                'admin*' => ['admin'],
                'login'   => ['guest']
            ]
        ]
    ]
];
```

> Only one role in a rule needs to be matched (it is an OR condition).

Those rules grant access to all admin routes to users that have the "admin" role, and grant access to the "login"
route to users that have the "guest" role (eg.: most likely unauthenticated users).

> The route pattern is not a regex. It only supports the wildcard (*) character, that replaces any segment.

You can also use the wildcard character * for roles:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RouteGuard' => [
                'home' => ['*']
            ]
        ]
    ]
];
```

This rule grants access to the "home" route to anyone.

Finally, you can also omit the roles array to completely block a route, for maintenance purpose for example :

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RouteGuard' => [
                'route_under_construction'
            ]
        ]
    ]
];
```

This rule will be inaccessible.

Note : this last example could be (and should be) written in a more explicit way :

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RouteGuard' => [
                'route_under_construction' => []
            ]
        ]
    ]
];
```


### RoutePermissionsGuard

> The RoutePermissionsGuard listens to the `MvcEvent::EVENT_ROUTE` event with a priority of -8.

The RoutePermissionsGuard allows your application to protect a route or a hierarchy of routes. You must provide an array of "key" => "value",
where the key is a route pattern and the value is an array of permission names:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RoutePermissionsGuard' => [
                'admin*' => ['admin'],
                'post/manage' => ['post.update', 'post.delete']
            ]
        ]
    ]
];
```

> By default, all permissions in a rule must be matched (an AND condition).

In the previous example, one must have ```post.update``` **AND** ```post.delete``` permissions
to access the ```post/manage``` route. You can also specify an OR condition like so:

```php
use LmcRbacMvc\Guard\GuardInterface;

return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RoutePermissionsGuard' => [
                'post/manage'   => [
                    'permissions' => ['post.update', 'post.delete'],
                    'condition'   => GuardInterface::CONDITION_OR
                ]
            ]
        ]
    ]
];
```

> Permissions are linked to roles, not to users

Those rules grant access to all admin routes to roles that have the "admin" permission, and grant access to the
"post/delete" route to roles that have the "post.delete" or "admin" permissions.

> The route pattern is not a regex. It only supports the wildcard (*) character, that replaces any segment.

You can also use the wildcard character * for permissions:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RoutePermissionsGuard' => [
                'home' => ['*']
            ]
        ]
    ]
];
```

This rule grants access to the "home" route to anyone.

Finally, you can also use an empty array to completly block a route, for maintenance purpose for example :

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RoutePermissionsGuard' => [
                'route_under_construction' => []
            ]
        ]
    ]
];
```

This route will be inaccessible.


### ControllerGuard

> The ControllerGuard listens to the `MvcEvent::EVENT_ROUTE` event with a priority of -10.

The ControllerGuard allows your application to protect a controller. You must provide an array of arrays:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\ControllerGuard' => [
                [
                    'controller' => 'MyController',
                    'roles'      => ['guest', 'member']
                ]
            ]
        ]
    ]
];
```

> Only one role in a rule need to be matched (it is an OR condition).

Those rules grant access to each actions of the MyController controller to users that have either the "guest" or
"member" roles.

As for RouteGuard, you can use a wildcard (*) character for roles.

You can also specify optional actions, so that the rule only apply to one or several actions:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\ControllerGuard' => [
                [
                    'controller' => 'MyController',
                    'actions'    => ['read', 'edit'],
                    'roles'      => ['guest', 'member']
                ]
            ]
        ]
    ]
];
```

You can combine a generic rule and a specific action rule for the same controller, as follows:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\ControllerGuard' => [
                [
                    'controller' => 'PostController',
                    'roles'      => ['member']
                ],
                [
                    'controller' => 'PostController',
                    'actions'    => ['delete'],
                    'roles'      => ['admin']
                ]
            ]
        ]
    ]
];
```

These rules grant access to each controller action to users that have the "member" role, but restrict the
"delete" action to "admin" only.

### ControllerPermissionsGuard

> The ControllerPermissionsGuard listens to the `MvcEvent::EVENT_ROUTE` event with a priority of -13.

The ControllerPermissionsGuard allows your application to protect a controller using permissions. You must provide an array of arrays:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\ControllerPermissionsGuard' => [
                [
                    'controller'  => 'MyController',
                    'permissions' => ['post.update', 'post.delete']
                ]
            ]
        ]
    ]
];
```

> All permissions in a rule must be matched (it is an AND condition).

In the previous example, the user must have ```post.update``` **AND** ```post.delete``` permissions
to access each action of the MyController controller.

As for all other guards, you can use a wildcard (*) character for permissions.

The configuration rules are the same as for ControllerGuard.

### Security notice

RouteGuard and ControllerGuard listen to the `MvcEvent::EVENT_ROUTE` event. Therefore, if you use the
`forward` method in your controller, those guards will not intercept and check requests (because internally
Laminas MVC does not trigger again a new MVC loop).

Most of the time, this is not an issue, but you must be aware of it, and this is an additional reason why you
should always protect your services too.

## Creating custom guards

LmcRbacMvc is flexible enough to allow you to create custom guards. Let's say we want to create a guard that will
refuse access based on an IP addresses blacklist.

First create the guard:

```php
namespace Application\Guard;

use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;
use LmcRbacMvc\Guard\AbstractGuard;

class IpGuard extends AbstractGuard
{
    const EVENT_PRIORITY = 100;

    /**
     * List of IPs to blacklist
     */
    protected $ipAddresses = [];

    /**
     * @param array $ipAddresses
     */
    public function __construct(array $ipAddresses)
    {
        $this->ipAddresses = $ipAddresses;
    }

    /**
     * @param  MvcEvent $event
     * @return bool
     */
    public function isGranted(MvcEvent $event)
    {
        $request = $event->getRequest();

        if (!$request instanceof HttpRequest) {
            return true;
        }

        $clientIp = $_SERVER['REMOTE_ADDR'];

        return !in_array($clientIp, $this->ipAddresses);
    }
}
```

> Guards must implement `LmcRbacMvc\Guard\GuardInterface`.

By default, guards are listening to the event `MvcEvent::EVENT_ROUTE` with a priority of -5 (you can change the default
event to listen by overriding the `EVENT_NAME` constant in your guard subclass). However, in this case, we don't
even need to wait for the route to be matched, so we overload the `EVENT_PRIORITY` constant to be executed earlier.

The `isGranted` method simply retrieves the client IP address, and checks it against the blacklist.

However, for this to work, we must register the newly created guard with the guard plugin manager. To do so, add the
following code in your config:

```php
return [
    'lmc_rbac' => [
        'guard_manager' => [
            'factories' => [
                'Application\Guard\IpGuard' => 'Application\Factory\IpGuardFactory'
            ]
        ]
    ]
];
```

The `guard_manager` config follows a conventional service manager configuration format.

Now, let's create the factory:

```php
namespace Application\Factory;

use Application\Guard\IpGuard;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IpGuardFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (null === $options) {
            $options = [];
        }
        return new IpGuard($options);
    }
}
```

In a real use case, you would likely fetched the blacklist from a database.

Now we just need to add the guard to the `guards` option, so that LmcRbacMvc can execute the logic behind this guard. In
your config, add the following code:

```php
return [
    'lmc_rbac' => [
        'guards' => [
            'Application\Guard\IpGuard' => [
                '87.45.66.46',
                '65.87.35.43'
            ]
        ]
    ]
];
```
The array of IP addresses will be passed to `IpGuardFactory::__invoke` in the `$options` parameter.
