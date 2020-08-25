<?php

namespace Flextype\Plugin\Admin\Controllers;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
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
     * __construct
     */
     public function __construct()
     {

     }

    /**
     * Get Entry ID
     *
     * @param array Query
     */
    protected function getEntryID($query)
    {
        if (isset($query['id'])) {
            $_id = $query['id'];
        } else {
            $_id = '';
        }

        return $_id;
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
        // Get Query Params
        $query = $request->getQueryParams();

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        // Init Fieldsets
        $fieldsets = [];

        // Get fieldsets files
        $fieldsets_list = Filesystem::listContents(PATH['project'] . '/fieldsets/');

        // If there is any fieldset file then go...
        if (count($fieldsets_list) > 0) {
            foreach ($fieldsets_list as $fieldset) {
                if ($fieldset['type'] == 'file' && $fieldset['extension'] == 'yaml') {
                    $fieldset_content = flextype('yaml')->decode(Filesystem::read($fieldset['path']));
                    if (isset($fieldset_content['form']) &&
                        isset($fieldset_content['form']['tabs']) &&
                        isset($fieldset_content['form']['tabs']['main']['fields']) &&
                        isset($fieldset_content['form']['tabs']['main']['fields']['title'])) {
                        if (isset($fieldset_content['hide']) && $fieldset_content['hide'] == true) {
                            continue;
                        }
                        $fieldsets[$fieldset['basename']] = $fieldset_content;
                    }
                }
            }
        }

        $entry_current = flextype('entries')->fetch($this->getEntryID($query));

        if (isset($entry_current['items_view'])) {
            $items_view = $entry_current['items_view'];
        } else {
            $items_view = flextype('registry')->get('plugins.admin.settings.entries.items_view_default');
        }

        $entries_list = [];
        $entries_collection = [];
        $entries_collection = collect(flextype('entries')->fetchCollection($this->getEntryID($query), ['depth' => ['1']]))->orderBy('published_at', 'DESC')->all();

        foreach ($entries_collection as $slug => $body) {
            $entries_list[$slug] = $body;
            if (find()->in(PATH['project'] . '/entries/' . $slug)->depth('>=1')->depth('<=2')->hasResults()) {
                $entries_list[$slug] += ['has_children' => true];
            }
        }

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/index.html',
            [
                            'entries_list' => $entries_list,
                            'id_current' => $this->getEntryID($query),
                            'entry_current' => $entry_current,
                            'items_view' => $items_view,
                            'menu_item' => 'entries',
                            'fieldsets' => $fieldsets,
                            'parts' => $parts,
                            'i' => count($parts),
                            'last' => array_pop($parts),
                            'links' => [
                                        'entries' => [
                                                'link' => flextype('router')->pathFor('admin.entries.index'),
                                                'title' => __('admin_entries'),
                                                'active' => true
                                            ]
                                        ],
                            'buttons'  => [
                                        'create' => [
                                                'link'    => 'javascript:;',
                                                'title'   => __('admin_create_new_entry'),
                                                'onclick' => 'event.preventDefault(); selectEntryType("'.$this->getEntryID($query).'", 0);'
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
    public function add(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        $type = isset($query['type']) ? $query['type']: '';

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/add.html',
            [
                            'entries_list' => collect(flextype('entries')->fetchCollection($this->getEntryID($query)))->orderBy('order_by', 'ASC')->all(),
                            'menu_item' => 'entries',
                            'current_id' => $this->getEntryID($query),
                            'parts' => $parts,
                            'i' => count($parts),
                            'last' => array_pop($parts),
                            'type' => $type,
                            'links' => [
                                        'entries' => [
                                            'link' => flextype('router')->pathFor('admin.entries.index'),
                                            'title' => __('admin_entries'),

                                        ],
                                        'entries_add' => [
                                            'link' => flextype('router')->pathFor('admin.entries.add') . '?id=' . $this->getEntryID($query),
                                            'title' => __('admin_create_new_entry'),
                                            'active' => true
                                            ]
                                        ]
                        ]
        );
    }

    /**
     * Select Entry Type - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function selectEntryTypeProcess(Request $request, Response $response) : Response
    {
        // Get data from POST
        $data = $request->getParsedBody();

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.add') . '?id=' . $data['id'] . '&type=' . $data['type']);
    }

    /**
     * Create new entry - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function addProcess(Request $request, Response $response) : Response
    {
        // Get data from POST
        $data = $request->getParsedBody();

        // Set parent Entry ID
        if ($data['current_id']) {
            $parent_entry_id = $data['current_id'];
        } else {
            $parent_entry_id = '';
        }

        // Set new Entry ID using slugify or without it
        if (flextype('registry')->get('plugins.admin.settings.entries.slugify') == true) {
            $id = ltrim($parent_entry_id . '/' . flextype('slugify')->slugify($data['id']), '/');
        } else {
            $id = ltrim($parent_entry_id . '/' . $data['id'], '/');
        }

        // Check if entry exists then try to create it
        if (!flextype('entries')->has($id)) {

            // Check if we have fieldset for this entry
            if (flextype('fieldsets')->has($data['fieldset'])) {

                // Get fieldset
                $fieldset = flextype('fieldsets')->fetch($data['fieldset']);

                // Init entry data
                $data_from_post          = [];
                $data_from_post_override = [];
                $data_result             = [];

                // Define data values based on POST data
                $data_from_post['created_by'] = flextype('acl')->getUserLoggedInUuid();
                $data_from_post['published_by'] = flextype('acl')->getUserLoggedInUuid();
                $data_from_post['title']      = $data['title'];
                $data_from_post['fieldset']   = $data['fieldset'];
                $data_from_post['visibility'] = $data['visibility'];
                $data_from_post['published_at'] = date(flextype('registry')->get('flextype.settings.date_format'), time());
                $data_from_post['routable']   = isset($data['routable']) ? (bool) $data['routable'] : false;

                // Themes/Templates support for Site Plugin
                // We need to check if template for current fieldset is exists
                // if template is not exist then `default` template will be used!
                if (flextype('registry')->has('plugins.site')) {
                    $template_path = PATH['project'] . '/themes/' . flextype('registry')->get('plugins.site.settings.theme') . '/templates/' . $data['fieldset'] . '.html';
                    $template = (Filesystem::has($template_path)) ? $data['fieldset'] : 'default';
                    $data_from_post['template']   = $template;
                }

                //foreach ($fieldset['sections'] as $section_name => $section_body) {
                //    foreach ($section_body['form']['fields'] as $field => $properties) {

                // Predefine data values based on fieldset default values
                foreach ($fieldset['form']['tabs'] as $form_tab => $form_tab_body) {
                    foreach ($form_tab_body['fields'] as $field => $properties) {

                        // Ingnore fields where property: heading
                        if ($properties['type'] == 'heading') {
                            continue;
                        }

                        // Get values from $data_from_post
                        if (isset($data_from_post[$field])) {
                            $value = $data_from_post[$field];

                        // Get values from fieldsets predefined field values
                        } elseif (isset($properties['value'])) {
                            $value = $properties['value'];

                        // or set empty value
                        } else {
                            $value = '';
                        }

                        $data_from_post_override[$field] = $value;
                    }
                }

                // Merge data
                if (count($data_from_post_override) > 0) {
                    $data_result = array_replace_recursive($data_from_post_override, $data_from_post);
                } else {
                    $data_result = $data_from_post;
                }

                if (flextype('entries')->create($id, $data_result)) {
                    flextype('media_folders')->create('entries/' . $id);
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

        if (isset($data['create-and-edit'])) {
            return $response->withRedirect(flextype('router')->pathFor('admin.entries.edit') . '?id=' . $id . '&type=editor');
        } else {
            return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . $parent_entry_id);
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
    public function type(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        $entry = flextype('entries')->fetch($this->getEntryID($query));

        $fieldsets = [];

        // Get fieldsets files
        $_fieldsets = Filesystem::listContents(PATH['project'] . '/fieldsets/');

        // If there is any fieldsets file then go...
        if (count($_fieldsets) > 0) {
            foreach ($_fieldsets as $fieldset) {
                if ($fieldset['type'] == 'file' && $fieldset['extension'] == 'yaml') {
                    $fieldset_content = flextype('yaml')->decode(Filesystem::read($fieldset['path']));
                    if (isset($fieldset_content['form']) &&
                        isset($fieldset_content['form']['tabs']['main']) &&
                        isset($fieldset_content['form']['tabs']['main']['fields']) &&
                        isset($fieldset_content['form']['tabs']['main']['fields']['title'])) {
                        if (isset($fieldset_content['hide']) && $fieldset_content['hide'] == true) {
                            continue;
                        }
                        $fieldsets[$fieldset['basename']] = $fieldset_content['title'];
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
                            'parts' => $parts,
                            'i' => count($parts),
                            'last' => array_pop($parts),
                            'links' => [
                                'entries' => [
                                    'link' => flextype('router')->pathFor('admin.entries.index'),
                                    'title' => __('admin_entries'),

                                ],
                                'entries_type' => [
                                    'link' => flextype('router')->pathFor('admin.entries.type') . '?id=' . $this->getEntryID($query),
                                    'title' => __('admin_type'),
                                    'active' => true
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
    public function typeProcess(Request $request, Response $response) : Response
    {
        $post_data = $request->getParsedBody();

        $id = $post_data['id'];

        $entry = flextype('entries')->fetch($id);

        Arrays::delete($entry, 'slug');
        Arrays::delete($entry, 'id');
        Arrays::delete($entry, 'modified_at');
        Arrays::delete($entry, 'created_at');
        Arrays::delete($entry, 'published_at');

        Arrays::delete($post_data, 'csrf_name');
        Arrays::delete($post_data, 'csrf_value');
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
    public function move(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Get Entry from Query Params
        $entry_id = $this->getEntryID($query);

        // Get current Entry ID
        $parts = explode("/", $entry_id);
        $entry_id_current = array_pop($parts);

        // Fetch entry
        $entry = flextype('entries')->fetch($this->getEntryID($query));

        // Set Entries IDs in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        // Get entries list
        $entries_list['/'] = '/';
        foreach (flextype('entries')->fetchCollection('', ['depth' => '>0', 'order_by' => ['field' => ['id']]]) as $_entry) {
            if ($_entry['id'] != '') {
                $entries_list[$_entry['id']] = $_entry['id'];
            } else {
                $entries_list[flextype('registry')->get('flextype.entries.main')] = flextype('registry')->get('flextype.entries.main');
            }
        }

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/move.html',
            [
                            'menu_item' => 'entries',
                            'entries_list' => $entries_list,
                            'entry_id_current' => $entry_id_current,
                            'entry_id_path_current' => $entry_id,
                            'entry_id_path_parent' => implode('/', array_slice(explode("/", $entry_id), 0, -1)),
                            'parts' => $parts,
                            'i' => count($parts),
                            'last' => array_pop($parts),
                            'links' => [
                                'entries' => [
                                    'link' => flextype('router')->pathFor('admin.entries.index'),
                                    'title' => __('admin_entries'),

                                ],
                                'entries_move' => [
                                    'link' => flextype('router')->pathFor('admin.entries.move'),
                                    'title' => __('admin_move'),
                                    'active' => true
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
            if (flextype('entries')->rename(
                $data['entry_id_path_current'],
                $data['parent_entry'] . '/' . $entry_id_current
            )) {
                flextype('media_folders')->rename('entries/' . $data['entry_id_path_current'], 'entries/' . $data['parent_entry'] . '/' . $entry_id_current);

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
    public function rename(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        $entry_id     = explode("/", $this->getEntryID($query));
        $name_current = array_pop($entry_id);

        return flextype('twig')->render(
            $response,
            'plugins/admin/templates/content/entries/rename.html',
            [
                            'name_current' => $name_current,
                            'entry_path_current' => $this->getEntryID($query),
                            'entry_parent' => implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1)),
                            'menu_item' => 'entries',
                            'parts' => $parts,
                            'i' => count($parts),
                            'last' => array_pop($parts),
                            'links' => [
                                'entries' => [
                                    'link' => flextype('router')->pathFor('admin.entries.index'),
                                    'title' => __('admin_entries'),

                                ],
                                'entries_type' => [
                                    'link' => flextype('router')->pathFor('admin.entries.rename') . '?id=' . $this->getEntryID($query),
                                    'title' => __('admin_rename'),
                                    'active' => true
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
    public function renameProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        // Set name
        if (flextype('registry')->get('plugins.admin.settings.entries.slugify') == true) {
            $name = flextype('slugify')->slugify($data['name']);
        } else {
            $name = $data['name'];
        }

        if (flextype('entries')->rename(
            $data['entry_path_current'],
            $data['entry_parent'] . '/' . $name)
        ) {
            flextype('media_folders')->rename('entries/' . $data['entry_path_current'], 'entries/' . $data['entry_parent'] . '/' . flextype('slugify')->slugify($data['name']));
            flextype('flash')->addMessage('success', __('admin_message_entry_renamed'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_was_not_renamed'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . $data['entry_parent']);
    }

    /**
     * Delete entry - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function deleteProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        $id = $data['id'];
        $id_current = $data['id-current'];

        if (flextype('entries')->delete($id)) {

            flextype('media_folders')->delete('entries/' . $id);

            flextype('flash')->addMessage('success', __('admin_message_entry_deleted'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_was_not_deleted'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.index') . '?id=' . $id_current);
    }

    /**
     * Duplicate entry - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function duplicateProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        $id = $data['id'];
        $parent_id = implode('/', array_slice(explode("/", $id), 0, -1));

        $random_date = date("Ymd_His");

        flextype('entries')->copy($id, $id . '-duplicate-' . $random_date, true);

        if (Filesystem::has(PATH['project'] . '/uploads' . '/entries/' . $id)) {
            Filesystem::copy(PATH['project'] . '/uploads' . '/entries/' . $id, PATH['project'] . '/uploads' . '/entries/' . $id . '-duplicate-' . $random_date, true);
        } else {
            Filesystem::createDir(PATH['project'] . '/uploads' . '/entries/' . $id . '-duplicate-' . $random_date);
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
    public function edit(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        // Get Entry type
        $type = $request->getQueryParams()['type'];

        flextype('registry')->set('entries.fields.parsers.settings.enabled', false);

        // Get Entry
        $entry = flextype('entries')->fetch($this->getEntryID($query));

        Arrays::delete($entry, 'slug');
        Arrays::delete($entry, 'id');
        Arrays::delete($entry, 'modified_at');

        // Fieldsets for current entry template
        $fieldsets_path = PATH['project'] . '/fieldsets/' . (isset($entry['fieldset']) ? $entry['fieldset'] : 'default') . '.yaml';
        $fieldsets = flextype('yaml')->decode(Filesystem::read($fieldsets_path));
        is_null($fieldsets) and $fieldsets = [];

        if ($type == 'source') {
            $entry['published_at'] = date(flextype('registry')->get('flextype.settings.date_format'), $entry['published_at']);
            $entry['created_at'] = date(flextype('registry')->get('flextype.settings.date_format'), $entry['created_at']);

            return flextype('twig')->render(
                $response,
                'plugins/admin/templates/content/entries/source.html',
                [
                        'parts' => $parts,
                        'i' => count($parts),
                        'last' => array_pop($parts),
                        'id' => $this->getEntryID($query),
                        'data' => flextype('frontmatter')->encode($entry),
                        'type' => $type,
                        'menu_item' => 'entries',
                        'links' => [
                            'entries' => [
                                'link' => flextype('router')->pathFor('admin.entries.index'),
                                'title' => __('admin_entries'),

                            ],
                            'edit_entry' => [
                                'link' => flextype('router')->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query). '&type=editor',
                                'title' => __('admin_editor'),

                            ],
                            'edit_entry_media' => [
                                'link' => flextype('router')->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=media',
                                'title' => __('admin_media'),

                            ],
                            'edit_entry_source' => [
                                'link' => flextype('router')->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=source',
                                'title' => __('admin_source'),
                                'active' => true
                            ],
                        ],
                        'buttons' => [
                            'save_entry' => [
                                            'id' => 'form',
                                            'link'       => 'javascript:;',
                                            'title'      => __('admin_save'),
                                            'type' => 'action'
                                        ],
                        ]
                ]
            );
        } elseif ($type == 'media') {
            return flextype('twig')->render(
                $response,
                'plugins/admin/templates/content/entries/media.html',
                [
                        'parts' => $parts,
                        'i' => count($parts),
                        'last' => array_pop($parts),
                        'id' => $this->getEntryID($query),
                        'files' => $this->getMediaList($this->getEntryID($query), true),
                        'menu_item' => 'entries',
                        'links' => [
                            'entries' => [
                                'link' => flextype('router')->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1)),
                                'title' => __('admin_entries'),

                            ],
                            'edit_entry' => [
                                'link' => flextype('router')->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=editor',
                                'title' => __('admin_editor'),

                            ],
                            'edit_entry_media' => [
                                'link' => flextype('router')->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=media',
                                'title' => __('admin_media'),
                                'active' => true
                            ],
                            'edit_entry_source' => [
                                'link' => flextype('router')->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=source',
                                'title' => __('admin_source'),
                            ],
                        ]
                ]
            );
        } else {

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
                        'parts' => $parts,
                        'i' => count($parts),
                        'last' => array_pop($parts),
                        'form' => $form,
                        'menu_item' => 'entries',
                        'links' => [
                            'entries' => [
                                'link' => flextype('router')->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1)),
                                'title' => __('admin_entries')
                            ],
                            'edit_entry' => [
                                'link' => flextype('router')->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=editor',
                                'title' => __('admin_editor'),
                                'active' => true
                            ],
                            'edit_entry_media' => [
                                'link' => flextype('router')->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=media',
                                'title' => __('admin_media')
                            ],
                            'edit_entry_source' => [
                                'link' => flextype('router')->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=source',
                                'title' => __('admin_source')
                            ],
                        ],
                        'buttons' => [
                            'save_entry' => [
                                            'id' => 'form',
                                            'link'  => 'javascript:;',
                                            'title' => __('admin_save'),
                                            'type' => 'action'
                                        ],
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
    public function editProcess(Request $request, Response $response) : Response
    {
        $query = $request->getQueryParams();

        // Get Entry ID and TYPE from GET param
        $id = $query['id'];
        $type = $query['type'];

        if ($type == 'source') {

            // Data from POST
            $data = $request->getParsedBody();

            $entry = flextype('frontmatter')->decode($data['data']);

            $entry['created_by'] = flextype('acl')->getUserLoggedInUuid();
            $entry['published_by'] = flextype('acl')->getUserLoggedInUuid();

            Arrays::delete($entry, 'slug');
            Arrays::delete($entry, 'id');
            Arrays::delete($entry, 'modified_at');

            // Update entry
            if (Filesystem::write(PATH['project'] . '/entries' . '/' . $id . '/entry.md', flextype('frontmatter')->encode($entry))) {
                flextype('flash')->addMessage('success', __('admin_message_entry_changes_saved'));
            } else {
                flextype('flash')->addMessage('error', __('admin_message_entry_changes_not_saved'));
            }
        } else {
            // Result data to save
            $result_data = [];

            // Data from POST
            $data = $request->getParsedBody();

            // Delete system fields
            isset($data['slug'])                  and Arrays::delete($data, 'slug');
            isset($data['id'])                    and Arrays::delete($data, 'id');
            isset($data['csrf_value'])            and Arrays::delete($data, 'csrf_value');
            isset($data['csrf_name'])             and Arrays::delete($data, 'csrf_name');
            isset($data['form-save-action'])      and Arrays::delete($data, 'form-save-action');
            isset($data['trumbowyg-icons-path'])  and Arrays::delete($data, 'trumbowyg-icons-path');
            isset($data['trumbowyg-locale'])      and Arrays::delete($data, 'trumbowyg-locale');
            isset($data['flatpickr-date-format']) and Arrays::delete($data, 'flatpickr-date-format');
            isset($data['flatpickr-locale'])      and Arrays::delete($data, 'flatpickr-locale');


            $data['published_by'] = Session::get('uuid');

            // Fetch entry
            $entry = flextype('entries')->fetch($id);
            Arrays::delete($entry, 'slug');
            Arrays::delete($entry, 'id');
            Arrays::delete($entry, 'modified_at');

            if (isset($data['created_at'])) {
                $data['created_at'] = date(flextype('registry')->get('flextype.settings.date_format'), strtotime($data['created_at']));
            } else {
                $data['created_at'] = date(flextype('registry')->get('flextype.settings.date_format'), $entry['created_at']);
            }

            if (isset($data['published_at'])) {
                $data['published_at'] = (string) date(flextype('registry')->get('flextype.settings.date_format'), strtotime($data['published_at']));
            } else {
                $data['published_at'] = (string) date(flextype('registry')->get('flextype.settings.date_format'), $entry['published_at']);
            }

            if (isset($data['routable'])) {
                $data['routable'] = (bool) $data['routable'];
            } elseif(isset($entry['routable'])) {
                $data['routable'] = (bool) $entry['routable'];
            } else {
                $data['routable'] = true;
            }

            // Merge entry data with $data
            $result_data = array_merge($entry, $data);

            // Update entry
            if (flextype('entries')->update($id, $result_data)) {
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
    public function deleteMediaFileProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        $entry_id = $data['entry-id'];
        $media_id = $data['media-id'];

        flextype('media_files')->delete('entries/' . $entry_id . '/' . $media_id);

        flextype('flash')->addMessage('success', __('admin_message_entry_file_deleted'));

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.edit') . '?id=' . $entry_id . '&type=media');
    }

    /**
     * Upload media file - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function uploadMediaFileProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        if (flextype('media_files')->upload($_FILES['file'], '/entries/' . $data['entry-id'] . '/')) {
            flextype('flash')->addMessage('success', __('admin_message_entry_file_uploaded'));
        } else {
            flextype('flash')->addMessage('error', __('admin_message_entry_file_not_uploaded'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.entries.edit') . '?id=' . $data['entry-id'] . '&type=media');
    }

    /**
     * Get media list
     *
     * @param string $id Entry ID
     * @param bool   $path if true returns with url paths
     *
     * @return array
     */
    public function getMediaList(string $id, bool $path = false) : array
    {
        $base_url = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER))->getBaseUrl();
        $files = [];

        if (!Filesystem::has(PATH['project'] . '/uploads/entries/' . $id)) {
            Filesystem::createDir(PATH['project'] . '/uploads/entries/' . $id);
        }

        foreach (array_diff(scandir(PATH['project'] . '/uploads/entries/' . $id), ['..', '.']) as $file) {
            if (strpos(flextype('registry')->get('plugins.admin.settings.entries.media.accept_file_types'), $file_ext = substr(strrchr($file, '.'), 1)) !== false) {
                if (strpos($file, strtolower($file_ext), 1)) {
                    if ($file !== 'entry.md') {
                        if ($path) {
                            $files[$base_url . '/' . $id . '/' . $file] = $base_url . '/' . $id . '/' . $file;
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
