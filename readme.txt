=== Notification ===
Contributors: Kubitomakita
Tags: notification, notify, email, mail
Requires at least: 3.6
Tested up to: 4.6
Stable tag: 1.3.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Customisable email notifications with API for custom triggers

== Description ==

This plugin allows you to send custom email notifications about various events in WordPress. It also comes with a simple API by which you can add literally **any** trigger action.

In messages you can use defined merge tags which will be later changed to content applicable for trigger.

Please see Screenshots tab to see it in action.

= Default recipients =

Plugin comes with few registered by default recipient types:

* Email address - free type email address
* Administrator - takes an email from General Settings page
* Merge tag - email rendered by merge tag

= Default triggers =

These are already defined in plugin's core and are ready to use.

Post:

* Published post notification
* Post send for review (pending post) notification
* Post moved to trash notification

Page:

* Published page notification
* Page send for review (pending page) notification
* Page moved to trash notification

Comment:

* New comment notification
* Comment approved notification
* Comment unapproved notification
* Comment marked as spam notification
* Comment moved to trash notification

Pingback:

* New pingback notification
* Pingback approved notification
* Pingback unapproved notification
* Pingback marked as spam notification
* Pingback moved to trash notification

Trackback:

* New trackback notification
* Trackback approved notification
* Trackback unapproved notification
* Trackback marked as spam notification
* Trackback moved to trash notification

More to come:

* Pending post/page accepted
* User triggers
* Media triggers
* Feel free to suggest new core triggers in support forum

`-------------------------------`

* [GitHub repository](https://github.com/Kubitomakita/Notification)
* [Report a bug](https://github.com/Kubitomakita/Notification/issues/new)

== Installation ==

= Requirements =

This plugin require at least PHP 5.3.0.

= Plugin install =

Download and install this plugin from Plugins -> Add New admin screen.

= Distributing in a plugin or theme =

Notification can be loaded also as a part of any plugin or theme. To do it just include plugins's `load.php` file. It will figure out if it's loaded from theme or from plugin.

== Frequently Asked Questions ==

= How to change notification email headers =

There's no such option at the moment. Please use some other plugin to adjust wp_mail() headers.

== Screenshots ==

1. Trigger edit screen
2. All triggers

== Changelog ==

= NEXT =
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

== API ==

All below functions are available as early as `init` action with priority 5.

= Registering new triggers =

You can use `register_trigger()` function to register new notification trigger. Sample usage:

`
register_trigger( array(
	'slug' => 'my_plugin/action',
	'name' => __( 'Custom action', 'textdomain' ),
	'group' => __( 'My Plugin', 'textdomain' ),
	'tags' => array(
		'page_ID'    => 'integer',
		'page_url'   => 'url',
		'user_email' => 'email'
	)
) );
`

Possible merge_tags types:

* integer
* float
* string
* url
* email
* boolean
* ip

Group and tags are optional. You don't have to register them.

= Executing triggers =

To actualy trigger new notification call `notification()` function with 2 parameters: trigger slug and merge tags array. Sample usage:

`
notification( 'my_plugin/action', array(
	'page_ID'    => $ID,
	'page_url'   => get_permalink( $ID ),
	'user_email' => $user_email,
) );
`

= Registering new triggers =

You can deregister any trigger with `deregister_trigger()` function. Just pass the trigger slug as an argument. Sample usage:

`
deregister_trigger( 'my_plugin/action' );
`
