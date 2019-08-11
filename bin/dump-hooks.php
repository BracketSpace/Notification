<?php
/**
 * This file dumps all the Dochooks to an external file: /inc/hooks.php
 * It's used only when OP Cache has `save_comments` setting saved to false.
 *
 * @usage: wp eval-file dump-hooks.php
 * @usage: wp eval-file wp-content/plugins/notification/bin/dump-hooks.php
 */

WP_CLI::success( 'Works!' );
