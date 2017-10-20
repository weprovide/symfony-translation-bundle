# We Provide Translation Bundle

The We Provide Translation Bundle incorporates the JMS/TranslationBundle into the Sonata/AdminBundle. Both bundles are awesome but do not work together out of the box. Using this bundle you'll be able to manage your translations through the easy-to-use admin.

## Note

This bundle for now only supports translations in YML files.


## License

This bundle is free to use and open for improvements, please share your thoughts which will be much appreciated.

## Dependencies

This bundle is developed with a Symfony Standard Edition on PHP 7.1 using We Provide version of valet. Obviously it has dependencies to other projects and/or bundles. Below a list of dependencies, please use the installation guides of these bundles.
 * Symfony Standard Edition 3.3
 * Sonata Admin Bundle 3.23
 * JMS Translation Bundle 1.3

## How does this work?

You can use the JMS Translation Bundle to grab and collect all the translations in your own bundles (or all bundles in your project). This will generate YML files for each locale you've configured in a folder you want. Configure this folder and the locales you wish to manage and use this bundle to manage the translations.

// TODO: this bundle in the future will grab and collect the translations for you through a button in the admin.


## Installation

Install this bundle into your project using Composer.

```
composer require .../symfony-translation-bundle
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
    default_locale: '%locale%'
    locales: [en, nl]
    translation_files: ['@YourAppBundle/Resources/translations']
```

Add the bundle to your Sonata Admin Bundle in the `config.yml`.

```yaml
sonata_admin:
  dashboard:
    groups:
      translations:
        label: 'Translations'
        items:
          - we_provide_admin_translation  
```