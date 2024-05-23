---
sidebar_position: 7
---
# Using the Authorization Service

This section will teach you how to use the AuthorizationService to its full extent.

## Injecting the Authorization Service

### Using initializers

To automatically inject the authorization service into your classes, you can implement the
`AuthorizationServiceAwareInterface` and use the trait, as shown below:

```php
namespace YourModule;

use LmcRbacMvc\Service\AuthorizationServiceAwareInterface;
use LmcRbacMvc\Service\AuthorizationServiceAwareTrait;

class MyClass implements AuthorizationServiceAwareInterface
{
    use AuthorizationServiceAwareTrait;

    public function doSomethingThatRequiresAuth()
    {
        if (! $this->getAuthorizationService()->isGranted('deletePost')) {
            throw new UnauthorizedException('You are not allowed !');
        }

        return true;
    }
}
```

Then, register the initializer in your config (it is not registered by default):

```php
class Module
{
    // ...

    public function getServiceConfig()
    {
        return [
            'initializers' => [
                'LmcRbacMvc\Initializer\AuthorizationServiceInitializer'
            ]
        ];
    }
}
```

> While initializers allow rapid prototyping, their use can lead to more fragile code. We'd suggest using factories.

### Using delegator factory

LmcRbacMvc is shipped with a `LmcRbacMvc\Factory\AuthorizationServiceDelegatorFactory` [delegator factory](https://docs.laminas.dev/laminas-servicemanager/delegators/)
to automatically inject the authorization service into your classes.

As for the initializer, the class must implement the `AuthorizationServiceAwareInterface`.

You just have to add your classes to the right delegator :

```php
class Module
{
    // ...

    public function getServiceConfig()
    {
        return [
            'invokables' => [
                'Application\Service\MyClass' => 'Application\Service\MyClassService',
            ],
            'delegators' => [
                'Application\Service\MyClass' => [
                     'LmcRbacMvc\Factory\AuthorizationServiceDelegatorFactory',
                     // eventually add more delegators here
                ],
            ],
        ];
    }
}
```

> While they need a little more configuration, delegator factories have better performances than initializers.

### Using Factories

You can inject the AuthorizationService into your factories by using Laminas' ServiceManager. The AuthorizationService
is known to the ServiceManager as `'LmcRbacMvc\Service\AuthorizationService'`. Here is a classic example for injecting
the AuthorizationService:

*YourModule/Module.php*

```php
class Module
{
    // getAutoloaderConfig(), etc...

    public function getServiceConfig()
    {
        return [
            'factories' => [
                 'MyService' => function($sm) {
                     $authService = $sm->get('LmcRbacMvc\Service\AuthorizationService');
                     return new MyService($authService);
                 }
            ]
        ];
    }
}
```


## Permissions and Assertions

Since you now know how to inject the AuthorizationService, let's use it!

One of the great things the AuthorizationService brings are **assertions**. Assertions get executed *if the identity
in fact holds the permission you are requesting*. A common example is a blog post, which only the author can edit. In
this case, you have a `post.edit` permission and run an assertion checking the author afterwards.

### Defining assertions

The AssertionPluginManager is a great way for you to use assertions and IOC. You can add new assertions quite easily
by adding this to your `module.config.php` file:

```php
return [
    'lmc_rbac' => [
        'assertion_manager' => [
            'factories' => [
                'MyAssertion' => 'MyAssertionFactory'
            ]
        ]
    ]
];
```

### Defining the assertion map

The assertion map can automatically map permissions to assertions. This means that every time you check for a
permission with an assertion map, you'll include the assertion in your check. You can define the assertion map by
adding this to your `module.config.php` file:

```php
return [
    'lmc_rbac' => [
        'assertion_map' => [
            'myPermission' => 'myAssertion'
        ]
    ]
];
```

Now, every time you check for `myPermission`, `myAssertion` will be checked as well.

### Checking permissions in a service

So let's check for a permission, shall we?

```php
$authorizationService->isGranted('myPermission');
```

That was easy, wasn't it?

`isGranted` checks if the current identity is granted the permission and additionally runs the assertion that is
provided by the assertion map.

### Checking permissions in controllers and views

LmcRbacMvc comes with both a controller plugin and a view helper to check permissions.

#### In a controller :

```php
    public function doSomethingAction()
    {
        if (!$this->isGranted('myPermission')) {
            // redirect if not granted for example
        }
    }
```

#### In a view :

```php
    <?php if ($this->isGranted('myPermission')): ?>
    <div>
        <p>Display only if granted</p>
    </div>
    <?php endif ?>
```

### Defining additional permissions

But what if you don't want to use the assertion map? That's quite easy as well!

Here are four examples of how to run an assertion without using the assertion map:

Disable the assertion:

```php
$authorizationService->setAssertion('myPermission', null);
$authorizationService->isGranted('myPermission');
```

Callback assertion:
```php
$something = true;

$authorizationService->setAssertion(
   'myPermission',
   function(AuthorizationService $authorization, $context = true) use ($something) {
      return $something === $context
   }
);

$authorizationService->isGranted('myPermission'); // returns true, when the identity holds the permission `myPermission`
```

Object implementing `AssertionInterface`:
```php
$context = true;

$authorizationService->setAssertion('myPermission', new MyAssertion($foo, $bar));
$authorizationService->isGranted('myPermission', $context);
```

Using the AssertionPluginManager:
```php
$context = true;
$authorizationService->setAssertion('myPermission', 'MyAssertion');
$authorizationService->isGranted('myPermission', $context);
```

*Please note: The context parameter is optional!*
