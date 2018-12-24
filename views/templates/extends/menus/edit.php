<?php
namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Form\Form;
use Flextype\Component\Html\Html;
use Flextype\Component\Token\Token;
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links', [
                       'menu' => [
                                        'link' => Http::getBaseUrl() . '/admin/menus/edit?menu=' . Http::get('menu'),
                                        'title' => __('admin_menu_heading'),
                                        'attributes' => ['class' => 'navbar-item active']
                                      ]
                      ])
  ->assign('buttons', [
                          'save_entry' => [
                                              'link'       => 'javascript:;',
                                              'title'      => __('admin_save'),
                                              'attributes' => ['class' => 'js-save-form-submit float-right btn']
                                          ]
                      ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">
    <div class="col-md-6">
        <?= Form::open(null, ['id' => 'form']) ?>
        <?= Form::hidden('token', Token::generate()) ?>
        <?= Form::hidden('action', 'save-form') ?>
        <?= Form::hidden('name', Http::get('menu')) ?>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <?= Form::textarea('menu', $menu, ['class' => 'form-control', 'style' => 'min-height:500px;', 'id' => 'codeMirrorEditor']) ?>
                </div>
            </div>
        </div>
        <?= Form::close() ?>
    </div>
</div>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
