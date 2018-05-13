<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, I18n\I18n};
?>


<?php Themes::template('admin/views/partials/head')->display(); ?>

<h2 class="page-heading">
    <?php echo I18n::find('admin_pages_heading', 'admin', Registry::get('site.locale')); ?>
    <a class="btn pull-right btn-black" href="<?php echo Http::getBaseUrl(); ?>/admin/pages/add" role="button"><?php echo I18n::find('admin_pages_create_new', 'admin', Registry::get('site.locale')); ?></a>
</h2>

<table class="table">
  <thead>
    <tr>
      <th scope="col"><?php echo I18n::find('admin_pages_name', 'admin', Registry::get('site.locale')); ?></th>
      <th scope="col"><?php echo I18n::find('admin_pages_url', 'admin', Registry::get('site.locale')); ?></th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($pages_list as $page) { ?>
    <tr>
      <td scope="row"><a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/edit?page=<?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('site.pages.main'); ?>"><?php echo $page['title']; ?></a></td>
      <td scope="row"><a href="<?php echo Http::getBaseUrl(); ?>/<?php echo $page['slug']; ?>">/<?php echo $page['slug']; ?></a></td>
      <td scope="row" class="text-right"><a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/delete?page=<?php echo $page['slug']; ?>"><?php echo I18n::find('admin_pages_delete', 'admin', Registry::get('site.locale')); ?></a></td>
    </tr>
    <?php } ?>
  </tbody>
</table>

<?php Themes::template('admin/views/partials/footer')->display(); ?>
