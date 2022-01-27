=== Notification - Custom Notifications and Alerts for WordPress ===
Contributors: notification, bracketspace, Kubitomakita, tomaszadamowicz, insejn, mateuszgbiorczyk
Tags: notification, notify, alert, email, mail, webhook, API, developer, framework
Requires at least: 4.9
Tested up to: 5.8
Stable tag: 8.0.10
Requires PHP: 7.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.

== Description ==

Custom Notifications and Alerts without a hassle. Notify anyone about any action in your WordPress. With powerful Merge Tags, you can endlessly customize your messages. Set unlimited Notifications in your WordPress Admin via the beautiful and intuitive interface within 5 minutes.

[youtube https://www.youtube.com/watch?v=gW2KHrT_a7U]

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
* Creating custom Trigger - one intuitive class definition and registration with a single method call
* Defining Global Merge Tag - 1 line of code
* Creating new Extension - we have a [Boilerplate](https://github.com/BracketSpace/Notification-Extension-Boilerplate/) ready for you to start hacking

[See the developer documentation](https://docs.bracketspace.com/notification/developer/general) if you don't believe us.

= DEFAULT CARRIERS =

* Email
* Webhook
* Webhook JSON

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
* [Slack](https://bracketspace.com/downloads/notification-slack/) - post messages to Slack channel
* [Push](https://bracketspace.com/downloads/notification-push/) - send push notifications via browser's native system
* [Discord](https://bracketspace.com/downloads/notification-discord/) - post messages to Discord channel
* [Twilio](https://bracketspace.com/downloads/notification-twilio/) - send bulk SMS messages from your Twilio registered phone number
* [Scheduled Triggers](https://bracketspace.com/downloads/notification-scheduled-triggers/) - schedule your notifications based on events time
* [Review Queue](https://bracketspace.com/downloads/notification-review-queue/) - catch your Notifications into queue for a manual review
* [WooCommerce](https://bracketspace.com/downloads/notification-woocommerce/) - triggers specific to WooCommerce
* [Pushbullet](https://bracketspace.com/downloads/notification-pushbullet/) - send Push and SMS Notifications via your phone
* [Pushover](https://bracketspace.com/downloads/notification-pushover/) - send Push messages to devices registered in Pushover
* [SendGrid](https://bracketspace.com/downloads/notification-sendgid/) - send emails using SendGrid service
* [Mailgun](https://bracketspace.com/downloads/notification-mailgun/) - send emails using Mailgun service
* [File Log](https://bracketspace.com/downloads/notification-file-log/) - save Notifications as file logs on the server
* [bbPress](https://wordpress.org/plugins/notification-bbpress/) - bbPress triggers
* [BuddyPress](https://wordpress.org/plugins/notification-buddypress/) - BuddyPress triggers and integration with their notification system
* [Signature](https://wordpress.org/plugins/signature-notification/) - add a signature to all your emails automatically
* [AppPresser](https://bracketspace.com/downloads/notification-apppresser) - push messages to your mobile app built with AppPresser
* [Email Attachments](https://bracketspace.com/downloads/notification-email-attachments/) - attach files to your notification

*Coming soon* - vote for the extensions

* [Facebook](https://bracketspace.com/downloads/notification-facebook/) - post messages to Facebook
* [Twitter](https://bracketspace.com/downloads/notification-twitter/) - post messages to Twitter
* [Zapier](https://bracketspace.com/downloads/notification-zapier/) - connect any WordPress event with Zapier
* [WordPress Poster](https://bracketspace.com/downloads/notification-wordpress-poster/) - create WordPress posts

= POSSIBLE USE CASES =

* Overwriting default WordPress Emails
* Post publication notification to the post author
* Custom comment approved notification to post author and administrator
* User logged in notification to the administrator
* Notification about removed user account

= USEFUL LINKS =

* [Documentation](https://docs.bracketspace.com/notification/)
* [GitHub repository](https://github.com/BracketSpace/Notification)
* [Report a bug](https://github.com/BracketSpace/Notification/issues/new)

= CUSTOM DEVELOPMENT =

BracketSpace - the company behind this plugin provides [custom WordPress plugin development services](https://bracketspace.com/custom-development/). We can create any custom plugin for you.

== Installation ==

= Requirements =

This plugin require at least PHP 7.0.

= Plugin install =

Download and install this plugin from Plugins -> Add New admin screen.

= Distributing in a plugin or theme =

Notification can be loaded also as a part of any plugin or theme. To do it just include plugins's `load.php` file. It will figure out if it's loaded from theme or from plugin.

[See the detailed guide](https://docs.bracketspace.com/notification/developer/general/bundling)

== Frequently Asked Questions ==

= How can I test my notifications? =

It's not needed to install 3rd-party plugins to catch your emails or other notifications. The Notification plugin comes with a logger which you can activate in the settings and see all the notification configuration parameters.

= Why I'm not receiving any emails? =

Is your WordPress sending any emails at all? The best way to test it is to try to reset your password. If you don't get any email than there's something wrong with your server configuration. You could use any SMTP plugin to fix that.

You can also try to activate the debug log in plugin settings to see if the email is triggered.

= Is this plugin for regular users? =

Ofcourse it is! We are trying to make both parties happy - the Users and Developers. Users got their intuitive and beautiful panel in WordPress Admin and Developers got an awesome API by which they can extend the Notification plugin.

So it doesn't matter if you don't have any coding skills, they are not required to setup the notifications with this plugin.

= How is this plugin different from Better Notifications for WordPress (BNFW)? =

The Notification plugin works very similar to BNFW but it has better codebase and interface. You can read the full comparison in the [Notification vs Better Notifications for WordPress](https://bracketspace.com/notification-vs-better-notifications-for-wordpress/) article.

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

= Can you create a plugin for me? =

Yes! We're offering a [custom plugin development](https://bracketspace.com/custom-development/) services. Feel free to contact us to find out how we can help you.

== Screenshots ==

1. Trigger edit screen
2. All triggers
3. Settings
4. Extensions
5. Help tab with global Merge Tags
6. Wizard
7. Default email disabler

== Changelog ==

= 8.0.10 =

* [Fixed] User logout trigger. In WordPress 5.5 the context is set properly.
* [Fixed] Issue with persistent Trigger state if two or more actions assigned to the same trigger were called.
* [Changed] Carrier's recipients field is now returned with resolved data if available.
* [Added] Post Published privately trigger.

= 8.0.9 =

* [Fixed] Merge Tags resolver problem caused by overriding the processed trigger instance.
* [Changed] `notification/should_send` filter is now executed when the queue is processed, not before the notification is added to the queue.
* [Added] New queue methods: `remove()` and `clear()`.

= 8.0.8 =

* [Fixed] Two or more same triggers processed in the same request overwriting each other data.

= 8.0.7 =

* [Fixed] Shortcode stripping regex that was matching JSON arrays.
* [Changed] Extensions are now reporting updates even if they are not activated.
* [Changed] Updated EDD Updater class.
* [Added] Webhook warning logging when response is not valid.

= 8.0.6 =

* [Fixed] Extension activation notice link.
* [Fixed] Extension activation process.
* [Fixed] Incorrect empty merge tag cleaning which was misreading JSON format.

= 8.0.5 =

* [Changed] Updated PHP dependencies.

= 8.0.4 =

* [Changed] Updated PHP dependencies.
* [Changed] Extension license notice is now printed once and covers all the plugins.
* [Changed] Some of the core fields like Import/Export now have own setting classes.
* [Fixed] Remaining template variable escaping.
* [Removed] HTML Settings field, introduced in v8.0.3. Now it's required to create purpose-specific field classes.

= 8.0.3 =

* [Added] HTML Settings field.
* [Added] Notification hash column in the Notification table.
* [Changed] Some of the Settings to HTML field instead of the Message field.
* [Fixed] Broken Import/Export sections.
* [Fixed] Notifications cache is now cleared when creating notification via wizard.

= 8.0.2 =

* [Added] HTML escaping and nonce verifications.
* [Changed] Notification file syncing is now using Filesystem methods.
* [Changed] Internal cache classes with `micropackage/cache`.
* [Changed] Menu icon.
* [Changed] Vue is now loaded from within the plugin instead of CDN.
* [Removed] Internal cache classes `Bracketspace\Notification\Utils\Cache` and `Bracketspace\Notification\Utils\Interfaces` namespaces.
* [Removed] Settings internal caching that couldn't wait for all the fields to be registered. Now we're relying on the get_option() core function caching.

= 8.0.1 =

* [Changed] Field and Merge Tag description field is now escaped and cannot contain any HTML tags.
* [Fixed] Recipients parser which didn't resolved Email Merge Tags.

= 8.0.0 =

**Compatibility Breaking Changes**

1. Runtime `get_filesystems()` method has been changed to `get_filesystem()` and now only root file system is defined.
2. Trigger `action()` method has been renamed to `context()`.
3. Trigger doesn't have the postponing feature anymore, as processing is happening on the `shutdown` action.
4. Trigger is now only a description object, all the processing is handled by the Runner class.
5. `notification/carrier/sent` action doesn't have the Notification context anymore, so there's no 3rd parameter.
6. Store classes now live under `BracketSpace\Notification\Store` namespace rather than `BracketSpace\Notification\Defaults\Store`.
7. Plugin doesn't cache anything anymore, the loading process is more streamlined and things like Post Types are lazy loaded when needed
8. Registration functions has been replaced with `Register` class and its static methods.
9. Multiple functions has been replaced with their static method equivalents.
10. `notification/elements` action has been deprecated, use `notification/init` instead.
11. `NOTIFICATION_VERSION` constant has been removed, use `Notification::version()` instead.
12. `BracketSpace\Notification\Vendor` namespace is replaced with `BracketSpace\Notification\Dependencies`.

Removed deprecated hooks:
- `notification/notification/pre-send`, use `notification/carrier/pre-send`
- `notificaiton/notification/field/resolving`, use `notification/carrier/field/resolving`
- `notification/value/strip_empty_mergetags`, use `notification/resolve/strip_empty_mergetags`
- `notification/value/strip_shortcodes`, use `notification/carrier/field/value/strip_shortcodes`
- `notificaiton/notification/field/resolved`, use `notification/carrier/field/value/resolved`
- `notificaiton/merge_tag/value/resolved`, use `notification/merge_tag/value/resolved`
- `notification/webhook/remote_args/{$method}`, use `notification/carrier/webhook/remote_args/{$method}`
- `notification/webhook/called/{$method}`, use `notification/carrier/webhook/called/{$method}`
- `notification/boot/initial`, use `notification/init`
- `notification/boot`, use `notification/init`

**Full changelog**

* [Fixed] Code issues from not using static analysis.
* [Fixed] WordPress' balanceTags filter which was breaking the Notification content.
* [Fixed] Notification importing.
* [Fixed] Setting fields escaping.
* [Fixed] Post Updated Trigger which failed for updating pending posts, that doesn't have the slug yet.
* [Changed] Always return the single root filesystem in Runtime.
* [Changed] Stores with plugin objects, now they are much simpler and don't use WP filters.
* [Changed] Plugin loading stack, [see docs](https://docs.bracketspace.com/notification/developer/general/plugin-loading-chain) for more details.
* [Changed] Plugin settings now are initialized on `notification/init 5` action.
* [Changed] Recipients now can be loaded anytime, not only before Carriers get registered.
* [Changed] PHP Dependency handling, now all the PHP dependencies lives in src/Dependencies dir.
* [Removed] `Common` Abstract that has been replaced by HasName and HasSlug Traits.
* [Removed] Cache class and all caching mechanism for post types, taxonomies and comment types.
* [Removed] Trait Users. This is replaced with `BracketSpace\Notification\Queries\UserQueries` class.
* [Removed] Deprecated hooks for actions and filters.
* [Removed] Carrier helper functions: `notification_register_carrier`, `notification_get_carriers`, `notification_get_carrier`.
* [Removed] Recipient helper functions: `notification_register_recipient`, `notification_get_recipients`, `notification_get_carrier_recipients`, `notification_get_recipient`, `notification_parse_recipient`.
* [Removed] Resolver helper functions: `notification_register_resolver`, `notification_resolve`, `notification_clear_tags`.
* [Removed] Trigger helper functions: `notification_register_trigger`, `notification_get_triggers`, `notification_get_trigger`, `notification_get_triggers_grouped`.
* [Removed] GLobal Merge Tags helper functions: `notification_add_global_merge_tag`, `notification_get_global_merge_tags`.
* [Removed] Misc functions: `notification_display_wizard`, `notification_ajax_handler`, `notification_filesystem`.
* [Removed] Template functions: `notification_template`, `notification_get_template`.
* [Removed] Notification post functions: `notification_get_posts`, `notification_get_post_by_hash`, `notification_post_is_new`.
* [Removed] Syncing functions: `notification_sync`, `notification_get_sync_path`, `notification_is_syncing`.
* [Removed] Whitelabeling functions: `notification_whitelabel`, `notification_is_whitelabeled`.
* [Removed] Editor and Code Editor fields sanitizers to allow for HTML usage, ie. email templates.
* [Removed] `notification/elements` action hoook.
* [Removed] NOTIFICATION_VERSION constant.
* [Added] Runner class that processes the Triggers.
* [Added] ErrorHandler class that helps handle errors. It can throw an exception when NOTIFICATION_DEBUG is enabled or save a warning to error_log when it's disabled.
* [Added] Plugin settings value lazy loading.
* [Added] Email error catcher.
* [Added] Free and Premium extensions upselling.
* [Added] `Notification::fs()` helper that returns plugin filesystem.
* [Added] Core\Templates wrapper for Templates provider.

== Upgrade Notice ==

= 8.0.0 =
Compatibility breaking changes and security fixes. Please make sure to review the changelog before upgrading and adjust your customizations.
The premium plugins won't work with Notification v8 unless updated.

= 7.0.0 =
Compatibility breaking changes. Please make sure to review the changelog before upgrading and adjust your customizations.
The premium plugins won't work with Notification v7 unless updated.
