<?php

declare(strict_types=1);

use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInMiddleware;
use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInRolesInMiddleware;

flextype()->group('/' . $admin_route, function () : void {
    // Dashboard
    flextype()->get('', 'DashboardController:index')->setName('admin.dashboard.index');

    // EntriesController
    flextype()->get('/entries', 'EntriesController:index')->setName('admin.entries.index');
    flextype()->get('/entries/edit', 'EntriesController:edit')->setName('admin.entries.edit');
    flextype()->post('/entries/edit', 'EntriesController:editProcess')->setName('admin.entries.editProcess');
    flextype()->get('/entries/add', 'EntriesController:add')->setName('admin.entries.add');
    flextype()->post('/entries/add', 'EntriesController:addProcess')->setName('admin.entries.addProcess');
    flextype()->post('/entries/select-entry-type', 'EntriesController:selectEntryTypeProcess')->setName('admin.entries.selectEntryTypeProcess');
    flextype()->get('/entries/move', 'EntriesController:move')->setName('admin.entries.move');
    flextype()->post('/entries/move', 'EntriesController:moveProcess')->setName('admin.entries.moveProcess');
    flextype()->get('/entries/rename', 'EntriesController:rename')->setName('admin.entries.rename');
    flextype()->post('/entries/rename', 'EntriesController:renameProcess')->setName('admin.entries.renameProcess');
    flextype()->get('/entries/type', 'EntriesController:type')->setName('admin.entries.type');
    flextype()->post('/entries/type', 'EntriesController:typeProcess')->setName('admin.entries.typeProcess');
    flextype()->post('/entries/duplicate', 'EntriesController:duplicateProcess')->setName('admin.entries.duplicateProcess');
    flextype()->post('/entries/delete', 'EntriesController:deleteProcess')->setName('admin.entries.deleteProcess');
    flextype()->post('/entries/delete-media-file', 'EntriesController:deleteMediaFileProcess')->setName('admin.entries.deleteMediaFileProcess');
    flextype()->post('/entries/upload-media-file', 'EntriesController:uploadMediaFileProcess')->setName('admin.entries.uploadMediaFileProcess');
    flextype()->post('/entries/display-view-process', 'EntriesController:displayViewProcess')->setName('admin.entries.displayViewProcess');

    // Settings Controller
    flextype()->get('/settings', 'SettingsController:index')->setName('admin.settings.index');
    flextype()->post('/settings', 'SettingsController:updateSettingsProcess')->setName('admin.settings.update');

    // Plugins Controller
    flextype()->get('/plugins', 'PluginsController:index')->setName('admin.plugins.index');
    flextype()->get('/plugins/information', 'PluginsController:information')->setName('admin.plugins.information');
    flextype()->get('/plugins/settings', 'PluginsController:settings')->setName('admin.plugins.settings');
    flextype()->post('/plugins/settings', 'PluginsController:settingsProcess')->setName('admin.plugins.settingsProcess');
    flextype()->post('/plugins/update-status', 'PluginsController:pluginStatusProcess')->setName('admin.plugins.update-status');

    // ToolsController
    flextype()->get('/tools', 'ToolsController:index')->setName('admin.tools.index');
    flextype()->get('/tools/information', 'ToolsController:information')->setName('admin.tools.information');
    flextype()->get('/tools/registry', 'ToolsController:registry')->setName('admin.tools.registry');
    flextype()->get('/tools/cache', 'ToolsController:cache')->setName('admin.tools.cache');
    flextype()->post('/tools/cache', 'ToolsController:clearCacheProcess')->setName('admin.tools.clearCacheProcess');
    flextype()->post('/tools/cache-all', 'ToolsController:clearCacheAllProcess')->setName('admin.tools.clearCacheAllProcess');

    // ApiController
    flextype()->get('/api', 'ApiController:index')->setName('admin.api.index');

    flextype()->get('/api/entries', 'ApiEntriesController:index')->setName('admin.api_entries.index');
    flextype()->get('/api/entries/add', 'ApiEntriesController:add')->setName('admin.api_entries.add');
    flextype()->post('/api/entries/add', 'ApiEntriesController:addProcess')->setName('admin.api_entries.addProcess');
    flextype()->get('/api/entries/edit', 'ApiEntriesController:edit')->setName('admin.api_entries.edit');
    flextype()->post('/api/entries/edit', 'ApiEntriesController:editProcess')->setName('admin.api_entries.editProcess');
    flextype()->post('/api/entries/delete', 'ApiEntriesController:deleteProcess')->setName('admin.api_entries.deleteProcess');

    flextype()->get('/api/files', 'ApiFilesController:index')->setName('admin.api_files.index');
    flextype()->get('/api/files/add', 'ApiFilesController:add')->setName('admin.api_files.add');
    flextype()->post('/api/files/add', 'ApiFilesController:addProcess')->setName('admin.api_files.addProcess');
    flextype()->get('/api/files/edit', 'ApiFilesController:edit')->setName('admin.api_files.edit');
    flextype()->post('/api/files/edit', 'ApiFilesController:editProcess')->setName('admin.api_files.editProcess');
    flextype()->post('/api/files/delete', 'ApiFilesController:deleteProcess')->setName('admin.api_files.deleteProcess');

    flextype()->get('/api/folders', 'ApiFoldersController:index')->setName('admin.api_folders.index');
    flextype()->get('/api/folders/add', 'ApiFoldersController:add')->setName('admin.api_folders.add');
    flextype()->post('/api/folders/add', 'ApiFoldersController:addProcess')->setName('admin.api_folders.addProcess');
    flextype()->get('/api/folders/edit', 'ApiFoldersController:edit')->setName('admin.api_folders.edit');
    flextype()->post('/api/folders/edit', 'ApiFoldersController:editProcess')->setName('admin.api_folders.editProcess');
    flextype()->post('/api/folders/delete', 'ApiFoldersController:deleteProcess')->setName('admin.api_folders.deleteProcess');

    flextype()->get('/api/registry', 'ApiRegistryController:index')->setName('admin.api_registry.index');
    flextype()->get('/api/registry/add', 'ApiRegistryController:add')->setName('admin.api_registry.add');
    flextype()->post('/api/registry/add', 'ApiRegistryController:addProcess')->setName('admin.api_registry.addProcess');
    flextype()->get('/api/registry/edit', 'ApiRegistryController:edit')->setName('admin.api_registry.edit');
    flextype()->post('/api/registry/edit', 'ApiRegistryController:editProcess')->setName('admin.api_registry.editProcess');
    flextype()->post('/api/registry/delete', 'ApiRegistryController:deleteProcess')->setName('admin.api_registry.deleteProcess');

    flextype()->get('/api/images', 'ApiImagesController:index')->setName('admin.api_images.index');
    flextype()->get('/api/images/add', 'ApiImagesController:add')->setName('admin.api_images.add');
    flextype()->post('/api/images/add', 'ApiImagesController:addProcess')->setName('admin.api_images.addProcess');
    flextype()->get('/api/images/edit', 'ApiImagesController:edit')->setName('admin.api_images.edit');
    flextype()->post('/api/images/edit', 'ApiImagesController:editProcess')->setName('admin.api_images.editProcess');
    flextype()->post('/api/images/delete', 'ApiImagesController:deleteProcess')->setName('admin.api_images.deleteProcess');

    flextype()->get('/api/access', 'ApiAccessController:index')->setName('admin.api_access.index');
    flextype()->get('/api/access/add', 'ApiAccessController:add')->setName('admin.api_access.add');
    flextype()->post('/api/access/add', 'ApiAccessController:addProcess')->setName('admin.api_access.addProcess');
    flextype()->get('/api/access/edit', 'ApiAccessController:edit')->setName('admin.api_access.edit');
    flextype()->post('/api/access/edit', 'ApiAccessController:editProcess')->setName('admin.api_access.editProcess');
    flextype()->post('/api/access/delete', 'ApiAccessController:deleteProcess')->setName('admin.api_access.deleteProcess');

})->add(new AclIsUserLoggedInMiddleware(['redirect' => 'admin.accounts.login']))
  ->add(new AclIsUserLoggedInRolesInMiddleware(['redirect' => (flextype()->getContainer()->acl->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                      'roles' => 'admin']))
  ->add('csrf');
