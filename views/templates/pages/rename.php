<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Http\Http, Form\Form, Token\Token};
?>

<?php Themes::view('admin/views/partials/head')->display(); ?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-fixed">
    <div class="container-fluid">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
        </button>
        <div class="navbar-wrapper">
            <a class="navbar-brand" href="<?php echo Http::getBaseUrl(); ?>/admin/pages/"><?php echo I18n::find('admin_pages_heading', 'admin', Registry::get('system.locale')); ?></a>
            &nbsp;/&nbsp;
            <a class="navbar-brand" href="#"><?php echo I18n::find('admin_pages_rename_page', 'admin', Registry::get('system.locale')); ?></a>
        </div>
    </div>
</nav>
<!-- End Navbar -->

<?php
    Themes::view('admin/views/templates/pages/partials/navbar')
        ->assign('links', ['pages' => ['url' => Http::getBaseUrl() . '/admin/pages', 'title' => I18n::find('admin_pages_heading', 'admin', Registry::get('system.locale'))],
                                      ['url' => '#', 'title' => I18n::find('admin_pages_rename_page', 'admin', Registry::get('system.locale'))]])
        ->display();
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5" style="margin: 0 auto;">

                <div class="card">
                    <div class="card-body">

                        <?php echo Form::open(); ?>
                        <?php echo Form::hidden('token', Token::generate()); ?>
                        <?php echo Form::hidden('page_path_current', $page_path_current); ?>
                        <?php echo Form::hidden('page_name_current', $page_name); ?>

                        <div class="form-group">
                            <?php
                                echo (
                                    Form::label('title', I18n::find('admin_pages_title', 'admin', Registry::get('system.locale')), ['for' => 'pageTitle']).
                                    Form::input('title', $page_title, ['class' => 'form-control', 'id' => 'pageTitle', 'required'])
                                );
                            ?>
                        </div>

                        <div class="form-group">
                            <?php
                                echo (
                                    Form::label('name', I18n::find('admin_pages_name', 'admin', Registry::get('system.locale')), ['for' => 'pageName']).
                                    Form::input('name', $page_name, ['class' => 'form-control', 'id' => 'pageName', 'required'])
                                );
                            ?>
                        </div>

                        <div class="form-group">
                           <?php
                               echo (
                                   Form::label('parent_page', I18n::find('admin_pages_parent_page', 'admin', Registry::get('system.locale'))).
                                   Form::select('parent_page', $pages_list, $page_parent, array('class' => 'form-control'))
                               );
                           ?>
                         </div>

                         <?php echo Form::close(); ?>

                     </div>

                     <div class="card-footer text-center">
                         <?php echo Form::submit('rename_page', I18n::find('admin_save', 'admin', Registry::get('system.locale')), ['class' => 'btn btn-black btn-fill btn-wd']); ?>
                     </div>

                 </div>

            </div>
        </div>
    </div>
</div>


<?php Themes::view('admin/views/partials/footer')->display(); ?>
