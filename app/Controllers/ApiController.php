<?php

declare(strict_types=1);

namespace Flextype\Plugin\Admin\Controllers;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use function bin2hex;
use function date;
use function Flextype\Component\I18n\__;
use function random_bytes;
use function time;


class ApiController
{
    /**
     * __construct
     */
     public function __construct()
     {

     }

    /**
     * Index page for API's
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response) : Response
    {
        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/api/index.html',
            [
                'menu_item' => 'api',
                'api_list' => [
                                'entries' => [
                                  'title' => __('admin_entries'),
                                  'icon' => 'fas fa-database'
                                ],
                                'registry' => [
                                  'title' => __('admin_registry'),
                                  'icon' => 'fas fa-archive'
                                ],
                                'images' => [
                                  'title' => __('admin_images'),
                                  'icon' => 'far fa-images'
                                ],
                                'files' => [
                                  'title' => __('admin_files'),
                                  'icon' => 'fas fa-file'
                                ],
                                'folders' => [
                                  'title' => __('admin_folders'),
                                  'icon' => 'fas fa-folder'
                                ],
                                'access' => [
                                  'title' => __('admin_access'),
                                  'icon' => 'fas fa-user-shield'
                                ],
                               ],
                'links' =>  [
                    'api' => [
                        'link' => flextype('router')->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                        'active' => true,
                    ],
                ],
            ]
        );
    }
}
