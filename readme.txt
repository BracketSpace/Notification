=== Notification - Custom Notifications and Alerts for WordPress ===
Contributors: notification, bracketspace, Kubitomakita, insejn, mateuszgbiorczyk
Tags: notification, notify, alert, email, mail, webhook, API, developer, framework
Requires at least: 4.9
Tested up to: 5.2
Stable tag: 6.2.0
Requires PHP: 7.0
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

= How to target only specific post / category / user etc? =

You can control when exactly the notification is sending with the [Conditionals extension](https://bracketspace.com/downloads/notification-conditionals/).

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

= 6.2.0 =
* [Fixed] Checkbox in plugin settings now can specify true-ish default value.
* [Fixed] Trigger select box margins.
* [Fixed] Role recipient for Email picking wrong roles with LIKE statement.
* [Changed] Input field sanitizer for Carriers, allowing for some HTML tags.
* [Added] Background processing feature, which load the actions into WP Cron.
* [Added] Comment published trigger.
* [Added] Post publication date and time merge tag.

= 6.1.6 =
* [Fixed] Notification duplication feature, thanks to Erik West.

= 6.1.5 =
* [Fixed] Error when a new user was added by logged in user, the password reset notification was sent.

= 6.1.4 =
* [Fixed] Addig the same Notification twice from the Wizard. Now Notification hash is regenerated.
* [Changed] Post triggers now setup properties after checking all the conditions. This way second action call with wrong params won't change the trigger state. Thanks to Tom Angell.

= 6.1.3 =
* [Fixed] Password reset link fatal error when default WordPress notification was disabled.

= 6.1.2 =
* [Fixed] Carrier adding section being booted too early and being broken with other extensions.

= 6.1.1 =
* [Fixed] The Gutenberg integration causing an error for triggers other than related to post types.

= 6.1.0 =
* [Fixed] File mtime method now checks if file exists.
* [Fixed] Cache is now cleared after saving the notification.
* [Fixed] Uninstallation process.
* [Fixed] License deactivation.
* [Fixed] Issue with overlooping notifications when more than one action was called in the same request.
* [Added] Ability to define email headers.
* [Added] Webhook args and headers can be now not included if value is empty.
* [Added] `notification/debug/suppress` filter to disable suppression of notifications when debug log is activated.
* [Added] Confirmation before deleting the notification.
* [Added] Default WordPress emails disabler.
* [Added] `notification/integration/gutenberg` filter to disable Gutenberg support for specific post types. Useful when the post is in REST but no Gutenberg is used.
* [Added] Wizard.
* [Changed] Internationalization for JS files.
* [Changed] Notification repeater field is now by default sortable.
* [Changed] Carrier textarea field now can be unfiltered, so no HTML will be stripped.
* [Changed] Webpack for assets processing instead of Gulp.
* [Changed] OP Cache is no longer a requirement, instead hooks compatibility file is loaded when OP Cache config is incompatible.
* [Changed] Better Carriers management, now Carrier can be added and enabled independently.
* [Removed] Freemius.
* [Removed] The story screen.
* [Removed] Plugin internationalization files as all the translations comes from wp.org.

= 6.0.4 =
* [Fixed] Webhook waring using empty header values.
* [Fixed] Quick switch in Notifications table.
* [Fixed] Catching Notifications.
* [Added] Basic Gutenberg compatibility, post triggers are now postponed to `rest_after_insert_{$post_type}` action.

= 6.0.3 =
* [Fixed] On/off switch in notifications table.
* [Fixed] Duplicate feature.
* [Fixed] Licensing.
* [Changed] Notification trash link wording.

= 6.0.2 =
* [Fixed] Error in admin notice while manipulating extension license.
* [Fixed] Scripts and styles conditional loading.

= 6.0.1 =
* [Changed] Added soft-fail for not valid JSON configuration for Notifications. This is most likely caused by updating from a very old version.

= 6.0.0 =
* [Fixed] Theme Update trigger errors on update.
* [Added] Notification object as a wrapper for Notification Post.
* [Added] `notification_create_view` function for seamless view creation.
* [Added] `notification/post/column/main` action for notification edit screen addons.
* [Added] `notification_get_posts` function.
* [Added] Import and Export feature using JSON files.
* [Added] Composer support with unified testing.
* [Added] Merge Tags groups.
* [Added] Notification Adapters - WordPress and JSON.
* [Added] `notification_ajax_handler` function.
* [Added] Ability to define Notifications programmatically.
* [Added] JSON synchronization feature.
* [Added] `add_quick_merge_tag` Trigger method.
* [Added] Collapse option for plugin settings groups.
* [Added] Common error log for all extensions, you can use `notification_log` function.
* [Added] Post thumbnail URL and featured imager URL Merge Tags.
* [Added] Comment content HTML merge tag.
* [Added] Resolver API which allows to register more Merge Tag resolvers.
* [Added] `notification/should_send` filter to hold off the whole Notification.
* [Removed] Trigger usage tracking.
* [Changed] PostData class has been removed in favor of Notification object and procedural functions.
* [Changed] Admin Classes: MergeTags, Notifications, PostData, Recipients, Triggers has been removed and their content included in the Admin/PostType class.
* [Changed] Notification data is now using single nonce field and additional data should be saved with `notification/data/save` action.
* [Changed] Namespaces of Cron, Internationalization, License and Whitelabel classes.
* [Changed] Native class autoloader to Composer autoloader.
* [Changed] User recipients optimization with direct database calls.
* [Changed] Notification (in "type" context) has been renamed to Carrier.
* [Changed] The View object is not injected anymore to any Class, all use the `notification_create_view` function.
* [Changed] ScreenHelp class has been renamed to Screen and render methods from PostType class has been moved to this new class.
* [Changed] Notifications are now loaded on every page load and the Trigger action is not executing at all if no Notification is using it.
* [Changed] Notifications doesn't have the trash anymore, the items are removed right away.
* [Changed] On notification edit screen the editor styles are no longer applied.
* [Changed] Carriers now have two step status - they can be either added to a Notification and be disabled at the same time.
* [Changed] strip_shortcodes function to custom preg_replace for better stripping.
* [Changed] Trigger storage now contains whole Notifications instead of just Carriers.


= Compatibility breaking changes =

*Hooks* - Some of the hooks names has been renamed for better consistency across the plugin. List of all changes:

* notification/notification/pre-send -> notification/carrier/pre-send
* notification/notification/sent -> notification/carrier/sent
* notificaiton/notification/field/resolving -> notification/carrier/field/resolving
* notification/value/strip_empty_mergetags -> notification/resolve/strip_empty_mergetags
* notification/value/strip_shortcodes -> notification/carrier/field/value/strip_shortcodes
* notificaiton/notification/field/resolved -> notification/carrier/field/value/resolved
* notificaiton/merge_tag/value/resolved -> notification/merge_tag/value/resolved
* notitication/admin/notifications/pre -> notification/admin/carriers/pre
* notitication/admin/notifications -> notification/admin/carriers
* notification/webhook/called/get -> notification/carrier/webhook/called/get
* notification/webhook/called/post -> notification/carrier/webhook/called/post
* notification/notification/box/pre -> notification/carrier/box/pre
* notification/notification/box/post -> notification/carrier/box/post
* notification/notification/box/field/pre -> notification/carrier/box/field/pre
* notification/notification/box/field/post -> notification/carrier/box/field/post
* notification/notification/form_fields/values -> notification/carrier/fields/values
* notification/email/use_html_mime -> notification/carrier/email/use_html_mime
* notification/email/recipients -> notification/carrier/email/recipients
* notification/email/subject -> notification/carrier/email/subject
* notification/email/message/pre -> notification/carrier/email/message/pre
* notification/email/message/use_autop -> notification/carrier/email/message/use_autop
* notification/email/message -> notification/carrier/email/message
* notification/email/headers -> notification/carrier/email/headers
* notification/email/attachments -> notification/carrier/email/attachments
* notification/webhook/args -> notification/carrier/webhook/args
* notification/webhook/args/$type -> notification/carrier/webhook/args/$type
* notification/webhook/remote_args/get -> notification/carrier/webhook/remote_args/get
* notification/webhook/remote_args/post -> notification/carrier/webhook/remote_args/post

*Classes* - Some of the classes or namespaces has been renamed or removed. List of all changes:

* BracketSpace\Notification\Admin\MergeTags - removed
* BracketSpace\Notification\Admin\Notifications - removed
* BracketSpace\Notification\Admin\PostData - removed
* BracketSpace\Notification\Admin\Recipients - removed
* BracketSpace\Notification\Admin\Triggers - removed
* BracketSpace\Notification\Admin\PostData - removed
* BracketSpace\Notification\Tracking - removed
* BracketSpace\Notification\Admin\BoxRenderer - removed
* BracketSpace\Notification\Admin\FormRenderer - removed
* BracketSpace\Notification\Admin\ScreenHelp - removed
* BracketSpace\Notification\Admin\FieldsResolver - removed
* BracketSpace\Notification\Abstracts\Notification -> BracketSpace\Notification\Abstracts\Carrier
* BracketSpace\Notification\Defaults\Notification -> BracketSpace\Notification\Defaults\Carrier
* BracketSpace\Notification\Admin\Cron -> BracketSpace\Notification\Core\Cron
* BracketSpace\Notification\Internationalization -> BracketSpace\Notification\Core\Internationalization
* BracketSpace\Notification\License -> BracketSpace\Notification\Core\License
* BracketSpace\Notification\Whitelabel -> BracketSpace\Notification\Core\Whitelabel

*Functions* - Some of the functions has been renamed for better consistency across the plugin. List of all changes:

* notification_is_new_notification -> notification_post_is_new
* register_notification -> notification_register_carrier
* notification_get_notifications -> notification_get_carriers
* notification_get_single_notification -> notification_get_carrier
* register_trigger -> notification_register_trigger
* notification_get_single_recipient -> notification_get_recipient
* notification_get_notification_recipients -> notification_get_carrier_recipients
* notification_get_single_trigger -> notification_get_trigger
* register_recipient -> notification_register_recipient
