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
                                                        'attributes' => ['class' => 'navbar-item']
                                                     ],
                            'edit_page_media'     => [
                                                        'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&media=true',
                                                        'title'      => __('admin_pages_edit_media'),
                                                        'attributes' => ['class' => 'navbar-item active']
                                                     ],
                            'edit_page_settings'  => [
                                                        'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name,
                                                        'title'      => __('admin_pages_edit_settings'),
                                                        'attributes' => ['class' => 'navbar-item']
                                                     ]
                        ])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">
    <div class="col-4">
        <div class="form-group">
            <?php
                echo (
                    Form::label('page_visibility', __('admin_pages_visibility'),  ['for' => 'pageTitle']).
                    Form::select('page_visibility', ['visible' => 'visible', 'draft' => 'draft'], $page_visibility, ['class' => 'form-control', 'id' => 'pageTitle'])
                );
            ?>
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            <?php
                echo (
                    Form::label('page_template', __('admin_pages_template'),  ['for' => 'pageTemplate']).
                    Form::select('page_template', $templates, $page_template, ['class' => 'form-control', 'id' => 'pageTemplate'])
                );
            ?>
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            <?php
                echo (
                    Form::label('page_date', __('admin_pages_date'), ['for' => 'pageDate']).
                    Form::input('page_date', $page_date, ['class' => 'form-control', 'id' => 'pageDate'])
                );
            ?>
        </div>
    </div>
</div>

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
