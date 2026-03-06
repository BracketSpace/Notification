# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Is

Notification is a WordPress plugin that lets users create custom notifications triggered by WordPress events. It ships with Email, Webhook, and Webhook JSON carriers by default. The plugin is distributed on WordPress.org.

Namespace: `BracketSpace\Notification\` (PSR-4 autoloaded from `src/`)

## Development Commands

```bash
# Frontend
yarn start                # Dev build with watch
yarn build                # Dev build
yarn build:production     # Production build
yarn lint                 # Lint CSS + JS
yarn fix                  # Fix CSS + JS lint issues

# PHP quality (use Herd for composer/php commands)
herd composer phpcs       # PHPCS (PSR-12 Neutron Hybrid ruleset)
herd composer phpcbf      # Auto-fix PHPCS issues
herd composer phpstan     # PHPStan at max level (uses phpstan-baseline.neon)
herd composer phplint     # PHP syntax check

# Testing (uses Pest v1 on PHPUnit 9, requires wp-env)
yarn start-env            # Start wp-env Docker environment
yarn test-php             # Run full test suite via wp-env

# Dependency management
herd composer install     # Installs deps + runs Strauss namespace prefixing
```

### Running a Single Test

```bash
# Via wp-env (the standard way):
wp-env run tests-wordpress --env-cwd=wp-content/plugins/notification \
  ./vendor/bin/pest -- --configuration phpunit.xml --filter="test name pattern"
```

### Hook Documentation

When editing/adding/removing functions with `@action` or `@filter` docblock annotations, regenerate the hooks compatibility file:

```bash
lando wp notification dump-hooks
```

This updates `compat/register-hooks.php`, which is the compiled fallback for DocHooks annotations.

## Architecture

### Core Processing Pipeline

1. **Trigger fires** — A WordPress action runs, and a registered Trigger's `context()` method captures data
2. **Runner** (`Core\Runner`) — Finds all Notifications using that Trigger, clones them, and adds them to the Queue
3. **Queue** (`Core\Queue`) — Holds Notification+Trigger pairs for the current request
4. **Processor** (`Core\Processor`) — On `shutdown`, iterates the Queue. Either processes immediately or schedules via WP-Cron (background processing)
5. **Carrier sends** — Each enabled Carrier resolves its merge tags from the Trigger, prepares data, and sends

### Key Design Patterns

**Store/Repository pattern**: Static in-memory registries (`Store\*`) hold runtime objects (Triggers, Carriers, Recipients, Resolvers, Notifications). `Repository\*` classes handle default registration. `Register` class is the public API for inserting into Stores.

**DocHooks**: Methods annotated with `@action` or `@filter` in their docblocks are automatically registered as WordPress hooks via the `micropackage/dochooks` library. When DocHooks isn't available, the compiled `compat/register-hooks.php` file is used as fallback.

**Component system**: `Runtime::singletons()` registers all components (Core, Admin, Integration, API, etc.) into `$this->components`. Access via `Notification::component(FQCN::class)`.

**Converter pattern**: `Notification::from('array', $data)` and `$notification->to('json')` use WordPress filters (`notification/from/{type}`, `notification/to/{type}`) dispatched to converter classes in `Repository\Converter\`.

**Strauss namespace prefixing**: Third-party libraries are prefixed to `BracketSpace\Notification\Dependencies\` in the `dependencies/` directory to avoid conflicts with other plugins. Runs automatically on `composer install`.

### Directory Layout

- `src/Core/` — Processing pipeline: Notification, Runner, Queue, Processor, Binder, Settings, Sync, Cron
- `src/Repository/` — Default registrations: Triggers (Post, Comment, User, Media, etc.), Carriers, Recipients, Resolvers, MergeTags, plus Converters
- `src/Store/` — Static in-memory registries using the `Storage` trait
- `src/Admin/` — WP admin UI: post type, scripts, settings pages, import/export, wizard
- `src/Database/` — Custom DB tables (`wp_notifications`, `wp_notification_carriers`, `wp_notification_extras`) with WP Post sync
- `src/Interfaces/` — Triggerable, Sendable, Receivable, Resolvable, Taggable, etc.
- `src/Traits/` — Storage, HasName, HasSlug, HasDescription, HasGroup, etc.
- `compat/` — Backward compatibility: deprecated classes/functions, compiled hooks file, component aliases
- `resources/` — Templates (PHP), JS/CSS (src→dist via webpack), wizard data (JSON)
- `dependencies/` — Strauss-prefixed vendor libraries (auto-generated, do not edit manually)

### Database

Notifications are stored in both custom tables and `wp_posts` (type `notification`) for backward compatibility. The `NotificationDatabaseService` handles both in sync. Custom tables: `wp_notifications`, `wp_notification_carriers`, `wp_notification_extras`.

## Coding Conventions

- Extended PSR-12 via `szepeviktor/phpcs-psr-12-neutron-hybrid-ruleset` — tabs for indentation, not spaces
- PHPStan at max level with a baseline file
- PHP 7.4+ compatibility required (tested up to 8.4)
- DocHooks annotations: use `@action hook_name [priority]` and `@filter hook_name [priority]` in method docblocks

## Changelog & Workflow

- **Changelog**: BEFORE committing, document changes in `readme.txt` under the `== Changelog ==` section. Use format `= X.Y.Z =` with `* [Added/Changed/Fixed] Description`. For unreleased changes, use `[Next]` as the version header (e.g., `= [Next] =`). Keep entries meaningful — don't bloat with granular changes. Group entries by type: Added first, then Changed, then Fixed.
- **Text domain**: `notification`.
- **Version bumps**: Version appears in `notification.php` (plugin header + Runtime instantiation). The release workflow handles this automatically.
- **Unreleased version references**: Use `[Next]` wherever a future version number would go (changelog headers, docblock `@since` tags, etc.). The release workflow automatically replaces `[Next]` with the actual version number.
- **Release**: GitHub Actions workflow using git-flow. `master` is for releases, `develop` is the working branch.
- **Git**: Gitflow — branch from `develop`, PR back to `develop`. Branch naming: `feature/Name-of-the-Feature`. Conventional Commits.
