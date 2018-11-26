<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Filesystem\Filesystem, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links', ['information' => ['url' => Http::getBaseUrl() . '/admin/information', 'title' => __('admin_system')],
                           'information_server' => ['url' => Http::getBaseUrl() . '/admin/information?server=true', 'title' => __('admin_server'), 'class' => 'active'],
                           'information_security_check_results' => ['url' => Http::getBaseUrl() . '/admin/information?security_check_results=true', 'title' => __('admin_security_check_results')]])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

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

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
