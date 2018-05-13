<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Http\Http};
?>
<?php Themes::template('admin/views/partials/head')->display(); ?>

<h2 class="page-heading">
    Create New Page
</h2>

<form method="post">
    <div class="row">
      <div class="col-4">
        <div class="form-group">
          <label for="formGroupPageTitleInput">Page title</label>
          <input type="text" name="title" class="form-control" id="formGroupPageTitleInput" placeholder="">
        </div>
        <div class="form-group">
          <label for="formGroupPageTitleInput">Page slug (url)</label>
          <input type="text" name="slug" class="form-control" id="formGroupPageTitleInput" placeholder="">
        </div>
        <div class="form-group">
           <label for="formGroupParentPageInput">Parent page</label>
           <select class="form-control" id="formGroupParentPageInput" name="parent_page">
             <option value="">/</option>
             <?php foreach($pages_list as $page) { ?>
             <option value="<?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('site.pages.main'); ?>"><?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('site.pages.main'); ?></option>
             <?php } ?>
           </select>
         </div>
      </div>
    </div>
    <br>
    <button class="btn btn-lg btn-dark" name="create_page" type="submit"><?php echo I18n::find('admin_save', 'admin', Registry::get('site.locale')); ?></button>
    <a class="btn btn-lg btn-dark" href="<?php echo Http::getBaseUrl(); ?>/admin/pages"><?php echo I18n::find('admin_save', 'admin', Registry::get('site.locale')); ?></a>
</form>

<?php Themes::template('admin/views/partials/footer')->display(); ?>
