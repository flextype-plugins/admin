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
use Flextype\App\Foundation\Container;

/**
 * @property View $view
 * @property Router $router
 * @property Cache $cache
 * @property Registry $registry
 */
class PluginsController extends Container
{
    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {

        $plugins_list = $this->registry->get('plugins');

        ksort($plugins_list);

        return $this->twig->render(
            $response,
            'plugins/admin/templates/extends/plugins/index.html',
            [
                'plugins_list' => $plugins_list,
                'menu_item' => 'plugins',
                'links' =>  [
                    'plugins' => [
                        'link' => $this->router->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                        'active' => true
                    ],
                ],
                'buttons' =>  [
                    'plugins_get_more' => [
                        'link' => 'https://github.com/flextype/plugins',
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
    public function pluginStatusProcess(Request $request, Response $response) : Response
    {
        // Get data from the request
        $post_data = $request->getParsedBody();

        $custom_plugin_settings_file = PATH['project'] . '/config/' . '/plugins/' . $post_data['plugin-key'] . '/settings.yaml';
        $custom_plugin_settings_file_data = $this->yaml->decode(Filesystem::read($custom_plugin_settings_file));

        $status = ($post_data['plugin-set-status'] == 'true') ? true : false;

        Arrays::set($custom_plugin_settings_file_data, 'enabled', $status);

        Filesystem::write($custom_plugin_settings_file, $this->yaml->encode($custom_plugin_settings_file_data));

        // Clear doctrine cache
        $this->cache->clear('doctrine');

        // Redirect to plugins index page
        return $response->withRedirect($this->router->pathFor('admin.plugins.index'));
    }

    /**
     * Plugin information
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function information(Request $request, Response $response) : Response
    {
        // Get Plugin ID
        $id = $request->getQueryParams()['id'];

        // Set plugin custom manifest content
        $custom_plugin_manifest_file = PATH['project'] . '/plugins/' . '/' . $id . '/plugin.yaml';

        // Get plugin custom manifest content
        $custom_plugin_manifest_file_content = Filesystem::read($custom_plugin_manifest_file);

        return $this->twig->render(
            $response,
            'plugins/admin/templates/extends/plugins/information.html',
            [
                'menu_item' => 'plugins',
                'id' => $id,
                'plugin_manifest' => $this->yaml->decode($custom_plugin_manifest_file_content),
                'links' =>  [
                    'plugins' => [
                        'link' => $this->router->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),

                    ],
                    'plugins_information' => [
                        'link' => $this->router->pathFor('admin.plugins.information') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_information'),
                        'active' => true
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
    public function settings(Request $request, Response $response) : Response
    {
        // Get Plugin ID
        $id = $request->getQueryParams()['id'];

        // Set plugin custom setting file
        $custom_plugin_settings_file = PATH['project'] . '/config/' . '/plugins/' . $id . '/settings.yaml';

        // Get plugin custom setting file content
        $custom_plugin_settings_file_content = Filesystem::read($custom_plugin_settings_file);

        return $this->twig->render(
            $response,
            'plugins/admin/templates/extends/plugins/settings.html',
            [
                'menu_item' => 'plugins',
                'id' => $id,
                'plugin_settings' => $custom_plugin_settings_file_content,
                'links' =>  [
                    'plugins' => [
                        'link' => $this->router->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                    ],
                    'plugins_settings' => [
                        'link' => $this->router->pathFor('admin.plugins.settings') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_settings'),
                        'active' => true
                    ],
                ],
                'buttons' => [
                    'save_plugin_settings' => [
                        'link' => 'javascript:;',
                        'title' => __('admin_save'),
                        'type' => 'action'
                    ],
                ],
            ]
        );
    }

    /**
     * Plugin settings process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function settingsProcess(Request $request, Response $response) : Response
    {
        $post_data = $request->getParsedBody();

        $id   = $post_data['id'];
        $data = $post_data['data'];

        $custom_plugin_settings_dir  = PATH['project'] . '/config/' . '/plugins/' . $id;
        $custom_plugin_settings_file = PATH['project'] . '/config/' . '/plugins/' . $id . '/settings.yaml';

        if (Filesystem::write($custom_plugin_settings_file, $data)) {
            $this->flash->addMessage('success', __('admin_message_plugin_settings_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_plugin_settings_not_saved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.plugins.settings') . '?id=' . $id);
    }
}
