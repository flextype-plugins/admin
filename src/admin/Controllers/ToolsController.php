<?php

declare(strict_types=1);

namespace Flextype\Plugin\Admin\Controllers;

use FilesystemIterator;
use Flextype\Component\Number\Number;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function array_merge;
use function file_exists;
use function Flextype\Component\I18n\__;
use function getenv;
use function is_array;
use function php_sapi_name;
use function php_uname;
use function realpath;

class ToolsController
{
    /**
     * __construct()
     */
    public function __construct()
    {
        registry()->set('workspace', ['icon' => ['name' => 'briefcase', 'set' => 'bootstrap']]);
    }

    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response): Response
    {
        return twig()->render(
            $response,
            'plugins/admin/templates/system/tools/index.html',
            [
                'menu_item' => 'tools',
                'tools' => [
                    'information' => [
                        'title' => __('admin_information'),
                        'icon' => ['name' => 'info-circle', 'set' => 'bootstrap'],
                        'route' => 'admin.tools.information'
                      ],
                    'сache' => [
                        'title' => __('admin_сache'),
                        'icon' => ['name' => 'journal-richtext', 'set' => 'bootstrap'],
                        'route' => 'admin.tools.cache'
                    ],
                    'registry' => [
                        'title' => __('admin_registry'),
                        'icon' => ['name' => 'archive', 'set' => 'bootstrap'],
                        'route' => 'admin.tools.registry'
                    ],           
                ],
                'links' =>  [
                    'tools' => [
                        'link' => urlFor('admin.tools.index'),
                        'title' => __('admin_tools')
                    ],
                ],
            ]
        );
    }

    /**
     * Information page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function information(Request $request, Response $response): Response
    {
        return twig()->render(
            $response,
            'plugins/admin/templates/system/tools/information.html',
            [
                'menu_item' => 'tools',
                'php_uname' => php_uname(),
                'webserver' => $_SERVER['SERVER_SOFTWARE'] ?? @getenv('SERVER_SOFTWARE'),
                'php_sapi_name' => php_sapi_name(),
                'links' =>  [
                    'tools' => [
                        'link' => urlFor('admin.tools.index'),
                        'title' => __('admin_tools')
                    ],
                    'information' => [
                        'link' => urlFor('admin.tools.information'),
                        'title' => __('admin_information'),

                    ],
                ],
            ]
        );
    }

    /**
     * Cache page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function cache(Request $request, Response $response): Response
    {
        return twig()->render(
            $response,
            'plugins/admin/templates/system/tools/cache.html',
            [
                'menu_item' => 'tools',
                'data_size' => Number::byteFormat($this->getDirectorySize(PATH['tmp'] . '/data')),
                'glide_size' => Number::byteFormat($this->getDirectorySize(PATH['tmp'] . '/glide')),
                'twig_size' => Number::byteFormat($this->getDirectorySize(PATH['tmp'] . '/twig')),
                'preflight_size' => Number::byteFormat($this->getDirectorySize(PATH['tmp'] . '/preflight')),
                'links' =>  [
                    'tools' => [
                        'link' => urlFor('admin.tools.index'),
                        'title' => __('admin_tools'),

                    ],
                    'cache' => [
                        'link' => urlFor('admin.tools.cache'),
                        'title' => __('admin_cache'),
                        'active' => true
                    ],
                ],
            ]
        );
    }

    /**
     * Registry page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function registry(Request $request, Response $response): Response
    {
        return twig()->render(
            $response,
            'plugins/admin/templates/system/tools/registry.html',
            [
                'menu_item' => 'tools',
                'registryDump' => registry()->copy()->dot()->all(),
                'links' =>  [
                    'tools' => [
                        'link' => urlFor('admin.tools.index'),
                        'title' => __('admin_tools'),

                    ],
                    'registry' => [
                        'link' => urlFor('admin.tools.registry'),
                        'title' => __('admin_registry'),
                        'active' => true
                    ],
                ],
            ]
        );
    }

    /**
     * Clear cache process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function clearCacheProcess(Request $request, Response $response): Response
    {
        $id = $request->getParsedBody()['cache-id'];

        if ($id == 'data') {
            Filesystem::deleteDir(PATH['tmp'] . '/data');
        }

        if ($id == 'twig') {
            Filesystem::deleteDir(PATH['tmp'] . '/twig');
        }

        if ($id == 'glide') {
            Filesystem::deleteDir(PATH['tmp'] . '/glide');
        }

        if ($id == 'preflight') {
            Filesystem::deleteDir(PATH['tmp'] . '/preflight');
        }

        if ($id == 'all') {
            Filesystem::deleteDir(ROOT_DIR . '/var');
        }

        container()->get('flash')->addMessage('success', __('admin_message_cache_cleared'));

        return redirect('admin.tools.cache');
    }

    /**
     * Clear cache process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function clearCacheKeyProcess(Request $request, Response $response): Response
    {
        $key = strings($request->getParsedBody()['key'])->hash()->toString();

        if (cache()->has($key)) {
            if (cache()->delete($key)) {
                container()->get('flash')->addMessage('success', __('admin_message_cache_cleared'));
            } else {
                container()->get('flash')->addMessage('error', __('admin_message_cache_was_not_cleared'));
            }
        } else {
            container()->get('flash')->addMessage('error', __('admin_message_cache_was_not_cleared'));
        }

        return redirect('admin.tools.cache');
    }

    /**
     * _getDirectorySize
     */
    private function getDirectorySize($path)
    {
        $bytestotal = 0;
        $path       = realpath($path);
        if ($path!==false && $path!=='' && file_exists($path)) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
                $bytestotal += $object->getSize();
            }
        }

        return $bytestotal;
    }
}
