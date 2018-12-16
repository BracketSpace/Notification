=== Notification - Custom Notifications and Alerts for WordPress ===
Contributors: notification, bracketspace, Kubitomakita, insejn
Donate link: https://www.paypal.me/underDEV/
Tags: notification, notify, alert, email, mail, webhook, API, developer, framework
Requires at least: 4.9
Tested up to: 5.0
Stable tag: 5.3.2
Requires PHP: 5.6
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.

== Description ==

Custom Notifications and Alerts without a hassle. Notify anyone about any action in your WordPress. With powerful Merge Tags, you can endlessly customize your messages. Set unlimited Notifications in your WordPress Admin via the beautiful and intuitive interface within 5 minutes.

[youtube https://www.youtube.com/watch?v=UPqVBhLGTek]

= HOW DOES IT WORK =

The Notification plugin is built with three main components:

* Trigger - a WordPress action, ie. User registration or Post publication
* Notification - the thing which is being sent, ie. Email or Push
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

* You can easily load it copying the plugin files and including `load.php` file. A function known from Advanced Custom Fields plugin.
* You can white label the plugin with just one function which is shipped in the plugin's core. For free.

How easy extending the Notification plugin is? Let's see:

* Adding another Merge Tag to existing trigger - 1 line of code
* Creating custom Trigger - one intuitive class definition and registration with a single function call
* Defining Global Merge Tag - 1 line of code
* Creating new Extension - we have a [Boilerplate](https://github.com/BracketSpace/Notification-Extension-Boilerplate/) ready for you to start hacking

[See the developer documentation](https://docs.bracketspace.com/docs-category/developer/) if you don't believe us.

= DEFAULT NOTIFICATIONS =

* Email
* Webhook

= DEFAULT RECIPIENTS =

The plugin comes with few registered by default recipient types:

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

*Coming soon* - vote for the extensions

* [WooCommerce](https://bracketspace.com/downloads/notification-woocommerce/) - triggers specific to WooCommerce
* [Email Templates](https://bracketspace.com/downloads/notification-email-templates/) - use beautiful templates for your emails
* [Facebook](https://bracketspace.com/downloads/notification-facebook/) - post messages to Facebook
* [Twitter](https://bracketspace.com/downloads/notification-twitter/) - post messages to Twitter
* [Zapier](https://bracketspace.com/downloads/notification-zapier/) - connect any WordPress event with Zapier
* [Pushover](https://bracketspace.com/downloads/notification-pushover/) - send push notifications using Pushover service
* [Email Attachments](https://bracketspace.com/downloads/notification-email-attachments/) - attach files to your notification
* [WordPress Poster](https://bracketspace.com/downloads/notification-wordpress-poster/) - create WordPress posts

= POSSIBLE USE CASES =

* Post publication notification to the post author
* Custom comment approved notification to post author and administrator
* User logged in notification to the administrator
* Notification about removed user account

= USEFUL LINKS =

* [Documentation](https://docs.bracketspace.com/docs-category/notification/)
* [Support plugin developement](https://www.paypal.me/underDEV/)
* [GitHub repository](https://github.com/BracketSpace/Notification)
* [Report a bug](https://github.com/BracketSpace/Notification/issues/new)

== Installation ==

= Requirements =

This plugin require at least PHP 5.6.

= Plugin install =

Download and install this plugin from Plugins -> Add New admin screen.

= Distributing in a plugin or theme =

Notification can be loaded also as a part of any plugin or theme. To do it just include plugins's `load.php` file. It will figure out if it's loaded from theme or from plugin.

[See the detailed guide](https://docs.bracketspace.com/docs/including-notification-in-the-plugin-or-theme/)

== Frequently Asked Questions ==

= How is this plugin different from Better Notifications for WordPress (BNFW)? =

The Notification plugin works very similar to BNFW but it has better codebase and interface. You can read the full comparison in the [Notification vs Better Notifications for WordPress](https://bracketspace.com/notification-vs-better-notifications-for-wordpress/) article.

= Why I'm not receiving any emails? =

Is your WordPress sending any emails at all? The best way to test it is to try to reset your password. If you don't get any email than there's something wrong with your server configuration. You could use any SMTP plugin to fix that.

You can also try to activate the debug log in plugin settings to see if the email is triggered.

= Is this plugin for regular users? =

Ofcourse it is! We are trying to make both parties happy - the Users and Developers. Users got their intuitive and beautiful panel in WordPress Admin and Developers got an awesome API by which they can extend the Notification plugin.

So it doesn't matter if you don't have any coding skills, they are not required to setup the notifications with this plugin.

= How to register my own triggers? =

With `register_trigger()` function. [See the detailed guide](https://docs.bracketspace.com/docs/registering-custom-triggers/)

= How to include a custom field in the notification? =

You can [write a merge tag](https://docs.bracketspace.com/docs/adding-merge-tags-to-existing-triggers/) by yourself or go with a no-brainer [Custom Fields extension](https://bracketspace.com/downloads/notification-custom-fields/).

= How to target only specific post / category / user etc? =

You can control when exactly the notification is sending with the [Conditionals extension](https://bracketspace.com/downloads/notification-conditionals/).

= Can I bundle the plugin with my plugin or theme? =

Yes, you can. [See the detailed guide](https://docs.bracketspace.com/docs/including-notification-in-the-plugin-or-theme/)

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

== Changelog ==

= 5.3.2 =
* [Fixed] Double post notification called for post revisions.
* [Fixed] Wrong `wordpress` setting slug caused the option to not be saved.
* [Added] `NOTIFICATION_VERSION` constant.
* [Added] `remove_merge_tag` trigger method.

= 5.3.1 =
* [Added] Initial unit tests.
* [Fixed] Comment replied trigger not called from wp-admin.

= 5.3.0 =
* [Changed] Added parent class for user triggers.
* [Changed] Added parent class for media triggers.
* [Changed] PHP requirement to 5.6.
* [Changed] Freemius updated to version 2.1.3.
* [Changed] Uninstallation logic moved to the Freemius handler.
* [Changed] Merge Tag validation now accepts empty values.
* [Changed] Post Trigger postponing to avoid looping.
* [Changed] Custom Fields postponing causing the loops, now it's in a separate integration class.
* [Changed] Minimum WordPress version to 4.9.
* [Changed] Plugin autoload function.
* [Added] Global date, time and datetime merge tags for trigger execution date.
* [Added] `notification/scripts/allowed_hooks` filter to control where the scripts are loaded.
* [Added] Color Picker field.
* [Added] Image field.
* [Added] User role changed trigger.
* [Added] Post scheduled trigger.
* [Added] `notification/scripts` action.
* [Added] Comment datetime merge tag.
* [Added] Debug log.
* [Added] Plugin triggers.
* [Added] Theme triggers.
* [Added] `notification/data/save` action.
* [Added] Usage tracking. No data is tracked unless allowed.
* [Fixed] Comment type check causing the comment triggers failing.
* [Fixed] Postponing looping over itself.
* [Fixed] Fatal error with Triggers select on certain conditions.
* [Fixed] Date and Time merge tags giving values of 0.
* [Fixed] User password reset request being called upon registration.
* [Fixed] Timezone being ignored in some merge tags.

= 5.2.4 =
* [Fixed] When `Strip all shortcodes` setting is disabled, the shortcodes are rendered.
* [Fixed] Coding Standards.
* [Fixed] Gutenberg JS Hooks conflict.
* [Fixed] Bug when recipient types has been changed.
* [Fixed] Every comment type being targeted in Comment trigger.
* [Fixed] Used failed login datetime merge tag.
* [Added] `remove_action` metod in Trigger class.
* [Added] Option to allow unfiltered HTML in Email body, thanks to @martinellimarco.
* [Added] Post content HTML tag, for unfiltered output.
* [Added] Better JSON support for webhooks, if webhook is sent as JSON the default Content-Type header is added.
* [Added] Support for merge tags and shortcodes in Webhook recipient field.
* [Added] Disabled OP Cache requirement.

= 5.2.3 =
* [Fixed] Duplicated meta for active notification type.
* [Fixed] Post Title merge tag HTML entities, thanks to @freshjustin.
* [Fixed] Problem with disabling active notification types when switching notification status, thanks to @freshjustin.
* [Added] Notification duplicator to easly duplicate notifications.
* [Added] `notification/trigger/action/did` action in Trigger class.
* [Added] `get_post_type` method for PostTrigger.
* [Added] `notification/load/default/priority` filter for registering default things.
* [Added] `user_IP` Merge Tag for User loged in Trigger.
* [Changed] Plugin initializes the default things on `plugins_loaded` action.

= 5.2.2 =
* [Fixed] Post Updated Trigger postponing.
* [Added] `notificaiton/merge_tag/value/resolved` filter.
* [Added] `post_sticky` status Merge Tag only for Post.
* [Added] Email From Name and From Email settings.
* [Added] The story screen.
* [Changed] Merge Tag resolver is now set via `set_resolver` method.
* [Changed] PHP version requirement to 5.3.9.

= 5.2.1 =
* [Fixed] Not existing post type.
* [Fixed] Merge tag values are cleaned when Trigger is executed second time in the same run.
* [Added] Post saved as a draft trigger.
* [Added] `notification_add_doc_hooks` function for creating doc hooks handlers.
* [Added] Classes to Settings sections.
* [Added] Taxonomy term created trigger
* [Added] Taxonomy term updated trigger
* [Added] Taxonomy term deleted trigger
* [Added] User login failed trigger.
* [Added] User password reset request trigger.
* [Added] User password change trigger.

= 5.2.0 =
* [Fixed] User ID assignment in User deleted trigger, thanks to @Matthewnie.
* [Fixed] ACF postponed action bail.
* [Fixed] Field value filter name.
* [Fixed] Notices.
* [Fixed] Empty email body not being sent.
* [Fixed] Notification form attributes escaping.
* [Changed] Trigger `action` methods has been unified with callback method parameters.
* [Changed] The Merge Tags are now resolved only while they are used.
* [Changed] Post Pending trigger to not send multiple emails when pending post is updated, thanks to @jdaniel.
* [Added] Dynamic property setting for Attachment merge tags.
* [Added] Better post updated messages while saving the Notification.
* [Added] Option to change Email MIME Type, now you can send HTML or Plain text.
* [Added] Filter for post statuses which controls when the "Updated" notification should be send.
* [Added] Notification Form fields value filter.
* [Added] Notification Form row class.
* [Added] Doc Hooks class for better actions and filters definitions.

= 5.1.7 =
* [Fixed] Post Terms merge tags not rendering the values, thanks to @stocker.
* [Changed] register_new_user action for User registered trigger to user_register.
* [Added] new_to_publish action for Post published trigger, thanks to @JBCSU.
* [Added] Post Added trigger.
* [Added] Comment replied trigger.

= 5.1.6 =
* [Fixed] Notice from PostTerms merge tags and from empty result.
* [Fixed] Cloning Notification object which used the same fields instances, thanks to @JohanHjalmarsson.

= 5.1.5 =
* [Fixed] Comment author display name bug, thanks to Aga Bury.
* [Fixed] Post Published and Post Pending trigger fired twice.
* [Fixed] Assets modification time as a cache buster.
* [Fixed] Missing translations.
* [Fixed] Notice thrown while saving email administrator recipient.
* [Added] Comment Post Type merge tag for Comment triggers.
* [Added] Comment moderation links for Comment triggers.
* [Added] HtmlTag merge tag type.
* [Added] WordPress updates available trigger.
* [Added] Post Type merge tag.
* [Added] More merge tags for comment triggers.

= 5.1.4 =
* [Fixed] Object class name error on PHP 7.2 when using paid extension.
* [Added] User password setup link for User registered trigger.
* [Added] Ability to encode Webhook args as a JSON
* [Added] Post status merge tag for Post triggers.
* [Changed] Recipient Free type email field now supports comma separated emails.
* [Changed] ACF integration postponed action for Post triggers has been changed to `save_post` which makes it more universal.

= 5.1.3 =
* [Fixed] Pretty select in repeater is now rendered correctly while adding new row.
* [Fixed] User Registration Trigger action.
* [Fixed] ACF Postponing when there's no data from ACF to save.
* [Fixed] Post object property name for Custom Post Types.
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

For more changelogs please refer to the [changelog.txt](https://github.com/BracketSpace/Notification/blob/master/changelog.txt) file.
