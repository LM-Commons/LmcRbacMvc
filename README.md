# LmcRbacMvc

[![Master Branch Build Status](https://travis-ci.org/Laminas-Commons/LmcRbacMvc.svg?branch=master)](http://travis-ci.org/Laminas-Commons/LmcRbac)
[![Gitter](https://badges.gitter.im/LaminasCommons/community.svg)](https://gitter.im/LaminasCommons/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

Role-based access control module to provide additional features on top of Zend\Permissions\Rbac

Based on [ZF-Commons/zfc-rbac](https://github.com/ZF-Commons/zfc-rbac) v2.6.x. If you are looking for the Laminas version of zfc-rbac v3, please use [Laminas-Commons/LmcRbac](https://github.com/Laminas-Commons/LmcRbac).

<!--
[![Coverage Status](https://coveralls.io/repos/ZF-Commons/zfc-rbac/badge.png)](https://coveralls.io/r/ZF-Commons/zfc-rbac)
[![Latest Stable Version](https://poser.pugx.org/zf-commons/zfc-rbac/v/stable.png)](https://packagist.org/packages/zf-commons/zfc-rbac)
[![Latest Unstable Version](https://poser.pugx.org/zf-commons/zfc-rbac/v/unstable.png)](https://packagist.org/packages/zf-commons/zfc-rbac)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ZF-Commons/zfc-rbac/badges/quality-score.png?s=685a2b34dc626a0af9934f9c8d246b68a8cac884)](https://scrutinizer-ci.com/g/ZF-Commons/zfc-rbac/)
[![Total Downloads](https://poser.pugx.org/zf-commons/zfc-rbac/downloads.png)](https://packagist.org/packages/zf-commons/zfc-rbac)
-->

### Important Notes:  

This version has breaking changes with respect to ZfcRbac v2. See the [Upgrade](#upgrade) section for details.


## Requirements

- PHP 7.2 or higher
- [Zf-fr/Rbac component v1](https://github.com/zf-fr/rbac): this is actually a prototype for the ZF3 Rbac component.
- [Laminas Components 2.x | 3.x or higher](http://www.github.com/laminas)

> 

## Optional

- [DoctrineModule](https://github.com/doctrine/DoctrineModule): if you want to use some built-in role and permission providers.
- [Laminas\DeveloperTools](https://github.com/laminas/Laminas\DeveloperTools): if you want to have useful stats added to
the Laminas Developer toolbar.

## Upgrade

LmcRbac introduces breaking changes from zfcrbac v2:
- The namespace has been changed from `ZfcRbac` to `LmcRbacMvc`. 
- The key `zfc-rbac` in autoload and module config files has been replaced
by the `lmc-rbac` key.

You can find an [upgrade guide](UPGRADE.md) to quickly upgrade your application from major versions of ZfcRbac.

## Installation

LmcRbacMvc only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Install the module:

```sh
$ php composer.phar require laminas-commons/lmc-rbac-mvc:^3.0
```
This will install a Laminas MVC equivalent of zfc-rbac 2.6.3.

Enable the module by adding `LmcRbacMvc` key to your `application.config.php` or `modules.config.php` file. Customize the module by copy-pasting
the `lmc_rbac.global.php.dist` file to your `config/autoload` folder.

## Documentation

The official documentation is available in the [/docs](/docs) folder.

You can also find some Doctrine entities in the [/data](/data) folder that will help you to more quickly take advantage
of LmcRbac.

## Support

- File issues at https://github.com/Laminas-Commons/LmcRbacMvc/issues.
- Ask questions in the [LaminasCommons gitter](https://gitter.im/LaminasCommons) chat.
