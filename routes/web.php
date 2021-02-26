<?php

declare(strict_types=1);

use Flextype\Middlewares\CsrfMiddleware;
use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInMiddleware;
use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInRolesInMiddleware;
use Flextype\Plugin\Admin\Controllers\DashboardController;
use Flextype\Plugin\Admin\Controllers\SettingsController;
use Flextype\Plugin\Admin\Controllers\PluginsController;
use Flextype\Plugin\Admin\Controllers\EntriesController;
use Flextype\Plugin\Admin\Controllers\MediaController;
use Flextype\Plugin\Admin\Controllers\ToolsController;
use Flextype\Plugin\Admin\Controllers\ApiController;
use Flextype\Plugin\Admin\Controllers\ApiImagesController;
use Flextype\Plugin\Admin\Controllers\ApiRegistryController;
use Flextype\Plugin\Admin\Controllers\ApiEntriesController;
use Flextype\Plugin\Admin\Controllers\ApiFilesController;
use Flextype\Plugin\Admin\Controllers\ApiFoldersController;
use Flextype\Plugin\Admin\Controllers\ApiAccessController;

flextype()->group('/' . $admin_route, function () : void {
    // Dashboard
    flextype()->get('', DashboardController::class . ':index')->setName('admin.dashboard.index');

    // Entries Controller
    flextype()->get('/entries', EntriesController::class . ':index')->setName('admin.entries.index');
    flextype()->get('/entries/edit', EntriesController::class . ':edit')->setName('admin.entries.edit');
    flextype()->post('/entries/edit', EntriesController::class . ':editProcess')->setName('admin.entries.editProcess');
    flextype()->get('/entries/add', EntriesController::class . ':add')->setName('admin.entries.add');
    flextype()->post('/entries/add', EntriesController::class . ':addProcess')->setName('admin.entries.addProcess');
    flextype()->post('/entries/select-entry-type', EntriesController::class . ':selectEntryTypeProcess')->setName('admin.entries.selectEntryTypeProcess');
    flextype()->get('/entries/move', EntriesController::class . ':move')->setName('admin.entries.move');
    flextype()->post('/entries/move', EntriesController::class . ':moveProcess')->setName('admin.entries.moveProcess');
    flextype()->get('/entries/rename', EntriesController::class . ':rename')->setName('admin.entries.rename');
    flextype()->post('/entries/rename', EntriesController::class . ':renameProcess')->setName('admin.entries.renameProcess');
    flextype()->get('/entries/type', EntriesController::class . ':type')->setName('admin.entries.type');
    flextype()->post('/entries/type', EntriesController::class . ':typeProcess')->setName('admin.entries.typeProcess');
    flextype()->post('/entries/duplicate', EntriesController::class . ':duplicateProcess')->setName('admin.entries.duplicateProcess');
    flextype()->post('/entries/delete', EntriesController::class . ':deleteProcess')->setName('admin.entries.deleteProcess');
    flextype()->post('/entries/display-view-process', EntriesController::class . ':displayViewProcess')->setName('admin.entries.displayViewProcess');

    // Media Controller
    flextype()->get('/media', MediaController::class . ':index')->setName('admin.media.index');
    flextype()->get('/media/upload', MediaController::class . ':upload')->setName('admin.media.upload');
    flextype()->post('/media/upload', MediaController::class . ':uploadProcess')->setName('admin.media.uploadProcess');
    flextype()->post('/media/delete-file', MediaController::class . ':deleteFileProcess')->setName('admin.media.deleteFileProcess');
    flextype()->post('/media/delete-folder', MediaController::class . ':deleteFolderProcess')->setName('admin.media.deleteFolderProcess');
    flextype()->get('/media/create-directory', MediaController::class . ':createDirectory')->setName('admin.media.createDirectory');
    flextype()->post('/media/create-directory', MediaController::class . ':createDirectoryProcess')->setName('admin.media.createDirectoryProcess');
    flextype()->get('/media/edit', MediaController::class . ':edit')->setName('admin.media.edit');
    flextype()->post('/media/edit', MediaController::class . ':editProcess')->setName('admin.media.editProcess');

    // Settings Controller
    flextype()->get('/settings', SettingsController::class . ':index')->setName('admin.settings.index');
    flextype()->post('/settings', SettingsController::class . ':updateSettingsProcess')->setName('admin.settings.update');

    // Plugins Controller
    flextype()->get('/plugins', PluginsController::class . ':index')->setName('admin.plugins.index');
    flextype()->get('/plugins/information', PluginsController::class . ':information')->setName('admin.plugins.information');
    flextype()->get('/plugins/settings', PluginsController::class . ':settings')->setName('admin.plugins.settings');
    flextype()->post('/plugins/settings', PluginsController::class . ':settingsProcess')->setName('admin.plugins.settingsProcess');
    flextype()->post('/plugins/update-status', PluginsController::class . ':pluginStatusProcess')->setName('admin.plugins.update-status');

    // Tools Controller
    flextype()->get('/tools', ToolsController::class . ':index')->setName('admin.tools.index');
    flextype()->get('/tools/information', ToolsController::class . ':information')->setName('admin.tools.information');
    flextype()->get('/tools/registry', ToolsController::class . ':registry')->setName('admin.tools.registry');
    flextype()->get('/tools/reports', ToolsController::class . ':reports')->setName('admin.tools.reports');
    flextype()->get('/tools/cache', ToolsController::class . ':cache')->setName('admin.tools.cache');
    flextype()->post('/tools/cache-clear', ToolsController::class . ':clearCacheProcess')->setName('admin.tools.clearCacheProcess');
    flextype()->post('/tools/cache-clear-key', ToolsController::class . ':clearCacheKeyProcess')->setName('admin.tools.clearCacheKeyProcess');

    // Api Controller
    flextype()->get('/api', ApiController::class . ':index')->setName('admin.api.index');
    flextype()->post('/api/delete-api-tokens', ApiController::class . ':deleteApiTokensProcess')->setName('admin.api.deleteApiTokensProcess');

    flextype()->get('/api/tokens', ApiController::class . ':tokens')->setName('admin.api.tokens');
    flextype()->get('/api/tokens/add', ApiController::class . ':add')->setName('admin.api.add');
    flextype()->post('/api/tokens/add', ApiController::class . ':addProcess')->setName('admin.api.addProcess');
    flextype()->get('/api/tokens/edit', ApiController::class . ':edit')->setName('admin.api.edit');
    flextype()->post('/api/tokens/edit', ApiController::class . ':editProcess')->setName('admin.api.editProcess');
    flextype()->post('/api/tokens/delete', ApiController::class . ':deleteProcess')->setName('admin.api.deleteProcess');

})->add(new AclIsUserLoggedInMiddleware(['redirect' => 'admin.accounts.login']))
  ->add(new AclIsUserLoggedInRolesInMiddleware(['redirect' => (flextype('acl')->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                'roles' => 'admin']))
  ->add(new CsrfMiddleware());
