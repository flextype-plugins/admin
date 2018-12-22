<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;

class TemplatesManager
{
    public static function getTemplatesManager()
    {
        Registry::set('sidebar_menu_item', 'templates');
        Themes::view('admin/views/templates/extends/templates/list')->display();
    }
}
