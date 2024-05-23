# LmcRbacMvc

[![Build](https://github.com/visto9259/LmcRbacMvc/actions/workflows/build_test.yml/badge.svg)](https://github.com/visto9259/LmcRbacMvc/actions/workflows/build_test.yml)
[![Version](https://poser.pugx.org/lm-commons/lmc-rbac-mvc/version)](//packagist.org/packages/lm-commons/lmc-rbac-mvc)
[![Total Downloads](https://poser.pugx.org/lm-commons/lmc-rbac-mvc/downloads)](//packagist.org/packages/lm-commons/lmc-rbac-mvc)
[![License](https://poser.pugx.org/lm-commons/lmc-rbac-mvc/license)](//packagist.org/packages/lm-commons/lmc-rbac-mvc)
[![Coverage Status](https://coveralls.io/repos/github/LM-Commons/LmcRbacMvc/badge.svg?branch=master)](https://coveralls.io/github/LM-Commons/LmcRbacMvc?branch=master)
![Dynamic JSON Badge](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fapi.github.com%2Frepos%2Flm-commons%2Flmcrbacmvc%2Fproperties%2Fvalues&query=%24%5B%3A1%5D.value&label=Maintenance%20Status)
[![Static Badge](https://img.shields.io/badge/Chat_on-Slack-blue)](https://join.slack.com/t/lm-commons/shared_invite/zt-2gankt2wj-FTS45hp1W~JEj1tWvDsUHQ)

Role-based access control module to provide additional features on top of Zend\Permissions\Rbac

Based on [ZF-Commons/zfc-rbac](https://github.com/ZF-Commons/zfc-rbac) v2.6.x. If you are looking for the Laminas version of zfc-rbac v3, please use [LM-Commons/LmcRbac](https://github.com/LM-Commons/LmcRbac).

### Important Notes:  
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

The official documentation is available [here](https://lm-commons.github.io/LmcRbacMvc).

You can also find some Doctrine entities in the [/data](/data) folder that will help you to more quickly take advantage
of LmcRbac.

## Support

- File issues at https://github.com/LM-Commons/LmcRbacMvc/issues.
- Ask questions in the [LM-Commons Slack](https://join.slack.com/t/lm-commons/shared_invite/zt-2gankt2wj-FTS45hp1W~JEj1tWvDsUHQ) chat.
