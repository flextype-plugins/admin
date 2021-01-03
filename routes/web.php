<?php

declare(strict_types=1);

use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInMiddleware;
use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInRolesInMiddleware;
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

flextype()->group('/' . $admin_route, function () : void {
    // Dashboard
    flextype()->get('', DashboardController::class . ':index')->setName('admin.dashboard.index');

    // EntriesController
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
    flextype()->post('/entries/delete-media-file', EntriesController::class . ':deleteMediaFileProcess')->setName('admin.entries.deleteMediaFileProcess');
    flextype()->post('/entries/upload-media-file', EntriesController::class . ':uploadMediaFileProcess')->setName('admin.entries.uploadMediaFileProcess');
    flextype()->post('/entries/display-view-process', EntriesController::class . ':displayViewProcess')->setName('admin.entries.displayViewProcess');

    // Settings Controller
    flextype()->get('/settings', SettingsController::class . ':index')->setName('admin.settings.index');
    flextype()->post('/settings', SettingsController::class . ':updateSettingsProcess')->setName('admin.settings.update');

    // Plugins Controller
    flextype()->get('/plugins', PluginsController::class . ':index')->setName('admin.plugins.index');
    flextype()->get('/plugins/information', PluginsController::class . ':information')->setName('admin.plugins.information');
    flextype()->get('/plugins/settings', PluginsController::class . ':settings')->setName('admin.plugins.settings');
    flextype()->post('/plugins/settings', PluginsController::class . ':settingsProcess')->setName('admin.plugins.settingsProcess');
    flextype()->post('/plugins/update-status', PluginsController::class . ':pluginStatusProcess')->setName('admin.plugins.update-status');

    // ToolsController
    flextype()->get('/tools', ToolsController::class . ':index')->setName('admin.tools.index');
    flextype()->get('/tools/information', ToolsController::class . ':information')->setName('admin.tools.information');
    flextype()->get('/tools/registry', ToolsController::class . ':registry')->setName('admin.tools.registry');
    flextype()->get('/tools/cache', ToolsController::class . ':cache')->setName('admin.tools.cache');
    flextype()->post('/tools/cache', ToolsController::class . ':clearCacheProcess')->setName('admin.tools.clearCacheProcess');
    flextype()->post('/tools/cache-all', ToolsController::class . ':clearCacheAllProcess')->setName('admin.tools.clearCacheAllProcess');

    // ApiController
    flextype()->get('/api', ApiController::class . ':index')->setName('admin.api.index');

    flextype()->get('/api/entries', ApiEntriesController::class . ':index')->setName('admin.api_entries.index');
    flextype()->get('/api/entries/add', ApiEntriesController::class . ':add')->setName('admin.api_entries.add');
    flextype()->post('/api/entries/add', ApiEntriesController::class . ':addProcess')->setName('admin.api_entries.addProcess');
    flextype()->get('/api/entries/edit', ApiEntriesController::class . ':edit')->setName('admin.api_entries.edit');
    flextype()->post('/api/entries/edit', ApiEntriesController::class . ':editProcess')->setName('admin.api_entries.editProcess');
    flextype()->post('/api/entries/delete', ApiEntriesController::class . ':deleteProcess')->setName('admin.api_entries.deleteProcess');

    flextype()->get('/api/files', ApiFilesController::class . ':index')->setName('admin.api_files.index');
    flextype()->get('/api/files/add', ApiFilesController::class . ':add')->setName('admin.api_files.add');
    flextype()->post('/api/files/add', ApiFilesController::class . ':addProcess')->setName('admin.api_files.addProcess');
    flextype()->get('/api/files/edit', ApiFilesController::class . ':edit')->setName('admin.api_files.edit');
    flextype()->post('/api/files/edit', ApiFilesController::class . ':editProcess')->setName('admin.api_files.editProcess');
    flextype()->post('/api/files/delete', ApiFilesController::class . ':deleteProcess')->setName('admin.api_files.deleteProcess');

    flextype()->get('/api/folders', ApiFoldersController::class . ':index')->setName('admin.api_folders.index');
    flextype()->get('/api/folders/add', ApiFoldersController::class . ':add')->setName('admin.api_folders.add');
    flextype()->post('/api/folders/add', ApiFoldersController::class . ':addProcess')->setName('admin.api_folders.addProcess');
    flextype()->get('/api/folders/edit', ApiFoldersController::class . ':edit')->setName('admin.api_folders.edit');
    flextype()->post('/api/folders/edit', ApiFoldersController::class . ':editProcess')->setName('admin.api_folders.editProcess');
    flextype()->post('/api/folders/delete', ApiFoldersController::class . ':deleteProcess')->setName('admin.api_folders.deleteProcess');

    flextype()->get('/api/registry', ApiRegistryController::class . ':index')->setName('admin.api_registry.index');
    flextype()->get('/api/registry/add', ApiRegistryController::class . ':add')->setName('admin.api_registry.add');
    flextype()->post('/api/registry/add', ApiRegistryController::class . ':addProcess')->setName('admin.api_registry.addProcess');
    flextype()->get('/api/registry/edit', ApiRegistryController::class . ':edit')->setName('admin.api_registry.edit');
    flextype()->post('/api/registry/edit', ApiRegistryController::class . ':editProcess')->setName('admin.api_registry.editProcess');
    flextype()->post('/api/registry/delete', ApiRegistryController::class . ':deleteProcess')->setName('admin.api_registry.deleteProcess');

    flextype()->get('/api/images', ApiImagesController::class . ':index')->setName('admin.api_images.index');
    flextype()->get('/api/images/add', ApiImagesController::class . ':add')->setName('admin.api_images.add');
    flextype()->post('/api/images/add', ApiImagesController::class . ':addProcess')->setName('admin.api_images.addProcess');
    flextype()->get('/api/images/edit', ApiImagesController::class . ':edit')->setName('admin.api_images.edit');
    flextype()->post('/api/images/edit', ApiImagesController::class . ':editProcess')->setName('admin.api_images.editProcess');
    flextype()->post('/api/images/delete', ApiImagesController::class . ':deleteProcess')->setName('admin.api_images.deleteProcess');

    flextype()->get('/api/access', ApiAccessController::class . ':index')->setName('admin.api_access.index');
    flextype()->get('/api/access/add', ApiAccessController::class . ':add')->setName('admin.api_access.add');
    flextype()->post('/api/access/add', ApiAccessController::class . ':addProcess')->setName('admin.api_access.addProcess');
    flextype()->get('/api/access/edit', ApiAccessController::class . ':edit')->setName('admin.api_access.edit');
    flextype()->post('/api/access/edit', ApiAccessController::class . ':editProcess')->setName('admin.api_access.editProcess');
    flextype()->post('/api/access/delete', ApiAccessController::class . ':deleteProcess')->setName('admin.api_access.deleteProcess');

})->add(new AclIsUserLoggedInMiddleware(['redirect' => 'admin.accounts.login']))
  ->add(new AclIsUserLoggedInRolesInMiddleware(['redirect' => (flextype('acl')->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                'roles' => 'admin']))
  ->add('csrf');
