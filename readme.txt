=== Notification ===
Contributors: Kubitomakita
Tags: notification, notify, email, mail
Requires at least: 3.6
Tested up to: 4.7
Stable tag: 2.0.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Customisable email notifications with developer friendly API for custom triggers

== Description ==

This plugin allows you to send custom email notifications about various events in WordPress. It also comes with a simple API by which you can add literally **any** trigger action.

In messages you can use defined merge tags which will be later changed to content applicable for trigger.

Please see Screenshots tab to get the overall idea about the plugin and see it in action.

You may also want to check [Other Notes](https://wordpress.org/plugins/notification/other_notes/) to see how to use the plugin's API and include it in your own theme or plugin.

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
3. Settings

== Changelog ==

= Next release =
* [Fixed] Warning when no post or comment type are selected in the settings. Thanks to JoeHana

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

== API ==

All below functions are available as early as `init` action with priority 5.

= Registering new triggers =

You can use `register_trigger()` function to register new notification trigger. Sample usage:

`
register_trigger( array(
	'slug'     => 'my_plugin/action',
	'name'     => __( 'Custom action', 'textdomain' ),
	'group'    => __( 'My Plugin', 'textdomain' ),
	'template' => 'This is default template using {merge_tag}. It can accept <strong>HTML</strong>',
	'tags'     => array(
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

Group, tags and template are optional. You don't have to register them.

= Executing triggers =

To actualy trigger new notification call `notification()` function with 2 parameters: trigger slug and merge tags array.

It's a good practice to first check if notification should be send to not pull all the data for nothing.

Sample usage:

`
if ( is_notification_defined( 'my_plugin/action' ) ) {
	notification( 'my_plugin/action', array(
		'page_ID'    => $ID,
		'page_url'   => get_permalink( $ID ),
		'user_email' => $user_email,
	) );
}
`

= Deregistering triggers =

You can deregister any trigger with `deregister_trigger()` function. Just pass the trigger slug as an argument. Sample usage:

`
deregister_trigger( 'my_plugin/action' );
`

= Include Notification in other plugin or theme =

Including it in another plugin or theme requires *just one thing*. Take a look at that:

`require_once( 'path/to/plugin/notification/load.php' );`

Notification will figure out from where it's loaded and will set all paths and URIs automatically.
