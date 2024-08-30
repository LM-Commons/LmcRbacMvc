---
sidebar_position: 5
---

# Plugins and Helpers

LmcRbacMvc provides a controller plugin and view helpers to check authorization.


## Controller plugins

#### `isGranted(string $permission, $context=null): bool`

Basic usage:

```php
    public function doSomethingAction()
    {
        if (!$this->isGranted('myPermission', $context)) {
            // redirect if not granted for example
        }
    }
```

## View Helpers

#### `isGranted(string $permission, $context=null): bool`

Basic usage:

```php
    <?php if ($this->isGranted('myPermission', $myContext)): ?>
    <div>
        <p>Display only if granted</p>
    </div>
    <?php endif ?>
```

#### `hasRole(array $roleOrRoles): bool`

Basic usage:

```php
    <?php if ($this->hasRole(['admin'])): ?>
    <div>
        <p>Display only if user has the admin role</p>
    </div>
    <?php endif ?>
```


