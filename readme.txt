=== Notification ===
Contributors: Kubitomakita
Tags: notification, notify, email, mail
Requires at least: 3.6
Tested up to: 4.7
Stable tag: 2.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Customisable email notifications with developer friendly API for custom triggers

== Description ==

This plugin allows you to send custom email notifications about various events in WordPress. It also comes with a simple API by which you can add literally **any** trigger action.

In messages you can use defined merge tags which will be later changed to content applicable for trigger.

> [See Notification homepage](https://notification.underdev.it) and check Developer docs

https://www.youtube.com/watch?v=usdBMPjdiuw

= Default recipients =

Plugin comes with few registered by default recipient types:

* Email address - free type email address
* Administrator - takes an email from General Settings page
* User - takes an email from WordPress user profile
* Merge tag - email rendered by merge tag

= Default triggers =

These are already defined in plugin's core and are ready to use. You can enable or disable them on the Settings page.

Any Post Type:

* Published post notification
* Updated post notification
* Post send for review (pending post) notification
* Post moved to trash notification

Comment / Pingback / Trackback:

* New comment notification
* Comment approved notification
* Comment unapproved notification
* Comment marked as spam notification
* Comment moved to trash notification

More to come:

* User triggers
* Media triggers
* Feel free to suggest new core triggers in support forum

= Useful links =

* [Homepage](https://notification.underdev.it)
* [GitHub repository](https://github.com/Kubitomakita/Notification)
* [Report a bug](https://github.com/Kubitomakita/Notification/issues/new)

== Installation ==

= Requirements =

This plugin require at least PHP 5.3.0.

= Plugin install =

Download and install this plugin from Plugins -> Add New admin screen.

= Distributing in a plugin or theme =

Notification can be loaded also as a part of any plugin or theme. To do it just include plugins's `load.php` file. It will figure out if it's loaded from theme or from plugin.

[See the detailed guide](https://notification.underdev.it/including-notification-plugin-theme/)

== Frequently Asked Questions ==

= How to change notification email headers? =

There's no such option at the moment. Please use some other plugin to adjust wp_mail() headers.

= How to register my triggers? =

With `register_trigger()` function. [See the detailed guide](https://notification.underdev.it/registering-new-triggers/)

= How do I fire my trigger to send an email? =

With `notification()` function. [See the detailed guide](https://notification.underdev.it/sending-notifications/)

= Can I deregister trigger I don't want to use? =

Yes, with `deregister_trigger()` function. [See the detailed guide](https://notification.underdev.it/deregistering-triggers/)

= Can I bundle the plugin with my plugin or theme? =

Yes, you can. [See the detailed guide](https://notification.underdev.it/including-notification-plugin-theme/)

== Screenshots ==

1. Trigger edit screen
2. All triggers
3. Settings

== Changelog ==

= 2.2 =
* [Added] `notification/metabox/trigger/tags/before` and `notification/metabox/trigger/tags/after` actions to merge tags metabox
* [Added] `notification/metabox/recipients/before` and `notification/metabox/recipients/after` actions to recipients metabox
* [Added] `notification/metabox/trigger/before` and `notification/metabox/trigger/after` actions to trigger metabox
* [Fixed] Settings register action priority
* [Fixed] Post type trashed template
* [Changed] Gulpfile to not include any browser
* [Fixed] Comment type added template
* [Changed] Comment added trigger now is Akismet compatibile, thanks to Nels Johnson
* [Changed] Core triggers current type global to anonymous functions, thanks to Bartosz Romanowski @toszcze

= 2.1 =
* [Fixed] Warning when no post or comment type are selected in the settings. Thanks to JoeHana
* [Fixed] post published trigger
* [Changed] Post type name in trigger title is now singular
* [Added] {author_login} merge tag to each post trigger
* [Added] Promo video: https://www.youtube.com/watch?v=usdBMPjdiuw

= 2.0.1 =
* [Fixed] Issue with not sent emails because of wrong current post type while registering notification action. Thanks to Karl Camenzuli

= 2.0 =
* [Fixed]: Correct choice selected for WP User recipient after saving notification. Thanks to whitwye
* [Added]: Settings API
* [Added]: Setting - what to remove upon plugin removal
* [Added]: Plugin cleanup procedure
* [Added]: Plugin deactivation feedback popup
* [Added]: Conditional tag `is_notification_defined()` to check if notification will be send
* [Added]: Post permalink to comment triggers
* [Changed]: Notifications class is now singleton and partialy moved to Admin class
* [Changed]: Notification trigger metabox is now under the subject
* [Changed]: On the single Notification edit screen there are only allowed metaboxes displayed
* [Changed]: You can now controll what post types and comment types trigger use via plugin Settings

= 1.4 =
* [Fixed]: Missing 3rd argument on page publish
* [Fixed]: Namespace issue for PHP < 5.3
* [Fixed]: Constant notification on post edit. Thanks to @pehbeh
* [Changed]: Allow for merge tags empty values. Thanks to kokoq
* [Added]: Admin notice: beg for a review. It will display only if there's at least one notification set, on the Notification plugin screens and can be dismissed easly.

= 1.3.1 =
* [Fixed]: Error with "Can't use function return value in write context" in empty() function. Thanks to Błażej Zabłotny

= 1.3 =
* [Added]: PHP version check
* [Changed]: Typos in readme.txt file thanks to justyn-clark (https://github.com/Kubitomakita/Notification/pull/1)

= 1.2 =
* [Added]: New User recipient (takes WordPress users)
* [Added]: Post/Page updated trigger
* [Added]: Template for triggers. You can now load default template for user
* [Changed]: Default post published trigger for posts and pages - it was triggered every time post was updated
* [Changed]: In Notifications table values are now parsed before displaying
* [Changed]: Readme file

= 1.1.2 =
* Changed priority for main init action from 10 to 5
* Added 'notification/cpt/capability_type' filter for capability mapping

= 1.1 =
* Added ability to distribute in any plugin or theme

= 1.0 =
* Release

== Upgrade Notice ==

= 1.1 =
* Added ability to distribute in any plugin or theme

= 1.0 =
* Release
