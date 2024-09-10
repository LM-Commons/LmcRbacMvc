---
title: Create a custom identity provider
sidebar_label: Custom Identity Providers
sidebar_position: 2
---

Identity providers return the current identity. Most of the time, this means the logged in user. LmcRbacMvc comes with a
default identity provider (`Lmc\Rbac\Mvc\Identity\AuthenticationIdentityProvider`) that uses the
`Laminas\Authentication\AuthenticationService` service.

### Create your own identity provider

If you want to implement your own identity provider, create a new class that implements
`Lmc\Rbac\Mvc\Identity\IdentityProviderInterface` class. Then, change the `identity_provider` option in LmcRbacMvc config,
as shown below:

```php
return [
    'lmc_rbac' => [
        'identity_provider' => 'MyCustomIdentityProvider'
    ]
];
```

The identity provider is automatically pulled from the service manager.
