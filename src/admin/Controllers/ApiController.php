<?php

declare(strict_types=1);

namespace Flextype\Plugin\Admin\Controllers;

use Flextype\Component\Filesystem\Filesystem;

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
     * __construct()
     */
    public function __construct()
    {
        registry()->set('workspace', ['icon' => ['name' => 'diagram-3', 'set' => 'bootstrap']]);
    }

    /**
     * Index page for API's
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response): Response
    {
        $api_stats = ['entries' => $this->getStats('entries'),
                      'registry' => $this->getStats('registry'),
                      'images' => $this->getStats('images'),
                      'files' => $this->getStats('files'),
                      'folders' => $this->getStats('folders'),
                      'access' => $this->getStats('access')];

        return twig()->render(
            $response,
            'plugins/admin/templates/system/api/index.html',
            [
                'api_stats' => $api_stats,
                'menu_item' => 'api',
                'api_list' => [
                                'entries' => [
                                  'title' => __('admin_entries'),
                                  'icon' => ['name' => 'newspaper', 'set' => 'bootstrap'],
                                ],
                                'registry' => [
                                  'title' => __('admin_registry'),
                                  'icon' => ['name' => 'archive', 'set' => 'bootstrap'],
                                ],
                                'images' => [
                                  'title' => __('admin_images'),
                                  'icon' => ['name' => 'images', 'set' => 'bootstrap'],
                                ],
                                'media' => [
                                  'title' => __('admin_media'),
                                  'icon' => ['name' => 'collection', 'set' => 'bootstrap'],
                                ],
                                'access' => [
                                  'title' => __('admin_access'),
                                  'icon' => ['name' => 'key', 'set' => 'bootstrap'],
                                ],
                             ],
                'links' =>  [
                    'api' => [
                        'link' => router()->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                        'active' => true,
                    ],
                ],
            ]
        );
    }

    public function deleteApiTokensProcess(Request $request, Response $response): Response
    {
        // Get POST data
        $data = $request->getParsedBody();

        $apiTokenDirPath = PATH['project'] . '/tokens/' . $data['token'];

        if (filesystem()->directory($apiTokenDirPath)->delete()) {
            if (filesystem()->directory($apiTokenDirPath)->create()) {
                container()->get('flash')->addMessage('success', __('admin_message_api_tokens_deleted'));
            } else {
                container()->get('flash')->addMessage('error', __('admin_message_api_tokens_was_not_deleted'));
            }
        } else {
            container()->get('flash')->addMessage('error', __('admin_message_api_tokens_was_not_deleted'));
        }

        return $response->withRedirect(router()->pathFor('admin.api.index'));
    }

    /**
     * Entries Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function tokens(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();

        $tokens = [];
        $tokens_list = Filesystem::listContents(PATH['project'] . '/tokens/' . $query['api']);

        if (count($tokens_list) > 0) {
            foreach ($tokens_list as $token) {
                if ($token['type'] == 'dir' && Filesystem::has(PATH['project'] . '/tokens/' . $query['api'] . '/' . $token['dirname'] . '/token.yaml')) {
                    $tokens[] = $token;
                }
            }
        }

        return twig()->render(
            $response,
            'plugins/admin/templates/system/api/tokens.html',
            [
                'menu_item' => 'api',
                'query' => $query,
                'icons' => ['entries' => ['name' => 'newspaper', 'set' => 'bootstrap'],
                            'registry' => ['name' => 'archive', 'set' => 'bootstrap'],
                            'images' => ['name' => 'images', 'set' => 'bootstrap'],
                            'files' =>  ['name' => 'file-text', 'set' => 'bootstrap'],
                            'folders' => ['name' => 'folder', 'set' => 'bootstrap'],
                            'access' => ['name' => 'people', 'set' => 'bootstrap'],
                            ],
                'tokens' => $tokens,
                'links' =>  [
                    'api' => [
                        'link' => router()->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                    ],
                    'api_tokens' => [
                        'link' => router()->pathFor('admin.api.tokens') . '?api=' . $query['api'],
                        'title' => __('admin_' . $query['api'])
                    ],
                ]
            ]
        );
    }

    /**
     * Add token page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function add(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();

        return twig()->render(
            $response,
            'plugins/admin/templates/system/api/add.html',
            [
                'menu_item' => 'api',
                'query' => $query,
                'uuid' => Uuid::uuid4()->toString(),
                'time' => date(registry()->get('flextype.settings.date_format'), time()),
                'links' =>  [
                    'api' => [
                        'link' => router()->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                    ],
                    'api_tokens' => [
                        'link' => router()->pathFor('admin.api.tokens') . '?api=' . $query['api'],
                        'title' => __('admin_' . $query['api'])
                    ],
                ]
            ]
        );
    }

    /**
     * Add new token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function addProcess(Request $request, Response $response): Response
    {
        // Get data from POST
        $data = $request->getParsedBody();

        // Process form
        $form = blueprints()->form($data)->process();

        // Generate API token
        $APIToken = bin2hex(random_bytes(16));

        $apiTokenDirPath  = PATH['project'] . '/tokens/' . $form->get('fields.api') . '/' . $APIToken;
        $apiTokenFilePath = $apiTokenDirPath . '/token.yaml';

        if (!filesystem()->file($apiTokenFilePath)->exists()) {
            $result = filesystem()->directory($apiTokenDirPath)->create(0755, true);
            $result = filesystem()->file($apiTokenFilePath)->put(serializers()->yaml()->encode($form->copy()->delete('fields.api')->get('fields')));

            if ($result) {
                container()->get('flash')->addMessage('success', $form->get('messages.success'));
            } else {
                container()->get('flash')->addMessage('error', $form->get('messages.error'));
            }
        } else {
            container()->get('flash')->addMessage('error', $form->get('messages.error'));
        }

        return $response->withRedirect($form->get('redirect'));
    }

    /**
     * Edit token page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function edit(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();

        $token      = $query['token'];
        $tokenData  = serializers()->yaml()->decode(Filesystem::read(PATH['project'] . '/tokens/' . $query['api'] . '/' . $token . '/token.yaml'));

        return twig()->render(
            $response,
            'plugins/admin/templates/system/api/edit.html',
            [
                'menu_item' => 'api',
                'token' => $token,
                'tokenData' => $tokenData,
                'query' => $query,
                'time' => date(registry()->get('flextype.settings.date_format'), time()),
                'links' =>  [
                    'api' => [
                        'link' => router()->pathFor('admin.api.index'),
                        'title' => __('admin_api')
                    ],
                    'api_tokens' => [
                        'link' => router()->pathFor('admin.api.tokens') . '?api=' . $query['api'],
                        'title' => __('admin_' . $query['api'])
                    ],
                ]
            ]
        );
    }

    /**
     * Edit token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function editProcess(Request $request, Response $response): Response
    {
        // Get data from POST
        $data = $request->getParsedBody();

        // Process form
        $form = blueprints()->form($data)->process();

        $apiTokenFilePath = PATH['project'] . '/tokens/'. $form->get('fields.api') .'/' . $form->get('fields.token') . '/' . 'token.yaml';

        if (filesystem()->file($apiTokenFilePath)->exists()) {
          
            $tokenData  = serializers()->yaml()->decode(Filesystem::read(PATH['project'] . '/tokens/' . $form->get('fields.api') . '/' . $form->get('fields.token') . '/token.yaml'));

            $result = filesystem()->file($apiTokenFilePath)->put(serializers()->yaml()->encode(array_merge($tokenData, $form->copy()->delete('fields.api')->delete('fields.token')->get('fields'))));

            if ($result) {
                container()->get('flash')->addMessage('success', $form->get('messages.success'));
            } else {
                container()->get('flash')->addMessage('error', $form->get('messages.error'));
            }
        } else {
            container()->get('flash')->addMessage('error', $form->get('messages.error'));
        }

        return $response->withRedirect($form->get('redirect'));
    }

    /**
     * Delete token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function deleteProcess(Request $request, Response $response): Response
    {
        // Get POST data
        $data = $request->getParsedBody();

        $apiTokenDirPath = PATH['project'] . '/tokens/' . $data['api'] . '/' . $data['token'];

        if (Filesystem::deleteDir($apiTokenDirPath)) {
            container()->get('flash')->addMessage('success', __('admin_message_'. $data['api'] .'_api_token_deleted'));
        } else {
            container()->get('flash')->addMessage('error', __('admin_message_'. $data['api'] .'_api_token_was_not_deleted'));
        }

        return $response->withRedirect(router()->pathFor('admin.api.tokens') . '?api=' . $data['api']);
    }

    private function getStats(string $api) {
        $files = glob(PATH['project'] . '/tokens/' . $api . '/*/*.yaml');

        $i = 0;
        $data = [];

        foreach ($files as $file) {
            $calls = serializers()->yaml()->decode(filesystem()->file($file)->get())['calls'] ?? 0;
            $data['tokens'] = ++$i;
            $data['calls'] = $calls++;
        }

        return $data;
    }

}
