<?php
namespace Flextype;
use Flextype\Component\{Registry\Registry, Html\Html, Form\Form, Http\Http, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links', [
                                'pages'               => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages',
                                                            'title'      => __('admin_pages_heading'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                         ],
                                'edit_page'           => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name,
                                                            'title'      => __('admin_pages_editor'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                         ],
                                'edit_page_media'     => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&media=true',
                                                            'title'      => __('admin_pages_edit_media'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                        ],
                                  'edit_page_blueprint'       => [
                                                              'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&blueprint=true&blueprint_name='.$blueprint_name,
                                                              'title'      => __('admin_pages_editor_blueprint'),
                                                              'attributes' => ['class' => 'navbar-item']
                                                           ],
                                   'edit_page_template'       => [
                                                               'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&template=true&template_name='.$template_name,
                                                               'title'      => __('admin_pages_editor_template'),
                                                               'attributes' => ['class' => 'navbar-item']
                                                            ],
                                    'edit_page_source'           => [
                                                                'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&expert=true',
                                                                'title'      => __('admin_pages_editor_source'),
                                                                'attributes' => ['class' => 'navbar-item']
                                                             ],
                                     'preview'           => [
                                                                 'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&preview=true',
                                                                 'title'      => __('admin_pages_preview'),
                                                                 'attributes' => ['class' => 'navbar-item active']
                                                              ],
                            ])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>


<iframe width="100%" height="100%" src="http://flextype.org" frameborder="0"  />


<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
