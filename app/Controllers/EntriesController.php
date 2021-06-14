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

        flextype('registry')->set('workspace', ['icon' => ['name' => 'newspaper', 'set' => 'bootstrap']]);
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

        // Set entry ID
        $query['id'] ??= '';
        
        // Get blueprints
        $blueprints = [];
        foreach(flextype('blueprints')->fetch('', ['collection' => true]) as $name => $blueprint) {
            if (!empty($blueprint)) {
                $blueprints[$name] = $blueprint['title'];
            }
        }

        // Get entries
        $entries = [];
        $entriesCollection = [];
        $entriesCollection = arrays(flextype('entries')->fetch($query['id'], ['collection' => true, 'depth' => ['1']]))
                                ->sortBy('published_at', 'DESC')
                                ->toArray();

        foreach ($entriesCollection as $entryID => $entryBody) {
            $entries[$entryID] = $entryBody;
            if (filesystem()->find()->in(PATH['project'] . '/entries/' . $entryID)->depth('>=1')->depth('<=2')->hasResults()) {
                $entries[$entryID]['has_children'] = true;
            }
        }

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/index.html',
            [
                'id' => $query['id'],
                'menu_item' => 'entries',
                'entries' => $entries,
                'blueprints' => $blueprints,
                'links' => [
                    'entries' => [
                        'link' => flextype('router')->pathFor('admin.entries.index'),
                        'title' => __('admin_entries')
                    ]
                ]
            ]
        );
    }

    /**
     * Create new entry page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function add(Request $request, Response $response): Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set entry ID
        $query['id'] ??= '';

        // Get blueprints
        $blueprints = [];
        foreach(flextype('blueprints')->fetch('', ['collection' => true]) as $name => $blueprint) {
            if (!empty($blueprint)) {
                $blueprints[$name] = $blueprint['title'];
            }
        }

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/add.html',
            [
                'id' => $query['id'],
                'menu_item' => 'entries',
                'blueprints' => $blueprints,
                'routable' => $this->routable,
                'visibility' => $this->visibility,
                'links' => [
                    'entries' => [
                        'link' => flextype('router')->pathFor('admin.entries.index'),
                        'title' => __('admin_entries')
                    ]
                ]
            ]
        );
    }

    /**
     * Create new entry - process
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
        $form = flextype('blueprints')->form($data)->process();

        // Check if entry exists then try to create entry
        if (!flextype('entries')->has($form['fields']['id'])) {
            if (flextype('entries')->create($form['fields']['id'], $form->copy()->delete('fields.id')->toArray())) {
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
     * Move entry
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

        // Get entry ID
        $query['id'] ??= '';

        // Get current entry ID
        $entryCurrentID = arraysFromString($query['id'], '/')->last();

        // Get parrent entry ID
        $entryParentID = arraysFromString($query['id'], '/')->slice(0, -1)->toString('/');

        if (empty($entryParentID)) {
            $entryParentID = '/';
        } else {
            $entries['/'] = '/';
        }

        // Fetch entry
        $entry = flextype('entries')->fetch($query['id'])->toArray();

        // Get entries
        foreach (flextype('entries')->fetch('', ['collection' => true, 'find' => ['depth' => '>0'], 'filter' => ['order_by' => ['field' => ['id']]]])->toArray() as $_entry) {
            if ($_entry['id'] != $query['id']) {
                if ($_entry['id'] != '') {
                    $entries[$_entry['id']] = $_entry['id'];
                } else {
                    $entries[flextype('registry')->get('flextype.entries.main')] = flextype('registry')->get('flextype.entries.main');
                }
            }
        }

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/move.html',
            [
                'menu_item' => 'entries',
                'id' => $query['id'],
                'entries' => $entries,
                'entryCurrentID' => $entryCurrentID,
                'entryParentID' => $entryParentID,
                'links' => [
                    'entries' => [
                        'link' => flextype('router')->pathFor('admin.entries.index'),
                        'title' => __('admin_entries')
                    ]
                ]
            ]
        );
    }

    /**
     * Move entry - process
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
        $form = flextype('blueprints')->form($data)->process();

        if (!flextype('entries')->has(strings($form['fields']['to'] . '/' . $form['fields']['entry_current_id'])->trim('/')->toString())) {
            if (flextype('entries')->move(
                $form['fields']['id'],
                strings($form['fields']['to'] . '/' . $form['fields']['entry_current_id'])->trim('/')->toString()
            )) {
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
     * Rename entry
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

        // Set entry ID
        $query['id'] ??= '';

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/rename.html',
            [
                'menu_item' => 'entries',
                'id' => $query['id'],
                'links' => [
                    'entries' => [
                        'link' => flextype('router')->pathFor('admin.entries.index'),
                        'title' => __('admin_entries')
                    ]
                ]
            ]
        );
    }

    /**
     * Rename entry - process
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
        $form = flextype('blueprints')->form($data)->process();

        if (flextype('entries')->move(
            $form['fields']['id'],
            $form['fields']['new_id'])
        ) {
            flextype('flash')->addMessage('success', $form['messages']['success']);
        } else {
            flextype('flash')->addMessage('error', $form['messages']['error']);
        }

        return $response->withRedirect($form['redirect']);
    }

    /**
     * Delete entry - process
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
        $entryCurrentID = $data['id-current'];

        if (flextype('entries')->delete($id)) {
            flextype('flash')->addMessage('success', __('admin_message_entry_deleted'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_was_not_deleted'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . $entryCurrentID);
    }

    /**
     * Duplicate entry - process
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

        // Get current entry title, copy current entry and update title for new entry
        $title = flextype('entries')->fetch($id)['title'];
        flextype('entries')->copy($id, $newID, true);
        flextype('entries')->update($newID, ['title' => $title . ' copy']);

        flextype('flash')->addMessage('success', __('admin_message_entry_duplicated'));

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . $parentID);
    }

    /**
     * Edit entry
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

        // Set entry ID
        $query['id'] ??= '';

        // Disable entry parsers
        flextype('registry')->set('entries.fields.parsers.settings.enabled', false);

        // Fetch entry
        $entry = flextype('entries')->fetch($query['id'])->toArray();

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/edit.html',
            [
                'menu_item' => 'entries',
                'id' => $query['id'],
                'entry' => $entry,
                'routable' => $this->routable,
                'visibility' => $this->visibility,
                'query' => $query,
                'links' => [
                    'entries' => [
                        'link' => flextype('router')->pathFor('admin.entries.index') . '?id=' . arraysFromString($query['id'], '/')->slice(0, -1)->toString('/'),
                        'title' => __('admin_entries')
                    ]
                ]
            ]
        );
    }

    /**
     * Edit entry process
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
        $form = flextype('blueprints')->form($data)->process();

        if (flextype('entries')->update($form['fields']['id'], $form->copy()->delete('fields.id')->toArray())) {
            flextype('flash')->addMessage('success', $form['messages']['success']);
        } else {
            flextype('flash')->addMessage('error', $form['messages']['error']);
        }

        return $response->withRedirect($form['redirect']);  
    }
}
