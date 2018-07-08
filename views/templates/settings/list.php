<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, I18n\I18n, Token\Token, Form\Form, Event\Event};
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links',   ['settings' => ['url' => Http::getBaseUrl() . '/admin/settings', 'title' => I18n::find('admin_settings_heading', 'admin', Registry::get('system.locale'))]])
        ->assign('buttons', ['settings' =>
                                            ['url' => Http::getBaseUrl() . '/admin/settings',
                                            'title' => I18n::find('admin_clear_cache', 'admin', Registry::get('system.locale')),
                                            'class' => 'btn-light']])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <?php echo Form::open('settings', ['name' => 'settingsSiteForm']); ?>
            <?php echo Form::hidden('token', Token::generate()); ?>
            <div class="card-header">
                admin_settings_site
            </div>
            <div class="card-body">
                <?php foreach($site_settings as $key => $value) { ?>
                    <?php if (!is_array($value)) { ?>
                        <div class="form-group">
                            <?php
                                echo (
                                    Form::label($key, I18n::find('admin_site_'.$key, 'admin', Registry::get('system.locale')), ['for' => 'site'.$key]).
                                    Form::input($key, $value, ['class' => 'form-control', 'id' => 'site'.$key])
                                );
                            ?>
                        </div>
                        <?php } else { ?>
                            <?php foreach ($value as $_key => $_value) { ?>
                                <div class="form-group">
                                    <?php
                                        echo (
                                            Form::label($key.'['.$_key.']', I18n::find('admin_site_'.$key.'_'.$_key, 'admin', Registry::get('system.locale')), ['for' => 'site'.$key.'_'.$_key]).
                                            Form::input($key.'['.$_key.']', $_value, ['class' => 'form-control', 'id' => 'site'.$key.'_'.$_key])
                                        );
                                    ?>
                                </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
             <div class="card-footer text-left">
                 <?php echo Form::submit('admin_settings_site_save', I18n::find('admin_save', 'admin', Registry::get('system.locale')), ['class' => 'btn btn-black']); ?>
             </div>
             <?php echo Form::close(); ?>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <?php echo Form::open('', ['name' => 'settingsSystemForm']); ?>
            <?php echo Form::hidden('token', Token::generate()); ?>
            <div class="card-header">
                admin_settings_system
            </div>
            <div class="card-body">
                <?php foreach($system_settings as $key => $value) { ?>
                    <?php if (!is_array($value)) { ?>
                        <div class="form-group">
                            <?php
                                echo (
                                    Form::label($key, I18n::find('admin_site_'.$key, 'admin', Registry::get('system.locale')), ['for' => 'site'.$key]).
                                    Form::input($key, $value, ['class' => 'form-control', 'id' => 'site'.$key])
                                );
                            ?>
                        </div>
                        <?php } else { ?>
                            <?php foreach ($value as $_key => $_value) { ?>
                                <?php if (is_bool($_value) === true) { ?>
                                    <div class="form-group">
                                        <?php
                                            echo (
                                                Form::label($key.'['.$_key.']', I18n::find('admin_site_'.$key.'_'.$_key, 'admin', Registry::get('system.locale')), ['for' => 'site'.$key.'_'.$_key]).
                                                Form::select($key.'['.$_key.']', ['true' => 'true', 'false' => 'false'], (($_value === true) ? 'true' : 'false'), ['class' => 'form-control', 'id' => 'site'.$key.'_'.$_key])
                                            );
                                        ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="form-group">
                                        <?php
                                            echo (
                                                Form::label($key.'['.$_key.']', I18n::find('admin_site_'.$key.'_'.$_key, 'admin', Registry::get('system.locale')), ['for' => 'site'.$key.'_'.$_key]).
                                                Form::input($key.'['.$_key.']', $_value, ['class' => 'form-control', 'id' => 'site'.$key.'_'.$_key])
                                            );
                                        ?>
                                    </div>
                                <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="card-footer text-left">
                <?php echo Form::submit('admin_settings_system_save', I18n::find('admin_save', 'admin', Registry::get('system.locale')), ['class' => 'btn btn-black']); ?>
            </div>
            <?php echo Form::close(); ?>
        </div>
    </div>
</div>

<?php

Event::addListener('onAdminThemeFooter', function () {
    echo("
    <script>
            $(document).ready(function() {
                    $('.settings-save').click(function() {
                        $('[name=settingsForm]').submit();
                    });
            });
    </script>
    ");
});

?>


<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
