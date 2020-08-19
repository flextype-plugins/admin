<?php

declare(strict_types=1);

/**
 * @link https://digital.flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype\Plugin\Admin;

use Flextype\Component\I18n\I18n;
use function Flextype\Component\I18n\__;
use Flextype\Plugin\Admin\Controllers\DashboardController;
use Flextype\Plugin\Admin\Controllers\SettingsController;
use Flextype\Plugin\Admin\Controllers\PluginsController;
use Flextype\Plugin\Admin\Controllers\EntriesController;
use Flextype\Plugin\Admin\Controllers\ToolsController;
use Flextype\Plugin\Admin\Controllers\ApiController;
use Flextype\Plugin\Admin\Controllers\ApiImagesController;
use Flextype\Plugin\Admin\Controllers\ApiRegistryController;
use Flextype\Plugin\Admin\Controllers\ApiEntriesController;
use Flextype\Plugin\Admin\Controllers\ApiFilesController;
use Flextype\Plugin\Admin\Controllers\ApiFoldersController;
use Flextype\Plugin\Admin\Controllers\ApiAccessController;

// Set Default Admin locale
I18n::$locale = $flextype->container('registry')->get('flextype.settings.locale');

// Add Admin Navigation
$flextype->container('registry')->set('plugins.admin.settings.navigation.content.entries', ['title' => __('admin_entries'), 'icon' => 'fas fa-database', 'link' => $flextype->container('router')->pathFor('admin.entries.index')]);
$flextype->container('registry')->set('plugins.admin.settings.navigation.extends.plugins', ['title' => __('admin_plugins'),'icon' => 'fas fa-plug', 'link' => $flextype->container('router')->pathFor('admin.plugins.index')]);
$flextype->container('registry')->set('plugins.admin.settings.navigation.system.tools', ['title' => __('admin_tools'),'icon' => 'fas fa-toolbox', 'link' => $flextype->container('router')->pathFor('admin.tools.index')]);
$flextype->container('registry')->set('plugins.admin.settings.navigation.system.api', ['title' => __('admin_api'),'icon' => 'fas fa-network-wired', 'link' => $flextype->container('router')->pathFor('admin.api.index')]);

/**
 * Add Assets
 */
$_admin_css = ($flextype->container('registry')->has('assets.admin.css')) ? $flextype->container('registry')->get('assets.admin.css') : [];
$_admin_js  = ($flextype->container('registry')->has('assets.admin.js')) ? $flextype->container('registry')->get('assets.admin.js') : [];

$flextype->container('registry')->set('assets.admin.css',
                           array_merge($_admin_css,
                           ['project/plugins/admin/assets/dist/css/admin-vendor-build.min.css',
                            'project/plugins/admin/assets/dist/css/admin-build.min.css']));

$flextype->container('registry')->set('assets.admin.js',
                       array_merge($_admin_js,
                       ['project/plugins/admin/assets/dist/js/admin-vendor-build.min.js']));

$flextype->container()['DashboardController'] = static function () use ($flextype) {
    return new DashboardController($flextype);
};

$flextype->container()['SettingsController'] = static function () use ($flextype) {
    return new SettingsController($flextype);
};

$flextype->container()['PluginsController'] = static function () use ($flextype) {
    return new PluginsController($flextype);
};

$flextype->container()['EntriesController'] = static function () use ($flextype) {
    return new EntriesController($flextype);
};

$flextype->container()['ToolsController'] = static function () use ($flextype) {
    return new ToolsController($flextype);
};

$flextype->container()['ApiController'] = static function () use ($flextype) {
    return new ApiController($flextype);
};

$flextype->container()['ApiEntriesController'] = static function () use ($flextype) {
    return new ApiEntriesController($flextype);
};

$flextype->container()['ApiFilesController'] = static function () use ($flextype) {
    return new ApiFilesController($flextype);
};

$flextype->container()['ApiFoldersController'] = static function () use ($flextype) {
    return new ApiFoldersController($flextype);
};

$flextype->container()['ApiImagesController'] = static function () use ($flextype) {
    return new ApiImagesController($flextype);
};

$flextype->container()['ApiAccessController'] = static function () use ($flextype) {
    return new ApiAccessController($flextype);
};

$flextype->container()['ApiRegistryController'] = static function () use ($flextype) {
    return new ApiRegistryController($flextype);
};
