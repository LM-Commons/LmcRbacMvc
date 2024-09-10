---
sidebar_label: From v3 to v4
---

# Upgrading from v3 to v4

LmcRbacMvc v4 is a major upgrade with many breaking changes that prevent
straightforward upgrading.

LmcRbacMvc v3 and LmcRbac v1 shared a lot of code. LmcRbacMvc v4 is now based
on LmcRbac v2 which was augmented such that common code is now part of LmcRbac.
This has rendered many components of LmcRbacMvc unnecessary and they are deprecated.

In addition, LmcRbac v2 is now based on laminas-permissions-rbac's Role classes and interfaces.

### Namespace change

In an effort to In an effort to normalize LM-Commons components into a common Lmc namespace, the namespace has 
been refactored to Lmc\Rbac\Mvc.

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
