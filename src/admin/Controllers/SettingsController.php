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
     * __construct()
     */
    public function __construct()
    {
        registry()->set('workspace', ['icon' => ['name' => 'gear', 'set' => 'bootstrap']]);
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
            'plugins/admin/templates/system/settings/index.html',
            [
                'settings' => filesystem()->file(PATH['project'] . '/config/flextype/settings.yaml')->get(),
                'menu_item' => 'settings',
                'links' => [
                    'settings' => [
                        'link' => router()->pathFor('admin.settings.index'),
                        'title' => __('admin_settings')
                    ],
                ]
            ]
        );
    }

    /**
     * Update settings process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function updateSettingsProcess(Request $request, Response $response): Response
    {
        // Process form
        $form = blueprints()->form($request->getParsedBody())->process();

        if (filesystem()->file(PATH['project'] . '/config/flextype/settings.yaml')->put($form['fields']['settings'])) {
            container()->get('flash')->addMessage('success', $form['messages']['success']);
        } else {
            container()->get('flash')->addMessage('error', $form['messages']['error']);
        }

        return $response->withRedirect($form['redirect']); 
    }

    /**
     * Return date formats allowed
     *
     * @return array
     */
    public function dateFormats(): array
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
    public function displayDateFormats(): array
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
