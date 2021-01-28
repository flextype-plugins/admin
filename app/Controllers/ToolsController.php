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
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response) : Response
    {
        flextype('registry')->set('workspace', ['icon' => ['name' => 'briefcase', 'set' => 'bootstrap']]);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/tools/index.html',
            [
                'menu_item' => 'tools',
                'links' =>  [
                    'tools' => [
                        'link' => flextype('router')->pathFor('admin.tools.index'),
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
    public function information(Request $request, Response $response) : Response
    {
        flextype('registry')->set('workspace', ['icon' => ['name' => 'briefcase', 'set' => 'bootstrap']]);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/tools/information.html',
            [
                'menu_item' => 'tools',
                'php_uname' => php_uname(),
                'webserver' => $_SERVER['SERVER_SOFTWARE'] ?? @getenv('SERVER_SOFTWARE'),
                'php_sapi_name' => php_sapi_name(),
                'links' =>  [
                    'tools' => [
                        'link' => flextype('router')->pathFor('admin.tools.index'),
                        'title' => __('admin_tools')
                    ],
                    'information' => [
                        'link' => flextype('router')->pathFor('admin.tools.information'),
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
    public function cache(Request $request, Response $response) : Response
    {
        flextype('registry')->set('workspace', ['icon' => ['name' => 'briefcase', 'set' => 'bootstrap']]);

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
                    'tools' => [
                        'link' => flextype('router')->pathFor('admin.tools.index'),
                        'title' => __('admin_tools'),

                    ],
                    'cache' => [
                        'link' => flextype('router')->pathFor('admin.tools.cache'),
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
    public function registry(Request $request, Response $response) : Response
    {
        flextype('registry')->set('workspace', ['icon' => ['name' => 'briefcase', 'set' => 'bootstrap']]);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/tools/registry.html',
            [
                'menu_item' => 'tools',
                'registry_dump' => $this->dotArray(flextype('registry')->all()),
                'links' =>  [
                    'tools' => [
                        'link' => flextype('router')->pathFor('admin.tools.index'),
                        'title' => __('admin_tools'),

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
     * Reports page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function reports(Request $request, Response $response) : Response
    {
        flextype('registry')->set('workspace', ['icon' => ['name' => 'briefcase', 'set' => 'bootstrap']]);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/tools/reports.html',
            [
                'menu_item' => 'tools',
                'php_info' => 'PHP ' . phpversion() . '<br> Copyright (c) The PHP Group <br> Zend Engine v' . zend_version() . ', Copyright (c) Zend Technologies',
                'apache_modules' => apache_get_modules(),
                'links' =>  [
                    'tools' => [
                        'link' => flextype('router')->pathFor('admin.tools.index'),
                        'title' => __('admin_tools'),

                    ],
                    'reports' => [
                        'link' => flextype('router')->pathFor('admin.tools.reports'),
                        'title' => __('admin_reports')
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

        if ($id == 'all') {
            Filesystem::deleteDir(ROOT_DIR . '/var');
        }

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
