<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, I18n\I18n, Token\Token};
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links',   ['information' => ['url' => Http::getBaseUrl() . '/admin/information', 'title' => I18n::find('admin_information_heading', Registry::get('system.locale'))]])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<div class="card">
    <div class="card-header">
        <?php echo I18n::find('admin_system', Registry::get('system.locale')); ?>
    </div>
    <div class="card-body no-padding">
        <table class="table no-margin">
            <tbody>
                <tr>
                    <td><?php echo I18n::find('admin_flextype_version', Registry::get('system.locale')); ?></td>
                    <td><?php echo Flextype::VERSION; ?></td>
                </tr>
                <tr>
                    <td><?php echo I18n::find('admin_debugging', Registry::get('system.locale')); ?></td>
                    <td><?php if (Registry::get('system.errors.display')) echo I18n::find('admin_on', Registry::get('system.locale')); else echo I18n::find('admin_off', Registry::get('system.locale')); ?></td>
                </tr>
                <tr>
                    <td><?php echo I18n::find('admin_cache', Registry::get('system.locale')); ?></td>
                    <td><?php if (Registry::get('system.cache.enabled')) echo I18n::find('admin_on', Registry::get('system.locale')); else echo I18n::find('admin_off', Registry::get('system.locale')); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <?php echo I18n::find('admin_server', Registry::get('system.locale')); ?>
    </div>
    <div class="card-body no-padding">
        <table class="table no-margin">
            <tbody>
                <tr>
                    <td><?php echo I18n::find('admin_php_version', Registry::get('system.locale')); ?></td>
                    <td><?php echo PHP_VERSION; ?></td>
                </tr>
                <tr>
                    <td><?php echo I18n::find('admin_php_built_on', Registry::get('system.locale')); ?></td>
                    <td><?php echo php_uname(); ?></td>
                </tr>
                <tr>
                    <td><?php echo I18n::find('admin_web_server', Registry::get('system.locale')); ?></td>
                    <td><?php echo (isset($_SERVER['SERVER_SOFTWARE'])) ? $_SERVER['SERVER_SOFTWARE'] : @getenv('SERVER_SOFTWARE'); ?></td>
                </tr>
                <tr>
                    <td><?php echo I18n::find('admin_web_server_php_interface', Registry::get('system.locale')); ?></td>
                    <td><?php echo php_sapi_name(); ?></td>
                </tr>
                <?php
                    if (function_exists('apache_get_modules')) {
                        if ( ! in_array('mod_rewrite',apache_get_modules())) {
                            echo '<tr><td>'.'Apache Mod Rewrite'.'</td><td>'.I18n::find('admin_not_installed', Registry::get('system.locale')).'</td></tr>';
                        } else {
                            echo '<tr><td>'.'Apache Mod Rewrite'.'</td><td>'.I18n::find('admin_installed', Registry::get('system.locale')).'</td></tr>';
                        }
                    } else {
                        echo '<tr><td>'.'Apache Mod Rewrite'.'</td><td>'.I18n::find('admin_installed', Registry::get('system.locale')).'</td></tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
