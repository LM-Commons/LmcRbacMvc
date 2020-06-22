# LmcRbac

Based on zfc-rbac (v2.6.x) from the Zf-Commons team.


<!--
[![Coverage Status](https://coveralls.io/repos/ZF-Commons/zfc-rbac/badge.png)](https://coveralls.io/r/ZF-Commons/zfc-rbac)
[![Latest Stable Version](https://poser.pugx.org/zf-commons/zfc-rbac/v/stable.png)](https://packagist.org/packages/zf-commons/zfc-rbac)
[![Latest Unstable Version](https://poser.pugx.org/zf-commons/zfc-rbac/v/unstable.png)](https://packagist.org/packages/zf-commons/zfc-rbac)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ZF-Commons/zfc-rbac/badges/quality-score.png?s=685a2b34dc626a0af9934f9c8d246b68a8cac884)](https://scrutinizer-ci.com/g/ZF-Commons/zfc-rbac/)
[![Total Downloads](https://poser.pugx.org/zf-commons/zfc-rbac/downloads.png)](https://packagist.org/packages/zf-commons/zfc-rbac)
-->
[![Master Branch Build Status](https://travis-ci.org/Laminas-Commons/LmcRbac.svg?branch=master)](http://travis-ci.org/Laminas-Commons/LmcRbac)
[![Gitter](https://badges.gitter.im/LaminasCommons/community.svg)](https://gitter.im/LaminasCommons/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)


LmcRbac is an access control module for Laminas Framework, based on the RBAC permission model.

### Important Notes:  

The migration to Laminas is in progress.  
Currently, built passed only against latest version of Laminas components.   
This version has breaking changes with respect to ZfcRbac. See the [Upgrade](#upgrade) section for details.


## Requirements

- PHP 5.6, PHP 7.0 or higher
- [Rbac component](https://github.com/zf-fr/rbac): this is actually a prototype for the ZF3 Rbac component.
- [Laminas Components 3.x or higher](http://www.github.com/laminas)

> 

## Optional

- [DoctrineModule](https://github.com/doctrine/DoctrineModule): if you want to use some built-in role and permission providers.
- [Laminas\DeveloperTools](https://github.com/laminas/Laminas\DeveloperTools): if you want to have useful stats added to
the Laminas Developer toolbar.

## Upgrade

LmcRbac introduces a breaking change from ZfcRbac.  The key `zfc-rbac` in autoload and module config files has been replaced
by the `lmc-rbac` key.

You can find an [upgrade guide](UPGRADE.md) to quickly upgrade your application from major versions of ZfcRbac.

## Installation

LmcRbac only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Install the module:

```sh
$ php composer.phar require laminas-commons/lmc-rbac:dev-master
```

Enable the module by adding `LmcRbac` key to your `application.config.php` or `modules.config.php` file. Customize the module by copy-pasting
the `lmc_rbac.global.php.dist` file to your `config/autoload` folder.

## Documentation

The official documentation is available in the [/docs](/docs) folder.

You can also find some Doctrine entities in the [/data](/data) folder that will help you to more quickly take advantage
of LmcRbac.

## Support

- File issues at https://github.com/Laminas-Commons/LmcRbac/issues.
- Ask questions in the [LaminasCommons gitter](https://gitter.im/LaminasCommons) chat.
