=== WP SendFox ===
Contributors: bogdanfix
Donate link: https://paypal.me/bogdanfix
Tags: sendfox, integration, woocommerce, wordpress, wp, export, emails, users, api
Requires at least: 4.6
Tested up to: 6.4
Requires PHP: 5.2.4
Stable tag: 1.3.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Capture emails and add them to your SendFox list via comments, registration, WooCommerce checkout, Gutenberg page or Divi Builder page. Export your WP users and WooCommerce customers to your list.

== Description ==

This plugin lets you capture emails from your WP comment form, WP registration form, WooCommerce checkout form, pages built with Gutenberg and Divi Builder.

You can:
* add subscribe checkbox to any of these forms
* make it pre-checked
* make subscription implicit (hidden checkbox)

Also you can easily export your WordPress users and/or WooCommerce customers to one of your lists in just 3 clicks, literally.

All you need to start using this plugin is your SendFox API key (aka "Personal Access Token").

###### SendFox website, title and logo are owned by [Sumo Group, Inc.](https://sumo.com/?utm_source=sf4wp&utm_medium=web&utm_campaign=wpplugindesc) ######

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-sendfox` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Go to [your SendFox account settings](https://sendfox.com/account/oauth) to create "Personal Access Token".
1. Copy your new "Personal Access Token".
1. Go to the admin menu->SendFox->Connect screen to configure the plugin.
1. Paste your "Personal Access Token" into "API Token" field and click "Save Changes".
1. If "Status" field shows green box saying "CONNECTED: Your_Account_Name", your SendFox account is connected successfully.
1. Now you can go to "Integrations" and "Export" tabs. Use "Integrations" tab to configure how your visitors will subscribe to your list from your site. And "Export" tab to export existing WP users or WooCommerce customers.

== Frequently Asked Questions ==

= Do I need to reveal my SendFox login and/or password to use this plugin? =

No, definitely not. This plugin uses a secure API connection to SendFox via the special API key, that you need to generate in your account first. Your login credentials are not revealed.

== Screenshots ==

1. Connect screen. Add your SendFox API key here and check connection.
2. Integrations screen. List of all available integrations.
3. Comment Form integration screen. Capture emails of those who comments on your site.
4. WooCommerce Checkout integration screen. Capture emails of those who buys in your store.
5. Export screen. Export emails of WordPress users or WooCommerce customers to your SendFox list.
6. Gutenberg Editor. New Email Optin block connected to your SendFox account.
7. Divi Builder. SendFox added to the list of email providers in Divi Email Optin block.

== Changelog ==

= 1.3.1 =
* feature: support for WooCommerce HPOS;
* tweak: improved Access Control for gb_sf4wp_process_sync() (Big thanks to Abdi Pranata);

= 1.3.0 =
* integration: LearnDash Course Enrollment (Big thanks to Rodolfo Martinez!);
* feature: added WooCommerce HPOS support;

= 1.2.0 =
* feature: (Gutenberg Opt-in Block) added form border styling options, button border styling options, form width (fixed or 100%), added separate sections for Form and Button options in Gutenberg Sidebar, added transparency selector for Form Background colorpicker;
* tweak: added minified CSS file and enqueued it instead; updated minified js;
* tweak: fixed typo for comment_id variable and logics filtering comment email submission;

= 1.1.0 =
* feature: integrated SendFox to the Divi Builder's Email Optin module (Big thanks to Rodolfo Martinez!);
* feature: added a custom Gutenberg Email Optin block that allows to pick a list and subscribe to SendFox (Big thanks to Rodolfo Martinez!);
* feature: (optionally) submit comment author email to SendFox only after comment is approved manually;
* tweak: submit comment author email to SendFox only after comment is approved automatically;
* tweak: replaced esc_url with esc_attr to fix "Clear log" link;

= 1.0.2 =
* tweak: fixed duplicate "select the list..." in "SendFox list" dropdown fields;
* tweak: "SendFox list" dropdown was only showing the first 10 lists from your account, fixed that;
* feature: added 24-hour cache for lists available in "SendFox list" dropdown to speed up the admin pages, and "Reload lists" button under "Connect" tab to clear that cache manually;

= 1.0.1 =
* tweak: skip options filtering on the first save, otherwise options are not saved and overwritten by FALSE; 
* tweak: added optional argument to the logging function;

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.1.0 =
Integration for Divi, Gutenberg opt-in block, spam fix for Comment Form integration.

= 1.0.2 =
A couple of minor tweaks + speed improvement.

= 1.0.1 =
Fixed plugin settings not saving correctly.

= 1.0.0 =
Initial release.