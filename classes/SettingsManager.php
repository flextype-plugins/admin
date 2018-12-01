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
        $settings_site_save = Http::post('settings_site_save');
        $settings_system_save = Http::post('settings_system_save');

        // Clear cache
        if (Http::get('clear_cache')) {
            if (Token::check((Http::get('token')))) {
                Cache::clear();
                Notification::set('success', __('message_cache_files_deleted'));
                Http::redirect(Http::getBaseUrl().'/admin/settings');
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
            }
        }

        if (isset($settings_site_save)) {
            if (Token::check((Http::post('token')))) {
                Arr::delete($_POST, 'token');
                Arr::delete($_POST, 'settings_site_save');

                if (Filesystem::setFileContent(PATH['config'] . '/' . 'site.yaml', Yaml::dump($_POST))) {
                    Http::redirect(Http::getBaseUrl().'/admin/settings');
                }
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
            }
        }

        if (isset($settings_system_save)) {
            if (Token::check((Http::post('token')))) {
                Arr::delete($_POST, 'token');
                Arr::delete($_POST, 'settings_system_save');

                Arr::set($_POST, 'errors.display', (Http::post('errors.display') == '1' ? true : false));
                Arr::set($_POST, 'cache.enabled', (Http::post('cache.enabled') == '1' ? true : false));
                Arr::set($_POST, 'cache.lifetime', (int) Http::post('cache.lifetime'));

                if (Filesystem::setFileContent(PATH['config'] . '/' . 'system.yaml', Yaml::dump($_POST))) {
                    Http::redirect(Http::getBaseUrl().'/admin/settings');
                }
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
            }
        }

        $site_settings = [];
        $system_settings = [];

        // Set site items if site config exists
        if (Filesystem::fileExists($site_config = PATH['config'] . '/' . 'site.yaml')) {
            $site_settings = Yaml::parseFile($site_config);
        } else {
            throw new \RuntimeException("Flextype site config file does not exist.");
        }

        // Set site items if system config exists
        if (Filesystem::fileExists($system_config = PATH['config'] . '/' . 'system.yaml')) {
            $system_settings = Yaml::parseFile($system_config);
        } else {
            throw new \RuntimeException("Flextype system config file does not exist.");
        }

        Themes::view('admin/views/templates/system/settings/list')
                ->assign('site_settings', $site_settings)
                ->assign('system_settings', $system_settings)
                ->assign('locales', Plugins::getLocales())
                ->display();
    }
}
