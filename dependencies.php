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
use Flextype\Plugin\Admin\Controllers\ApiAccessController;

// Set Default Admin locale
I18n::$locale = $flextype->registry->get('flextype.settings.locale');

// Add Admin Navigation
$flextype->registry->set('plugins.admin.settings.navigation.content.entries', ['title' => __('admin_entries'), 'icon' => 'fas fa-database', 'link' => $flextype->router->pathFor('admin.entries.index')]);
$flextype->registry->set('plugins.admin.settings.navigation.extends.plugins', ['title' => __('admin_plugins'),'icon' => 'fas fa-plug', 'link' => $flextype->router->pathFor('admin.plugins.index')]);
$flextype->registry->set('plugins.admin.settings.navigation.system.tools', ['title' => __('admin_tools'),'icon' => 'fas fa-toolbox', 'link' => $flextype->router->pathFor('admin.tools.index')]);
$flextype->registry->set('plugins.admin.settings.navigation.system.api', ['title' => __('admin_api'),'icon' => 'fas fa-network-wired', 'link' => $flextype->router->pathFor('admin.api.index')]);

/**
 * Add Assets
 */
$_admin_css = ($flextype['registry']->has('assets.admin.css')) ? $flextype['registry']->get('assets.admin.css') : [];
$_admin_js  = ($flextype['registry']->has('assets.admin.js')) ? $flextype['registry']->get('assets.admin.js') : [];

$flextype['registry']->set('assets.admin.css',
                           array_merge($_admin_css,
                           ['project/plugins/admin/assets/dist/css/admin-vendor-build.min.css',
                            'project/plugins/admin/assets/dist/css/admin-build.min.css']));

$flextype['registry']->set('assets.admin.js',
                       array_merge($_admin_js,
                       ['project/plugins/admin/assets/dist/js/admin-vendor-build.min.js']));

$flextype['DashboardController'] = static function ($container) {
    return new DashboardController($container);
};

$flextype['SettingsController'] = static function ($container) {
    return new SettingsController($container);
};

$flextype['PluginsController'] = static function ($container) {
    return new PluginsController($container);
};

$flextype['EntriesController'] = static function ($container) {
    return new EntriesController($container);
};

$flextype['ToolsController'] = static function ($container) {
    return new ToolsController($container);
};

$flextype['ApiController'] = static function ($container) {
    return new ApiController($container);
};

$flextype['ApiEntriesController'] = static function ($container) {
    return new ApiEntriesController($container);
};

$flextype['ApiImagesController'] = static function ($container) {
    return new ApiImagesController($container);
};

$flextype['ApiAccessController'] = static function ($container) {
    return new ApiAccessController($container);
};

$flextype['ApiRegistryController'] = static function ($container) {
    return new ApiRegistryController($container);
};
