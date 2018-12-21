<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Token\Token, Text\Text};
use function Flextype\Component\I18n\__;
?>
<?php Themes::view('admin/views/partials/head')->display() ?>
<?php
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
                                            'title' => __('admin_menus_create_new_menu'),
                                            'attributes' => ['class' => 'float-right btn']
                                       ]
                        ])
    ->display();
?>
<?php Themes::view('admin/views/partials/content-start')->display() ?>

<h3 class="no-data-message"><?= __('admin_menus_empty') ?></h3>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
