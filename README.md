# LmcRbacMvc

[![Version](https://poser.pugx.org/lm-commons/lmc-rbac-mvc/version)](//packagist.org/packages/lm-commons/lmc-rbac-mvc)
[![Total Downloads](https://poser.pugx.org/lm-commons/lmc-rbac-mvc/downloads)](//packagist.org/packages/lm-commons/lmc-rbac-mvc)
[![License](https://poser.pugx.org/lm-commons/lmc-rbac-mvc/license)](//packagist.org/packages/lm-commons/lmc-rbac-mvc)
[![Build](https://github.com/lm-commons/LmcRbacMvc/actions/workflows/build_test.yml/badge.svg)](https://github.com/lm-commons/LmcRbacMvc/actions/workflows/build_test.yml)
<!-- ![Gitter](https://badges.gitter.im/LM-Commons/community.svg)](https://gitter.im/LM-Commons/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge) -->
[![Coverage Status](https://coveralls.io/repos/github/LM-Commons/LmcRbacMvc/badge.svg?branch=master)](https://coveralls.io/github/LM-Commons/LmcRbacMvc?branch=master)
<!-- [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LM-Commons/LmcRbacMvc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LM-Commons/LmcRbacMvc/?branch=master) -->

Role-based access control module to provide additional features on top of Laminas\Permissions\Rbac

## Requirements

- PHP 8.1 or higher
- [Laminas Components 3.x or higher](http://www.github.com/laminas)


## Optional

- [DoctrineModule](https://github.com/doctrine/DoctrineModule): if you want to use some built-in role and permission providers.
- [Laminas\DeveloperTools](https://github.com/laminas/Laminas\DeveloperTools): if you want to have useful stats added to
the Laminas Developer toolbar.

## Upgrade

LmcRbacMvc introduces breaking changes from zfcrbac v2:
- The namespace has been changed from `ZfcRbac` to `LmcRbacMvc`. 
- The key `zfc_rbac` in autoload and module config files has been replaced
by the `lmc_rbac` key.

You can find an [upgrade guide](UPGRADE.md) to quickly upgrade your application from major versions of ZfcRbac.

## Installation

LmcRbacMvc only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Install the module:

```sh
$ php composer.phar require lm-commons/lmc-rbac-mvc
```
Enable the module by adding `LmcRbacMvc` key to your `application.config.php` or `modules.config.php` file. Customize the module by copy-pasting
the `lmc_rbac.global.php.dist` file to your `config/autoload` folder.

## Documentation

The official documentation is available in the [/docs](/docs) folder.

You can also find some Doctrine entities in the [/data](/data) folder that will help you to more quickly take advantage
of LmcRbac.

## Support

- File issues at https://github.com/LM-Commons/LmcRbacMvc/issues.
