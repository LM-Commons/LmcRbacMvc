---
title: Strategies
sidebar_position: 3
---

## What are strategies?

A strategy is an object that listens to the `MvcEvent::EVENT_DISPATCH_ERROR` event. It is used to describe what
happens when access to a resource is unauthorized by LmcRbacMvc.

LmcRbacMvc strategies all check if an `Lmc\Rbac\Mvc\Exception\UnauthorizedExceptionInterface` has been thrown.

By default, LmcRbacMvc does not register any strategy for you. The best place to register it in a config file under the
`'listeners'` key:

```php
return [
    // other configs...
    
    'listeners' => [
        \Lmc\Rbac\Mvc\View\Strategy\UnauthorizedStrategy::class
    ],
];
```
## Built-in strategies

LmcRbacMvc comes with two built-in strategies: 
- `\Lmc\Rbac\Mvc\View\Strategy\RedirectStrategy` 
- `\Lmc\Rbac\Mvc\View\Strategy\UnauthorizedStrategy`.

### RedirectStrategy

This strategy allows your application to redirect any unauthorized request to another route by optionally appending the previous
URL as a query parameter.

To register it, copy-paste this code into a configuration file:

```php
return [
    // other configs...
    
    'listeners' => [
        \Lmc\Rbac\Mvc\View\Strategy\RedirectStrategy::class
    ],
];
```

You can configure the strategy using the `redirect_strategy` subkey:

```php
return [
    'lmc_rbac' => [
        'redirect_strategy' => [
            'redirect_when_connected'        => true,
            'redirect_to_route_connected'    => 'home',
            'redirect_to_route_disconnected' => 'login',
            'append_previous_uri'            => true,
            'previous_uri_query_key'         => 'redirectTo'
        ],
    ]
];
```

If users try to access an unauthorized resource (eg.: http://www.example.com/delete), they will be
redirected to the "login" route if is not connected and to the "home" route otherwise with the previous URL appended:

> http://www.example.com/login?redirectTo=http://www.example.com/delete

You can prevent redirection when a user is connected (i.e. so that the user gets a 403 page) by setting `redirect_when_connected` to `false`.

### UnauthorizedStrategy

This strategy allows your application to render a template on any unauthorized request.

To register it, copy-paste this code into your Module.php class:

```php
return [
    // other configs...
    
    'listeners' => [
        \Lmc\Rbac\Mvc\View\Strategy\UnauthorizedStrategy::class
    ],
];
```

You can configure the strategy using the `unauthorized_strategy` subkey:

```php
return [
    'lmc_rbac' => [
        'unauthorized_strategy' => [
            'template' => 'error/custom-403'
        ],
    ]
];
```

> By default, LmcRbacMvc uses a template called `error/403`.

## Creating custom strategies

Creating a custom strategy is rather easy. Let's say we want to create a strategy that integrates with
the [ApiProblem](https://github.com/laminas-api-tools/api-tools-api-problem) Laminas Api Tools module:

```php
namespace Application\View\Strategy;

use Laminas\Http\Response as HttpResponse;
use Laminas\Mvc\MvcEvent;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Lmc\Rbac\Mvc\View\Strategy\AbstractStrategy;
use Lmc\Rbac\Mvc\Exception\UnauthorizedExceptionInterface;

class ApiProblemStrategy extends AbstractStrategy
{
    public function onError(MvcEvent $event)
    {
        // Do nothing if no error or if response is not HTTP response
        if (!($exception = $event->getParam('exception') instanceof UnauthorizedExceptionInterface)
            || ($result = $event->getResult() instanceof HttpResponse)
            || !($response = $event->getResponse() instanceof HttpResponse)
        ) {
            return;
        }

        return new ApiProblemResponse(new ApiProblem($exception->getMessage()));
    }
}
```

Register your strategy:

```php
return [
    // other configs...
    
    'listeners' => [
        Application\View\Strategy\ApiProblemStrategy::class,
    ],
];
```
