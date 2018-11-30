<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use function Flextype\Component\I18n\__;
use Flextype\Navigation;

class DashboardManager
{
    public static function getDashboard()
    {
        Http::redirect(Http::getBaseUrl().'/admin/pages');
    }
}
