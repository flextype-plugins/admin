<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Filesystem\Filesystem, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links',   ['information' => ['url' => Http::getBaseUrl() . '/admin/information', 'title' => __('admin_information_heading')]])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<div class="card">
    <div class="card-header">
        <?php echo __('admin_system'); ?>
    </div>
    <div class="card-body no-padding">
        <table class="table no-margin">
            <tbody>
                <tr>
                    <td><?php echo __('admin_flextype_version'); ?></td>
                    <td><?php echo Flextype::VERSION; ?></td>
                </tr>
                <tr>
                    <td><?php echo __('admin_debugging'); ?></td>
                    <td><?php if (Registry::get('system.errors.display')) echo __('admin_on'); else echo __('admin_off'); ?></td>
                </tr>
                <tr>
                    <td><?php echo __('admin_cache'); ?></td>
                    <td><?php if (Registry::get('system.cache.enabled')) echo __('admin_on'); else echo __('admin_off'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <?php echo __('admin_server'); ?>
    </div>
    <div class="card-body no-padding">
        <table class="table no-margin">
            <tbody>
                <tr>
                    <td><?php echo __('admin_php_version'); ?></td>
                    <td><?php echo PHP_VERSION; ?></td>
                </tr>
                <tr>
                    <td><?php echo __('admin_php_built_on'); ?></td>
                    <td><?php echo php_uname(); ?></td>
                </tr>
                <tr>
                    <td><?php echo __('admin_web_server'); ?></td>
                    <td><?php echo (isset($_SERVER['SERVER_SOFTWARE'])) ? $_SERVER['SERVER_SOFTWARE'] : @getenv('SERVER_SOFTWARE'); ?></td>
                </tr>
                <tr>
                    <td><?php echo __('admin_web_server_php_interface'); ?></td>
                    <td><?php echo php_sapi_name(); ?></td>
                </tr>
                <?php
                    if (function_exists('apache_get_modules')) {
                        if ( ! in_array('mod_rewrite',apache_get_modules())) {
                            echo '<tr><td>'.'Apache Mod Rewrite'.'</td><td>'.__('admin_not_installed').'</td></tr>';
                        } else {
                            echo '<tr><td>'.'Apache Mod Rewrite'.'</td><td>'.__('admin_installed').'</td></tr>';
                        }
                    } else {
                        echo '<tr><td>'.'Apache Mod Rewrite'.'</td><td>'.__('admin_installed').'</td></tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (Filesystem::isFileWritable(ROOT_DIR . '/.htaccess') or
          Filesystem::isFileWritable(ROOT_DIR . '/index.php') or
          Registry::get('system.errors.display') === true) { ?>
<div class="card">
    <div class="card-header">
        <?php echo __('admin_security_check_results'); ?>
    </div>
    <div class="card-body no-padding">
        <table class="table no-margin">
            <tbody>
                <?php if (Filesystem::isFileWritable(ROOT_DIR . '/.htaccess')) { ?>
                <tr>
                    <td><?php echo __('admin_security_check_results_htaccess', [':path' => ROOT_DIR . '/.htaccess']); ?></td>
                </tr>
                <?php } ?>
                <?php if (Filesystem::isFileWritable(ROOT_DIR . '/index.php')) { ?>
                <tr>
                    <td><?php echo __('admin_security_check_results_index', [':path' => ROOT_DIR . '/index.php']); ?></td>
                </tr>
                <?php } ?>
                <?php if (Registry::get('system.errors.display') === true) { ?>
                <tr>
                    <td><?php echo __('admin_security_check_results_debug'); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
