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
I18n::$locale = flextype('registry')->get('flextype.settings.locale');

// Add Admin Navigation
flextype('registry')->set('plugins.admin.settings.navigation.content.entries', ['title' => __('admin_entries'), 'icon' => 'fas fa-database', 'link' => flextype('router')->pathFor('admin.entries.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.extends.plugins', ['title' => __('admin_plugins'),'icon' => 'fas fa-plug', 'link' => flextype('router')->pathFor('admin.plugins.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.system.tools', ['title' => __('admin_tools'),'icon' => 'fas fa-toolbox', 'link' => flextype('router')->pathFor('admin.tools.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.system.api', ['title' => __('admin_api'),'icon' => 'fas fa-network-wired', 'link' => flextype('router')->pathFor('admin.api.index')]);

/**
 * Add Assets
 */
$_admin_css = (flextype('registry')->has('assets.admin.css')) ? flextype('registry')->get('assets.admin.css') : [];
$_admin_js  = (flextype('registry')->has('assets.admin.js')) ? flextype('registry')->get('assets.admin.js') : [];

flextype('registry')->set('assets.admin.css',
                           array_merge($_admin_css,
                           ['project/plugins/admin/assets/dist/css/admin-vendor-build.min.css',
                            'project/plugins/admin/assets/dist/css/admin-build.min.css']));

flextype('registry')->set('assets.admin.js',
                       array_merge($_admin_js,
                       ['project/plugins/admin/assets/dist/js/admin-vendor-build.min.js']));

flextype()->container()['DashboardController'] = fn() => new DashboardController();
flextype()->container()['SettingsController'] = fn() => new SettingsController();
flextype()->container()['PluginsController'] = fn() => new PluginsController();
flextype()->container()['EntriesController'] = fn() => new EntriesController();
flextype()->container()['ToolsController'] = fn() => new ToolsController();
flextype()->container()['ApiController'] = fn() => new ApiController();
flextype()->container()['ApiEntriesController'] = fn() => new ApiEntriesController();
flextype()->container()['ApiFilesController'] = fn() => new ApiFilesController();
flextype()->container()['ApiFoldersController'] = fn() => new ApiFoldersController();
flextype()->container()['ApiImagesController'] = fn() => new ApiImagesController();
flextype()->container()['ApiAccessController'] = fn() => new ApiAccessController();
flextype()->container()['ApiRegistryController'] = fn() => new ApiRegistryController();
