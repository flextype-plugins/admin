<?php
namespace Flextype;
use Flextype\Component\{Registry\Registry, Html\Html, Form\Form, Http\Http, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links',   [
                                'edit_page' => ['url' => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name, 'title' => __('admin_pages_editor'), 'class' => 'active'],
                                'edit_page_media' => ['url' => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&media=true', 'title' => __('admin_pages_edit_media')],
                                'edit_page_templates' => ['url' => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name, 'title' => __('admin_pages_edit_template')],
                                'edit_page_settings' => ['url' => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name, 'title' => __('admin_pages_edit_settings')],
                            ])
        ->assign('buttons', ['pages' =>
                                        ['url' => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&expert=true', 'title' => __('admin_pages_view_page')]])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>


<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

        <?php echo Form::open(); ?>
        <?php echo Form::hidden('token', Token::generate()); ?>
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
        

        <?php echo Form::submit('page_save', __('admin_save'), ['class' => 'btn btn-black']); ?>
        <?php echo Form::close(); ?>
    </div>
</div>


<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
