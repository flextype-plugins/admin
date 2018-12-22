<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;

class FieldsetsManager
{
    public static function getFieldsetsManager()
    {
        Registry::set('sidebar_menu_item', 'fieldsets');
        Themes::view('admin/views/templates/extends/fieldsets/list')->display();
    }
}
