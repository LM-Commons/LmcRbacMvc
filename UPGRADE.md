# Upgrade guide

## From LmcRbacMvc v3 to LmcRbacMvc v4

LmcRbacMvc v4 is a major upgrade with many breaking changes that prevent
straightforward upgrading.

LmcRbacMvc v3 and LmcRbac v1 shared a lot of code. LmcRbacMvc v2 is now based
on LmcRbac v2 which was augmented such that common code is now part of LmcRbac.
This has rendered many components of LmcRbacMvc unnecessary and they are deprecated

### Namespace change

In an effort to In an effort to normalize LM-Commons components into a common Lmc namespace, the namespace will 
be refactored to Lmc\Rbac\Mvc.

Please update your code to replace `LmcRbacMvc` by `Lmc\Rbac\Mvc`.

### Deprecations

The following components that were shared with LmcRbac are deprecated in LmcRbacMvc and should be replaced by their 
LmcRbac equivalent:

- Lmc\Rbac\Mvc\Exception\ExceptionInterface
- Lmc\Rbac\Mvc\Exception\InvalidArgumentException
- Lmc\Rbac\Mvc\Exception\RoleNotFoundException
- Lmc\Rbac\Mvc\Exception\RuntimeException
- Lmc\Rbac\Mvc\Permission\PermissionInterface
- Lmc\Rbac\Mvc\Identity\IdentityInterface
- Lmc\Rbac\Mvc\Role\RoleProviderInterface

### Refactored and removed classes

#### Factories
- The factory classes were refactored from the `LmcRbacMvc\Factory` namespace to be colocated with
the service that the factory is creating. All the factories that were in `LmcRbacMvc\Factory` namespace have been
deleted.

#### Role providers
The former LmcRbacMvc v3 role providers are no longer available and replaced by LmcRbac equivalent. LmcRbac will throw
an exception if your config file still refers to them. In addition, the role provider plugin manager
was removed as it was not necessary.
- LmcRbacMvc\Role\RoleProviderPluginManager *no longer used*
- LmcRbacMvc\Role\InMemoryRoleProvider *replaced by a LmcRbac equivalent*
- LmcRbacMvc\Role\ObjectRepositoryRoleProvider *replaced by a LmcRbac equivalent*

#### Assertion
- LMcRbacMvc\Assertion\AssertionPluginManagerFactory *no longer used*
- LMcRbacMvc\Assertion\AssertionPluginManager *no longer used*

### Assertions refactoring
LmcRbacMvc is now using LmcRbac assertions and assertion plugin manager instead of its own.

Therefore all previous assertions in LmcRbacMvc v3 must now implement the `\Lmc\Rbac\Assertion\AssertionInterface` 
otherwise LmcRbac will throw an exception.

`\Lmc\Rbac\Assertion\AssertionInterface` is a more generic interface for asserting permissions. An assertion under this 
interface will be passed the permission, identity and context whereas in LmcRbacMvc v3, the assertion is
passed the AuthorizationService from which one had to get the identity. Having the permission as a parameter allows to 
reuse the same assertion to handle multiple permissions.


## From zfc-rbac v2.x to LmcRbacMvc v3.0

- [BC] The namespace has been changed to `LmcRbacMvc`
- [BC] The `zfc_rbac` configuration key has been changed to `lmc_rbac`
- Requires PHP 7.2 or later
- Requires Laminas MVC components 3.x or later
- Uses PSR-4 autoload


## Previous zfc-rbac versions

### From v2.2 to v2.3

- No BC

### From v2.1 to v2.2

- [Potential BC] To simplify unit tests, we have introduced a new `AuthorizationServiceInterface` that the
`AuthorizationService` now implements. We didn't touch any interface (such as `AssertionInterface`) that rely explicitly
on typehinting the `AuthorizationService` to avoid big BC. However, we have updated the view helper and controller
plugins to use the interface instead. This can lead to a BC if you created subclasses of those plugins (which is
not a typical use case). If this is the case, just change `AuthorizationService` to `AuthorizationServiceInterface`.

### From v2.0 to v2.1

- [Potential BC] A potential BC have been introduced in v2.1 to respect interfaces of RBAC component more strictly.
However there is great chance that you have nothing to do. Now, ZfcRbac no longer cast permissions to string before
passing it to your "hasPermission" method in the Role entity. If you used to call `isGranted` using a string permission,
like this: `isGranted('myPermission')`, then you have nothing to do. However, if you are passing a `PermissionInterface`
object, you will now receive this object instead of a string. It's up to you to getting the name from your permission.

### From v1 to v2

Here are the major breaking changes from ZfcRbac 1 to ZfcRbac 2:

- [BC] Dependency to the ZF2 RBAC component has been replaced in favour of a ZF3 prototype which fixes a lot
of design issues.
- [BC] ZfcRbac no longer accepts multiple role providers. Therefore, the option `role_providers` has been renamed
to `role_provider`
- [BC] Permission providers are gone (hence, the options `permission_providers` as well as `permission_manager` should
be removed). Instead, roles now embed all the necessary information
- [BC] The `redirect_to_route` option for the `RedirectStrategy` is gone. Instead, we now have two options:
`redirect_to_route_connected` and `redirect_to_route_disconnected`. This solves an issue when people used to have
a guard on `login` for non-authenticated users only, which leaded to circular redirections.
- [BC] The default protection policy is now `POLICY_ALLOW`. `POLICY_DENY` was way too restrictive and annoying to
work with by default.
- [BC] `isGranted` method of the AuthorizationService no longer accepts an assertion as a second parameter. Instead,
the AuthorizationService now has an assertion map, that allows to map an assertion to a permission. This allows to
inject dependencies into assertions, as well as making the use of assertions much more transparent.
- [BC] Each assertions now receive the whole `AuthorizationService` instead of the current identity. This allows to
support use cases where an assertion needs to check another permission.
- [BC] Entity schema for hierarchical role have changed and no longer require to implement `RecursiveIterator`. Please have a look at the new schema in the `data` folder.
