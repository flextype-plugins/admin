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
        $cancelUrl = flextype('router')->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1));

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
                flextype('flash')->addMessage('error', __('admin_message_fieldset_not_found'));
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

        $entry = flextype('entries')->fetch($this->getEntryID($query))->toArray();

        $fieldsets = [];

        // Get fieldsets files
        $_fieldsets = Filesystem::listContents(PATH['project'] . '/fieldsets/');

        // If there is any fieldsets file then go...
        if (count($_fieldsets) > 0) {
            foreach ($_fieldsets as $fieldset) {
                if ($fieldset['type'] == 'file' && $fieldset['extension'] == 'yaml') {
                    $fieldsetContent = flextype('serializers')->yaml()->decode(Filesystem::read($fieldset['path']));
                    if (isset($fieldsetContent['form']) &&
                        isset($fieldsetContent['form']['tabs']['main']) &&
                        isset($fieldsetContent['form']['tabs']['main']['fields']) &&
                        isset($fieldsetContent['form']['tabs']['main']['fields']['title'])) {
                        if (isset($fieldsetContent['hide']) && $fieldsetContent['hide'] == true) {
                            continue;
                        }
                        $fieldsets[$fieldset['basename']] = $fieldsetContent['title'];
                    }
                }
            }
        }

        $fieldset = $entry['fieldset'] ?? [];

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/type.html',
            [
                'fieldset' => $fieldset,
                'fieldsets' => $fieldsets,
                'id' => $this->getEntryID($query),
                'menu_item' => 'entries',
                'cancelUrl' => flextype('router')->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1)),
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
        $post_data = $request->getParsedBody();

        $id = $post_data['id'];

        $entry = flextype('entries')->fetch($id)->toArray();

        Arrays::delete($entry, 'slug');
        Arrays::delete($entry, 'id');
        Arrays::delete($entry, 'modified_at');
        Arrays::delete($entry, 'created_at');
        Arrays::delete($entry, 'published_at');

        Arrays::delete($post_data, 'csrf-token');

        Arrays::delete($post_data, 'save_entry');
        Arrays::delete($post_data, 'id');

        $post_data['created_by'] = flextype('acl')->getUserLoggedInUuid();
        $post_data['published_by'] = flextype('acl')->getUserLoggedInUuid();

        $data = array_merge($entry, $post_data);

        if (flextype('entries')->update(
            $id,
            $data
        )) {
            flextype('flash')->addMessage('success', __('admin_message_entry_changes_saved'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_was_not_moved'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $id), 0, -1)));
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

        $entryID = $this->getEntryID($query);

        $entryIDParts = explode("/", $entryID);

        $entryCurrentID = array_pop($entryIDParts);

        $entryPathParentID = implode('/', array_slice(explode("/", $entryID), 0, -1));

        if (empty($entryPathParentID)) {
            $entryPathParentID = '/';
        } else {
            $entriesList['/'] = '/';
        }

        // Fetch entry
        $entry = flextype('entries')->fetch($this->getEntryID($query))->toArray();

        // Get entries list
        foreach (flextype('entries')->fetch('', ['collection' => true, 'find' => ['depth' => '>0'], 'filter' => ['order_by' => ['field' => ['id']]]])->toArray() as $_entry) {
            if ($_entry['id'] != $entryID) {
                if ($_entry['id'] != '') {
                    $entriesList[$_entry['id']] = $_entry['id'];
                } else {
                    $entriesList[flextype('registry')->get('flextype.entries.main')] = flextype('registry')->get('flextype.entries.main');
                }
            }
        }

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/move.html',
            [
                'menu_item' => 'entries',
                'id' => $entryID,
                'entriesList' => $entriesList,
                'entryCurrentID' => $entryCurrentID,
                'entryPathCurrentID' => $entryID,
                'entryPathParentID' => $entryPathParentID,
                'cancelUrl' => flextype('router')->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1)),
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

        // Set entry id current
        $entry_id_current = $data['entry_id_current'];

        if (!flextype('entries')->has($data['parent_entry'] . '/' . $entry_id_current)) {
            if (flextype('entries')->move(
                $data['entry_id_path_current'],
                $data['parent_entry'] . '/' . $entry_id_current
            )) {
                flextype('flash')->addMessage('success', __('admin_message_entry_moved'));
            } else {
                flextype('flash')->addMessage('error', __('admin_message_entry_was_not_moved'));
            }
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_was_not_moved'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . (($data['parent_entry'] == '/') ? '' : $data['parent_entry']));
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
        $cancelUrl = flextype('router')->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $id), 0, -1));

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
        $data = $request->getParsedBody();

        $id = $data['id'];
        $parent_id = implode('/', array_slice(explode("/", $id), 0, -1));

        $random_date = date("Ymd_His");

        flextype('entries')->copy($id, $id . '-duplicate-' . $random_date, true);

        if (Filesystem::has(PATH['project'] . '/media' . '/entries/' . $id)) {
            filesystem()
                ->directory(PATH['project'] . '/media' . '/entries/' . $id)
                ->copy(PATH['project'] . '/media' . '/entries/' . $id . '-duplicate-' . $random_date);
        } else {
            Filesystem::createDir(PATH['project'] . '/media' . '/entries/' . $id . '-duplicate-' . $random_date);
        }

        flextype('flash')->addMessage('success', __('admin_message_entry_duplicated'));

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . $parent_id);
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

        // Get Entry type
        $type = $request->getQueryParams()['type'];

        $cancelUrl = flextype('router')->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1));

        flextype('registry')->set('entries.fields.parsers.settings.enabled', false);

        // Get Entry
        $entry = flextype('entries')->fetch($this->getEntryID($query))->toArray();

        Arrays::delete($entry, 'slug');
        Arrays::delete($entry, 'id');
        Arrays::delete($entry, 'modified_at');

        // Fieldsets for current entry template
        $fieldsets_path = PATH['project'] . '/fieldsets/' . (isset($entry['fieldset']) ? $entry['fieldset'] : 'default') . '.yaml';
        $fieldsets = flextype('serializers')->yaml()->decode(Filesystem::read($fieldsets_path));
        is_null($fieldsets) and $fieldsets = [];

        if ($type == 'source') {

            $entrySource = filesystem()->file(flextype('entries')->getFileLocation($this->getEntryID($query)))->get();

            return flextype('twig')->render(
                $response,
                'plugins/admin/templates/content/entries/source.html',
                [
                    'id' => $this->getEntryID($query),
                    'data' => $entrySource,
                    'type' => $type,
                    'menu_item' => 'entries',
                    'cancelUrl' => $cancelUrl,
                    'links' => [
                        'entries' => [
                            'link' => flextype('router')->pathFor('admin.entries.index'),
                            'title' => __('admin_entries')
                        ]
                    ]
                ]
            );
        } elseif ($type == 'editor') {

            $fieldsets = arrays($fieldsets)
                            ->set('header.buttons.cancelUrl.href', $cancelUrl)
                            ->set('header.buttons.submit.href', 'javascript:void(0);')
                            ->set('header.buttons.submit.class', 'js-submit-entries-form-editor')
                            ->toArray();

            // Merge current entry fieldset with global fildset
            if (isset($entry['entry_fieldset'])) {
                $form = flextype('form')->render(array_replace_recursive($fieldsets, $entry['entry_fieldset']), $entry);
            } else {
                $form = flextype('form')->render($fieldsets, $entry);
            }

            return flextype('twig')->render(
                $response,
                'plugins/admin/templates/content/entries/edit.html',
                [
                    'id' => $this->getEntryID($query),
                    'form' => $form,
                    'menu_item' => 'entries',
                    'cancelUrl' => $cancelUrl,
                    'links' => [
                        'entries' => [
                            'link' => flextype('router')->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1)),
                            'title' => __('admin_entries')
                        ]
                    ]
                ]
            );
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
        $query = $request->getQueryParams();

        // Get Entry ID and TYPE from GET param
        $id   = $query['id'];
        $type = $query['type'];

        if ($type == 'source') {

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
        } else {
            // Result data to save
            $dataResult = [];

            // Data from POST
            $dataPost = $request->getParsedBody();

            // Delete system fields
            isset($dataPost['slug'])                  and arrays($dataPost)->delete('slug')->toArray();
            isset($dataPost['id'])                    and arrays($dataPost)->delete('id')->toArray();
            isset($dataPost['csrf-token'])            and arrays($dataPost)->delete('csrf-token')->toArray();
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
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.edit') . '?id=' . $id . '&type=' . $type);
    }

    /**
     * Delete media file - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function deleteMediaFileProcess(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $entry_id = $data['entry-id'];
        $media_id = $data['media-id'];

        flextype('flash')->addMessage('success', __('admin_message_entry_file_deleted'));

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.edit') . '?id=' . $entry_id . '&type=media');
    }

    /**
     * Get media list
     *
     * @param string $id Entry ID
     * @param bool   $path if true returns with url paths
     *
     * @return array
     */
    public function getMediaList(string $id, bool $path = false): array
    {
        $baseUrl = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER))->getBaseUrl();
        $files = [];

        if (!Filesystem::has(PATH['project'] . '/media/entries/' . $id)) {
            Filesystem::createDir(PATH['project'] . '/media/entries/' . $id);
        }

        foreach (array_diff(scandir(PATH['project'] . '/media/entries/' . $id), ['..', '.']) as $file) {
            if (strpos(flextype('registry')->get('plugins.admin.settings.entries.media.accept_file_types'), $file_ext = substr(strrchr($file, '.'), 1)) !== false) {
                if (strpos($file, strtolower($file_ext), 1)) {
                    if ($file !== 'entry.md') {
                        if ($path) {
                            $files[$baseUrl . '/' . $id . '/' . $file] = $baseUrl . '/' . $id . '/' . $file;
                        } else {
                            $files[$file] = $file;
                        }
                    }
                }
            }
        }
        return $files;
    }

}
