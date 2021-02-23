<?php

namespace Flextype\Plugin\Admin\Controllers;

use Flextype\Component\Filesystem\Filesystem;

use Flextype\Component\Arrays\Arrays;
use function Flextype\Component\I18n\__;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class MediaController
{

    /**
     * __construct()
     */
    public function __construct()
    {
        flextype('registry')->set('workspace', ['icon' => ['name' => 'images', 'set' => 'bootstrap']]);
    }

    /**
     * Get Media ID
     *
     * @param array Query
     */
    protected function getMediaID($query)
    {
        $id = '';

        if (isset($query['id'])) {
            $id = $query['id'];
        }

        return $id;
    }

    /**
     * Get Media Parent ID
     *
     * @param array Query
     */
    protected function getMediaParentID($query)
    {
        $parentID = strings($this->getMediaID($query))->beforeLast('/')->toString();

        if ($this->getMediaID($query) === $parentID) {
            $parentID = '';
        }

        return $parentID;
    }

    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        $id = $this->getMediaID($query);
        $parentID = $this->getMediaParentID($query);

        $mediaFoldersList = [];
        $mediaFilesList = [];

        $mediaFoldersList = flextype('media')->folders()->fetch($id, ['collection' => true]);
        $mediaFilesList = flextype('media')->files()->fetch($id, ['collection' => true]);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/media/index.html',
            [
                'mediaFoldersList' => $mediaFoldersList,
                'mediaFilesList' => $mediaFilesList,
                'id' => $id,
                'parent_id' => $parentID,
                'links' => [
                    'media' => [
                        'link' => flextype('router')->pathFor('admin.media.index'),
                        'title' => __('admin_media')
                    ]
                ]
            ]
        );
    }

    /**
     * Edit page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function edit(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();

        $id = $this->getMediaID($query);

        $meta_data = flextype('serializers')
                        ->yaml()
                        ->decode(filesystem()
                                    ->file(flextype('media')->files()->meta()->getFileMetaLocation($id))
                                    ->get());

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/media/edit.html',
            [
                'id' => $id,
                'meta_data' => $meta_data,
                'links' => [
                    'media' => [
                        'link' => flextype('router')->pathFor('admin.media.index'),
                        'title' => __('admin_media')
                    ]
                ]
            ]
        );
    }

    /**
     * Edit process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function editProcess(Request $request, Response $response): Response
    {
        $data  = $request->getParsedBody();
        $query = $request->getQueryParams();

        $meta_data = flextype('serializers')
                        ->yaml()
                        ->decode(filesystem()
                                    ->file(flextype('media')->files()->meta()->getFileMetaLocation($query['id']))
                                    ->get());

        $data = arrays($data)
                    ->delete('_csrf_name')
                    ->delete('_csrf_value')
                    ->toArray();

        $result = flextype('serializers')
                    ->yaml()
                    ->encode(array_merge($meta_data, $data));

        if (filesystem()
                    ->file(flextype('media')->files()->meta()->getFileMetaLocation($query['id']))
                    ->put($result)) {
            flextype('flash')->addMessage('success', __('admin_message_changes_saved'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_changes_not_saved'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.media.edit') . '?id=' . $query['id']);
    }

    /**
     * Upload page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function upload(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/media/upload.html',
            [
                'id' => $this->getMediaID($query),
                'links' => [
                    'media' => [
                        'link' => flextype('router')->pathFor('admin.media.index'),
                        'title' => __('admin_media')
                    ]
                ]
            ]
        );
    }

    /**
     * Upload media file - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function uploadProcess(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (flextype('media')->files()->upload($_FILES['file'], $data['id'] . '/')) {
            flextype('flash')->addMessage('success', __('admin_message_entry_file_uploaded'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_file_not_uploaded'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.media.index') . '?id=' . $data['id']);
    }
}
