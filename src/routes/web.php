<?php

declare(strict_types=1);

use Flextype\Middlewares\CsrfMiddleware;
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
use Flextype\Plugin\Admin\Controllers\ApiContentController;
use Flextype\Plugin\Admin\Controllers\ApiFilesController;
use Flextype\Plugin\Admin\Controllers\ApiFoldersController;
use Flextype\Plugin\Admin\Controllers\ApiAccessController;
use Slim\Routing\RouteCollectorProxy;

app()->group('/' . $adminRoute, function (RouteCollectorProxy $group) : void {
    // Dashboard
    $group->get('', DashboardController::class . ':index')->setName('admin.dashboard.index');

    // Entries Controller
    $group->get('/entries', EntriesController::class . ':index')->setName('admin.entries.index');
    $group->get('/entries/edit', EntriesController::class . ':edit')->setName('admin.entries.edit');
    $group->post('/entries/edit', EntriesController::class . ':editProcess')->setName('admin.entries.editProcess');
    $group->get('/entries/add', EntriesController::class . ':add')->setName('admin.entries.add');
    $group->post('/entries/add', EntriesController::class . ':addProcess')->setName('admin.entries.addProcess');
    $group->get('/entries/move', EntriesController::class . ':move')->setName('admin.entries.move');
    $group->post('/entries/move', EntriesController::class . ':moveProcess')->setName('admin.entries.moveProcess');
    $group->get('/entries/rename', EntriesController::class . ':rename')->setName('admin.entries.rename');
    $group->post('/entries/rename', EntriesController::class . ':renameProcess')->setName('admin.entries.renameProcess');
    $group->post('/entries/duplicate', EntriesController::class . ':duplicateProcess')->setName('admin.entries.duplicateProcess');
    $group->post('/entries/delete', EntriesController::class . ':deleteProcess')->setName('admin.entries.deleteProcess');

    // Media Controller
    $group->get('/media', MediaController::class . ':index')->setName('admin.media.index');
    $group->get('/media/upload', MediaController::class . ':upload')->setName('admin.media.upload');
    $group->post('/media/upload', MediaController::class . ':uploadProcess')->setName('admin.media.uploadProcess');
    $group->post('/media/delete-file', MediaController::class . ':deleteFileProcess')->setName('admin.media.deleteFileProcess');
    $group->post('/media/delete-folder', MediaController::class . ':deleteFolderProcess')->setName('admin.media.deleteFolderProcess');
    $group->get('/media/create-directory', MediaController::class . ':createDirectory')->setName('admin.media.createDirectory');
    $group->post('/media/create-directory', MediaController::class . ':createDirectoryProcess')->setName('admin.media.createDirectoryProcess');
    $group->get('/media/edit', MediaController::class . ':edit')->setName('admin.media.edit');
    $group->post('/media/edit', MediaController::class . ':editProcess')->setName('admin.media.editProcess');

    // Settings Controller
    $group->get('/settings', SettingsController::class . ':index')->setName('admin.settings.index');
    $group->post('/settings', SettingsController::class . ':updateSettingsProcess')->setName('admin.settings.update');

    // Plugins Controller
    $group->get('/plugins', PluginsController::class . ':index')->setName('admin.plugins.index');
    $group->get('/plugins/information', PluginsController::class . ':information')->setName('admin.plugins.information');
    $group->get('/plugins/settings', PluginsController::class . ':settings')->setName('admin.plugins.settings');
    $group->post('/plugins/settings', PluginsController::class . ':settingsProcess')->setName('admin.plugins.settingsProcess');
    $group->post('/plugins/update-status', PluginsController::class . ':pluginStatusProcess')->setName('admin.plugins.update-status');

    // Tools Controller
    $group->get('/tools', ToolsController::class . ':index')->setName('admin.tools.index');
    $group->get('/tools/information', ToolsController::class . ':information')->setName('admin.tools.information');
    $group->get('/tools/registry', ToolsController::class . ':registry')->setName('admin.tools.registry');
    $group->get('/tools/reports', ToolsController::class . ':reports')->setName('admin.tools.reports');
    $group->get('/tools/cache', ToolsController::class . ':cache')->setName('admin.tools.cache');
    $group->post('/tools/cache-clear', ToolsController::class . ':clearCacheProcess')->setName('admin.tools.clearCacheProcess');
    $group->post('/tools/cache-clear-key', ToolsController::class . ':clearCacheKeyProcess')->setName('admin.tools.clearCacheKeyProcess');

    // Api Controller
    $group->get('/api', ApiController::class . ':index')->setName('admin.api.index');
    $group->post('/api/delete-api-tokens', ApiController::class . ':deleteApiTokensProcess')->setName('admin.api.deleteApiTokensProcess');

    $group->get('/api/tokens', ApiController::class . ':tokens')->setName('admin.api.tokens');
    $group->get('/api/tokens/add', ApiController::class . ':add')->setName('admin.api.add');
    $group->post('/api/tokens/add', ApiController::class . ':addProcess')->setName('admin.api.addProcess');
    $group->get('/api/tokens/edit', ApiController::class . ':edit')->setName('admin.api.edit');
    $group->post('/api/tokens/edit', ApiController::class . ':editProcess')->setName('admin.api.editProcess');
    $group->post('/api/tokens/delete', ApiController::class . ':deleteProcess')->setName('admin.api.deleteProcess');

})->add(new AclIsUserLoggedInMiddleware(['redirect' => 'admin.accounts.login']))
  ->add(new AclIsUserLoggedInRolesInMiddleware(['redirect' => (acl()->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                'roles' => 'admin']))
  ->add(new CsrfMiddleware());
