---
sidebar_position: 4
sidebar_label: Authorization Service
---
# Using the Authorization Service

The LmcRbacMvc Authorization service is a wrapper for the LmcRbac Authorization service.

`\Lmc\Rbac\Mvc\Service\AuthorizationService` provides the same methods as `\Lmc\Rbac\Service\AuthorizationService`
except for the `isGranted()` method which does not required an identity parameter. 

`\Lmc\Rbac\Mvc\Service\AuthorizationService::isGranted()` will get the identity from the
Laminas Authentication Service directly.

## Injecting the Authorization Service

Different techniques for injecting the Authorization Service are described in LmcRbac. The same techniques apply
to LmcRbacMvc using the equivalent wrapper classes and trait:

- `\Lmc\Rbac\Mvc\Service\AuthorizationServiceAwareInterface`
- `\Lmc\Rbac\Mvc\Service\AuthorizationServiceAwareTrait`
- `\Lmc\Rbac\Mvc\Service\AuthorizationServiceDelegatorFactory`

Refer to this section in LmcRbac on how to inject the Authorization Service.

## Permissions and Assertions

Assertions are provided by LmcRbac.  Refer to the Assertion section in LmcRbac on how to configure assertions.

`\Lmc\Rbac\Mvc\Service\AuthorizationService` provides the same methods as `\Lmc\Rbac\Service\AuthorizationService` to
override assertions at run-time.

:::warning
If you are upgrading from v3, please not that assertions must now implement the 
`Lmc\Rbac\Assertion\AssertionInterface` interface which has a different signature for the `assert()` method.

Make sure to update your assertions to follow the `Lmc\Rbac\Assertion\AssertionInterface` interface.
:::

