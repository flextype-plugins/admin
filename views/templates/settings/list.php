<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, I18n\I18n, Token\Token};
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links',   ['settings' => ['url' => Http::getBaseUrl() . '/admin/settings', 'title' => I18n::find('admin_settings_heading', 'admin', Registry::get('system.locale'))]])
        ->assign('buttons', ['settings' => ['url' => Http::getBaseUrl() . '/admin/settings',
                                            'title' => I18n::find('admin_save', 'admin', Registry::get('system.locale')),
                                            'class' => 'settings-save'],
                                           ['url' => Http::getBaseUrl() . '/admin/settings',
                                            'title' => I18n::find('admin_clear_cache', 'admin', Registry::get('system.locale')),
                                            'class' => 'btn-light']])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                admin_settings_site
            </div>
            <div class="card-body">

            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                admin_settings_system
            </div>
            <div class="card-body">
                
            </div>
        </div>
    </div>

</div>

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
