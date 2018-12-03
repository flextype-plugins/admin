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
                                                              'attributes' => ['class' => 'navbar-item active']
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
                                                                 'attributes' => ['class' => 'navbar-item']
                                                              ],
                            ])
        ->assign('buttons', [
                                'save' => [
                                                    'link'       => 'javascript:;',
                                                    'title'      => __('admin_save'),
                                                    'attributes' => ['class' => 'js-page-edit-blueprint-save float-right btn']
                                                ]
                            ])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<?php echo Form::open(null, ['id' => 'editBlueprintForm']); ?>
<?php echo Form::hidden('token', Token::generate()); ?>
<?php echo Form::hidden('action', 'save-blueprint'); ?>
<?php echo Form::hidden('page_name', $page_name); ?>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <?php
                echo (
                    Form::textarea('blueprint', $blueprint, ['class' => 'form-control', 'style' => 'min-height:500px;', 'id' => 'pageExpertEditor'])
                );
            ?>
        </div>
    </div>
</div>
<?php echo Form::close(); ?>

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
