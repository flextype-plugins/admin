<?php
namespace Flextype;
use Flextype\Component\{Registry\Registry, Http\Http, Form\Form, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links', ['pages' => ['url' => Http::getBaseUrl() . '/admin/pages', 'title' => __('admin_pages_heading')],
                           'pages_add' => ['url' => Http::getBaseUrl() . '/admin/pages/rename', 'title' => __('admin_pages_rename'), 'class' => 'active']])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">
    <div class="col-md-6">

        <?php echo Form::open(); ?>
        <?php echo Form::hidden('token', Token::generate()); ?>
        <?php echo Form::hidden('page_path_current', $page_path_current); ?>
        <?php echo Form::hidden('page_name_current', $page_name); ?>

        <div class="form-group">
            <?php
                echo (
                    Form::label('title', __('admin_pages_title'), ['for' => 'pageTitle']).
                    Form::input('title', $page_title, ['class' => 'form-control', 'id' => 'pageTitle', 'required'])
                );
            ?>
        </div>
        <div class="form-group">
            <?php
                echo (
                    Form::label('name', __('admin_pages_name'), ['for' => 'pageName']).
                    Form::input('name', $page_name, ['class' => 'form-control', 'id' => 'pageName', 'required'])
                );
            ?>
        </div>
        <div class="form-group">
           <?php
               echo (
                   Form::label('parent_page', __('admin_pages_parent_page')).
                   Form::select('parent_page', $pages_list, $page_parent, array('class' => 'form-control'))
               );
           ?>
         </div>

         <?php echo Form::submit('rename_page', __('admin_save'), ['class' => 'btn btn-black btn-fill btn-wd']); ?>
     <?php echo Form::close(); ?>

 </div>
</div>

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
