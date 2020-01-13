## Ambab StoreOrderPrefix

Admin can add the prefixes to an Order ID, Invoice ID, Shipment ID, Credit Memo ID as per the different store views.

Prefix can be set in any form numeric, alphanumeric and alphabetic.

## Features

- Add prefixes to an Order ID, Invoice ID, Shipment ID, Credit Memo ID.

- With admin configuration.

- 100% open source.

- Easy to install.


## Installation/ Uninstallation [Versions supported: 2.3.x onwards]

**Steps to install with composer**

- composer require ambab/module-storeorderprefix

- bin/magento module:enable Ambab_StoreOrderPrefix

- bin/magento setup:upgrade

- bin/magento setup:di:compile

- bin/magento cache:flush

**Steps to uninstall a composer installed module**

- bin/magento module:disable Ambab_StoreOrderPrefix

- bin/magento module:uninstall Ambab_StoreOrderPrefix

- composer remove ambab/module-slidingcart

- bin/magento cache:flush


**Steps to install module manually in app/code**

- Add directory to app/code/Ambab/StoreOrderPrefix/ manually

- bin/magento module:enable Ambab_StoreOrderPrefix

- bin/magento setup:upgrade

- bin/magento cache:flush

**Steps to uninstall a manually added module in app/code**

- bin/magento module:disable Ambab_StoreOrderPrefix

- remove directory app/code/Ambab/StoreOrderPrefix manually

- bin/magento setup:upgrade

- bin/magento cache:flush


## Configurations

Go to Admin -> Stores -> Configuration -> Ambab -> Store Order Prefix


## Contribute

Feel free to fork and contribute to this module by creating a PR to develop branch.

## Support

Please feel free to reach out at tech.support@ambab.com
