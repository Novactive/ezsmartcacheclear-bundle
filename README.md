# eZ (platform) Smart Cache Clear Bundle

## About

This eZ Platform bundle intends to provide an eZ publish like smart cache clearing mechanisme.

The following clearing rules are available :

* parents
* siblings
* children
* subtree

## Installation

The recommended way to install this bundle is through [Composer](http://getcomposer.org/). Just run :

```bash
composer require novactive/ezsmartcacheclear-bundle
```

Register the bundle in the kernel of your application :

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new Novactive\eZSmartCacheClearBundle\NovaEzSmartCacheClearBundle(),
    ];

    ...

    return $bundles;
}
```

## Configuration

Once the bundle registered, you should configure the rules you want to apply for your content types, by defining the following config :

```yaml
nova_ez_smart_cache_clear:
    config:
        my_siteaccess:
            publish:
                -
                    content_type: my_content_type
                    rules:
                        parents: { enabled: true, nbLevels: 4 }
                        children: { enabled: true }
                        siblings: { enabled: true }
                        subtree: { enabled: true }
```

## License

This bundle is released under the MIT license. See the complete license in the bundle:

```bash
LICENSE
```
