# We Provide Translation Bundle

The We Provide Translation Bundle for Symfony incorporates the JMS/TranslationBundle into the Sonata/AdminBundle. Both bundles are awesome but do not work together out of the box. Using this bundle you'll be able to extract and manage your translations through the easy-to-use admin.


## Dependencies

This bundle is developed with a Symfony Standard Edition on PHP 7.1 using We Provide's version of valet. Obviously it has dependencies to other projects and/or bundles. Below a list of dependencies, please use the installation guides of these bundles first.
 * [Symfony Standard Edition 3.3](https://symfony.com/doc/current/setup.html#creating-symfony-applications-with-composer)
 * [Sonata Admin Bundle 3.23](https://sonata-project.org/bundles/admin/3-x/doc/getting_started/installation.html) (with [SonataDoctrineORMAdminBundle](https://sonata-project.org/bundles/doctrine-orm-admin/master/doc/reference/installation.html))
 * [JMS Translation Bundle 1.3](https://jmsyst.com/bundles/JMSTranslationBundle/master/installation)
 * [Ivory CK Editor ^4.0](https://github.com/egeloen/IvoryCKEditorBundle)


## Installation

Install this bundle into your project using Composer.

```
composer require weprovide/symfony-translation-bundle
```

Enable the bundle by inserting it in your Symfony's `AppKernel.php`.
```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new WeProvide\TranslationBundle\WeProvideTranslationBundle(),
        );

        // ...
    }

    // ...
}
```

Configure your locales and translation files folder in your `config.yml`.

```yaml
we_provide_translation:
    # defaults to 'en' if not set, you can use strings like 'en' or a parameter
    default_locale: '%locale%'
    
    # required, array with locales you wish the manage
    locales: [en, nl_NL]
    
    # optional, array with (paths of) your bundles to extract translations from
    # if omitted, extracts from your complete project 
    translate_bundles: [YourAppBundle]
    
    # optional, location where translation files will be written
    # if omitted, '@WeProvidePortalBundle/Resources/translations' will be the target
    resource: '@YourAppBundle/Resources/translations'
```


## Commands

In production or other non-debug environments the translations stored in YML are only updated when the cache is cleared. This bundle provides a command to clear cache when a translation has been changed. It's safe to execute or set this command as cron.

```
php bin/console weprovide:translation:recache --env=[prod,stag,dev]
```

## License

This bundle has been released under the MIT license and open for improvements, please share your thoughts which will be much appreciated.



## Authors

- Mischa Braam ([@mischabraam](https://github.com/mischabraam))