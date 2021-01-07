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


// Set Default Admin locale
I18n::$locale = flextype('registry')->get('flextype.settings.locale');

// Add Admin Navigation
flextype('registry')->set('plugins.admin.settings.navigation.content.entries', ['title' => __('admin_entries'), 'icon' => ['name' => 'database', 'set' => 'fontawesome|solid'], 'link' => flextype('router')->pathFor('admin.entries.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.extends.plugins', ['title' => __('admin_plugins'),'icon' => ['name' => 'plug', 'set' => 'fontawesome|solid'], 'link' => flextype('router')->pathFor('admin.plugins.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.system.tools', ['title' => __('admin_tools'),'icon' => ['name' => 'toolbox', 'set' => 'fontawesome|solid'], 'link' => flextype('router')->pathFor('admin.tools.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.system.api', ['title' => __('admin_api'),'icon' => ['name' => 'network-wired', 'set' => 'fontawesome|solid'], 'link' => flextype('router')->pathFor('admin.api.index')]);

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
