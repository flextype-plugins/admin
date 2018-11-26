<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Filesystem\Filesystem, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links', ['information' => ['url' => Http::getBaseUrl() . '/admin/information', 'title' => __('admin_system')],
                           'information_server' => ['url' => Http::getBaseUrl() . '/admin/information?server=true', 'title' => __('admin_server')],
                           'information_security_check_results' => ['url' => Http::getBaseUrl() . '/admin/information?security_check_results=true', 'title' => __('admin_security_check_results'), 'class' => 'active']])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<?php if (Filesystem::isFileWritable(ROOT_DIR . '/.htaccess') or
          Filesystem::isFileWritable(ROOT_DIR . '/index.php') or
          Registry::get('system.errors.display') === true) { ?>

        <table class="table no-margin">
            <tbody>
                <?php if (Filesystem::isFileWritable(ROOT_DIR . '/.htaccess')) { ?>
                <tr>
                    <td><?php echo __('admin_security_check_results_htaccess', null, [':path' => ROOT_DIR . '/.htaccess']); ?></td>
                </tr>
                <?php } ?>
                <?php if (Filesystem::isFileWritable(ROOT_DIR . '/index.php')) { ?>
                <tr>
                    <td><?php echo __('admin_security_check_results_index', null, [':path' => ROOT_DIR . '/index.php']); ?></td>
                </tr>
                <?php } ?>
                <?php if (Registry::get('system.errors.display') === true) { ?>
                <tr>
                    <td><?php echo __('admin_security_check_results_debug'); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
<?php } ?>

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
