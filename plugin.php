<?php

declare(strict_types=1);

/**
 * @link https://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype\Plugin\Admin;

use Flextype\Component\I18n\I18n;
use function Flextype\Component\I18n\__;

/**
 * Set base admin route
 */
$adminRoute = registry()->get('plugins.admin.settings.route');

/**
 * Ensure vendor libraries exist
 */
! is_file($adminAutoload = __DIR__ . '/vendor/autoload.php') and exit('Please run: <i>composer install</i> admin plugin');

/**
 * Register The Auto Loader
 *
 * Composer provides a convenient, automatically generated class loader for
 * our application. We just need to utilize it! We'll simply require it
 * into the script here so that we don't have to worry about manual
 * loading any of our classes later on. It feels nice to relax.
 * Register The Auto Loader
 */
$adminLoader = require_once $adminAutoload;

/**
 * Include web routes
 */
include_once 'src/admin/routes/web.php';

// Set Default Admin locale
I18n::$locale = registry()->get('flextype.settings.locale');

// Add Admin Navigation
registry()->set('assets.admin.css.admin', ['project/plugins/admin/assets/dist/css/admin.min.css']);
registry()->set('assets.admin.js.admin', ['project/plugins/admin/assets/dist/js/admin.min.js']);


