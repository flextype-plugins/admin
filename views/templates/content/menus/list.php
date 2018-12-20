<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Token\Token, Text\Text};
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links',   [
                            'menus' => [
                                            'link' => Http::getBaseUrl() . '/admin/menus',
                                            'title' => __('admin_menus_heading'),
                                            'attributes' => ['class' => 'navbar-item active']
                                       ]
                        ])
    ->assign('buttons', [
                            'menus_add' => [
                                            'link' => Http::getBaseUrl() . '/admin/menus/add',
                                            'title' => __('admin_menus_create_new_category'),
                                            'attributes' => ['class' => 'float-right btn']
                                       ]
                        ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<?php if (count($menus_list) > 0): ?>
    <?php foreach ($menus_list as $category => $menu): ?>
        <?= $menu['title'] ?>
        <div class="float-right">
            <a href="<?= Http::getBaseUrl() ?>/admin/menus/create-category"><?= __('admin_menus_create_new_item') ?></a>
            |
            <a href="<?= Http::getBaseUrl() ?>/admin/menus/delete-category"><?= __('admin_menus_delete_category') ?></a>
        </div>
        <table class="table no-margin">
            <thead>
                <tr>
                    <th width="30%"><?php echo __('admin_menus_name'); ?></th>
                    <th width="30%"><?php echo __('admin_menus_url'); ?></th>
                    <th><?php echo __('admin_menus_order'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($menu['items'] as $id => $menu_items): ?>
                <tr>
                    <td><?= $menu_items['title'] ?></td>
                    <td class="text-left"><?= $menu_items['url'] ?></td>
                    <td class="text-left"><?= $menu_items['order'] ?></td>
                    <td class="text-right">
                        <a class="btn btn-default" href="<?= Http::getBaseUrl() ?>/admin/menus/edit?category=<?= $id ?>&category=<?= $category ?>"><?= __('admin_menu_item_edit'); ?></a>
                        <a class="btn btn-default" href="<?= Http::getBaseUrl() ?>/admin/menus/delete_item?item=<?= $id ?>&category=<?= $category ?>&token=<?= Token::generate() ?>"><?= __('admin_menu_item_delete'); ?></a>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <br><br>
    <?php endforeach ?>
<?php else: ?>
    <h3 class="no-data-message"><?= __('admin_menus_empty') ?></h3>
<?php endif ?>

<?php
Themes::view('admin/views/partials/content-end')->display();
Themes::view('admin/views/partials/footer')->display();
?>
