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
        flextype('registry')->set('workspace', ['icon' => ['name' => 'diagram-3', 'set' => 'bootstrap']]);
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

        return flextype('twig')->render(
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
                                'files' => [
                                  'title' => __('admin_files'),
                                  'icon' => ['name' => 'file-text', 'set' => 'bootstrap'],
                                ],
                                'folders' => [
                                  'title' => __('admin_folders'),
                                  'icon' => ['name' => 'folder', 'set' => 'bootstrap'],
                                ],
                                'access' => [
                                  'title' => __('admin_access'),
                                  'icon' => ['name' => 'people', 'set' => 'bootstrap'],
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

    public function deleteApiTokensProcess(Request $request, Response $response): Response
    {
        // Get POST data
        $data = $request->getParsedBody();

        $apiTokenDirPath = PATH['project'] . '/tokens/' . $data['token'];

        if (filesystem()->directory($apiTokenDirPath)->delete()) {
            if (filesystem()->directory($apiTokenDirPath)->create()) {
                flextype('flash')->addMessage('success', __('admin_message_api_tokens_deleted'));
            } else {
                flextype('flash')->addMessage('error', __('admin_message_api_tokens_was_not_deleted'));
            }
        } else {
            flextype('flash')->addMessage('error', __('admin_message_api_tokens_was_not_deleted'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.api.index'));
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

        return flextype('twig')->render(
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
                        'link' => flextype('router')->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                    ],
                    'api_tokens' => [
                        'link' => flextype('router')->pathFor('admin.api.tokens') . '?api=' . $query['api'],
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

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/api/add.html',
            [
                'menu_item' => 'api',
                'query' => $query,
                'uuid' => Uuid::uuid4()->toString(),
                'time' => date(flextype('registry')->get('flextype.settings.date_format'), time()),
                'links' =>  [
                    'api' => [
                        'link' => flextype('router')->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                    ],
                    'api_tokens' => [
                        'link' => flextype('router')->pathFor('admin.api.tokens') . '?api=' . $query['api'],
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
        $form = flextype('blueprints')->form($data)->process();

        // Generate API token
        $APIToken = bin2hex(random_bytes(16));

        $apiTokenDirPath  = PATH['project'] . '/tokens/' . $form['fields']['api'] . '/' . $APIToken;
        $apiTokenFilePath = $apiTokenDirPath . '/token.yaml';

        if (!filesystem()->file($apiTokenFilePath)->exists()) {
            $result = filesystem()->directory($apiTokenDirPath)->create(0755, true);
            $result = filesystem()->file($apiTokenFilePath)->put(flextype('serializers')->yaml()->encode($form->copy()->delete('fields.api')->toArray()));

            if ($result) {
                flextype('flash')->addMessage('success', $form['messages']['success']);
            } else {
                flextype('flash')->addMessage('error', $form['messages']['error']);
            }
        } else {
            flextype('flash')->addMessage('error', $form['messages']['error']);
        }

        return $response->withRedirect($form['redirect']);
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

        $token      = $request->getQueryParams()['token'];
        $tokenData  = flextype('serializers')->yaml()->decode(Filesystem::read(PATH['project'] . '/tokens/' . $query['api'] . '/' . $token . '/token.yaml'));

        flextype('registry')->set('workspace', ['icon' => ['name' => 'diagram-3', 'set' => 'bootstrap']]);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/api/edit.html',
            [
                'menu_item' => 'api',
                'token' => $token,
                'tokenData' => $tokenData,
                'query' => $query,
                'time' => date(flextype('registry')->get('flextype.settings.date_format'), time()),
                'links' =>  [
                    'api' => [
                        'link' => flextype('router')->pathFor('admin.api.index'),
                        'title' => __('admin_api')
                    ],
                    'api_tokens' => [
                        'link' => flextype('router')->pathFor('admin.api.tokens') . '?api=' . $query['api'],
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
        // Get POST data
        $data = $request->getParsedBody();

        $apiTokenDirPath  = PATH['project'] . '/tokens/'. $data['api'] .'/' . $data['token'];
        $apiTokenFilePath = $apiTokenDirPath . '/' . 'token.yaml';

        // Update API Token File
        if (Filesystem::has($apiTokenFilePath)) {
            if (Filesystem::write(
                $apiTokenFilePath,
                flextype('serializers')->yaml()->encode([
                    'title' => $data['title'],
                    'icon' => ['name' => $data['icon_name'],
                               'set' => $data['icon_set']],
                    'limit_calls' => (int) $data['limit_calls'],
                    'calls' => (int) $data['calls'],
                    'state' => $data['state'],
                    'uuid' => $data['uuid'],
                    'created_by' => $data['created_by'],
                    'created_at' => $data['created_at'],
                    'updated_by' => flextype('session')->get('uuid'),
                    'updated_at' => date(flextype('registry')->get('flextype.settings.date_format'), time()),
                ])
            )) {
                flextype('flash')->addMessage('success', __('admin_message_'. $data['api'] .'_api_token_updated'));
            }
        } else {
            flextype('flash')->addMessage('error', __('admin_message_'. $data['api'] .'_api_token_was_not_updated'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.api.tokens') . '?api=' . $data['api']);
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
            flextype('flash')->addMessage('success', __('admin_message_'. $data['api'] .'_api_token_deleted'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_'. $data['api'] .'_api_token_was_not_deleted'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.api.tokens') . '?api=' . $data['api']);
    }

    private function getStats(string $api) {
        $files = glob(PATH['project'] . '/tokens/' . $api . '/*/*.yaml');

        $i = 0;
        $data = [];

        foreach ($files as $file) {
            $calls = flextype('serializers')->yaml()->decode(filesystem()->file($file)->get())['calls'] ?? 0;
            $data['tokens'] = ++$i;
            $data['calls'] = $calls++;
        }

        return $data;
    }

}
