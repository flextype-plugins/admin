<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Http\Http};
?>
<?php Themes::view('admin/views/partials/head')->display(); ?>

<form method="post">
    <div class="row">
      <div class="col-4" style="margin: 0 auto;">
          <div class="dark-panel">
              <div class="dark-panel-header">
                  <h3 class="h3"><?php echo I18n::find('admin_pages_create_new', 'admin', Registry::get('site.locale')); ?></h3>
              </div>
              <div class="dark-panel-body">
                  <div class="form-group">
                    <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_title', 'admin', Registry::get('site.locale')); ?></label>
                    <input type="text" name="title" class="form-control" id="formGroupPageTitleInput" placeholder="" required>
                  </div>
                  <div class="form-group">
                    <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_slug', 'admin', Registry::get('site.locale')); ?></label>
                    <input type="text" name="slug" class="form-control" id="formGroupPageTitleInput" placeholder="" required>
                  </div>
                  <div class="form-group">
                     <label for="formGroupParentPageInput"><?php echo I18n::find('admin_pages_parent_page', 'admin', Registry::get('site.locale')); ?></label>
                     <select class="form-control" id="formGroupParentPageInput" name="parent_page">
                       <option value="">/</option>
                       <?php foreach($pages_list as $page) { ?>
                       <option value="<?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('site.pages.main'); ?>"><?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('site.pages.main'); ?></option>
                       <?php } ?>
                     </select>
                   </div>
              </div>
              <div class="dark-panel-footer">
                  <button class="btn btn-black" name="create_page" type="submit"><?php echo I18n::find('admin_create', 'admin', Registry::get('site.locale')); ?></button>
              </div>
          </div>
      </div>
    </div>
</form>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
