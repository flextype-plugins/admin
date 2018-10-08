<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, I18n\I18n, Token\Token, Form\Form, Event\Event};
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links',   ['settings' => ['url' => Http::getBaseUrl() . '/admin/settings', 'title' => I18n::find('admin_settings_heading', Registry::get('system.locale'))]])
        ->assign('buttons', ['settings' =>
                                            ['url' => Http::getBaseUrl() . '/admin/settings',
                                            'title' => I18n::find('admin_clear_cache', Registry::get('system.locale')),
                                            'class' => 'btn-light']])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">
    <div class="col-md-6">
        <?php Formgenerator::display(Registry::get('plugins.admin.forms.site_settings'), $site_settings); ?>
    </div>

<pre>
<?php
    //print_r($site_settings);
?>
<?php
    //print_r($system_settings);
?>
</pre>

    <div class="col-md-6">
        <?php Formgenerator::display(Registry::get('plugins.admin.forms.system_settings'), $system_settings); ?>
    </div>
</div>

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
