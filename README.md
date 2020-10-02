# LmcRbacMvc

[![Version](https://poser.pugx.org/lm-commons/lmc-rbac-mvc/version)](//packagist.org/packages/lm-commons/lmc-rbac-mvc)
[![Total Downloads](https://poser.pugx.org/lm-commons/lmc-rbac-mvc/downloads)](//packagist.org/packages/lm-commons/lmc-rbac-mvc)
[![License](https://poser.pugx.org/lm-commons/lmc-rbac-mvc/license)](//packagist.org/packages/lm-commons/lmc-rbac-mvc)
[![Master Branch Build Status](https://travis-ci.org/LM-Commons/LmcRbacMvc.svg?branch=master)](http://travis-ci.org/LM-Commons/LmcRbac)
[![Gitter](https://badges.gitter.im/LM-Commons/community.svg)](https://gitter.im/LM-Commons/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)
[![Coverage Status](https://coveralls.io/repos/github/LM-Commons/LmcRbacMvc/badge.svg?branch=master)](https://coveralls.io/github/LM-Commons/LmcRbacMvc?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LM-Commons/LmcRbacMvc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LM-Commons/LmcRbacMvc/?branch=master)

Role-based access control module to provide additional features on top of Zend\Permissions\Rbac

Based on [ZF-Commons/zfc-rbac](https://github.com/ZF-Commons/zfc-rbac) v2.6.x. If you are looking for the Laminas version of zfc-rbac v3, please use [LM-Commons/LmcRbac](https://github.com/LM-Commons/LmcRbac).

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
- The key `zfc_rbac` in autoload and module config files has been replaced
by the `lmc_rbac` key.

You can find an [upgrade guide](UPGRADE.md) to quickly upgrade your application from major versions of ZfcRbac.

## Installation

LmcRbacMvc only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Install the module:

```sh
$ php composer.phar require lm-commons/lmc-rbac-mvc:^3.0
```
This will install a Laminas MVC equivalent of zfc-rbac 2.6.3.

Enable the module by adding `LmcRbacMvc` key to your `application.config.php` or `modules.config.php` file. Customize the module by copy-pasting
the `lmc_rbac.global.php.dist` file to your `config/autoload` folder.

## Documentation

The official documentation is available in the [/docs](/docs) folder.

You can also find some Doctrine entities in the [/data](/data) folder that will help you to more quickly take advantage
of LmcRbac.

## Support

- File issues at https://github.com/LM-Commons/LmcRbacMvc/issues.
- Ask questions in the [LM-Commons gitter](https://gitter.im/LM-Commons/community) chat.
