<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Html\Html, Form\Form};
?>

<?php Themes::view('admin/views/partials/head')->display(); ?>

<h2 class="page-heading">
    <?php echo I18n::find('admin_pages_edit', 'admin', Registry::get('site.locale')); ?>
</h2>

<form method="post">
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_title', 'admin', Registry::get('site.locale')); ?></label>
          <input type="text" name="title" class="form-control" id="formGroupPageTitleInput" placeholder="">
        </div>
        <div class="form-group">
          <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_content', 'admin', Registry::get('site.locale')); ?></label>
          <?php echo Form::textarea('editor', $page_content); ?>
        </div>
         <br>
         <button class="btn btn-black btn-editor" name="save_page" type="submit"><?php echo I18n::find('admin_save_and_exit', 'admin', Registry::get('site.locale')); ?></button>
         <button class="btn btn-black btn-editor" name="save_page" type="submit"><?php echo I18n::find('admin_save', 'admin', Registry::get('site.locale')); ?></button>
         <button class="btn btn-black btn-black-default btn-editor" name="save_page" type="submit"><?php echo I18n::find('admin_cancel', 'admin', Registry::get('site.locale')); ?></button>
      </div>
    </div>
</form>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
