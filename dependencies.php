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
flextype('registry')->set('plugins.admin.settings.navigation.primary.content.entries', ['title' => __('admin_entries'), 'icon' => ['name' => 'newspaper', 'set' => 'bootstrap'], 'link' => flextype('router')->pathFor('admin.entries.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.primary.content.media', ['title' => __('admin_media'), 'icon' => ['name' => 'images', 'set' => 'bootstrap'], 'link' => flextype('router')->pathFor('admin.media.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.primary.extends.plugins', ['title' => __('admin_plugins'), 'icon' => ['name' => 'box', 'set' => 'bootstrap'], 'link' => flextype('router')->pathFor('admin.plugins.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.primary.system.tools', ['title' => __('admin_tools'), 'icon' => ['name' => 'briefcase', 'set' => 'bootstrap'], 'link' => flextype('router')->pathFor('admin.tools.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.primary.system.settings', ['title' => __('admin_settings'), 'icon' => ['name' => 'gear', 'set' => 'bootstrap'], 'link' => flextype('router')->pathFor('admin.settings.index')]);
flextype('registry')->set('plugins.admin.settings.navigation.primary.system.api', ['title' => __('admin_api'), 'icon' => ['name' => 'diagram-3', 'set' => 'bootstrap'], 'link' => flextype('router')->pathFor('admin.api.index')]);

// Add Blueprint block `InputEditorTrumbowyg`
flextype('registry')->set('plugins.blueprints.settings.blocks.InputEditorTrumbowyg', 
                         ['name' => 'InputEditorTrumbowyg',
                           'src' => 'plugins/admin/blocks/InputEditorTrumbowyg/block.html']);

/**
 * Add Assets
 */
$_admin_css = (flextype('registry')->has('assets.admin.css')) ? flextype('registry')->get('assets.admin.css') : [];
$_admin_js  = (flextype('registry')->has('assets.admin.js')) ? flextype('registry')->get('assets.admin.js') : [];


flextype('registry')->set('assets.admin.css',
                           array_merge($_admin_css,
                           ['project/plugins/admin/assets/dist/css/admin.min.css']));

flextype('registry')->set('assets.admin.js',
                       array_merge($_admin_js,
                       ['project/plugins/admin/assets/dist/js/admin.min.js']));

