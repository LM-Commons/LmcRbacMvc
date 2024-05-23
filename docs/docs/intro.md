---
sidebar_position: 1
---

# Introduction

LmcRbacMvc is a role-based access control Laminas MVC module to provide additional features on top of Laminas\Permissions\Rbac

LmcRbacMvc is part of the [LM-Commons](https://lm-commons.github.io) series of community developed packages for Laminas.

LM-Commons is a GitHub organization dedicated to the collaborative
and community-driven long-term maintenance of packages & libraries based on the Laminas MVC and Components.


:::tip
**Important Note:**
 
If you are migrating from ZfcRbac v2, there are breaking changes to take into account. See the [Upgrade](installation.md#upgrade) section for details.
:::

## Why should I use an authorization module?

The authorization part of an application is an essential aspect of securing your application. 
While the *authentication* part tells you who is using your website, the *authorization* answers if the given identity has the permission to
perform specific actions.

## What is the Rbac model?

Rbac stands for **role-based access control**. We use a very simple (albeit powerful) implementation of this model
through the use of the [zf-fr/rbac](https://github.com/zf-fr/rbac) library.


The basic idea of Rbac is to use roles and permissions:

* **Users** can have one or many **Roles**
* **Roles** request access to **Permissions**
* **Permissions** are granted to **Roles**

By default, LmcRbacMvc can be used for two kinds of Rbac model:

* Flat RBAC model: in this model, roles cannot have children. This is ideal for smaller applications, as it is easier
  to understand, and the database design is simpler (no need for a join table).
* Hierarchical RBAC model: in this model, roles can have child roles. When evaluating if a given role has a
  permission, this model also checks recursively if any of its child roles also have the permission.


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
