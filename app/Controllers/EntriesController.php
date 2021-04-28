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
     * Get Entry ID
     *
     * @param array $query Query
     */
    protected function getEntryID(array $query): string
    {
        return isset($query['id']) ? $query['id'] : '';
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

        // Get entry ID
        $id = $this->getEntryID($query);
        
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
        $entriesCollection = arrays(flextype('entries')->fetch($id, ['collection' => true, 'depth' => ['1']]))
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
                'id' => $id,
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

        // Get entry ID
        $id = $this->getEntryID($query);

        // Get blueprints
        $blueprints = [];
        foreach(flextype('blueprints')->fetch('', ['collection' => true]) as $name => $blueprint) {
            if (!empty($blueprint)) {
                $blueprints[$name] = $blueprint['title'];
            }
        }

        // Get cancel url
        $cancelUrl = flextype('router')->pathFor('admin.entries.index') . '?id=' . arraysFromString($id, '/')->slice(0, -1)->toString('/');

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/add.html',
            [
                'id' => $id,
                'menu_item' => 'entries',
                'cancelUrl' => $cancelUrl,
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
        $dataPost = $request->getParsedBody();

        // Set parent Entry ID
        if ($dataPost['current_id']) {
            $parentEntryID = $dataPost['current_id'];
        } else {
            $parentEntryID = '';
        }

        // Set new Entry ID using slugify or without it
        if (flextype('registry')->get('plugins.admin.settings.entries.slugify') == true) {
            $id = ltrim($parentEntryID . '/' . flextype('slugify')->slugify($dataPost['id']), '/');
        } else {
            $id = ltrim($parentEntryID . '/' . $dataPost['id'], '/');
        }

        // Check if entry exists then try to create entry
        if (!flextype('entries')->has($id)) {

            // Check if we have blueprint for this entry
            if (flextype('blueprints')->has($dataPost['blueprint'])) {

                // Get blueprint
                $blueprint = flextype('blueprints')->fetch($dataPost['blueprint']);

                // Init entry data
                $dataFromPost           = [];
                $dataFromPostOverride   = [];
                $dataResult             = [];

                // Define data values based on POST data
                $dataFromPost['created_by']   = flextype('acl')->getUserLoggedInUuid();
                $dataFromPost['published_by'] = flextype('acl')->getUserLoggedInUuid();
                $dataFromPost['title']        = $dataPost['title'];
                $dataFromPost['blueprint']    = $dataPost['blueprint'];
                $dataFromPost['visibility']   = $dataPost['visibility'];
                $dataFromPost['published_at'] = date(flextype('registry')->get('flextype.settings.date_format'), time());
                $dataFromPost['routable']     = isset($dataPost['routable']) ? (bool) $dataPost['routable'] : false;

                // Themes/Templates support for Site Plugin if it is enabled
                // We need to check if template for current fieldset is exists
                // if template is not exist then site `default` template will be used!
                if (flextype('registry')->has('plugins.site')) {
                    $templatePath = PATH['project'] . '/themes/' . flextype('registry')->get('plugins.site.settings.theme') . '/templates/' . $dataPost['blueprint'] . '.html';
                    $template = (filesystem()->file($templatePath)->exists()) ? $dataPost['blueprint'] : 'default';
                    $dataFromPost['template'] = $template;
                }

                // Set result data
                $dataResult = $dataFromPost;

                if (flextype('entries')->create($id, $dataResult)) {
                    flextype('flash')->addMessage('success', __('admin_message_entry_created'));
                } else {
                    flextype('flash')->addMessage('error', __('admin_message_entry_was_not_created'));
                }
            } else {
                flextype('flash')->addMessage('error', __('admin_message_blueprint_not_found'));
            }
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_was_not_created'));
        }

        switch ($dataPost['redirect']) {
            case 'edit':
                return $response->withRedirect(flextype('router')->pathFor('admin.entries.edit') . '?id=' . $id . '&type=editor');
                break;
            case 'add':
                return $response->withRedirect(flextype('router')->pathFor('admin.entries.add') . '?id=' . $dataPost['current_id']);
                break;
            case 'index':
            default:
                return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . $dataPost['current_id']);
                break;
        }
    }

    /**
     * Change entry type
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function type(Request $request, Response $response): Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Get entry ID
        $id = $this->getEntryID($query);

        // Get blueprints
        $blueprints = [];
        foreach(flextype('blueprints')->fetch('', ['collection' => true]) as $name => $blueprint) {
            if (!empty($blueprint)) {
                $blueprints[$name] = $blueprint['title'];
            }
        }

        // Get cancel url
        $cancelUrl = flextype('router')->pathFor('admin.entries.index') . '?id=' . arraysFromString($id, '/')->slice(0, -1)->toString('/');

        // Get entry
        $entry = flextype('entries')->fetch($id)->toArray();

        // Get blueprint
        $blueprint = $entry['blueprint'] ?? [];

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/type.html',
            [
                'menu_item' => 'entries',
                'id' => $id,
                'blueprint' => $blueprint,
                'blueprints' => $blueprints,
                'cancelUrl' => $cancelUrl,
                'links' => [
                    'entries' => [
                        'link' => flextype('router')->pathFor('admin.entries.index'),
                        'title' => __('admin_entries'),
                    ]
                ]
            ]
        );
    }

    /**
     * Change entry type - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function typeProcess(Request $request, Response $response): Response
    {
        // Get data from POST
        $dataPost = $request->getParsedBody();

        // Get entry ID
        $id = $dataPost['id'];

        // Get data to update
        $data = arrays(flextype('entries')
                        ->fetch($id)
                        ->except(['slug', 'id', 'modified_at', 'created_at', 'published_at']))
                    ->merge($dataPost)
                    ->delete('__csrf_token')
                    ->delete('id')
                    ->set('created_by', flextype('acl')->getUserLoggedInUuid())
                    ->set('published_by', flextype('acl')->getUserLoggedInUuid())
                    ->toArray();

        if (flextype('entries')->update(
            $id,
            $data
        )) {
            flextype('flash')->addMessage('success', __('admin_message_entry_changes_saved'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_was_not_moved'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . arraysFromString($id, '/')->slice(0, -1)->toString('/'));
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
        $id = $this->getEntryID($query);

        // Get current entry ID
        $entryCurrentID = arraysFromString($id, '/')->last();

        // Get parrent entry ID
        $entryParentID = arraysFromString($id, '/')->slice(0, -1)->toString('/');

        // Get cancel url
        $cancelUrl = flextype('router')->pathFor('admin.entries.index') . '?id=' . arraysFromString($id, '/')->slice(0, -1)->toString('/');

        if (empty($entryParentID)) {
            $entryParentID = '/';
        } else {
            $entries['/'] = '/';
        }

        // Fetch entry
        $entry = flextype('entries')->fetch($this->getEntryID($query))->toArray();

        // Get entries
        foreach (flextype('entries')->fetch('', ['collection' => true, 'find' => ['depth' => '>0'], 'filter' => ['order_by' => ['field' => ['id']]]])->toArray() as $_entry) {
            if ($_entry['id'] != $id) {
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
                'id' => $id,
                'entries' => $entries,
                'entryCurrentID' => $entryCurrentID,
                'entryParentID' => $entryParentID,
                'cancelUrl' => $cancelUrl,
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

        if (!flextype('entries')->has(strings($data['to'] . '/' . $data['entry_current_id'])->trim('/')->toString())) {
            if (flextype('entries')->move(
                $data['id'],
                strings($data['to'] . '/' . $data['entry_current_id'])->trim('/')->toString()
            )) {
                flextype('flash')->addMessage('success', __('admin_message_entry_moved'));
            } else {
                flextype('flash')->addMessage('error', __('admin_message_entry_was_not_moved'));
            }
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_was_not_moved'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . $data['to']);
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

        // Get entry ID
        $id = $this->getEntryID($query);

        // Get current entry ID
        $entryCurrentID = arraysFromString($id, '/')->last();

        // Get parrent entry ID
        $entryParentID = arraysFromString($id, '/')->slice(0, -1)->toString('/');

        // Get cancel url
        $cancelUrl = flextype('router')->pathFor('admin.entries.index') . '?id=' . arraysFromString($id, '/')->slice(0, -1)->toString('/');

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/rename.html',
            [
                'menu_item' => 'entries',
                'id' => $id,
                'entryCurrentID' => $entryCurrentID,
                'entryParentID' => $entryParentID,
                'cancelUrl' => $cancelUrl,
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

        // Set entry ID
        if (flextype('registry')->get('plugins.admin.settings.entries.slugify') == true) {
            $id = flextype('slugify')->slugify($data['id']);
        } else {
            $id = $data['id'];
        }

        if (flextype('entries')->move(
            $data['current_id'],
            $data['parent_id'] . '/' . $id)
        ) {
            flextype('flash')->addMessage('success', __('admin_message_entry_renamed'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_was_not_renamed'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . $data['parent_id']);
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

        // Get entry ID
        $id = $this->getEntryID($query);

        // Get Entry type
        $type = $request->getQueryParams()['type'];

        // Get cancel url
        $cancelUrl = flextype('router')->pathFor('admin.entries.index') . '?id=' . arraysFromString($id, '/')->slice(0, -1)->toString('/');

        // Disable entry parsers
        flextype('registry')->set('entries.fields.parsers.settings.enabled', false);

        // Get Entry
        $entry = arrays(flextype('entries')->fetch($id))
                ->delete('id')
                ->delete('slug')
                ->delete('modified_at')
                ->toArray();

        switch ($type) {
            case 'source':

                $source = filesystem()->file(flextype('entries')->getFileLocation($id))->get();

                return flextype('twig')->render(
                    $response,
                    'plugins/admin/templates/content/entries/source.html',
                    [
                        'menu_item' => 'entries',
                        'id' => $id,
                        'type' => $type,
                        'source' => $source,
                        'cancelUrl' => $cancelUrl,
                        'links' => [
                            'entries' => [
                                'link' => flextype('router')->pathFor('admin.entries.index'),
                                'title' => __('admin_entries')
                            ]
                        ]
                    ]
                );
                break;
            default:
            case 'editor':
                return flextype('twig')->render(
                    $response,
                    'plugins/admin/templates/content/entries/edit.html',
                    [
                        'menu_item' => 'entries',
                        'id' => $id,
                        'entry' => $entry,
                        'type' => $type,
                        'routable' => $this->routable,
                        'visibility' => $this->visibility,
                        'cancelUrl' => $cancelUrl,
                        'links' => [
                            'entries' => [
                                'link' => flextype('router')->pathFor('admin.entries.index') . '?id=' . arraysFromString($id, '/')->slice(0, -1)->toString('/'),
                                'title' => __('admin_entries')
                            ]
                        ]
                    ]
                );
                break;
        }
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
        // Get Query Params
        $query = $request->getQueryParams();

        // Get entry ID
        $id = $this->getEntryID($query);

        // Get Entry type
        $type = $request->getQueryParams()['type'];

        switch ($type) {
            case 'source':       

                // Data from POST
                $data = $request->getParsedBody();
    
                $entry = flextype('serializers')->frontmatter()->decode($data['data']);
    
                $entry['created_by'] = flextype('acl')->getUserLoggedInUuid();
                $entry['published_by'] = flextype('acl')->getUserLoggedInUuid();
    
                Arrays::delete($entry, 'slug');
                Arrays::delete($entry, 'id');
                Arrays::delete($entry, 'modified_at');
    
                // Update entry
                if (Filesystem::write(PATH['project'] . '/entries' . '/' . $id . '/entry.md', flextype('serializers')->frontmatter()->encode($entry))) {
                    flextype('flash')->addMessage('success', __('admin_message_entry_changes_saved'));
                } else {
                    flextype('flash')->addMessage('error', __('admin_message_entry_changes_not_saved'));
                }
                break;
            default:
            case 'editor':
                // Result data to save
                $dataResult = [];

                // Data from POST
                $dataPost = $request->getParsedBody();

                // Delete system fields
                isset($dataPost['slug'])                  and arrays($dataPost)->delete('slug')->toArray();
                isset($dataPost['id'])                    and arrays($dataPost)->delete('id')->toArray();
                isset($dataPost['__csrf_token'])          and arrays($dataPost)->delete('__csrf_token')->toArray();
                isset($dataPost['form-save-action'])      and arrays($dataPost)->delete('form-save-action')->toArray();
                isset($dataPost['trumbowyg-icons-path'])  and arrays($dataPost)->delete('trumbowyg-icons-path')->toArray();
                isset($dataPost['trumbowyg-locale'])      and arrays($dataPost)->delete('trumbowyg-locale')->toArray();
                isset($dataPost['flatpickr-date-format']) and arrays($dataPost)->delete('flatpickr-date-format')->toArray();
                isset($dataPost['flatpickr-locale'])      and arrays($dataPost)->delete('flatpickr-locale')->toArray();

                $dataPost['published_by'] = flextype('acl')->getUserLoggedInUuid();

                $entryFile = flextype('entries')->getFileLocation($id);

                $entry = flextype('serializers')
                            ->frontmatter()
                            ->decode(filesystem()->file($entryFile)->get($entryFile));

                $entryLastModified = filesystem()->file($entryFile)->lastModified();

                arrays($entry)->delete('slug')->toArray();
                arrays($entry)->delete('id')->toArray();

                if (isset($dataPost['created_at'])) {
                    $dataPost['created_at'] = date(flextype('registry')->get('flextype.settings.date_format'), strtotime($dataPost['created_at']));
                } elseif(isset($entry['created_at'])) {
                    $dataPost['created_at'] = $entry['created_at'];
                } else {
                    $dataPost['created_at'] = date(flextype('registry')->get('flextype.settings.date_format'), $entryLastModified);
                }

                if (isset($dataPost['published_at'])) {
                    $dataPost['published_at'] = date(flextype('registry')->get('flextype.settings.date_format'), strtotime($dataPost['published_at']));
                } elseif(isset($entry['published_at'])) {
                    $dataPost['published_at'] = $entry['published_at'];
                } else {
                    $dataPost['published_at'] = date(flextype('registry')->get('flextype.settings.date_format'), $entryLastModified);
                }

                if (isset($dataPost['routable'])) {
                    $dataPost['routable'] = (bool) $dataPost['routable'];
                } elseif(isset($entry['routable'])) {
                    $dataPost['routable'] = (bool) $entry['routable'];
                } else {
                    $dataPost['routable'] = true;
                }

                arrays($entry)->delete('modified_at')->toArray();

                // Merge entry data with $dataPost
                $dataResult = array_merge($entry, $dataPost);

                // Update entry
                if (flextype('entries')->update($id, $dataResult)) {
                    flextype('flash')->addMessage('success', __('admin_message_entry_changes_saved'));
                } else {
                    flextype('flash')->addMessage('error', __('admin_message_entry_changes_not_saved'));
                }
                break;
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.edit') . '?id=' . $id . '&type=' . $type);
    }
}
