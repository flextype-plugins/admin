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

class EntriesController
{
    /**
     * Entry visibility
     *
     * @var array
     * @access private
     */
    private array $visibility = [];

    /**
     * Entry routable
     *
     * @var array
     * @access private
     */
    private array $routable = [];

    /**
     * __construct()
     */
    public function __construct()
    {
        $this->visibility = ['draft'   => __('admin_entries_draft'),
                             'visible' => __('admin_entries_visible'), 
                             'hidden'  => __('admin_entries_hidden')];

        $this->routable = [true  => __('admin_yes'),
                           false => __('admin_no')];
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

        // Set Entry ID
        $query['id'] ??= '';
        
        // Get blueprints
        $blueprints = [];
        foreach(blueprints()->fetch('', ['collection' => true]) as $name => $blueprint) {
            if (!empty($blueprint)) {
                $blueprints[$name] = $blueprint['title'];
            }
        }

        // Get entries collection 
        $entries = [];
        $entriesCollection = [];
        $entriesCollection = arrays(entries()->fetch($query['id'], ['collection' => true, 'depth' => ['1']]))
                                ->sortBy('published_at', 'DESC')
                                ->toArray();

        foreach ($entriesCollection as $entryID => $entryBody) {
            $entries[$entryID] = $entryBody;
            if (filesystem()->find()->in(PATH['project'] . '/entries/' . $entryID)->depth('>=1')->depth('<=2')->hasResults()) {
                $entries[$entryID]['has_children'] = true;
            }
        }

        return twig()->render(
            $response,
            'plugins/admin/templates/entries/index.html',
            [
                'entries' => $entries,
                'blueprints' => $blueprints,
                'query' => $query,
                'links' => [
                    'content' => [
                        'link' => urlFor('admin.entries.index'),
                        'title' => __('admin_entries')
                    ]
                ]
            ]
        );
    }

    /**
     * Create new content page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function add(Request $request, Response $response): Response
    {
        // Get blueprints
        $blueprints = [];
        foreach(blueprints()->fetch('', ['collection' => true]) as $name => $blueprint) {
            if (!empty($blueprint)) {
                $blueprints[$name] = $blueprint['title'];
            }
        }

        return twig()->render(
            $response,
            'plugins/admin/templates/entries/add.html',
            [
                'blueprints' => $blueprints,
                'routable' => $this->routable,
                'visibility' => $this->visibility,
                'query' => $request->getQueryParams(),
                'links' => [
                    'content' => [
                        'link' => urlFor('admin.entries.index'),
                        'title' => __('admin_entries')
                    ]
                ]
            ]
        );
    }

    /**
     * Create new content - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function addProcess(Request $request, Response $response): Response
    {
        // Get data from POST
        $data = $request->getParsedBody();
        
        // Process form
        $form = blueprints()->form($data)->process();

        // Check if content exists then try to create content
        if (!entries()->has($form->get('fields.id'))) {
            if (entries()->create($form->get('fields.id'), $form->copy()->delete('fields.id')->get('fields'))) {
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
     * Move content
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function move(Request $request, Response $response): Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Get content ID
        $query['id'] ??= '';

        // Get current content ID
        $entryCurrentID = arraysFromString($query['id'], '/')->last();

        // Get parrent content ID
        $entryParentID = arraysFromString($query['id'], '/')->slice(0, -1)->toString('/');

        if (empty($entryParentID)) {
            $entryParentID = '/';
        } else {
            $entries['/'] = '/';
        }

        // Fetch content
        $entry = entries()->fetch($query['id'])->toArray();

        // Get enties
        foreach (entries()->fetch('', ['collection' => true, 'find' => ['depth' => '>0'], 'filter' => ['order_by' => ['field' => ['id']]]])->toArray() as $_entry) {
            if ($_entry['id'] != $query['id'] && $_entry['id'] != $entryParentID) {
                if ($_entry['id'] != '') {
                    $entries[$_entry['id']] = $_entry['id'];
                } else {
                    $entries[registry()->get('flextype.entry.main')] = registry()->get('flextype.entry.main');
                }
            }
        }

        return twig()->render(
            $response,
            'plugins/admin/templates/entries/move.html',
            [
                'query' => $query,
                'entries' => $entries,
                'entryCurrentID' => $entryCurrentID,
                'entryParentID' => $entryParentID,
                'links' => [
                    'content' => [
                        'link' => urlFor('admin.entries.index'),
                        'title' => __('admin_entries')
                    ]
                ]
            ]
        );
    }

    /**
     * Move content - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function moveProcess(Request $request, Response $response)
    {
        // Get data from POST
        $data = $request->getParsedBody();

        // Process form
        $form = blueprints()->form($data)->process();

        if (!entries()->has(strings($form->get('fields.to') . '/' . $form->get('fields.content_current_id'))->trim('/')->toString())) {
            if (entries()->move(
                $form->get('fields.id'),
                strings($form->get('fields.to') . '/' . $form->get('fields.content_current_id'))->trim('/')->toString()
            )) {
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
     * Rename content
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function rename(Request $request, Response $response): Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set content ID
        $query['id'] ??= '';

        return twig()->render(
            $response,
            'plugins/admin/templates/entries/rename.html',
            [
                'query' => $query,
                'links' => [
                    'content' => [
                        'link' => urlFor('admin.entries.index'),
                        'title' => __('admin_entries')
                    ]
                ]
            ]
        );
    }

    /**
     * Rename content - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function renameProcess(Request $request, Response $response): Response
    {
        // Get data from POST
        $data = $request->getParsedBody();

        // Process form
        $form = blueprints()->form($data)->process();

        if (entries()->move(
            $form->get('fields.id'),
            $form->get('fields.new_id'))
        ) {
            container()->get('flash')->addMessage('success', $form->get('messages.success'));
        } else {
            container()->get('flash')->addMessage('error', $form->get('messages.error'));
        }

        return $response->withRedirect($form->get('redirect'));
    }

    /**
     * Delete content - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function deleteProcess(Request $request, Response $response): Response
    {
        // Get data from POST
        $data = $request->getParsedBody();

        $id             = $data['id'];
        $contentCurrentID = $data['id-current'];

        if (entries()->delete($id)) {
            container()->get('flash')->addMessage('success', __('admin_message_entries_deleted'));
        } else {
            container()->get('flash')->addMessage('error', __('admin_message_entries_was_not_deleted'));
        }

        return $response->withRedirect(urlFor('admin.entries.index') . '?id=' . $contentCurrentID);
    }

    /**
     * Duplicate content - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function duplicateProcess(Request $request, Response $response): Response
    {
        // Get data from POST
        $data = $request->getParsedBody();

        // Get ID, parentID and newID
        $id        = $data['id'];
        $parentID  = arraysFromString($id, '/')->slice(0, -1)->toString('/');
        $newID     = $id . '-copy-' . date("Ymd-His");

        // Get current content title, copy current content and update title for new content
        $title = entries()->fetch($id)['title'];
        entries()->copy($id, $newID, true);
        entries()->update($newID, ['title' => $title . ' copy']);

        container()->get('flash')->addMessage('success', __('admin_message_entries_duplicated'));

        return $response->withRedirect(urlFor('admin.entries.index') . '?id=' . $parentID);
    }

    /**
     * Edit content
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function edit(Request $request, Response $response): Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set content ID
        $query['id'] ??= '';

        // Disable content parsers
        registry()->set('entry.fields.parsers.settings.enabled', false);

        // Fetch content
        $entry = entries()->fetch($query['id'])->toArray();

        return twig()->render(
            $response,
            'plugins/admin/templates/entries/edit.html',
            [
                'id' => $query['id'],
                'entry' => $entry,
                'routable' => $this->routable,
                'visibility' => $this->visibility,
                'query' => $query,
                'links' => [
                    'entry' => [
                        'link' => urlFor('admin.entries.index') . '?id=' . arraysFromString($query['id'], '/')->slice(0, -1)->toString('/'),
                        'title' => __('admin_entries')
                    ]
                ]
            ]
        );
    }

    /**
     * Edit content process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function editProcess(Request $request, Response $response): Response
    {
        // Get data from POST
        $data = $request->getParsedBody();

        // Process form
        $form = blueprints()->form($data)->process();

        if (entries()->update($form->get('fields.id'), $form->copy()->delete('fields.id')->get('fields'))) {
            container()->get('flash')->addMessage('success', $form->get('messages.success'));
        } else {
            container()->get('flash')->addMessage('error', $form->get('messages.error'));
        }

        return $response->withRedirect($form->get('redirect'));  
    }
}
