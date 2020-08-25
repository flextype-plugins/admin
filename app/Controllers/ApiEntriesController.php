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


class ApiEntriesController
{

    /**
     * __construct
     */
     public function __construct()
     {

     }

    /**
     * Entries Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response) : Response
    {
        $tokens = [];
        $tokens_list = Filesystem::listContents(PATH['project'] . '/tokens/entries/');

        if (count($tokens_list) > 0) {
            foreach ($tokens_list as $token) {
                if ($token['type'] == 'dir' && Filesystem::has(PATH['project'] . '/tokens/entries/' . $token['dirname'] . '/token.yaml')) {
                    $tokens[] = $token;
                }
            }
        }

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/api/entries/index.html',
            [
                'menu_item' => 'api',
                'tokens' => $tokens,
                'links' =>  [
                    'api' => [
                        'link' => flextype('router')->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                    ],
                    'api_entries' => [
                        'link' => flextype('router')->pathFor('admin.api_entries.index'),
                        'title' => __('admin_entries'),
                        'active' => true
                    ],
                ],
                'buttons' => [
                    'api_entries_add' => [
                        'link' => flextype('router')->pathFor('admin.api_entries.add'),
                        'title' => __('admin_create_new_token')
                    ],
                ],
            ]
        );
    }

    /**
     * Add token page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function add(Request $request, Response $response) : Response
    {
        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/api/entries/add.html',
            [
                'menu_item' => 'api',
                'links' =>  [
                    'api' => [
                        'link' => flextype('router')->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                    ],
                    'api_entries' => [
                        'link' => flextype('router')->pathFor('admin.api_entries.index'),
                        'title' => __('admin_entries')
                    ],
                    'api_entries_add' => [
                        'link' => flextype('router')->pathFor('admin.api_entries.add'),
                        'title' => __('admin_create_new_token'),
                        'active' => true
                    ],
                ],
            ]
        );
    }

    /**
     * Add new token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function addProcess(Request $request, Response $response) : Response
    {
        // Get POST data
        $post_data = $request->getParsedBody();

        // Generate API token
        $api_token = bin2hex(random_bytes(16));

        $api_token_dir_path  = PATH['project'] . '/tokens/entries/' . $api_token;
        $api_token_file_path = $api_token_dir_path . '/token.yaml';

        if (! Filesystem::has($api_token_file_path)) {

            Filesystem::createDir($api_token_dir_path);

            // Generate UUID
            $uuid = Uuid::uuid4()->toString();

            // Get time
            $time = date(flextype('registry')->get('flextype.settings.date_format'), time());

            // Create API Token account
            if (Filesystem::write(
                $api_token_file_path,
                flextype('yaml')->encode([
                    'title' => $post_data['title'],
                    'icon' => $post_data['icon'],
                    'limit_calls' => (int) $post_data['limit_calls'],
                    'calls' => (int) 0,
                    'state' => $post_data['state'],
                    'uuid' => $uuid,
                    'created_by' => Session::get('uuid'),
                    'created_at' => $time,
                    'updated_by' => Session::get('uuid'),
                    'updated_at' => $time,
                ])
            )) {
                flextype('flash')->addMessage('success', __('admin_message_entries_api_token_created'));
            } else {
                flextype('flash')->addMessage('error', __('admin_message_entries_api_token_was_not_created1'));
            }
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entries_api_token_was_not_created2'));
        }

        if (isset($post_data['create-and-edit'])) {
            return $response->withRedirect(flextype('router')->pathFor('admin.api_entries.edit') . '?token=' . $api_token);
        } else {
            return $response->withRedirect(flextype('router')->pathFor('admin.api_entries.index'));
        }
    }

    /**
     * Edit token page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function edit(Request $request, Response $response) : Response
    {
        $token      = $request->getQueryParams()['token'];
        $token_data = flextype('yaml')->decode(Filesystem::read(PATH['project'] . '/tokens/entries/' . $token . '/token.yaml'));

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/system/api/entries/edit.html',
            [
                'menu_item' => 'api',
                'token' => $token,
                'token_data' => $token_data,
                'links' =>  [
                    'api' => [
                        'link' => flextype('router')->pathFor('admin.api.index'),
                        'title' => __('admin_api')
                    ],
                    'api_entries' => [
                        'link' => flextype('router')->pathFor('admin.api_entries.index'),
                        'title' => __('admin_entries')
                    ],
                    'api_tokens_edit' => [
                        'link' => flextype('router')->pathFor('admin.api_entries.edit'),
                        'title' => __('admin_edit_token'),
                        'active' => true
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
    public function editProcess(Request $request, Response $response) : Response
    {
        // Get POST data
        $post_data = $request->getParsedBody();

        $api_token_dir_path  = PATH['project'] . '/tokens/entries/' . $post_data['token'];
        $api_token_file_path = $api_token_dir_path . '/' . 'token.yaml';

        // Update API Token File
        if (Filesystem::has($api_token_file_path)) {
            if (Filesystem::write(
                $api_token_file_path,
                flextype('yaml')->encode([
                    'title' => $post_data['title'],
                    'icon' => $post_data['icon'],
                    'limit_calls' => (int) $post_data['limit_calls'],
                    'calls' => (int) $post_data['calls'],
                    'state' => $post_data['state'],
                    'uuid' => $post_data['uuid'],
                    'created_by' => $post_data['created_by'],
                    'created_at' => $post_data['created_at'],
                    'updated_by' => Session::get('uuid'),
                    'updated_at' => date(flextype('registry')->get('flextype.settings.date_format'), time()),
                ])
            )) {
                flextype('flash')->addMessage('success', __('admin_message_entries_api_token_updated'));
            }
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entries_api_token_was_not_updated'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.api_entries.index'));
    }

    /**
     * Delete token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function deleteProcess(Request $request, Response $response) : Response
    {
        // Get POST data
        $post_data = $request->getParsedBody();

        $api_token_dir_path = PATH['project'] . '/tokens/entries/' . $post_data['token'];

        if (Filesystem::deleteDir($api_token_dir_path)) {
            flextype('flash')->addMessage('success', __('admin_message_entries_api_token_deleted'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entries_api_token_was_not_deleted'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.api_entries.index'));
    }
}
