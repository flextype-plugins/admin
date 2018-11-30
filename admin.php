<?php

namespace Flextype;

/**
 *
 * Flextype Admin Plugin
 *
 * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

//
// Add listner for onCurrentPageBeforeLoaded event
//
if (Admin::isAdminArea()) {
    Event::addListener('onCurrentPageBeforeLoaded', function () {

        // Ensure vendor libraries exist
        !is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

        // Register The Auto Loader
        $loader = require_once $autoload;

        NavigationManager::addItem('content', 'pages', __('admin_menu_content_pages', Registry::get('system.locale')), Http::getBaseUrl() . '/admin/pages', ['class' => 'nav-link']);
        NavigationManager::addItem('extends', 'plugins', __('admin_menu_extends_plugins', Registry::get('system.locale')), Http::getBaseUrl() . '/admin/plugins', ['class' => 'nav-link']);
        NavigationManager::addItem('settings', 'infomation', __('admin_menu_system_information', Registry::get('system.locale')), Http::getBaseUrl() . '/admin/information', ['class' => 'nav-link']);
        NavigationManager::addItem('settings', 'settings', __('admin_menu_system_settings', Registry::get('system.locale')), Http::getBaseUrl() . '/admin/settings', ['class' => 'nav-link']);
        NavigationManager::addItem('help', 'documentation', __('admin_menu_help_documentation', Registry::get('system.locale')), 'http://flextype.org/documentation', ['class' => 'nav-link', 'target' => '_blank']);
        NavigationManager::addItem('help', 'documentation', __('admin_menu_help_documentation', Registry::get('system.locale')), 'http://flextype.org/documentation', ['class' => 'nav-link', 'target' => '_blank']);

        Admin::getInstance();
    });
}

class Admin
{
    /**
     * An instance of the Admin class
     *
     * @var object
     * @access private
     */
    private static $instance = null;

    /**
     * Private clone method to enforce singleton behavior.
     *
     * @access private
     */
    private function __clone()
    {
    }

    /**
     * Private wakeup method to enforce singleton behavior.
     *
     * @access private
     */
    private function __wakeup()
    {
    }

    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    protected function __construct()
    {
        Admin::init();
    }

    /**
     * Init Flextype Admin
     *
     * @access private
     */
    private static function init() : void
    {
        // Set Default Admin locale
        I18n::$locale = Registry::get('system.locale');

        if (UsersManager::isLoggedIn()) {
            Admin::getAdminArea();
        } else {
            if (UsersManager::isUsersExists()) {
                UsersManager::getAuthPage();
            } else {
                UsersManager::getRegistrationPage();
            }
        }

        // Event: onBeforeRequestShutdown
        Event::dispatch('onBeforeRequestShutdown');

        Http::requestShutdown();
    }


    /**
     * Flextype Admin Area
     *
     * @access private
     */
    private static function getAdminArea() : void
    {

        // Event: onAdminArea
        Event::dispatch('onAdminArea');

        // Route the Uri
        Http::getUriSegment(1) == ''             and DashboardManager::getDashboard();
        Http::getUriSegment(1) == 'pages'        and PagesManager::getPagesManagerPage();
        Http::getUriSegment(1) == 'plugins'      and PluginsManager::getPluginsPage();
        Http::getUriSegment(1) == 'information'  and InformationManager::getInformationPage();
        Http::getUriSegment(1) == 'settings'     and SettingsManager::getSettingsPage();
        Http::getUriSegment(1) == 'logout'       and UsersManager::logout();
    }


    /**
     * Check is Admin Area
     *
     * @access public
     */
    public static function isAdminArea()
    {
        return (Http::getUriSegment(0) == 'admin') ? true : false;
    }

    /**
     * Get the Admin instance.
     *
     * @access public
     * @return object
     */
    public static function getInstance()
    {
        if (is_null(Admin::$instance)) {
            Admin::$instance = new self;
        }

        return Admin::$instance;
    }
}
