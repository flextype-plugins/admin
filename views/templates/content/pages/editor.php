<?php
namespace Flextype;
use Flextype\Component\{Registry\Registry, Html\Html, Form\Form, Http\Http, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links',   [
                                'pages'               => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages',
                                                            'title'      => __('admin_pages_heading'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                         ],
                                'edit_page'           => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name,
                                                            'title'      => __('admin_pages_editor'),
                                                            'attributes' => ['class' => 'navbar-item active']
                                                         ],
                                'edit_page_media'     => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&media=true',
                                                            'title'      => __('admin_pages_edit_media'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                         ],
                                'edit_page_templates' => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name,
                                                            'title'      => __('admin_pages_edit_template'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                         ],
                                'edit_page_settings'  => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name,
                                                            'title'      => __('admin_pages_edit_settings'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                         ]
                            ])
        ->assign('buttons', [
                                'view_page' => [
                                                    'link'       => Http::getBaseUrl() . '/' . $page_name,
                                                    'title'      => __('admin_pages_view_page'),
                                                    'attributes' => ['class' => 'float-right btn', 'target' => '_blank']
                                               ],
                                'save_page' => [
                                                    'link'       => 'javascript:;',
                                                    'title'      => __('admin_pages_save_page'),
                                                    'attributes' => ['class' => 'js-page-save-submit float-right btn']
                                                ]
                            ])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<?php echo Form::open(null, ['id' => 'editPage']); ?>
<?php echo Form::hidden('token', Token::generate()); ?>
<?php echo Form::hidden('action', 'edit-page'); ?>
<?php echo Form::hidden('page_name', $page_name); ?>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <?php
                echo (
                    Form::label('page_title', __('admin_pages_title'), ['for' => 'pageTitle']).
                    Form::input('page_title', $page_title, ['class' => 'form-control', 'id' => 'pageTitle', 'required'])
                );
            ?>
        </div>
        <div class="form-group">
            <?php
                echo (
                    Form::label('page_content', __('admin_pages_content'), ['for' => 'pageTitle']).
                    Form::textarea('page_content', $page_content, ['class' => 'form-control margin-hard-bottom', 'style' => 'height:400px;', 'id' => 'pageContent'])
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
