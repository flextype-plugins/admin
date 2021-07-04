<?php

declare(strict_types=1);

namespace Flextype\Plugin\Admin\Controllers;

use Flextype\Component\Arrays\Arrays;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function array_merge;
use function array_replace_recursive;
use function Flextype\Component\I18n\__;
use function trim;

class PluginsController
{
    /**
     * __construct()
     */
    public function __construct()
    {
        flextype('registry')->set('workspace', ['icon' => ['name' => 'box', 'set' => 'bootstrap']]);
    }

    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response): Response
    {
        $pluginsList = flextype('registry')->get('plugins');

        ksort($pluginsList);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/extends/plugins/index.html',
            [
                'pluginsList' => $pluginsList,
                'query' => $request->getQueryParams(),
                'menu_item' => 'plugins',
                'links' =>  [
                    'plugins' => [
                        'link' => flextype('router')->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                        'active' => true
                    ],
                ]
            ]
        );
    }

    /**
     * Сhange plugin status process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function pluginStatusProcess(Request $request, Response $response): Response
    {
        // Get data from the request
        $data = $request->getParsedBody();

        $customPluginSettingsFile = PATH['project'] . '/config/plugins/' . $data['plugin-key'] . '/settings.yaml';
        $customPluginSettingsFileContent = filesystem()->file($customPluginSettingsFile)->get();
        $customPluginSettingsFileData = empty($customPluginSettingsFileContent) ? [] : flextype('serializers')->yaml()->decode($customPluginSettingsFileContent);

        $status = ($data['plugin-set-status'] == 'true') ? true : false;

        $customPluginSettingsFileData = arrays($customPluginSettingsFileData)->set('enabled', $status)->toArray();

        filesystem()->file($customPluginSettingsFile)->put(flextype('serializers')->yaml()->encode($customPluginSettingsFileData));

        // clear cache
        filesystem()->directory(PATH['tmp'] . '/data')->delete(true);

        // Redirect to plugins index page
        return $response->withRedirect(flextype('router')->pathFor('admin.plugins.index'));
    }

    /**
     * Plugin information
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function information(Request $request, Response $response): Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set entry ID
        $query['id'] ??= '';
        
        // Get manifest
        $manifest = flextype('serializers')->yaml()->decode(filesystem()->file(PATH['project'] . '/plugins/' . $query['id'] . '/plugin.yaml')->get());

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/extends/plugins/information.html',
            [
                'menu_item' => 'plugins',
                'id' => $query['id'],
                'manifest' => $manifest,
                'links' =>  [
                    'plugins' => [
                        'link' => flextype('router')->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                    ],
                    'plugins_name' => [
                        'link' => flextype('router')->pathFor('admin.plugins.information') . '?id=' . $query['id'],
                        'title' => $manifest['name'],
                    ],
                ],
            ]
        );
    }

    /**
     * Plugin settings
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function settings(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();

        $customPluginSettingsFile = PATH['project'] . '/config/plugins/' . $query['id'] . '/settings.yaml';
        $customPluginSettingsFileContent = Filesystem::read($customPluginSettingsFile);
        $customPluginManifestFile = PATH['project'] . '/plugins/' . $query['id'] . '/plugin.yaml';
        $customPluginManifestFileContent = Filesystem::read($customPluginManifestFile);

        $pluginsManifest = flextype('serializers')->yaml()->decode($customPluginManifestFileContent);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/extends/plugins/settings.html',
            [
                'menu_item' => 'plugins',
                'id' => $query['id'],
                'settings' => $customPluginSettingsFileContent,
                'links' =>  [
                    'plugins' => [
                        'link' => flextype('router')->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                    ],
                    'plugins_settings' => [
                        'link' => flextype('router')->pathFor('admin.plugins.settings') . '?id=' . $query['id'],
                        'title' => $pluginsManifest['name']
                    ],
                ]
            ]
        );
    }

    /**
     * Plugin settings process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function settingsProcess(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $customPluginSettingsFile = PATH['project'] . '/config/plugins/' . $data['id'] . '/settings.yaml';

        if (filesystem()->file($customPluginSettingsFile)->put($data['data'])) {
            flextype('flash')->addMessage('success', __('admin_message_plugin_settings_saved'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_plugin_settings_not_saved'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.plugins.settings') . '?id=' . $data['id']);
    }
}
