=== Transact ===
Contributors: transact
Donate link: https://transact.io/
Tags: payments, micropayments, e-commerce, paywall, free, pay, online payment, subscription, subscriptions, monetization, premium content, premium, paywall, pay per view, content monetization, donations
Requires at least: 4.5
Tested up to: 4.9.4
Stable tag: 1.7.0
License: APACHE-2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0

Micropayments from $0.01.   Receive payments for digital content on WordPress.

== Description ==

Transact.io brings A la Carte revenue model to digital media.  Charge for content within your posts.

Features:

* The publisher sets the price, which can be as low as $0.01
* Transact.io enables publishers to regain control of distribution.
* Single post, you can set the price from $0.01 to $50.00
* Optionally you can enable Subscriptions,  allow for unlimited content for a fixed monthly or annual rate
* Transact has lower fees than credit cards or paypal.  For transactions less than $1 the commision is 12%, Over $1 it is 2%.

Read more about us at https://transact.io/

This plugin is open source.   Source code is available at https://gitlab.com/transact/transact-wordpress

If you need help setting up your account please do not hesitate to contact us: 
https://transact.io/about/contact

== Installation ==


1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Transact screen to configure the plugin
4. Sign up with a publisher account on https://transact.io/
5. Go to your Developer Settings->Keys   menu  to find your account ID and secret signing key.  Configure these key on WordPress plugin.
6. Create a new wordPress Post, and set the price.


Shortcode manual:
1. You can set up a shortcode directly on your post content, this shortcode will override the default button.
2. Is possible to set up new texts for purchase and subscribe buttons, also choose between the 3 model buttons (as in the post settings)
3. You can choose to show "Only Purchase Button", "Only Subscribe button", "Purchase and Subscribe button".
4. Shortcode is [transact_button]
5. If you do not set up any option, it will use default transact buttons.
6. Options are 'button_text' 'subscribe_text', 'button_type'
7. "Purchase and Subscribe button" = 1, "Only Purchase Button" = 2, "Only Subscribe button" = 3
8. Example: [transact_button button_text="purchase me" subscribe_text="subscribe to the site" button_type="1"]



== Frequently Asked Questions ==

= What is the lowest amount I can charge for content? =

$0.01

= What is the highest amount I can charge for content? =

$50.00

= What are the costs? =

12% of the first dollar of a purchase, after $1 USD 4%.
We are cheaper than any credit card service for small amounts.


= Is there support? =
Yes, please contact us at https://transact.io or  support"at"transact.io

== Screenshots ==

1.  Transact.io  Keys
2.  WordPress Settings
3.  Purchase button on your page, look can be customized on your theme

== Changelog ==

= 1.7.0 =
* Fix for possible URL varable conflict. Test on latest WP

= 1.6.0 =
* promo text fetching,  button styling

= 1.5.2 =
* content stamp

= 1.5.1 =
* shortcode fix

= 1.5.0 =
* Comments closed without purhase

= 1.4.0 =
* Support for donations

= 1.3.0 =
* Support for affiliates. you can put aff=ID in the URL to share affiliate revenue.
Note, you must configure affiliate settings on transact publisher dashboard.

= 1.2.2 =
* Change Tokens to Cents.

= 1.2.1 =
* Fix purchase and subscription button. Prevent multiple buttons from occluding each other.

= 1.2.0 =
* Support for subscriptions

= 1.1.0 =
* Update styling. Button size and fade linear-gradient
* Update File headers

= 1.0 =
* Initial public release


= 0.5 =
* Fixed issues.

== Upgrade Notice ==

= 1.0 =
Initial public release, compatible with old releases for testers.
