# Mage2 Module Ambab StoreOrderPrefix

    ``ambab/module-storeorderprefix``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities


## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Ambab`
 - Enable the module by running `php bin/magento module:enable Ambab_StoreOrderPrefix`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require ambab/module-storeorderprefix`
 - enable the module by running `php bin/magento module:enable Ambab_StoreOrderPrefix`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - enabled (prefix/general/enabled)

 - order_prefix (prefix/general/order_prefix)

 - order_sequence (prefix/general/order_sequence)


## Specifications




## Attributes



