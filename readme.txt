=== WP Sitemap Control ===
Contributors: iworks
Donate link: http://iworks.pl/donate/wp-sitemap-control.php
Tags: sitemap.xml
Requires at least: 5.5
Tested up to: 5.6
Stable tag: PLUGIN_VERSION
Requires PHP: 7.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

WP Sitemap Control allow choosing which public post types or taxonomies should be able in the /wp-sitemap.xml

== Description ==

The plugin allows you to control which post types should be included in the [WordPress sitemap][]. By default on build-in sitemap are all public post types and taxonomies, but now you can include or exclude the selected type of content from your sitemap. [Learn about sitemaps][].

[WordPress sitemap]: https://make.wordpress.org/core/2020/07/22/new-xml-sitemaps-functionality-in-wordpress-5-5/
[Learn about sitemaps]: https://developers.google.com/search/docs/advanced/sitemaps/overview

== Installation ==

There are 3 ways to install this plugin:

= The super-easy way =

1. **Login** to your WordPress Admin panel.
1. **Go to Plugins > Add New.**
1. **Type** ‘WP Sitemap Control’ into the Search Plugins field and hit Enter. Once found, you can view details such as the point release, rating, and description.
1. **Click** Install Now. After clicking the link, you’ll be asked if you’re sure you want to install the plugin.
1. **Click** Yes, and WordPress completes the installation.
1. **Activate** the plugin.
1. A new menu `Sitemap` in `Settings` will appear in your Admin Menu.

***

= The easy way =

1. Download the plugin (.zip file) on the right column of this page.
1. In your Admin, go to menu Plugins > Add.
1. Select button `Upload Plugin`.
1. Upload the .zip file you just downloaded.
1. Activate the plugin.
1. A new menu `Sitemap` in `Settings` will appear in your Admin Menu.

***

= The old and reliable way (FTP) =

1. Upload `WP Sitemap Control` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. A new menu `Sitemap` in `Settings` will appear in your Admin Menu.

== Frequently Asked Questions ==

= I do not see /wp-sitemap.xml =

The site needs to be public. If you ask for no indexing, then a sitemap is not available.


= What is a sitemap? =

A sitemap is a file where you provide information about the pages, videos, and other files on your site. Search engines read this file to more intelligently crawl your site.

== Screenshots ==


== Changelog ==

= 1.0.3 (2021-xx-xx) =
* Added ability to select/deselect all post types.
* Added ability to select/deselect all taxonomies.
* Renamed directory `vendor` into `includes`.

= 1.0.2 (2021-02-01) =
* Fixed wrong date format for `lastmod` tag.

= 1.0.1 (2020-12-02) =
* Fixed wrong method call. Props for Maciej Kuchnik.

= 1.0.0 =
* Init version. Props for [Sebastian Miśniakiewicz](https://profiles.wordpress.org/sebastianm/) for inspiration.

== Upgrade Notice ==

