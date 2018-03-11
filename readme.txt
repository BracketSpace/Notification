=== Notification - Custom Notifications and Alerts for WordPress ===
Contributors: notification, bracketspace, Kubitomakita, insejn
Donate link: https://www.paypal.me/underDEV/
Tags: notification, notify, alert, email, mail, webhook, API, developer, framework
Requires at least: 4.6
Tested up to: 5.0
Stable tag: 5.1.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.

== Description ==

This plugin allow you to send custom notifications or alerts about various events in WordPress. It also comes with simple yet powerful API by which you can add literally **any** trigger action.

In messages you can use defined merge tags which will be later changed to content applicable for the trigger.

= Version 5 has been released! =

Completely new codebase with awesome improvements. Now, Notification plugin can send not only Emails but also Webhooks!
With the brand new API you can create any notification type you want - SMS, Push, Slack... sky's the limit!

= New Triggers and Merge Tags =

We switched to truly objective code. Every trigger and merge tag is now an object. But don't worry, it's even simpler than it was before!

Take a look at documentation to see how easy and intuitive it is to [register custom trigger](https://docs.bracketspace.com/docs/registering-custom-triggers/).

= Default Notifications =

* Email
* Webhook

= Default Recipients =

Plugin comes with few registered by default recipient types:

* Email address or Merge tag - free type email address or a Merge Tag
* Administrator - takes an email from General Settings page
* User - takes an email from WordPress user profile
* Role - notify all Users having selected role at once

= Default Triggers =

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

User:

* User registered
* User profile updated
* User logged in
* User logged out
* User deleted

Media:

* Media added
* Media updated
* Media deleted

Feel free to suggest new core triggers in support forum

= Useful links =

* [Documentation](https://docs.bracketspace.com/docs-category/notification/)
* [Support plugin developement](https://www.paypal.me/underDEV/)
* [GitHub repository](https://github.com/BracketSpace/Notification)
* [Report a bug](https://github.com/BracketSpace/Notification/issues/new)

== Installation ==

= Requirements =

This plugin require at least PHP 5.3.0.

= Plugin install =

Download and install this plugin from Plugins -> Add New admin screen.

= Distributing in a plugin or theme =

Notification can be loaded also as a part of any plugin or theme. To do it just include plugins's `load.php` file. It will figure out if it's loaded from theme or from plugin.

[See the detailed guide](https://docs.bracketspace.com/docs/including-notification-in-the-plugin-or-theme/)

== Frequently Asked Questions ==

= How to register my own triggers? =

With `register_trigger()` function. [See the detailed guide](https://docs.bracketspace.com/docs/registering-custom-triggers/)

= Can I bundle the plugin with my plugin or theme? =

Yes, you can. [See the detailed guide](https://docs.bracketspace.com/docs/including-notification-in-the-plugin-or-theme/)

== Screenshots ==

1. Trigger edit screen
2. All triggers
3. Settings
4. Extensions
5. Help tab with global Merge Tags

== Changelog ==

= [Next] =
* [Fixed] Pretty select in repeater is now rendered correctly while adding new row.
* [Added] Global Merge Tags which can be used in any Trigger. This includes Site title, Trigger name etc.
* [Added] Screen help.
* [Added] User Display Name Merge Tag.
* [Added] Post Terms Merge Tags.

= 5.1.2 =
* [Added] Ability to suppress the Notification just before it's send.
* [Added] Freemius integration to better understand the users.
* [Added] Current Notification post ID property for Notifiation object.

= 5.1.1 =
* [Fixed] Posponed action callback.
* [Added] Support for NOTIFICATION_DEBUG constant. If it's defined the cache for extensions is not applied.
* [Added] notificaiton/notification/field/resolving filter before any value is resolved with merge tags.

= 5.1.0 =
* [Fixed] The Email notification it not enabled anymore for already saved notifications
* [Fixed] New Notification post is not automatically saved as an Auto Draft anymore
* [Fixed] Enabled switch state in Save metabox
* [Changed] Documentation link in Own Extension promo link
* [Changed] Extensions in Extension directory are now loaded from remote API
* [Added] TextareaField field
* [Added] License handler for premium extensions

= 5.0.0 =
* WARNING! This version is not compatible with previous version. No core notifications nor custom triggers will be transfered to the new version because of too many changes in the plugin. Consider updating the plugin in a safe, not-production environment.
* Plugin has been redesigned from ground up
* The only thing which is not available in new version is disabling the notifications

= 3.1.1 =
* [Fixed] Bug with directories/files names, thanks to Gregory Rick

= 3.1 =
* [Added] `notification/notify` filter which control if notification should be sent or not
* [Added] `notification/settings` action which accepts Settings API class as a parameter
* [Added] `post_author_email` merge tag for all comment types triggers, thanks to Wayne Davies
* [Added] Ongoing check of PHP and WP version, thanks to Max (@max-kk)
* [Added] Option to strip shortcodes from Notification subject and content, thanks to @Laracy
* [Added] Notification : Signature extension to extension directory
* [Changed] Settings and Singleton are now loaded from Composer libraries
* [Changed] Gulp default task to build, and added watch task which boots up BS
* [Changed] Action priority when default recipients and triggers are registered from 50 to 9
* [Changed] Action priority when settings are initialized from 15 to 8
* [Changed] Updated Composer libraries
* [Changed] Values for default trigger options from strings/arrays to null
* [Fixed] Bug when Text editor was active and the trigger was changed
* [Fixed] Post Visibility setting on other post types than Notification
* [Fixed] Default recipient merge_tag value. All recipient inputs are now dynamically refreshed
* [Fixed] Not cached exception in plugin's table when requiring this plugin from inside of another plugin or theme, thanks to Max (@max-kk)

= 3.0 =
* [Fixed] Ignore tags which has been passed to `notification` but hasn't be registered in the trigger
* [Fixed] Conflict with Advanced Custom Fields
* [Added] Filters for post and comment types to output disbale metabox. `notification/disable/post_types_allowed` and `notification/disable/comment_types_allowed`, default to saved general settings
* [Added] Extensions screen
* [Added] While registering triggers you can now provide a default title and recipients
* [Changed] bbPress post types are no longer available in the settings. Triggers for bbPress are provided by addon https://github.com/Kubitomakita/notification-bbpress
* [Changed] Place where merge tags metabox actions are executed
* [Changed] Chosen to Selectize.js

= 2.4 =
* [Fixed] Bug with "Can't use method return value in write context" in Settings class, thanks to @rozv
* [Fixed] Settings priorities, now every CPT registered not later than init 15 will be catched by the plugin, thanks to @rozv
* [Fixed] Double protocol in links added via TinyMCE insert link feature, thanks to Jozsef
* [Fixed] Notices in Notification validation method
* [Fixed] Empty Recipient value, ie. Administrator
* [Added] Post type triggers can be disabled for an user
* [Added] Database Upgrader
* [Added] User triggers - registered, profile updated, logged in, deleted
* [Added] Taxonomies merge tags for post types
* [Added] Media triggers - added, updated, deleted
* [Changed] Post updated notification is now triggered only if the post has been published before, suggested by nepali65
* [Changed] Content Type triggers setting has been changed to Enabled triggers

= 2.3.1 =
* [Fixed] Bug with not activated "Disable" option

= 2.3 =
* [Changed] Removed unused default post controls
* [Changed] Better error handling, plugin will not die now unless WP_DEBUG is active
* [Changed] Role class parse_value() method now must define 3rd parameter $human_readable
* [Added] Role recipient
* [Added] Option to disable notification for specific post (and in future for user or comment), thanks to Jeff Lehman
* [Changed] string, integer and float merge tags used in the message subject are now rendered

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
