=== Notification - Custom Notifications and Alerts for WordPress ===
Contributors: notification, bracketspace, Kubitomakita, tomaszadamowicz, insejn, mateuszgbiorczyk
Tags: notification, notify, alert, email, mail, webhook, API, developer, framework
Requires at least: 4.9
Tested up to: 5.6
Stable tag: 7.2.4
Requires PHP: 7.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.

== Description ==

Custom Notifications and Alerts without a hassle. Notify anyone about any action in your WordPress. With powerful Merge Tags, you can endlessly customize your messages. Set unlimited Notifications in your WordPress Admin via the beautiful and intuitive interface within 5 minutes.

[youtube https://www.youtube.com/watch?v=UPqVBhLGTek]

= DEFAULT WORDPRESS EMAILS OVERWRITE =

Now, with this plugin, you can easily disable default WordPress emails and replace them with your own. To do that you can use our awesome Wizard which will guide you through the process.

= HOW DOES IT WORK =

The Notification plugin is built with three main components:

* Trigger - a WordPress action, ie. User registration or Post publication
* Carrier - the thing which is being sent, ie. Email or Push
* Merge Tag - dynamic content, ie. {user_email} or {post_permalink}

You can use them in any combination, adding as many Notifications as you want. They can be sent to multiple Recipients with the content you write.

The process is simple:

* You select the Trigger
* Compose your message with Merge Tags
* Set Recipients
* Save the Notification

From now on the Notification is working. Test it out and add more!

= PERFECT FOR DEVELOPERS =

The Notification plugin is easy to set in the WordPress Admin, but it's even easier to extend with some sweet API.

You can create your own Triggers with any WordPress action. If you do in your code `do_action( 'my_plugin_doing_awesome_thing' )` you can create a Trigger out of it.

This allows you to use the Notification plugin as a notification system in your own plugin or theme. How? Well, because of two things:

* You can easily load it by copying the plugin files and including `load.php` file. A function known from Advanced Custom Fields plugin.
* You can white label the plugin with just one function which is shipped in the plugin's core. For free.

How easy extending the Notification plugin is? Let's see:

* Adding another Merge Tag to existing trigger - 1 line of code
* Creating custom Trigger - one intuitive class definition and registration with a single function call
* Defining Global Merge Tag - 1 line of code
* Creating new Extension - we have a [Boilerplate](https://github.com/BracketSpace/Notification-Extension-Boilerplate/) ready for you to start hacking

[See the developer documentation](https://docs.bracketspace.com/notification/developer/general) if you don't believe us.

= DEFAULT CARRIERS =

* Email
* Webhook

= DEFAULT RECIPIENTS =

The plugin comes with few registered by default recipient types for Email Carrier:

* Email address or Merge Tag – free type email address or a Merge Tag
* Administrator – takes an email from General Settings page
* User – takes an email from WordPress user profile
* Role – notify all Users having selected role at once

= DEFAULT TRIGGERS =

These are already defined in plugin’s core and are ready to use. You can enable or disable them on the Settings page.

WordPress:

* Available updates - sent as often as you set them, ie. every week

Post Type:

* Published post notification
* Post added to database notification
* Post drafted (saved as a draft) notification
* Updated post notification
* Post send for review (pending post) notification
* Post approved (pending to publish) notification
* Post moved to trash notification

The Notification plugin supports any Custom Post Type out of the box.

Taxonomy terms:

* Taxonomy term created notification
* Taxonomy term updated notification
* Taxonomy term deleted notification

The Notification plugin supports any Taxonomy out of the box.

Comment / Pingback / Trackback:

* New comment notification
* Comment replied notification
* Comment approved notification
* Comment unapproved notification
* Comment marked as spam notification
* Comment moved to trash notification

User:

* User registered notification
* User profile updated notification
* User logged in notification
* User failed to log in notification
* User logged out notification
* User password reset request notification
* User password changed notification
* User deleted notification

Media:

* Media added notification
* Media updated notification
* Media deleted notification

Plugin:

* Plugin activated notification
* Plugin deactivated notification
* Plugin installed notification
* Plugin removed notification
* Plugin updated notification

Theme:

* Theme installed notification
* Theme switched notification
* Theme updated notification

WordPress:

* Available updates notification

Privacy:

* Personal Data erased notification
* Personal Data erase request notification
* Personal Data exported notification
* Personal Data export request notification

Feel free to suggest new core triggers in the support forum.

Each Trigger has own set of Merge Tags but you can use the Global Merge Tags anywhere.

= GLOBAL MERGE TAGS =

Along the Trigger specific Merge Tags, you can use the below anywhere:

* Site homepage URL - `{home_url}`
* Site title - `{site_title}`
* Site tagline - `{site_tagline}`
* Site theme name - `{site_theme_name}`
* Site theme version - `{site_theme_version}`
* Current WordPress version - `{wordpress_version}`
* Admin email - `{admin_email}`
* Trigger name - `{trigger_name}`
* Trigger slug - `{trigger_slug}`

= AWESOME EXTENSIONS =

* [Conditionals](https://bracketspace.com/downloads/notification-conditionals/) - send Notifications in certain conditions
* [Custom Fields](https://bracketspace.com/downloads/notification-custom-fields/) - use any meta value in your Notifications
* [Review Queue](https://bracketspace.com/downloads/notification-review-queue/) - catch your Notifications into queue for a manual review
* [Scheduled Triggers](https://bracketspace.com/downloads/notification-scheduled-triggers/) - schedule your notifications based on events time
* [Discord](https://bracketspace.com/downloads/notification-discord/) - post messages on Discord channel
* [Slack](https://bracketspace.com/downloads/notification-slack/) - post messages on Slack channel
* [Pushbullet](https://bracketspace.com/downloads/notification-pushbullet/) - send Push and SMS Notifications via your phone
* [SendGrid](https://bracketspace.com/downloads/notification-sendgid/) - send emails using SendGrid service
* [Mailgun](https://bracketspace.com/downloads/notification-mailgun/) - send emails using Mailgun service
* [File Log](https://bracketspace.com/downloads/notification-file-log/) - save Notifications as file logs on the server
* [Signature](https://wordpress.org/plugins/signature-notification/) - add a signature to all your emails automatically
* [WooCommerce](https://bracketspace.com/downloads/notification-woocommerce/) - triggers specific to WooCommerce

*Coming soon* - vote for the extensions

* [Email Templates](https://bracketspace.com/downloads/notification-email-templates/) - use beautiful templates for your emails
* [Facebook](https://bracketspace.com/downloads/notification-facebook/) - post messages to Facebook
* [Twitter](https://bracketspace.com/downloads/notification-twitter/) - post messages to Twitter
* [Zapier](https://bracketspace.com/downloads/notification-zapier/) - connect any WordPress event with Zapier
* [Pushover](https://bracketspace.com/downloads/notification-pushover/) - send push notifications using Pushover service
* [Email Attachments](https://bracketspace.com/downloads/notification-email-attachments/) - attach files to your notification
* [WordPress Poster](https://bracketspace.com/downloads/notification-wordpress-poster/) - create WordPress posts

= POSSIBLE USE CASES =

* Overwriting default WordPress Emails
* Post publication notification to the post author
* Custom comment approved notification to post author and administrator
* User logged in notification to the administrator
* Notification about removed user account

= USEFUL LINKS =

* [Documentation](https://docs.bracketspace.com/notification/)
* [Support plugin developement](https://www.paypal.me/underDEV/)
* [GitHub repository](https://github.com/BracketSpace/Notification)
* [Report a bug](https://github.com/BracketSpace/Notification/issues/new)

== Installation ==

= Requirements =

This plugin require at least PHP 7.0.

= Plugin install =

Download and install this plugin from Plugins -> Add New admin screen.

= Distributing in a plugin or theme =

Notification can be loaded also as a part of any plugin or theme. To do it just include plugins's `load.php` file. It will figure out if it's loaded from theme or from plugin.

[See the detailed guide](https://docs.bracketspace.com/notification/developer/general/bundling)

== Frequently Asked Questions ==

= How is this plugin different from Better Notifications for WordPress (BNFW)? =

The Notification plugin works very similar to BNFW but it has better codebase and interface. You can read the full comparison in the [Notification vs Better Notifications for WordPress](https://bracketspace.com/notification-vs-better-notifications-for-wordpress/) article.

= How can I test my notifications? =

It's not needed to install 3rd-party plugins to catch your emails or other notifications. The Notification plugin comes with a logger which you can activate in the settings and see all the notification configuration parameters.

= Why I'm not receiving any emails? =

Is your WordPress sending any emails at all? The best way to test it is to try to reset your password. If you don't get any email than there's something wrong with your server configuration. You could use any SMTP plugin to fix that.

You can also try to activate the debug log in plugin settings to see if the email is triggered.

= Is this plugin for regular users? =

Ofcourse it is! We are trying to make both parties happy - the Users and Developers. Users got their intuitive and beautiful panel in WordPress Admin and Developers got an awesome API by which they can extend the Notification plugin.

So it doesn't matter if you don't have any coding skills, they are not required to setup the notifications with this plugin.

= How to register my own triggers? =

With `register_trigger()` function. [See the detailed guide](https://docs.bracketspace.com/notification/developer/triggers/custom-trigger)

= How to include a custom field in the notification? =

You can [write a merge tag](https://docs.bracketspace.com/notification/developer/triggers/adding-merge-tags-to-existing-triggers) by yourself or go with a no-brainer [Custom Fields extension](https://bracketspace.com/downloads/notification-custom-fields/).

= How to target only specific post / category / user etc? =

You can control when exactly the notification is sending with the [Conditionals extension](https://bracketspace.com/downloads/notification-conditionals/).

= Can I send to a custom recipient list based on my own plugin or theme logic? =

Yes, just include `filter-id:some-value` in the `Recipient` value (using the `Email/Merge tag` type with the `Email` carrier), then return your recipient list from the `notification/recipient/email/some-value` filter.

= Can I bundle the plugin with my plugin or theme? =

Yes, you can. [See the detailed guide](https://docs.bracketspace.com/notification/developer/general/bundling)

= Is this plugin capable of sending high volume emails? =

The plugin is capable and it can send milions of emails, but probably your server is not. To send thousands of emails at once we'd suggest using [SendGrid](https://bracketspace.com/downloads/notification-sendgrid/) or [Mailgun](https://bracketspace.com/downloads/notification-mailgun/) extensions which were designed to support high volume emails in a single API call.

When using SMTP it's nearly impossible to send more than a dozen emails at once due to timeouts.

= Can I test my notifications before sending? =

Yes, just activate the debug log in the DEBUGGING section of the plugin settings. All notifications will be catched into log visible only to you.

== Screenshots ==

1. Trigger edit screen
2. All triggers
3. Settings
4. Extensions
5. Help tab with global Merge Tags
6. Wizard
7. Default email disabler

== Changelog ==

= 7.2.4 =
* [Fixed] Fix Post published trigger which was triggered even if the post was just updated.

= 7.2.3 =
* [Fixed] Merge Tag cleaning regex which could lead in some cases to wiping entire Carrier field.
* [Fixed] Parent Comment ID Merge Tag returning reply ID not the parent.
* [Changed] A check for activation nag if the user can manage options. Otherwise the useless notice is printed when a paid extension is not activated with license key, thanks to @mircobabini.
* [Changed] Post published action to generic "publish_{post_type}" action which allows to trigger the notification when publishing from custom statuses.
* [Changed] Import process which now allows to import singular notification instead of always requireing a collection.
* [Added] [Filter for Background Processing](https://docs.bracketspace.com/notification/developer/snippets/general/background-processing-filter) which can be used to enable or disable particular trigger queueing.

= 7.2.2 =
* [Fixed] Wrong implementation of permission_callback while defining REST endpoints, thanks to @jphorn.
* [Fixed] REST endpoints authentication.
* [Fixed] PHP 8 compatibility, thanks to @g-kanoufi.

= 7.2.1 =
* [Fixed] Composer dev dependency causing platform requirements to go up all the way to PHP 7.3, thanks to @saowp.

= 7.2.0 =
* [Fixed] DB Upgrade running on every admin request, thanks to @pewu-dev.
* [Fixed] Missing permission_callback argument in REST endpoints.
* [Fixed] UserPasswordResetLink Merge Tag property names, thanks to @mircobabini.
* [Fixed] Uninstall process.
* [Fixed] TinyMCE plugin error.
* [Fixed] Notice when Suppressing is active and Debug log is inactive.
* [Fixed] Cache refreshing while running under WP CLI, thanks to @mircobabini.
* [Added] User avatar url to comment trigger and comment replied trigger.
* [Added] Privacy Triggers for User erase/export data request and user erased/exported data.

= 7.1.1 =
* [Fixed] License keys not being passed to the Updater class.
* [Fixed] Cache refreshing on front-end.
* [Fixed] `{comment_datetime}` merge tag being not rendered, thanks to @jphorn.
* [Fixed] Repeater field values being incorrectly parsed.
* [Changed] Non-public Post Types are cached too in case someone want's to unlock them.
* [Changed] Plugin settings are registered on front-end as well to ensure enough data is provided for the cache.
* [Added] Option in the Settings to log the Notification and still send it. Previously it was always suppressed.
* [Added] User role merge tag to all the Post triggers, thanks to Steven N.

= 7.1.0 =
* [Fixed] Carrier Recipients using the explicit slug, now it's configurable.
* [Added] Field class property multiple_section.
* [Added] Post approved Trigger.
* [Added] Revision link for updated post.
* [Added] Enable/Disable bulk actions for Notifications.
* [Changed] Fields usage validation in Section Repeater Vue component now checks Field properties to determine if field can be used in the same row.
* [Changed] Repeater/Recipients Carrier field based on Vue now displays an error when REST API endpoint is not reachable.

= 7.0.4 =
* [Fixed] Cache refresh process causing no Triggers and Carriers to display.
* [Added] Webhook error logging, thanks to @callum-veloxcommerce.
* [Added] Fallback for PRO extensions having a version number in the directory name. They are now properly recognized.
* [Changed] The Filesystem method is now set to `direct` when using this plugin.

= 7.0.3 =
* [Fixed] Wizard notifications trigger slugs.
* [Fixed] Logging dates, now the notification and error log displays the dates properly and respects the timezone.
* [Fixed] Logger now displays the extras key properly.
* [Fixed] Notification bulk delete confirmation message.
* [Fixed] Uninstallation process not fireing.

= 7.0.2 =
* [Fixed] Extensions screen error with premium extension.

= 7.0.1 =
* [Fixed] Param accessor causing PHP notices.
* [Fixed] TinyMCE error when using unfiltered HTML email body.
* [Changed] Updated Composer and NPM dependencies.
* [Changed] When using unfiltered HTML email body, the field is now an HTML editor.

= 7.0.0 =

**Breaking changes**

1. All trigger's slugs has been changed to unify them. Compare the [old slugs](https://docs.bracketspace.com/notification/v/6/developer/triggers/default-triggers) and [new slugs](https://docs.bracketspace.com/notification/v/7/developer/triggers/default-triggers).
2. Settings section `notifications` has been changed to `carriers`. Pay attention while registering the Carrier settings and update all `notification_get_setting( 'notifications/{$group}/{$option}' )` to `notification_get_setting( 'carriers/{$group}/{$option}' )`
3. Changed the plugin file structure and many internal classes which might be used by other plugins.
4. The plugin initializes now on `init 5` action and no functions/classes are available until then. You may use `notifiation/init` action to init the extensions and `notification/elements` to register custom Triggers and Carriers.
5. The Date and Time Merge Tags now require the Unix timestamp which shouldn't have the timezone offset. Use GMT timezone.
6. The `notification_runtime` function has been deprecated in favor of new `\Notification` static class.
7. Repeater and recipients fields on the front-end has been rewriten to use vue.js. Hooks for actions in js scripts for this fields provide now access to vue.js instance. Each repeater and recipient field, are now separate vue.js instances.

**Full changelog**

* [Changed] Added PUT, PATCH, DELETE http request methods to Webhook. Combined all http requests methods into one class method.
* [Changed] Webhook class methods http_request and parse_args move to trait.
* [Changed] Requirements utility to `micropackage/requirements`.
* [Changed] DocHooks utility to `micropackage/dochooks`.
* [Changed] Files utility to `micropackage/filesystem`. Now the plugin has few filesystems which can be accessed easily from outside the plugin.
* [Changed] View utility to `micropackage/templates`.
* [Changed] Ajax utility to `micropackage/ajax`.
* [Changed] Loading stack, now the plugin initializes on init 5 (or 4 if bundled).
* [Changed] Merge Tags don't need the requirements now and throwable resolver errors are caught and changed to notices.
* [Changed] Date and Time Merge Tags now expect Unix timestaps (GMT) without offset.
* [Changed] All Trigger's slugs.
* [Changed] Settings section `notifiations` to proper `carriers` to follow the standard established in version 6.
* [Changed] Repeater and recipient fields are now using vue.js on the front-end.
* [Changed] Pretty select fields in the repeater and recipient fields are now handled by vue.js lifecycle hooks.
* [Changed] `NOTIFICATION_VERSION` constant to `\Notification::version()` method.
* [Changed] User ID Email recipient now support the comma-separated value, thanks to Robert P.
* [Changed] The Recipients section in Carrier box now displays Type column even if a single recipient type is registered.
* [Added] Webhook and Cache trait.
* [Added] Webhook JSON Carrier with plain JSON input code field.
* [Added] Composer imposter package to aviod package conflicts.
* [Added] `notification_filesystem` function to get plugin filesystem(s).
* [Added] Scheduling user Merge Tags for Post Scheduled trigger.
* [Added] Last updated by user Merge Tags for Post triggers.
* [Added] Image field for settings page.
* [Added] Notification runtime cache with `notification_cache()` function wrapper.
* [Added] Two Factor plugin integration.
* [Added] Possibility to nest one level repeater field in another repeater field. Nested repeater field must have `nested_repeater` name.
* [Added] Rest API class to handle internal requests.
* [Added] `notification/settings/saved` action.
* [Fixed] Merge Tag used as anchor href now is not prefixed with protocol while adding the link.
* [Fixed] Selectize script breaking description field in select input.
* [Fixed] Bulk removing Notifications.
* [Removed] `NOTIFICATION_DIR` and `NOTIFICATION_URL` constants.
* [Removed] Ajax action `ajax_get_recipient_input`

== Upgrade Notice ==

= 7.0.0 =
Compatibility breaking changes. Please make sure to review the changelog before upgrading and adjust your customizations.
The premium plugins won't work with Notification v7 unless updated.
