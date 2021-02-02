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
     * Get Media Folder ID
     *
     * @param array Query
     */
    protected function getFolderID($query)
    {
        if (isset($query['id'])) {
            $id = $query['id'];
        } else {
            $id = '';
        }

        return $id;
    }

    /**
     * Get Media Folder Parent ID
     *
     * @param array Query
     */
    protected function getFolderParentID($query)
    {
        $parentID = strings($this->getFolderID($query))->beforeLast('/')->toString();

        if ($this->getFolderID($query) === $parentID) {
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
    public function index(Request $request, Response $response) : Response
    {
        flextype('registry')->set('workspace', ['icon' => ['name' => 'images', 'set' => 'bootstrap']]);

        // Get Query Params
        $query = $request->getQueryParams();

        $id = $this->getFolderID($query);
        $parentID = $this->getFolderParentID($query);

        $media_folders_list = [];
        $media_files_list = [];

        $media_folders_list = flextype('media')->folders()->fetch($id, ['collection' => true]);
        $media_files_list = flextype('media')->files()->fetch($id, ['collection' => true]);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/media/index.html',
            [
                'media_folders_list' => $media_folders_list,
                'media_files_list' => $media_files_list,
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

}
