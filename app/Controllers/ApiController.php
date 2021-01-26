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
     * Index page for API's
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response) : Response
    {
        flextype('registry')->set('workspace', ['icon' => ['name' => 'diagram-3', 'set' => 'bootstrap']]);

        $api_stats = ['entries' => $this->getStats('entries'),
                      'registry' => $this->getStats('registry'),
                      'images' => $this->getStats('images'),
                      'media' => $this->getStats('media'),
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

    public function deleteApiTokensProcess(Request $request, Response $response) : Response
    {
        // Get POST data
        $post_data = $request->getParsedBody();

        $api_token_dir_path = PATH['project'] . '/tokens/' . $post_data['token'];

        if (filesystem()->directory($api_token_dir_path)->delete()) {
            if (filesystem()->directory($api_token_dir_path)->create()) {
                flextype('flash')->addMessage('success', __('admin_message_api_tokens_deleted'));
            } else {
                flextype('flash')->addMessage('error', __('admin_message_api_tokens_was_not_deleted'));
            }
        } else {
            flextype('flash')->addMessage('error', __('admin_message_api_tokens_was_not_deleted'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.api.index'));
    }

    private function getStats(string $api) {
        $files = glob(PATH['project'] . '/tokens/' . $api . '/*/*.yaml');

        $i = 0;
        $data = [];

        foreach ($files as $file) {
            $data['tokens'] = ++$i;
            $data['calls'] = flextype('serializers')->yaml()->decode(filesystem()->file($file)->get())['calls']++;
        }

        return $data;
    }

}
