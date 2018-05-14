<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Http\Http};
?>
<?php Themes::template('admin/views/partials/head')->display(); ?>

<h2 class="page-heading">
    <?php echo I18n::find('admin_pages_create_new', 'admin', Registry::get('site.locale')); ?>
</h2>

<form method="post">
    <div class="row">
      <div class="col-4">
        <div class="form-group">
          <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_title', 'admin', Registry::get('site.locale')); ?></label>
          <input type="text" name="title" class="form-control" id="formGroupPageTitleInput" placeholder="">
        </div>
        <div class="form-group">
          <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_slug', 'admin', Registry::get('site.locale')); ?></label>
          <input type="text" name="slug" class="form-control" id="formGroupPageTitleInput" placeholder="">
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
         <br>
         <button class="btn btn-black" name="create_page" type="submit"><?php echo I18n::find('admin_create', 'admin', Registry::get('site.locale')); ?></button>
      </div>
    </div>
</form>

<?php Themes::template('admin/views/partials/footer')->display(); ?>
