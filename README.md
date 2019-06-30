éNuage version updater command
=======================

[![Packagist](https://img.shields.io/packagist/v/enuage/version-updater.svg)](https://packagist.org/packages/enuage/version-updater)
[![Packagist](https://img.shields.io/packagist/l/enuage/version-updater.svg)](https://packagist.org/packages/enuage/version-updater)

Installation
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require enuage/version-updater
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require version-updater
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Enuage\VersionUpdaterBundle\VersionUpdaterBundle(),
        );
        // ...
    }
    // ...
}
```

Usage
=====

## Option 1: Updating files via command line

### Step 1: Define files and regular expression for version updating

Use `\V` for define the version in regular expression. It will be replaced with the SemVer regular expression

Exapmple:

```yml
enuage_version_updater:
    files:
        - '.env': '/^(API_VERSION=)\V/m'
        - 'README.md': '/^(Version:\s)\V/m'
        - 'composer.json': '/^(\s*\"version\":\s*\")\V(\"\,?)/m'
```

### Step 2: Use the command for version updating

```
$ php bin/console enuage:version:update <version|--option>
```

Available options
-----------------

`--major`: Increase only major version

`--minor`: Increase only minor version

`--patch`: Increase only patch version

`--alpha`: Increase or define the alpha version

`--beta`: Increase or define the beta version

`--rc`: Increase or define the release candidate version

`--down`: Decrease defined version. It's also applicable to prerelease versions

`--release`: Remove all prerelease versions

`--date <none|PHP date format>`: Add date metadata to the version. By default date format is equal to `c`

`--meta <data>`: Add metadata to the version

## Option 2: Updating version via service

Example:

```php
/** @var Container $container */
use Enuage\VersionUpdaterBundle\DTO\VersionOptions;use Symfony\Component\DependencyInjection\Container;$service = $container->get('enuage.version.service');

$version = '0.1.0-alpha.2';

$options = new VersionOptions();
$options->increasePreRelease();

$service->update($version, $options); // Result: "0.1.0-alpha.3"
```

Available methods
-----------------

- `addDateMeta(format = null)`: Enable date meta in provided format or ['c'][1] by default
- `addMeta(value = null)`: Add custom meta to the tag
- `increaseMajor()`: Enable major version increase
- `decreaseMajor()`: Disable major version increase
- `increaseMinor()`: Enable minor version increase
- `decreaseMinor()`: Disable minor version increase
- `increasePatch()`: Enable patch version increase
- `decreasePatch()`: Disable patch version increase
- `updateAlpha()`: Enable pre-release version `alpha` modifications
- `updateBeta()`: Enable pre-release version `beta` modifications
- `updateReleaseCandidate()`: Enable pre-release version `rc` modifications
- `increasePreRelease()`: Increase pre-release version  (e.g: `0.1.0-alpha.2` -> `0.1.0-alpha.3`)
- `decreasePreRelease()`: Decrease pre-release version (e.g: `0.1.0-alpha.2` -> `0.1.0-alpha.1`)
- `setVersion()`: Set custom version value
- `downgrade()`: Downgrade the version, including pre-release
- `release()`: Disable all pre-release postfixes

[1]: https://www.php.net/manual/en/function.date.php
