<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Html\Html, Form\Form};
?>

<?php Themes::view('admin/views/partials/head')->display(); ?>

<form method="post">
    <div class="row">
      <div class="col-9">
        <?php echo Form::hidden('slug', $page_slug); ?>
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h3 class="h3"><?php echo I18n::find('admin_pages_edit', 'admin', Registry::get('site.locale')); ?></h3>
            </div>
            <div class="admin-panel-body">
                <div class="form-group">
                  <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_title', 'admin', Registry::get('site.locale')); ?></label>
                  <input type="text" name="title" class="form-control" id="formGroupPageTitleInput" value="<?php echo $page_title; ?>" placeholder="">
                </div>
                <div class="form-group">
                  <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_content', 'admin', Registry::get('site.locale')); ?></label>
                  <?php echo Form::textarea('editor', $page_content); ?>
                </div>
            </div>
        </div>
      </div>
      <div class="col-3">
          <div class="admin-panel">
              <div class="admin-panel-header">
                  <h3 class="h3">Publish</h3>
              </div>
              <div class="admin-panel-body">
                  <div class="form-group">
                    <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_visibility', 'admin', Registry::get('site.locale')); ?></label>
                    <input type="text" name="title" class="form-control" id="formGroupPageTitleInput" value="<?php echo $page_title; ?>" placeholder="">
                  </div>
                  <div class="form-group">
                     <label for="formGroupParentPageInput"><?php echo I18n::find('admin_pages_visibility', 'admin', Registry::get('site.locale')); ?></label>
                     <select class="form-control" id="formGroupParentPageInput" name="parent_page">
                       <option value="">visible</option>
                       <option value="">draft</option>
                     </select>
                   </div>
                   <div class="form-group">
                     <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_date', 'admin', Registry::get('site.locale')); ?></label>
                     <input type="text" name="date" class="form-control" id="formGroupPageTitleInput" value="<?php echo $page_date; ?>" placeholder="">
                   </div>
              </div>
              <div class="admin-panel-footer">
                  <button class="btn btn-black btn-editor" name="save_page" type="submit"><?php echo I18n::find('admin_save', 'admin', Registry::get('site.locale')); ?></button>
              </div>
          </div>

          <br>

          <div class="admin-panel">
              <div class="admin-panel-header">
                  <h3 class="h3">Page Attributes</h3>
              </div>
              <div class="admin-panel-body">
                  <div class="form-group">
                    <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_keywords', 'admin', Registry::get('site.locale')); ?></label>
                    <input type="text" name="keywords" class="form-control" id="formGroupPageTitleInput" value="<?php echo $page_keywords; ?>" placeholder="">
                  </div>
                  <div class="form-group">
                    <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_description', 'admin', Registry::get('site.locale')); ?></label>
                    <input type="text" name="description" class="form-control" id="formGroupPageTitleInput" value="<?php echo $page_description; ?>" placeholder="">
                  </div>
                  <div class="form-group">
                    <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_template', 'admin', Registry::get('site.locale')); ?></label>
                    <input type="text" name="template" class="form-control" id="formGroupPageTitleInput" value="<?php echo $page_template; ?>" placeholder="">
                  </div>
                  <div class="form-group">
                     <label for="formGroupParentPageInput"><?php echo I18n::find('admin_pages_parent_page', 'admin', Registry::get('site.locale')); ?></label>
                     <select class="form-control" id="formGroupParentPageInput" name="parent_page">
                       <option value="">default</option>
                     </select>
                   </div>
              </div>
          </div>
      </div>
    </div>
</form>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
