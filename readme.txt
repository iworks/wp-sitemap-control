=== WP Sitemap Control ===
Contributors: iworks
Donate link: https://ko-fi.com/iworks?utm_source=wp-sitemap-control&utm_medium=readme-donate
Tags: sitemap.xml, sitemap, post type, post types
Requires at least: PLUGIN_REQUIRES_WORDPRESS
Tested up to: PLUGIN_TESTED_WORDPRESS
Stable tag: PLUGIN_VERSION
Requires PHP: PLUGIN_REQUIRES_PHP
License: GPLv3 or later

PLUGIN_TAGLINE

== Description ==

The plugin allows you to control which post types should be included in the [WordPress sitemap][]. By default on build-in sitemap are all public post types and taxonomies, but now you can include or exclude the selected type of content from your sitemap. [Learn about sitemaps][].

The configuration allows you to turn off individual content from the sitemap - an example is "Privacy Policy", which we do not need to have in our sitemap, and by combining the plugin capabilities [Simple SEO Improvements][] we can also avoid indexing and archiving it.

[WordPress sitemap]: https://make.wordpress.org/core/2020/07/22/new-xml-sitemaps-functionality-in-wordpress-5-5/
[Learn about sitemaps]: https://developers.google.com/search/docs/advanced/sitemaps/overview
[Simple SEO Improvements]: https://wordpress.org/plugins/simple-seo-improvements/

= See room for improvement? =

Great! There are several ways you can get involved to help make PLUGIN_TITLE better:

1. **Report Bugs:** If you find a bug, error or other problem, please report it! You can do this by [creating a new topic](https://wordpress.org/support/plugin/wp-sitemap-control/) in the plugin forum. Once a developer can verify the bug by reproducing it, they will create an official bug report in [GitHub](PLUGIN_GITHUB_WEBSITE) where the bug will be worked on.
2. **Suggest New Features:** Have an awesome idea? Please share it! Simply [create a new topic](https://wordpress.org/support/plugin/wp-sitemap-control/) in the plugin forum to express your thoughts on why the feature should be included and get a discussion going around your idea.
3. **Issue Pull Requests:** If you're a developer, the easiest way to get involved is to help out on [issues already reported](PLUGIN_GITHUB_WEBSITE/issues) in GitHub. Be sure to check out the [contributing guide](PLUGIN_GITHUB_WEBSITE/blob/master/contributing.md) for developers.

Thank you for wanting to make PLUGIN_TITLE better for everyone!

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

1. Global configuration - post types settings.
1. Global configuration - taxonomies settings.
1. Global configuration - misc settings.
1. Post edit screen - exclude from sitemap.xml


== Changelog ==

Project maintained on github at [iworks/wp-sitemap-control](https://github.com/iworks/wp-sitemap-control).

= 1.0.7 (2024-02-16) =
* Missing translation domain names have been added.
* The function `date()` has been replaced by the function `gmdate()`.
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.9.1.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.1.8.
* The minimum PHP version has been bumped up from 7.2 to 8.0.
* The minimum WordPress version has been bumped up from 5.0 to 6.0.

= 1.0.6 (2022-02-16) =
* Cleaned plugin headers.
* Updated development tools.
* Updated iWorks Options to 2.8.1.

= 1.0.5 (2022-01-21) =
* Updated iWorks Options to 2.8.0.

= 1.0.4 (2022-01-20) =
* Fixed problem with `set_plugin` method.

= 1.0.3 (2022-01-20) =
* Added ability to exclude single entry from sitemap.xml.
* Added ability to select/deselect all post types.
* Added ability to select/deselect all taxonomies.
* Renamed directory `vendor` into `includes`.
* Updated iWorks Options to 2.7.3.
* Updated iWorks Rate to 2.0.6.

= 1.0.2 (2021-02-01) =
* Fixed wrong date format for `lastmod` tag.

= 1.0.1 (2020-12-02) =
* Fixed wrong method call. Props for Maciej Kuchnik.

= 1.0.0 =
* Init version. Props for [Sebastian Miśniakiewicz](https://profiles.wordpress.org/sebastianm/) for inspiration.

== Upgrade Notice ==

