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

use Flextype\Component\{Arr\Arr, Http\Http, Event\Event, Filesystem\Filesystem, Session\Session, Registry\Registry};
use Symfony\Component\Yaml\Yaml;

//
// Add listner for onCurrentPageBeforeProcessed event
//
if (Http::getUriSegment(0) == 'admin') {
    Event::addListener('onShortcodesInitialized', function () {
        Admin::instance();
    });
}


class Admin {

    /**
     * An instance of the Admin class
     *
     * @var object
     * @access  protected
     */
    protected static $instance = null;

    /**
     * Is logged in
     *
     * @var bool
     * @access  protected
     */
    protected static $isLoggedIn = false;

    /**
     * Protected clone method to enforce singleton behavior.
     *
     * @access  protected
     */
    protected function __clone()
    {
        // Nothing here.
    }

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        static::init();
    }

    protected static function init()
    {

        if (static::isLoggedIn()) {
            static::getAdminPage();
        } else {
            if (static::isUsersExists()) {
                static::getAuthPage();
            } else {
                static::getRegistrationPage();
            }
        }

        Http::requestShutdown();
    }

    protected static function getAdminPage()
    {
        switch (Http::getUriSegment(1)) {
            case 'pages':
                static::getPagesManagerPage();
            break;
            case 'logout':
                Session::destroy();
                Http::redirect(Http::getBaseUrl().'/admin/login');
            break;
            default:
                Http::redirect(Http::getBaseUrl().'/admin/pages');
            break;
        }
    }

    public static function addNavLink() {

    }

    protected static function getPagesManagerPage()
    {
        switch (Http::getUriSegment(2)) {
            case 'delete':
                if (Http::get('page') != '') {
                    Filesystem::deleteDir(PATH['pages'] . '/' . Http::get('page'));
                    Http::redirect(Http::getBaseUrl().'/admin/pages');
                }
            break;
            case 'add':
                $pages_list = Content::getPages('', false , 'slug');

                $create_page = Http::post('create_page');

                if (isset($create_page)) {
                    if (Filesystem::setFileContent(PATH['pages'] . '/' . Http::post('parent_page') . '/' . Http::post('slug') . '/page.html',
                                              '---'."\n".
                                              'title: '.Http::post('title')."\n".
                                              '---'."\n")) {

                                        Http::redirect(Http::getBaseUrl().'/admin/pages/');
                    }
                }

                Themes::view('admin/views/templates/pages/add')
                    ->assign('pages_list', $pages_list)
                    ->display();
            break;
            case 'edit':

                if (Http::get('expert') && Http::get('expert') == 'true') {

                    $save_page = Http::post('save_page_expert');

                    if (isset($save_page)) {

                        Filesystem::setFileContent(PATH['pages'] . '/' . Http::post('slug') . '/page.html',
                                                  Http::post('editor-codemirror'));
                    }

                    $page_content = Filesystem::getFileContent(PATH['pages'] . '/' . Http::get('page') . '/page.html');

                    Themes::view('admin/views/templates/pages/editor-expert')
                        ->assign('page_slug', Http::get('page'))
                        ->assign('page_content', $page_content)
                        ->display();
                } else {

                    $save_page = Http::post('save_page');

                    if (isset($save_page)) {

                        $page = Content::processPage(PATH['pages'] . '/' . Http::post('slug') . '/page.html');

                        Arr::set($page, 'title', Http::post('title'));
                        Arr::set($page, 'description', Http::post('description'));
                        Arr::set($page, 'visibility', Http::post('visibility'));
                        Arr::set($page, 'template', Http::post('template'));

                        Arr::delete($page, 'content'); // do not save 'content' into the frontmatter
                        Arr::delete($page, 'url');     // do not save 'url' into the frontmatter
                        Arr::delete($page, 'slug');    // do not save 'slug' into the frontmatter

                        $page_frontmatter = Yaml::dump($page);

                        Filesystem::setFileContent(PATH['pages'] . '/' . Http::post('slug') . '/page.html',
                                                  '---'."\n".
                                                  $page_frontmatter."\n".
                                                  '---'."\n".
                                                  Http::post('editor'));
                    }

                    $page = Content::processPage(PATH['pages'] . '/' . Http::get('page') . '/page.html');

                    Themes::view('admin/views/templates/pages/editor')
                        ->assign('page_slug', Http::get('page'))
                        ->assign('page_title', $page['title'])
                        ->assign('page_description', (isset($page['description']) ? $page['description'] : ''))
                        ->assign('page_template',(isset($page['temlate']) ? $page['template'] : ''))
                        ->assign('page_date',(isset($page['date']) ? $page['date'] : ''))
                        ->assign('page_visibility', (isset($page['visibility']) ? $page['visibility'] : ''))
                        ->assign('page_content', $page['content'])
                        ->display();
                }
            break;
            default:
                $pages_list = Content::getPages('', false , 'slug', 'ASC');

                Themes::view('admin/views/templates/pages/list')
                    ->assign('pages_list', $pages_list)
                    ->display();
            break;
        }
    }

    protected static function getAuthPage()
    {

        $login = Http::post('login');

        if (isset($login)) {
            if (Filesystem::fileExists($_user_file = PATH['site'] . '/accounts/' . Http::post('username') . '.yaml')) {
                $user_file = Yaml::parseFile($_user_file);
                Session::set('username', $user_file['username']);
                Session::set('role', $user_file['role']);

                Http::redirect(Http::getBaseUrl().'/admin/pages');
            }
        }

        Themes::view('admin/views/templates/auth/login')
            ->display();
    }

    protected static function getRegistrationPage()
    {

        $registration = Http::post('registration');

        if (isset($registration)) {
            if (Filesystem::fileExists($_user_file = PATH['site'] . '/accounts/' . Http::post('username') . '.yaml')) {

            } else {
                $user = ['username' => Http::post('username'),
                         'password' => Http::post('password'),
                         'email' => Http::post('email'),
                         'role'  => 'admin',
                         'state' => 'enabled'];

                Filesystem::setFileContent(PATH['site'] . '/accounts/' . Http::post('username') . '.yaml', Yaml::dump($user));

                Http::redirect(Http::getBaseUrl().'/admin/pages');
            }
        }

        Themes::view('admin/views/templates/auth/registration')
            ->display();
    }

    public static function isUsersExists()
    {
        $users = Filesystem::getFilesList(PATH['site'] . '/accounts/', 'yaml');

        if ($users && count($users) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function isLoggedIn()
    {
        if (Session::exists('role') && Session::get('role') == 'admin') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return the Admin instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new Admin();
    }
}
