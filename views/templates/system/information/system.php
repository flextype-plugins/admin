<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Filesystem\Filesystem, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links', ['information' => ['url' => Http::getBaseUrl() . '/admin/information', 'title' => __('admin_system'), 'class' => 'active'],
                           'information_server' => ['url' => Http::getBaseUrl() . '/admin/information?server=true', 'title' => __('admin_server')],
                           'information_security_check_results' => ['url' => Http::getBaseUrl() . '/admin/information?security_check_results=true', 'title' => __('admin_security_check_results')]])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

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

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
