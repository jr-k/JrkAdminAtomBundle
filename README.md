Getting started with JrkAdminAtomBundle
======================================

Setup
-----
JrkAdminAtomBundle is a CRUD generator for Symfony2


- Using composer

Add jrk/adminatom-bundle as a dependency in your project's composer.json file:

```
{
    "require": {
        "jrk/adminatom-bundle": "dev-master"
    }
}
```
Update composer
```
php composer update
or 
php composer.phar update


- Add JrkAdminAtomBundle to your application kernel

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Jrk\AdminAtomBundle\JrkAdminAtomBundle(),
    );
}
```


Configuration
-------------

``` yml
# app/config/config.yml

jrk_admin_atom:
    routing:
        prefix: 'back'
```

Usage
-----