=== User Domain Whitelist ===
Contributors: hungrymedia
Donate link: http://www.hungry-media.com
Tags: email address, domain, whitelist, blacklist, registration, user
Requires at least: 2.8.2
Tested up to: 3.4 RC2
Stable tag: trunk

The User Domain Whitelist/Blacklist plugin limits user registration to only registrants with an email address from the domain white list provided by the administrator or prevents user registration with an email address from the domain blacklist provided by the administrator.

== Description ==

The User Domain Whitelist/Blacklist plugin limits user registration to only registrants with an email address from the domain white list below OR prevents registrants with an email address from the domain black list below from registering. For example, <em>hortense@example.com</em> would only be allowed to register if <em>example.com</em> appeared in the domain white list. Conversely,  <em>hortense@example.com</em> would <strong>not</strong> be allowed to register if <em>example.com</em> appeared in the domain black list. Anyone attempting to register using an email address outside the white list or inside te black list will receive the error message below.Anyone attempting to register using an email address outside the white list will receive an error message. Both the domain whitelist and the error message can be modified via the plugin options page (available under the Settings menu).

== Installation ==

Unzip the user-domain-whitelist folder and upload to the /wp-content/plugins/ directory
Activate the User Domain Whitelist plugin through the 'Plugins' menu.
Go to the User Domain Whitelist/Blacklist options page (under Settings), add your list of allowed domains and (optionally) change the error message that disallowed registrants will receive.

== Frequently Asked Questions ==

= Why are there no questions yet? =

Because nobody has asked one yet. :P

== Screenshots ==

1. Shows the options page where you select the plugin mode whitelist/blacklist, add allowed domains to the whitelist or blacklist as well as customize your error message for non-allowed registrants.

2. Shows an example of the error message shown for non-allowed registrants. 

== Changelog ==

= 1.4 =
Added i18n functions to error message prefix (e.g. ERROR)

= 1.3 =
Added a blacklist to prevent registration from a set of domains as an alternative to white listing.

= 1.2 =
BUG FIX: Corrected error where plugin options page was displaying instead of standard WP admin dashboard.

= 1.1 =
BUG FIX: Corrected error that prevented non-admin users from accessing the WP backend.

= 1.0 =
Initial release.

