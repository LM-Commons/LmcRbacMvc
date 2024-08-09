---
sidebar_position: 2
sidebar_label: Requirements and Installation
---
# Requirements and Installation
## Requirements

- PHP 8.0 or higher
- [lm-commons/rbac component v1](https://github.com/zf-fr/rbac): this is actually a prototype for the ZF3 Rbac component.
- [Laminas Components 2.x | 3.x or higher](http://www.github.com/laminas)


## Optional

- [DoctrineModule](https://github.com/doctrine/DoctrineModule): if you want to use some built-in role and permission providers.
- [Laminas\DeveloperTools](https://github.com/laminas/Laminas\DeveloperTools): if you want to have useful stats added to
  the Laminas Developer toolbar.


## Installation

LmcRbacMvc only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Install the module:

```sh
$ composer require lm-commons/lmc-rbac-mvc:^3.0
```

Enable the module by adding `LmcRbacMvc` key to your `application.config.php` or `modules.config.php` file. Customize the module by copy-pasting
the `lmc_rbac.global.php.dist` file to your `config/autoload` folder.

## Upgrade

LmcRbacMvc introduces breaking changes from zfcrbac v2:
- [BC] The namespace has been changed from `ZfcRbac` to `LmcRbacMvc`.
- [BC] The key `zfc_rbac` in autoload and module config files has been replaced
  by the `lmc_rbac` key.
- Requires PHP 8.0 or later
- Requires Laminas MVC components 3.x or later
- Uses PSR-4 autoload
