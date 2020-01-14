## Ambab StoreOrderPrefix

Admin can add the prefixes to an Order ID, Invoice ID, Shipment ID, Credit Memo ID as per the different stores like Country wise store orders can have a different prefix. The admin can set the prefix in any form as it supports numeric, alphanumeric and alphabetic prefixes.

Magento default functionality does not allow the admin to edit or change the IDs. Hence it becomes difficult to search and filter the records just by using the numbers.  But, with the help of this Store Order Prefix module, In the backend, the store owner can set the prefixes for order Id, invoice Id, shipment Id, and credit memo. These prefixes can be created for different stores and store views. This extension for Magento 2 will help store owners to filter out the specific ids from a long list of records.

## Features

- Admin can add custom prefixes for the order IDs, shipment IDs, invoice IDs & shipment IDs.

- The custom prefix can set in the form of numeric, alphabetic, or alphanumeric.

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

Go to Admin -> Stores -> Configuration -> **Select store view** -> Ambab -> Store Order Prefix


## Contribute

Feel free to fork and contribute to this module by creating a PR to master branch (https://github.com/ambab-infotech/orderprefix).

## Support

For issues please raise here https://github.com/ambab-infotech/orderprefix/issues

In case of additional support feel free to reach out at tech.support@ambab.com
