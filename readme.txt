=== cbnet Favicon ===
Contributors: chipbennett
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QP3N9HUSYJPK6
Tags: cbnet, favicon
Requires at least: 3.0
Tested up to: 4.5
Stable tag: 3.1

Add a Favicon to your site. No bells or whistles; simply upload a (ICO, PNG, or GIF) file.

== Description ==

As of WordPress 4.3, this Plugin is no longer needed. The next update will integrate the Plugin into the core site icon feature, to allow for graceful 
retirement of the Plugin.

Add a Favicon to your site. No bells or whistles; simply upload a (ICO, PNG, or GIF) file.

Note: Plugin settings can be configured via Dashboard -> Settings -> Favicon.

== Installation ==

Manual installation:

1. Upload the `cbnet-favicon` folder to the `/wp-content/plugins/` directory

Installation using "Add New Plugin"

1. From your Admin UI (Dashboard), use the menu to select Plugins -> Add New
2. Search for 'cbnet Favicon'
3. Click the 'Install' button to open the plugin's repository listing
4. Click the 'Install' button

Activiation and Use

1. Activate the plugin through the 'Plugins' menu in WordPress
2. From your Admin UI (Dashboard), use the menu to select Options -> Favicon 
3. Configure settings, and save

== Frequently Asked Questions ==

= How do I choose a Favicon? =

Just upload a file of your own. Note that Favicons file type must be one of ICO, PNG, JPEG, or GIF.

= I use Internet Explorer. Why don't the icons appear on the options page? Are my options saved? =

Internet Explorer doesn't render .ico files in HTML files.

== Screenshots ==

Screenshots coming soon.


== Changelog ==

= 3.1 =
* Minor Revision
* Integrate into core Site Icon feature
** If WordPress 4.3 or greater, requires favicon configuration via core Site Icon feature.
** Images previously configured as favicons using the Plugin are available in the Media Library, and can be used for the core Site Icon feature
** Plugin will continue to work, but is not supported, in versions of WordPress older than 4.3.

= 3.0 =
* Major Revision
* Plugin completely rewritten:
** Settings API support
** Implement settings via wp_head
** Made Plugin parameters filterable
** Made Plugin translation-ready
** Removed all cruft code
* WARNING: Old settings will not be retained
= 2.1.1 =
* Readme.txt update
* Updated Donate Link in readme.txt
= 2.1 =
* Initial Release
* Forked from MaxBlogPress Favicon plugin version 2.0.9


== Upgrade Notice ==

= 3.1 =
Minor update. Core Site Icon feature-aware.
= 3.0 =
Major update. Plugin completely re-written. WARNING: Old settings will not be retained.
= 2.1.1 =
Readme.txt update. Updated Donate Line in readme.txt
= 2.1 =
Initial Release. Forked from MaxBlogPress Favicon plugin version 2.0.9
