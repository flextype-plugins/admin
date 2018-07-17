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
            <a class="navbar-brand" href="<?php echo Http::getBaseUrl(); ?>/admin/pages/"><?php echo I18n::find('admin_pages_heading', Registry::get('system.locale')); ?></a>
            &nbsp;/&nbsp;
            <a class="navbar-brand" href="#"><?php echo I18n::find('admin_pages_create_new', Registry::get('system.locale')); ?></a>
        </div>
    </div>
</nav>
<!-- End Navbar -->


<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5" style="margin: 0 auto;">

                <div class="card">
                    <div class="card-body">

                        <?php echo Form::open(); ?>
                        <?php echo Form::hidden('token', Token::generate()); ?>

                        <div class="form-group">
                          <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_title', Registry::get('system.locale')); ?></label>
                          <input type="text" name="title" class="form-control" id="formGroupPageTitleInput" placeholder="" required>
                        </div>
                        <div class="form-group">
                          <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_name', Registry::get('system.locale')); ?></label>
                          <input type="text" name="slug" class="form-control" id="formGroupPageTitleInput" placeholder="" required>
                        </div>
                        <div class="form-group">
                           <label for="formGroupParentPageInput"><?php echo I18n::find('admin_pages_parent_page', Registry::get('system.locale')); ?></label>
                           <select class="form-control" id="formGroupParentPageInput" name="parent_page">
                             <option value="">/</option>
                             <?php foreach($pages_list as $page) { ?>
                             <option value="<?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('system.pages.main'); ?>"><?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('system.pages.main'); ?></option>
                             <?php } ?>
                           </select>
                         </div>

                     </div>

                     <div class="card-footer text-center">
                         <?php echo Form::submit('create_page', I18n::find('admin_create', Registry::get('system.locale')), ['class' => 'btn btn-black btn-fill btn-wd']); ?>
                     </div>


                     <?php echo Form::close(); ?>

                 </div>

            </div>
        </div>
    </div>
</div>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
