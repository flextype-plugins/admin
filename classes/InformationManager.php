<?php

namespace Flextype;

class InformationManager
{
    public static function getInformationPage()
    {
        Themes::view('admin/views/templates/system/information/list')->display();
    }
}
