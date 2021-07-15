=== Woocommerce - amoCRM - Integration ===
Contributors: https://codecanyon.net/user/itgalaxycompany
Tags: amocrm, amocrm leads, business leads, woocommerce, woocommerce amocrm, order, integration, lead finder, lead management, lead scraper, leads, marketing leads, sales leads, crm

== Description ==

The main task of this plugin is a send your Woocommerce orders directly to your amoCRM account.

= Features =

* Integrate your `Woocommerce` orders with amoCRM;
* Integrate your `Woocommerce` customers with amoCRM;
* Creation of the lead, occurs together with the creation / binding (used existing if there is) of the contact and company. (if their fields are filled);
* Custom fields are loaded from the CRM;
* Sending data about the products in order to the lead (in note);
* Support amo products;
* Supports for sending order status changes;
* Supports for getting order status changes from CRM (via webhook amoCRM);
* Support creating task to lead;
* Supports for `utm` params in `URL` to use;
* Supports for `roistat_visit` cookie to use;
* Supports for `GA Client ID` cookie to use;
* Multiple pipeline support;
* Bulk order sending capability;
* Image previews;
* Super easy to set-up;

== Installation ==

1. Extract `woocommerce-amocrm-integration.zip` and upload it to your `WordPress` plugin directory
(usually /wp-content/plugins ), or upload the zip file directly from the WordPress plugins page.
Once completed, visit your plugins page.
2. Be sure `Woocommerce` Plugin is enabled.
3. Activate the plugin through the `Plugins` menu in WordPress.
4. Go to the `Woocommerce` -> `amoCRM`.
5. Create integration in your amo - Settings -> Integration.
6. Enter the domain name of your account `amoCRM` (without schema, i.e. http:// or https://).
7. Enter Secret key, Integration ID and Authorization code.
8. Save settings.

== Changelog ==

= 2.10.0 =
Feature: added new tag - [order_status_title].
Feature: added new tag - [order_status_id].

= 2.9.2 =
Chore: filterable lead timestamps.
Chore: more logs in the process of preparing products.
Feature: support sending cookie `_ym_uid`.
Feature: added new tags - [order_comments], [order_total_without_shipping_and_tax].

= 2.7.2 =
Fixed: access to the order object when updating fields.
Fixed: re-setting the amount in the lead after filling in the products so that the value of the amount exactly matches the site.
Feature: ability to disable sending status changes.
Feature: optional sending changes by fields when sending status changes (or order changed) for a lead (after the first data sending).

= 2.5.2 =
Fixed: fired send when renew order `WooCommerce Subscriptions`.
Chore: use `webpack` to build assets.
Feature: ability to delete all order links with amoCRM leads.

= 2.4.1 =
Chore: ability to change the redirect link for integration.
Feature: set only one last matching status, since if one stage is specified in several statuses, then this creates several status change events (amo webhook processing).
Feature: show shipping item meta data from the plugin `WooCommerce Local Pickup Plus`
Feature: show shipping item meta data in note.

= 2.1.11 =
Chore: minor improvements in downloading the log through the admin panel.
Chore: show lead id in order list.
Chore: remove the slash at the beginning and at the end of the domain, as the user can accidentally indicate this.
Fixed: added delay when processing amocrm event, since this can create an incorrect behavior with a reverse change in order status.
Fixed: compatibility with WP 5.5
Chore: clean auth code after ajax check.
Chore: compatibility check with WC 4.4
Chore: maybe empty fields in `order` section checkout fields.
Fixed: create amo product process.
Chore: processing disabled integration error.
Fixed: the logic of checking the integration activity when displaying an item in the bulk actions of the order list.
Feature: added new tag - [order_currency].
Feature: authorization process in amoCRM changed to oauth2 (using api key is no longer relevant).

= 1.32.0 =
Feature: added new tag - [order_total_weight].
Chore: more logs.
Feature: send by wp cron (with a delay) or immediately.
Feature: added new tags - [payment_method_id], [shipping_method_id].

= 1.29.1 =
Fixed: send enum fields.
Feature: more supports fields `Checkout Field Editor for WooCommerce`.
Feature: added field to create additional note for the lead.
Feature: added new tag - [shipping_price].

= 1.26.4 =
Fixed: processing send field with subtypes.
Chore: a more convenient field option when setting for the field type is `long text`.
Chore: clear spaces from phone number before searching.
Chore: more logs.
Feature: added new tag - [order_coupon_list].
Feature: use new api to create lead.
Chore: added notice if `status mapping` not configured.
Feature: reset fields cache by button without cron.

= 1.23.4 =
Chore: use composer autoloader.
Chore: show only deal stages in select.
Fixed: the menu item is not displayed when using `Admin Menu Editor`.
Fixed: if a responsible person is assigned for the deal, then assign it to the task.
Feature: support for processing any meta order values (value must be written before the order is sent to crm).

= 1.22.3 =
Fixed: if a responsible person is assigned for the deal, then assign it to the contact.
Chore: ability to override the log file path.
Chore: the list of users id is displayed next to the field of the responsible.
Feature: multiple responsible user.

= 1.21.0 =
Feature: new filter `itglx_wcamo_lead_fields`.
Chore: more logs.
Feature: show item meta data in note.

= 1.19.2 =
Chore: sku added to product list in note.
Chore: more logs.
Feature: added new tag - [order_product_titles_list].

= 1.18.0 =
Feature: support amo products.

= 1.17.0 =
Feature: added new tag - [order_product_cat_name_list].

= 1.16.1 =
Fixed: check whether the order was sent to CRM.

= 1.16.0 =
Feature: added the ability to log (disabled by default).

= 1.15.2 =
Fixed: handling deleted products when sending orders.

= 1.15.1 =
Fixed: getting real payment method title.
Fixed: using the delivery date from the plugin `Order Delivery Date for WooCommerce`.

= 1.15.0 =
Feature: using the delivery date from the plugin `Order Delivery Date for WooCommerce`.

= 1.14.2 =
Fixed: contact processing.

= 1.14.1 =
Fixed: getting the list of fields on the settings page.

= 1.14.0 =
Feature: added new tag - [order_product_sku_list].
Feature: added new tag - [order_product_titles_by_product_cat_:cat_id].

= 1.13.0 =
Feature: deal creation date is the order creation date.
Feature: added new tag - [order_create_date].

= 1.12.1 =
Chore: added new tag - [shipping_method_title].

= 1.12.0 =
Feature: populate the value of the select and multiselect field from the form field (lead).

= 1.11.2 =
Feature: using the delivery date from the plugin `Order Delivery Date for WooCommerce (Lite version)`.

= 1.11.1 =
Fixed: create task process.
Fixed: missed required task field.

= 1.11.0 =
Feature: creating a task for a deal.
Feature: sending the name of the payment method in a note.

= 1.10.1 =
Fixed: the name of the shipping method with zero cost was not sent.
Fixed: compatibility with `Woocommerce Poor Guys Swiss Knife`.

= 1.10.0 =
Feature: added new tag - [order_admin_edit_link].
Feature: ability add a link to the order on the site in the note.

= 1.9.0 =
Feature: ability create companies.

= 1.8.0 =
Feature: ability to delete orders when deleting a lead.
Feature: ability to specify one contact id for all leads.

= 1.7.0 =
Feature: compatibility with `WooCommerce Checkout Field Editor`.
Feature: compatibility with `WooCommerce Checkout Add-Ons`.
Feature: the ability to disable contact updates.

= 1.6.0 =
Feature: Bulk sending orders to CRM.

= 1.5.0 =
Feature: Getting lead status changes from CRM (via webhook amoCRM).

= 1.4.1 =
Feature: Support for `GA Client ID`.
Fixed: compatibility with `Booster for WooCommerce` custom checkout fields.
Fixed: compatibility with `WP Crowdfunding`.

= 1.4.0 =
Feature: Updating the data in an existing contact.
Feature: Create / update a contact when user registering / updates profile information.

= 1.3.0 =
Feature: The name of the deal can be specified by a template.
Feature: Added new tags - [order_number] [first_product_title] [payment_method_title].

= 1.2.0 =
Feature: Support for sending status changes.

= 1.1.0 =
Feature: Support sending cookie `roistat_visit` to CRM.

= 1.0.0 =
Initial public release
