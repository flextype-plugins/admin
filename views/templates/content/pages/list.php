<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links',   ['pages' => ['url' => Http::getBaseUrl() . '/admin/pages', 'title' => __('admin_pages_heading'), 'class' => 'active']])
        ->assign('buttons', ['pages' => ['url' => Http::getBaseUrl() . '/admin/pages/add', 'title' => __('admin_pages_create_new')]])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<table class="table no-margin">
    <thead>
        <tr>
            <th><?php echo __('admin_pages_name'); ?></th>
            <th><?php echo __('admin_pages_url'); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($pages_list as $page) { ?>
        <tr>
            <td>
                <a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/edit?page=<?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('system.pages.main'); ?>"><?php echo $page['title']; ?></a>
            </td>
            <td><a target="_blank"  href="<?php echo Http::getBaseUrl(); ?>/<?php echo $page['slug']; ?>">/<?php echo $page['slug']; ?></a></td>
            <td class="text-right">
                <div class="btn-group">
                  <a class="btn btn-default" href="<?php echo Http::getBaseUrl(); ?>/admin/pages/edit?page=<?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('system.pages.main'); ?>"><?php echo __('admin_pages_edit'); ?></a>
                  <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo Http::getBaseUrl(); ?>/admin/pages/add"><?php echo __('admin_pages_add'); ?></a>
                    <a class="dropdown-item" href="<?php echo Http::getBaseUrl(); ?>/admin/pages/clone?page=<?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('system.pages.main'); ?>&token=<?php echo Token::generate(); ?>"><?php echo __('admin_pages_clone'); ?></a>
                    <a class="dropdown-item" href="<?php echo Http::getBaseUrl(); ?>/admin/pages/rename?page=<?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('system.pages.main'); ?>"><?php echo __('admin_pages_rename'); ?></a>
                  </div>
                </div>
                <a class="btn btn-default" href="<?php echo Http::getBaseUrl(); ?>/admin/pages/delete?page=<?php echo $page['slug']; ?>&token=<?php echo Token::generate(); ?>"><?php echo __('admin_pages_delete'); ?></a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>


<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
