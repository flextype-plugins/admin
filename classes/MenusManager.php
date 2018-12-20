<?php

namespace Flextype;

use function Flextype\Component\I18n\__;
use Flextype\Component\Http\Http;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Token\Token;
use Flextype\Component\Arr\Arr;
use Flextype\Component\Notification\Notification;

class MenusManager
{
    public static function getMenusManagerPage()
    {
        // Setup active naviation item
        Registry::set('sidebar_menu_item', 'menus');


        switch (Http::getUriSegment(2)) {
            case 'delete_item':
                if (Http::get('item') && Http::get('category') && Http::get('token')) {
                    if (Token::check((Http::get('token')))) {
                        $menu_path = PATH['site'] . '/menus/' . Http::get('category') . '.yaml';

                        if (Filesystem::fileExists($menu_path)) {
                            $menu = YamlParser::decode(Filesystem::getFileContent($menu_path));
                            Arr::delete($menu, 'items.'.Http::get('item'));
                            Filesystem::setFileContent($menu_path, YamlParser::encode($menu));
                            Notification::set('success', __('admin_message_page_changes_saved'));
                            Http::redirect(Http::getBaseUrl().'/admin/menus');
                        } else {
                            // no
                        }


                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }
            break;
            default:
                $menus = [];
                $menus_list = [];

                $menus = Filesystem::getFilesList(PATH['site'] . '/menus', 'yaml');

                if (count($menus) > 0) {
                    foreach ($menus as $menu) {
                        $menus_list[basename($menu, '.yaml')] = YamlParser::decode(Filesystem::getFileContent($menu));
                    }
                }

                Themes::view('admin/views/templates/content/menus/list')
                    ->assign('menus_list', $menus_list)
                    ->display();
            break;
        }
    }
}
