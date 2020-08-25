<?php

declare(strict_types=1);

namespace Flextype\Plugin\Admin\Controllers;

use DateTime;
use Flextype\Component\Arrays\Arrays;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function array_merge;
use function explode;
use function Flextype\Component\I18n\__;

class SettingsController
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
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/settings/index.html',
            [
                'data' => Filesystem::read(PATH['project'] . '/config/flextype/settings.yaml'),
                'menu_item' => 'settings',
                'links' => [
                    'settings' => [
                        'link' => flextype('router')->pathFor('admin.settings.index'),
                        'title' => __('admin_settings'),
                        'active' => true
                    ],
                ],
                'buttons'  => [
                    'save' => [
                        'link'       => 'javascript:;',
                        'title'      => __('admin_save'),
                        'type' => 'action'
                    ],
                ],
            ]
        );
    }

    /**
     * Update settings process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function updateSettingsProcess(Request $request, Response $response) : Response
    {
        $post_data = $request->getParsedBody();

        if (Filesystem::write(PATH['project'] . '/config/flextype/' . '/settings.yaml', $post_data['data'])) {
            flextype('flash')->addMessage('success', __('admin_message_settings_saved'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_settings_was_not_saved'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.settings.index'));
    }

    /**
     * Return date formats allowed
     *
     * @return array
     */
    public function dateFormats() : array
    {
        $now = new DateTime();

        return [
            'd-m-Y H:i' => $now->format('d-m-Y H:i'),
            'Y-m-d H:i' => $now->format('Y-m-d H:i'),
            'm/d/Y h:i a' => $now->format('m/d/Y h:i a'),
            'H:i d-m-Y' => $now->format('H:i d-m-Y'),
            'h:i a m/d/Y' => $now->format('h:i a m/d/Y'),
        ];
    }

    /**
     * Return display date formats allowed
     *
     * @return array
     */
    public function displayDateFormats() : array
    {
        $now = new DateTime();

        return [
            'F jS \\a\\t g:ia' => $now->format('F jS \\a\\t g:ia'),
            'l jS \\of F g:i A' => $now->format('l jS \\of F g:i A'),
            'D, d M Y G:i:s' => $now->format('m/d/Y h:i a'),
            'd-m-y G:i' => $now->format('d-m-y G:i'),
            'jS M Y' => $now->format('jS M Y'),
        ];
    }
}
