=== Anonymous Restricted Content ===
Contributors: cayenne
Tags: restricted access, block content, content control, access control, restrict anonymous, hide content, limited access, permission, private
Requires at least: 5.3
Tested up to: 6.4
Stable tag: 1.6.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple but yet effective plugin to hide selected posts and pages from anonymous users.

== Description ==

This plugin is as simple as you expected!
And it support latest Wordpress Gutenberg editor now!

Mark the content you want to hide as restricted with just a checkbox - and it's done!

In case anonymous user will try to get access to restricted page or post - it will be redirected to Wordpress Login page first.
Or you can specify the exact URL (internal or external) to redirect anonymous users to.

After successful authorization, user is redirected back to the requested page.

Also, it hides restricted posts from Archive and Categories pages, RSS feed and from Latest Comments/Posts widgets!

== Installation ==

1. Upload `anonymous-restricted-content` folder into the `/wp-content/plugins/` directory or use automatic WP Plugin Installer.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. All done, you can start using it!

== Frequently Asked Questions ==

= How to hide post/page from public access? =

Go to post/page edit screen in WP Admin, and switch ON checkbox for "Restricted for anonymous users". You can find it in the sidebar, under Document -> Status & Visibility.

= I set post to restricted, but it's still visible?! =

It usually happens when you forget to press Update button.

= I'm not idiot, and I saved my post but it's still visible?! =

Please, use this url to submit support ticket: https://wordpress.org/support/plugin/anonymous-restricted-content/

= What happens to users who are restricted ? =

You can redirect them to any url you need or to the default WP login screen.

= Can I change redirect URL ? =

Yes, you can find this option under Settings -> Restricted Content -> Redirect Options.

= Can I set custom notification message for users who was restricted ? =

Yes, look at Settings -> Restricted Content -> Login Screen Message.

= All restricted options are reset after plugin re-install. What's going on? =

We strictly care about your database size, and clean up all the data created by this plugin during its lifetime when plugin is uninstalled.

= What about categories? =

Unfortunately, we refused to support this feature in latest version, cause to changes in WP which made it nearly impossible to do.

== Screenshots ==

1. Restrict access to any post for not logged-in users.
2. Hide any page from public access.
3. Not logged-in users are redirected to Login screen in case of trying access restricted content.
4. Admin settings page
5. Custom URL to redirect anonymous users
6. Bulk restrict feature
7. Alternative AJAX login screen


== Changelog ==
= 1.6.5 =
* Fixed bug with access to restricted content for authorized users via REST API

= 1.6.4 =
* Fixed issue with restricted content in REST API

= 1.6.3 =
* Fixed issue with posts listing

= 1.6.2 =
* Restricted content in REST API

= 1.6.1 =
* Tested up to WP 6.2.2

= 1.6 =
* Compatibility errors
* Tested up to WP 6.2

= 1.5.5 =
* Added support to be restricted by category and tag - PRO
* Fixed error display on settings page
* Tested up to WP 6.0

= 1.5.4 =
* fixed issue with AJAX login screen on mobile

= 1.5.3 =
* JS scripts included into translations
* Tested up to WP 5.7

= 1.5.2 =
* Started work on plugin translation
* Added Deutch and Ukrainian languages

= 1.5.1 =
* Added "Go Back" button on AJAX login screen
* Minor typo and styling fixes

= 1.5.0 =
* AJAX login screen

= 1.4.1 =
* Added timecode to redirect urls to prevent caching issues

= 1.4.0 =
* Bulk restriction. Restriction status in Posts/Pages list.
* Tested with brand new WP 5.5

= 1.3.2 =
* Resolved issue with Classic Editor and Yoast plugins compatibility.

= 1.3.1 =
* Fixed problem with "Undefined variable: is_content_restricted ...", my apologies about it!

= 1.3 =
* Stopped supporting restricted categories!
* Restricted pages are hidden from default primary menu.
* New, customizable message for users who forced to login screen.
* More efficient restriction function to cover even more cases.
* Speed and coding optimisation.

= 1.2.3 =
* Added Redirect URL option to Settings page

= 1.2.0 =
* Wordpress 5.2 Compatible
* Restricted posts hidden in archive
* Restricted posts hidden in Latest Posts widget
* Comments to restricted posts hidden in Latest Comments Widget
* Restricted posts hidden in Rss Feed

= 1.1.2 =
* Minor fixes related to Gutenberg editor

= 1.1.1 =
* Gutenberg compatibility

= 1.0.1 =
* WordPress 5.0 compatibility

= 1.0 =
* Added Restricted categories
* More efficient restricted options processing
* Prepared plugin to be uploaded to Wordpress.org storage

= 0.5 =
* First version of plugin with just basic functions
