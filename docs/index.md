# Pagar.me Split Payment for WooCommerce

## Requirements
- [Woocommerce](https://github.com/woocommerce/woocommerce)
- [WooCommerce Pagar.me](https://github.com/claudiosanches/woocommerce-pagarme) up and running
- Pagar.me account

## Install
- Send the plugin files to wp-content/plugins folder or install using the plugins installer from WordPress
- Activate the plugin

## Setup

##### Main Recipient
With the plugin installed, you should go to WordPress Dashboard > "Pagar.me Split Payment" and fill the fields with your bank information, document and your name. This will create a [recipient](https://docs.pagar.me/v4-Eng/docs/criando-um-recebedor-1) at Pagar.me and this recipient will be used as the main.

This recipient will receive all the remaining amount after the split rules have been applied, including values not designated to partners/recipients and other fees (e.g. installment fees). Also, it will be charged by transaction fees in the payment gateway.

##### Recipients
Every recipient must have an account in your WordPress.
To create a user you should go to WordPres > Users > "Add New".
The user must have the role "Partner".
"Partner Payment Data" needs to be filled with the user's bank information, document and name.

##### Add recipient to a product
In order to add a recipient to a product you should go to product edit screen (WordPress > Products > Your Product). 
In the edit screen there'll be a box called "Pagar.me Split Payment - Product Data" and in this box you should select if you want a percentage commission or a fixed amount.
After selecting the commission type you should add an entry, select the user and fill with its commission value.

Note: Fixed amount commission will limit the recipients to only one partner.

## Functionalities
- Multiple Recipients
- Percentage commission
- Fixed amount commission
- Commission History

## Changelog
Changelog can be found [here](https://github.com/insus-tecnologia/pagarme-split-payment-woocommerce/blob/master/CHANGELOG.md)
