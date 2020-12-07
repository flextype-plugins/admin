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
     * __construct
     */
     public function __construct()
     {

     }

    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response) : Response
    {
        return $response->withRedirect(flextype('router')->pathFor('admin.tools.information'));
    }

    /**
     * Information page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function information(Request $request, Response $response) : Response
    {
        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/tools/information.html',
            [
                'menu_item' => 'tools',
                'php_uname' => php_uname(),
                'webserver' => $_SERVER['SERVER_SOFTWARE'] ?? @getenv('SERVER_SOFTWARE'),
                'php_sapi_name' => php_sapi_name(),
                'links' =>  [
                    'information' => [
                        'link' => flextype('router')->pathFor('admin.tools.index'),
                        'title' => __('admin_information'),
                        'active' => true
                    ],
                    'cache' => [
                        'link' => flextype('router')->pathFor('admin.tools.cache'),
                        'title' => __('admin_cache'),

                    ],
                    'registry' => [
                        'link' => flextype('router')->pathFor('admin.tools.registry'),
                        'title' => __('admin_registry'),

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
    public function cache(Request $request, Response $response) : Response
    {
        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/tools/cache.html',
            [
                'menu_item' => 'tools',
                'data_size' => Number::byteFormat($this->getDirectorySize(PATH['tmp'] . '/data')),
                'glide_size' => Number::byteFormat($this->getDirectorySize(PATH['tmp'] . '/glide')),
                'twig_size' => Number::byteFormat($this->getDirectorySize(PATH['tmp'] . '/twig')),
                'preflight_size' => Number::byteFormat($this->getDirectorySize(PATH['tmp'] . '/preflight')),
                'links' =>  [
                    'information' => [
                        'link' => flextype('router')->pathFor('admin.tools.index'),
                        'title' => __('admin_information'),

                    ],
                    'cache' => [
                        'link' => flextype('router')->pathFor('admin.tools.cache'),
                        'title' => __('admin_cache'),
                        'active' => true
                    ],
                    'registry' => [
                        'link' => flextype('router')->pathFor('admin.tools.registry'),
                        'title' => __('admin_registry'),

                    ],
                ],
                'buttons' => [
                    'tools_clear_cache' => [
                        'type' => 'action',
                        'id' => 'clear-cache-all',
                        'link' => flextype('router')->pathFor('admin.tools.clearCacheAllProcess'),
                        'title' => __('admin_clear_cache_all'),
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
    public function registry(Request $request, Response $response) : Response
    {
        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/tools/registry.html',
            [
                'menu_item' => 'tools',
                'registry_dump' => $this->dotArray(flextype('registry')->all()),
                'links' =>  [
                    'information' => [
                        'link' => flextype('router')->pathFor('admin.tools.index'),
                        'title' => __('admin_information'),

                    ],
                    'cache' => [
                        'link' => flextype('router')->pathFor('admin.tools.cache'),
                        'title' => __('admin_cache'),

                    ],
                    'registry' => [
                        'link' => flextype('router')->pathFor('admin.tools.registry'),
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
    public function clearCacheProcess(Request $request, Response $response) : Response
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

        flextype('flash')->addMessage('success', __('admin_message_cache_files_deleted'));

        return $response->withRedirect(flextype('router')->pathFor('admin.tools.cache'));
    }

    /**
     * Clear all cache process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function clearCacheAllProcess(Request $request, Response $response) : Response
    {
        Filesystem::deleteDir(ROOT_DIR . '/var');

        flextype('flash')->addMessage('success', __('admin_message_cache_files_deleted'));

        return $response->withRedirect(flextype('router')->pathFor('admin.tools.cache'));
    }

    /**
     * _dotArray
     */
    private function dotArray($array, $prepend = '') : array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, $this->dotArray($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
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
