<?php

declare(strict_types=1);

use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInMiddleware;
use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInRolesInMiddleware;

$app->group('/' . $admin_route, function () use ($app, $flextype) : void {
    // Dashboard
    $app->get('', 'DashboardController:index')->setName('admin.dashboard.index');

    // EntriesController
    $app->get('/entries', 'EntriesController:index')->setName('admin.entries.index');
    $app->get('/entries/edit', 'EntriesController:edit')->setName('admin.entries.edit');
    $app->post('/entries/edit', 'EntriesController:editProcess')->setName('admin.entries.editProcess');
    $app->get('/entries/add', 'EntriesController:add')->setName('admin.entries.add');
    $app->post('/entries/add', 'EntriesController:addProcess')->setName('admin.entries.addProcess');
    $app->post('/entries/select-entry-type', 'EntriesController:selectEntryTypeProcess')->setName('admin.entries.selectEntryTypeProcess');
    $app->get('/entries/move', 'EntriesController:move')->setName('admin.entries.move');
    $app->post('/entries/move', 'EntriesController:moveProcess')->setName('admin.entries.moveProcess');
    $app->get('/entries/rename', 'EntriesController:rename')->setName('admin.entries.rename');
    $app->post('/entries/rename', 'EntriesController:renameProcess')->setName('admin.entries.renameProcess');
    $app->get('/entries/type', 'EntriesController:type')->setName('admin.entries.type');
    $app->post('/entries/type', 'EntriesController:typeProcess')->setName('admin.entries.typeProcess');
    $app->post('/entries/duplicate', 'EntriesController:duplicateProcess')->setName('admin.entries.duplicateProcess');
    $app->post('/entries/delete', 'EntriesController:deleteProcess')->setName('admin.entries.deleteProcess');
    $app->post('/entries/delete-media-file', 'EntriesController:deleteMediaFileProcess')->setName('admin.entries.deleteMediaFileProcess');
    $app->post('/entries/upload-media-file', 'EntriesController:uploadMediaFileProcess')->setName('admin.entries.uploadMediaFileProcess');
    $app->post('/entries/display-view-process', 'EntriesController:displayViewProcess')->setName('admin.entries.displayViewProcess');

    // Settings Controller
    $app->get('/settings', 'SettingsController:index')->setName('admin.settings.index');
    $app->post('/settings', 'SettingsController:updateSettingsProcess')->setName('admin.settings.update');

    // Plugins Controller
    $app->get('/plugins', 'PluginsController:index')->setName('admin.plugins.index');
    $app->get('/plugins/information', 'PluginsController:information')->setName('admin.plugins.information');
    $app->get('/plugins/settings', 'PluginsController:settings')->setName('admin.plugins.settings');
    $app->post('/plugins/settings', 'PluginsController:settingsProcess')->setName('admin.plugins.settingsProcess');
    $app->post('/plugins/update-status', 'PluginsController:pluginStatusProcess')->setName('admin.plugins.update-status');

    // ToolsController
    $app->get('/tools', 'ToolsController:index')->setName('admin.tools.index');
    $app->get('/tools/information', 'ToolsController:information')->setName('admin.tools.information');
    $app->get('/tools/registry', 'ToolsController:registry')->setName('admin.tools.registry');
    $app->get('/tools/cache', 'ToolsController:cache')->setName('admin.tools.cache');
    $app->post('/tools/cache', 'ToolsController:clearCacheProcess')->setName('admin.tools.clearCacheProcess');
    $app->post('/tools/cache-all', 'ToolsController:clearCacheAllProcess')->setName('admin.tools.clearCacheAllProcess');

    // ApiController
    $app->get('/api', 'ApiController:index')->setName('admin.api.index');

    $app->get('/api/entries', 'ApiEntriesController:index')->setName('admin.api_entries.index');
    $app->get('/api/entries/add', 'ApiEntriesController:add')->setName('admin.api_entries.add');
    $app->post('/api/entries/add', 'ApiEntriesController:addProcess')->setName('admin.api_entries.addProcess');
    $app->get('/api/entries/edit', 'ApiEntriesController:edit')->setName('admin.api_entries.edit');
    $app->post('/api/entries/edit', 'ApiEntriesController:editProcess')->setName('admin.api_entries.editProcess');
    $app->post('/api/entries/delete', 'ApiEntriesController:deleteProcess')->setName('admin.api_entries.deleteProcess');

    $app->get('/api/registry', 'ApiRegistryController:index')->setName('admin.api_registry.index');
    $app->get('/api/registry/add', 'ApiRegistryController:add')->setName('admin.api_registry.add');
    $app->post('/api/registry/add', 'ApiRegistryController:addProcess')->setName('admin.api_registry.addProcess');
    $app->get('/api/registry/edit', 'ApiRegistryController:edit')->setName('admin.api_registry.edit');
    $app->post('/api/registry/edit', 'ApiRegistryController:editProcess')->setName('admin.api_registry.editProcess');
    $app->post('/api/registry/delete', 'ApiRegistryController:deleteProcess')->setName('admin.api_registry.deleteProcess');

    $app->get('/api/images', 'ApiImagesController:index')->setName('admin.api_images.index');
    $app->get('/api/images/add', 'ApiImagesController:add')->setName('admin.api_images.add');
    $app->post('/api/images/add', 'ApiImagesController:addProcess')->setName('admin.api_images.addProcess');
    $app->get('/api/images/edit', 'ApiImagesController:edit')->setName('admin.api_images.edit');
    $app->post('/api/images/edit', 'ApiImagesController:editProcess')->setName('admin.api_images.editProcess');
    $app->post('/api/images/delete', 'ApiImagesController:deleteProcess')->setName('admin.api_images.deleteProcess');

    $app->get('/api/access', 'ApiAccessController:index')->setName('admin.api_access.index');
    $app->get('/api/access/add', 'ApiAccessController:add')->setName('admin.api_access.add');
    $app->post('/api/access/add', 'ApiAccessController:addProcess')->setName('admin.api_access.addProcess');
    $app->get('/api/access/edit', 'ApiAccessController:edit')->setName('admin.api_access.edit');
    $app->post('/api/access/edit', 'ApiAccessController:editProcess')->setName('admin.api_access.editProcess');
    $app->post('/api/access/delete', 'ApiAccessController:deleteProcess')->setName('admin.api_access.deleteProcess');

})->add(new AclIsUserLoggedInMiddleware(['container' => $flextype, 'redirect' => 'admin.accounts.login']))
  ->add(new AclIsUserLoggedInRolesInMiddleware(['container' => $flextype,
                                                'redirect' => ($flextype->acl->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                'roles' => 'admin']))
  ->add('csrf');
