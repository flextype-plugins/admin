<?php

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Number\Number;
use Flextype\Component\I18n\I18n;
use Flextype\Component\Http\Http;
use Flextype\Component\Event\Event;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Token\Token;
use Flextype\Component\Text\Text;
use Flextype\Component\Form\Form;
use Flextype\Component\Notification\Notification;
use function Flextype\Component\I18n\__;
use Symfony\Component\Yaml\Yaml;
use Gajus\Dindent\Indenter;

class SettingsManager
{
    public static function getSettingsPage()
    {
        Registry::set('sidebar_menu_item', 'settings');

        // Clear cache
        if (Http::get('clear_cache')) {
            if (Token::check((Http::get('token')))) {
                Cache::clear();
                Notification::set('success', __('admin_message_cache_files_deleted'));
                Http::redirect(Http::getBaseUrl().'/admin/settings');
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
            }
        }

        $action = Http::post('action');

        if (isset($action) && $action == 'save-form') {
            if (Token::check((Http::post('token')))) {

                $settings = $_POST;

                Arr::delete($settings, 'token');
                Arr::delete($settings, 'action');
                Arr::set($settings, 'errors.display', (Http::post('errors.display') == '1' ? true : false));
                Arr::set($settings, 'cache.enabled', (Http::post('cache.enabled') == '1' ? true : false));
                Arr::set($settings, 'cache.lifetime', (int) Http::post('cache.lifetime'));

                if (Filesystem::setFileContent(PATH['config']['site'] . '/settings.yaml', Yaml::dump(array_merge(Registry::get('settings'), $settings), 10, 2))) {
                    Notification::set('success', __('admin_message_settings_saved'));
                    Http::redirect(Http::getBaseUrl().'/admin/settings');
                }
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
            }
        }

        Themes::view('admin/views/templates/system/settings/list')
                ->assign('settings', Registry::get('settings'))
                ->assign('locales', Plugins::getLocales())
                ->display();
    }
}
