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
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response): Response
    {
        flextype('registry')->set('workspace', ['icon' => ['name' => 'box', 'set' => 'bootstrap']]);

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
                ],
                'buttons' =>  [
                    'plugins_get_more' => [
                        'link' => 'https://flextype.org/en/downloads/extend/plugins',
                        'title' => __('admin_get_more_plugins'),
                        'target' => '_blank'
                    ],
                ],
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
        $post_data = $request->getParsedBody();

        $customPluginSettingsFile = PATH['project'] . '/config/plugins/' . $post_data['plugin-key'] . '/settings.yaml';
        $customPluginSettingsFileContent = Filesystem::read($customPluginSettingsFile);
        $customPluginSettingsFileData = empty($customPluginSettingsFileContent) ? [] : flextype('serializers')->yaml()->decode($customPluginSettingsFileContent);

        $status = ($post_data['plugin-set-status'] == 'true') ? true : false;

        Arrays::set($customPluginSettingsFileData, 'enabled', $status);

        Filesystem::write($customPluginSettingsFile, flextype('serializers')->yaml()->encode($customPluginSettingsFileData));

        // clear cache
        Filesystem::deleteDir(PATH['tmp'] . '/data');

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
        flextype('registry')->set('workspace', ['icon' => ['name' => 'box', 'set' => 'bootstrap']]);

        // Get Plugin ID
        $id = $request->getQueryParams()['id'];

        // Set plugin custom manifest content
        $customPluginManifestFile = PATH['project'] . '/plugins/' . '/' . $id . '/plugin.yaml';

        // Get plugin custom manifest content
        $customPluginManifestFileContent = Filesystem::read($customPluginManifestFile);

        $pluginsManifest = flextype('serializers')->yaml()->decode($customPluginManifestFileContent);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/extends/plugins/information.html',
            [
                'menu_item' => 'plugins',
                'id' => $id,
                'plugin_manifest' => $pluginsManifest,
                'links' =>  [
                    'plugins' => [
                        'link' => flextype('router')->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                    ],
                    'plugins_name' => [
                        'link' => flextype('router')->pathFor('admin.plugins.information') . '?id=' . $request->getQueryParams()['id'],
                        'title' => $pluginsManifest['name'],
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
        flextype('registry')->set('workspace', ['icon' => ['name' => 'box', 'set' => 'bootstrap']]);

        // Get Plugin ID
        $id = $request->getQueryParams()['id'];

        $customPluginSettingsFile = PATH['project'] . '/config/' . '/plugins/' . $id . '/settings.yaml';
        $customPluginSettingsFileContent = Filesystem::read($customPluginSettingsFile);
        $customPluginManifestFile = PATH['project'] . '/plugins/' . '/' . $id . '/plugin.yaml';
        $customPluginManifestFileContent = Filesystem::read($customPluginManifestFile);

        $pluginsManifest = flextype('serializers')->yaml()->decode($customPluginManifestFileContent);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/extends/plugins/settings.html',
            [
                'menu_item' => 'plugins',
                'id' => $id,
                'plugin_settings' => $customPluginSettingsFileContent,
                'links' =>  [
                    'plugins' => [
                        'link' => flextype('router')->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                    ],
                    'plugins_settings' => [
                        'link' => flextype('router')->pathFor('admin.plugins.settings') . '?id=' . $request->getQueryParams()['id'],
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
        $post_data = $request->getParsedBody();

        $id   = $post_data['id'];
        $data = $post_data['data'];

        $custom_plugin_settings_dir  = PATH['project'] . '/config/' . '/plugins/' . $id;
        $customPluginSettingsFile = PATH['project'] . '/config/' . '/plugins/' . $id . '/settings.yaml';

        if (Filesystem::write($customPluginSettingsFile, $data)) {
            flextype('flash')->addMessage('success', __('admin_message_plugin_settings_saved'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_plugin_settings_not_saved'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.plugins.settings') . '?id=' . $id);
    }
}
