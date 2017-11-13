=== Plugin Name ===
Contributors: WP Church Team, Jordesign
Donate link: http://wpchurch.team
Tags: giving, tithely, church
Requires at least: 3.0.1
Tested up to: 4.9
Stable tag: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Tithely is an online giving solution for Churches. This plugin makes it easy to integrate their giving button into your WordPress website.

== Description ==

[Tithely](https://tithe.ly/) is an online giving solution for Churches. They offer giving through their app - but also provide a 'Give' button to be inserted into your website. This plugin makes it easy for you to insert your Tithely 'Give' button into your WordPress Site - using either a Sidebar Widget or a Shortcode.

#Requires SSL Certificate
Whilst Tithely processes payments securely - we recommend you only use the give button on  a page that is protected with SSL encryption. It's pretty cheap to add and usually costs around $30-50 a year depending on the service. Talk to your hosting provider to find out more details.



== Installation ==


1. Upload `wp-tithely.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Sign up for your church's Tithely account at [https://tithe.ly/](https://tithe.ly/) and note your 'Church ID'
1. Enter your 'Church ID' and some default text for your give button in the settings (Settings > Tithely Options)

== Frequently Asked Questions ==

= Where do I Find My Church ID =

Once you register an account and a church with [Tithely](https://tithe.ly/) you will be able to find your Church ID on the 'Website Giving' page. At the bottom there is a little snippet which looks like this

https://tithe.ly/give?c=xxxx

The four digit code (replacing the xxxx) is your Church ID

= How Do I Insert My Button? =

There are two ways you can insert your button.

1. Insert the WP Tithely Widget into one of your sidebars. You can insert your Church ID and the text you would like the button to display.

1. You can insert the button into your page content using the shortcode [tithely]. It will use the Church ID and Button Text defined in the options above, unless you include the additional attributes outlined below: [tithely button="Donate Now" id="12345" amount="100" styling_class="button" give-to="Building Fund"]

= Is this secure? =
Whilst Tithely processes payments securely - we recommend you only use the give button on  a page that is protected with SSL encryption. It's pretty cheap to add and usually costs around $30-50 a year depending on the service. Talk to your hosting provider to find out more details.


== Changelog ==

==2.0==
* Update plugin to use new Tithe.ly Javascript
* Remove TinyMCE popup as we don't really need it
* Add new 'amount' and 'giving-to' attributes
* Add classes to button to pick up styling from Divi, Beaver Builder and Elementor
* Add new fields to widget
* Update ReadMe with new details

==1.2==
* Move script to header so the button fires when clicked.
* Include Usage instructions in the Options page

= 1.1 =
* Update the shortcode to allow definition of Church ID.

= 1.0 =
* First Major Release.

