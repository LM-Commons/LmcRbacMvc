# LmcRbac

Based on zfc-rbac from the Zf-Commons team.


<!--
[![Master Branch Build Status](https://secure.travis-ci.org/ZF-Commons/zfc-rbac.png?branch=master)](http://travis-ci.org/ZF-Commons/zfc-rbac)
[![Coverage Status](https://coveralls.io/repos/ZF-Commons/zfc-rbac/badge.png)](https://coveralls.io/r/ZF-Commons/zfc-rbac)
[![Join the chat at https://gitter.im/LaminasCommons](https://badges.gitter.im/LaminasCommons.svg)](https://gitter.im/ZFCommons/zfc-rbac?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Latest Stable Version](https://poser.pugx.org/zf-commons/zfc-rbac/v/stable.png)](https://packagist.org/packages/zf-commons/zfc-rbac)
[![Latest Unstable Version](https://poser.pugx.org/zf-commons/zfc-rbac/v/unstable.png)](https://packagist.org/packages/zf-commons/zfc-rbac)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ZF-Commons/zfc-rbac/badges/quality-score.png?s=685a2b34dc626a0af9934f9c8d246b68a8cac884)](https://scrutinizer-ci.com/g/ZF-Commons/zfc-rbac/)
[![Total Downloads](https://poser.pugx.org/zf-commons/zfc-rbac/downloads.png)](https://packagist.org/packages/zf-commons/zfc-rbac)
-->
[![Gitter](https://badges.gitter.im/LaminasCommons/community.svg)](https://gitter.im/LaminasCommons/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)


LmcRbac is an access control module for Laminas Framework, based on the RBAC permission model.

### Important Note:  Port to Laminas is in progress. 



## Requirements

- PHP 5.6, PHP 7.0 or higher
- [Rbac component](https://github.com/zf-fr/rbac): this is actually a prototype for the ZF3 Rbac component.
- [Laminas Components 2.2 or higher](http://www.github.com/zendframework/zf2)

> If you are looking for older version of ZfcRbac, please refer to the 0.2.x branch.
> If you are using ZfcRbac 1.0, please upgrade to 2.0.

## Optional

- [DoctrineModule](https://github.com/doctrine/DoctrineModule): if you want to use some built-in role and permission providers.
- [Laminas\DeveloperTools](https://github.com/laminas/Laminas\DeveloperTools): if you want to have useful stats added to
the Laminas Developer toolbar.

## Upgrade

You can find an [upgrade guide](UPGRADE.md) to quickly upgrade your application from major versions of ZfcRbac.

## Installation

LmcRbac only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Install the module:

```sh
$ php composer.phar require laminas-commons/lmc-rbac:~2.4
```

Enable the module by adding `lmcRbac` key to your `application.config.php` file. Customize the module by copy-pasting
the `lmc_rbac.global.php.dist` file to your `config/autoload` folder.

## Documentation

The official documentation is available in the [/docs](/docs) folder.

You can also find some Doctrine entities in the [/data](/data) folder that will help you to more quickly take advantage
of LmcRbac.

## Support

- File issues at https://github.com/Laminas-Commons/LmcRbac/issues.
- Ask questions in the [LaminasCommons gitter](https://gitter.im/LaminasCommons) chat.
