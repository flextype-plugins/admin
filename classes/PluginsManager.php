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
use function Flextype\Component\I18n\__;
use Symfony\Component\Yaml\Yaml;
use Gajus\Dindent\Indenter;
use Flextype\Navigation;

class PluginsManager
{

    /**
     * _pluginsChangeStatusAjax
     */
    public static function _pluginsChangeStatusAjax()
    {
        if (Http::post('plugin_change_status')) {
            if (Token::check((Http::post('token')))) {
                $plugin_settings = Yaml::parseFile(PATH['plugins'] . '/' . Http::post('plugin')  . '/' . 'settings.yaml');

                Arr::set($plugin_settings, 'enabled', (Http::post('status') == 'true' ? true : false));

                Filesystem::setFileContent(PATH['plugins'] . '/' . Http::post('plugin')  . '/' . 'settings.yaml', Yaml::dump($plugin_settings));

                Cache::clear();
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
            }
        }
    }

    public static function getPluginsPage()
    {
        Event::addListener('onBeforeRequestShutdown', function () {
            PluginsManager::_pluginsChangeStatusAjax();
        });

        Themes::view('admin/views/templates/extends/plugins/list')
            ->assign('plugins_list', Registry::get('plugins'))
            ->display();
    }
}
