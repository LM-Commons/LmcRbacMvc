---
sidebar_position: 4
sidebar_label: Debugging Tools
---

# Debugging Tools

[LmcRbacMvcDeveloperTools](https://github.com/LM-Commons/LmcRbacMvcDeveloperTools) is an extension
for the [Laminas Developer Tools](https://github.com/laminas/laminas-developer-tools), 
that collects and displays debugging data on settings, guards and roles.

## Installation

```shell
$ composer require --dev lm-commons/lmc-rbac-mvc-devtools
```

Composer should ask to install the module. Typically, this module will go in `development.config.php`.

## Toolbar

LmcRbacMvcDeveloperTools provides toolbars to view settings, guards and roles:

![Settings](images/settings.png)
![Guards](images/guards.png)
![Roles](images/roles.png)
