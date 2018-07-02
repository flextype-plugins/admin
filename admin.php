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

use Flextype\Component\{Arr\Arr, Http\Http, Event\Event, Filesystem\Filesystem, Session\Session, Registry\Registry, Token\Token, Text\Text};
use Symfony\Component\Yaml\Yaml;

//
// Add listner for onCurrentPageBeforeProcessed event
//
if (Http::getUriSegment(0) == 'admin') {
    Event::addListener('onCurrentPageBeforeLoaded', function () {
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
    private function __clone() { }

    /**
     * Private wakeup method to enforce singleton behavior.
     *
     * @access private
     */
    private function __wakeup() { }

    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    protected function __construct()
    {
        Admin::init();
    }

    protected static function init()
    {
        if (Admin::isLoggedIn()) {
            Admin::getAdminArea();
        } else {
            if (Admin::isUsersExists()) {
                Admin::getAuthPage();
            } else {
                Admin::getRegistrationPage();
            }
        }

        Http::requestShutdown();
    }

    protected static function getAdminArea()
    {
        Http::getUriSegment(1) == ''       and Admin::getDashboard();
        Http::getUriSegment(1) == 'pages'  and Admin::getPagesManagerPage();
        Http::getUriSegment(1) == 'logout' and Admin::logout();

        // Event: onAdminArea
        Event::dispatch('onAdminArea');
    }

    protected static function logout()
    {
        if (Token::check((Http::get('token')))) {
            Session::destroy();
            Http::redirect(Http::getBaseUrl().'/admin');
        }
    }

    protected static function getDashboard() {
        Http::redirect(Http::getBaseUrl().'/admin/pages');
    }

    protected static function getPagesManagerPage()
    {
        switch (Http::getUriSegment(2)) {
            case 'delete':
                if (Http::get('page') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::deleteDir(PATH['pages'] . '/' . Http::get('page'));
                        Http::redirect(Http::getBaseUrl().'/admin/pages');
                    }
                }
            break;
            case 'add':
                $pages_list = Content::getPages('', false , 'slug');

                $create_page = Http::post('create_page');

                if (isset($create_page)) {
                    if (Token::check((Http::post('token')))) {
                        if (Filesystem::setFileContent(PATH['pages'] . '/' . Http::post('parent_page') . '/' . Text::safeString(Http::post('slug')) . '/page.html',
                                                  '---'."\n".
                                                  'title: '.Http::post('title')."\n".
                                                  '---'."\n")) {

                                            Http::redirect(Http::getBaseUrl().'/admin/pages/');
                        }
                    } else { die('Request was denied because it contained an invalid security token. Please refresh the page and try again.'); }
                }

                Themes::view('admin/views/templates/pages/add')
                    ->assign('pages_list', $pages_list)
                    ->display();
            break;
            case 'rename';
                $rename_page = Http::post('rename_page');

                if (isset($rename_page)) {
                    if (Token::check((Http::post('token')))) {

                        $page = Content::processPage(PATH['pages'] . '/' . Http::post('page_path_current') . '/page.html');

                        Arr::set($page, 'title', Http::post('title'));
                        $content = Arr::get($page, 'content');
                        Arr::delete($page, 'content'); // do not save 'content' into the frontmatter

                        $page_frontmatter = Yaml::dump($page);

                        $page_path_current = PATH['pages'] . '/' . Http::post('page_path_current') . '/page.html';
                        $page_new_current = PATH['pages'] . '/' . (Http::post('parent_page') == '/' ? '' : '/') . Http::post('slug') . '/page.html';

                        Filesystem::setFileContent($page_path_current,
                                                  '---'."\n".
                                                  $page_frontmatter."\n".
                                                  '---'."\n".
                                                  $content);


                        $path = pathinfo($page_new_current);

                        if (!file_exists($path['dirname'])) {
                            mkdir($path['dirname'], 0777, true);
                        }

                        if (Filesystem::copyFile($page_path_current, $page_new_current)) {
                            Filesystem::deleteFile($page_path_current);
                        }

                        Http::redirect(Http::getBaseUrl().'/admin/pages');

                    } else { die('Request was denied because it contained an invalid security token. Please refresh the page and try again.'); }
                }

                $_pages_list = Content::getPages('', false , 'slug');
                $pages_list['/'] = '/';
                foreach ($_pages_list as $_page) {
                    if ($_page['slug'] != '') {
                        $pages_list[$_page['slug']] = $_page['slug'];
                    } else {
                        $pages_list[Registry::get('system.pages.main')] = Registry::get('system.pages.main');
                    }
                }

                Themes::view('admin/views/templates/pages/rename')
                    ->assign('page_name', Arr::last(explode("/", Http::get('page'))))
                    ->assign('page_title', Content::processPage(PATH['pages'] . '/' . Http::get('page') . '/page.html')['title'])
                    ->assign('page_parent', implode('/', array_slice(explode("/", Http::get('page')), 0, -1)))
                    ->assign('page_path_current', Http::get('page'))
                    ->assign('pages_list', $pages_list)
                    ->display();
            break;
            case 'edit':

                if (Http::get('expert') && Http::get('expert') == 'true') {

                    $save_page = Http::post('save_page_expert');

                    if (isset($save_page)) {
                        if (Token::check((Http::post('token')))) {
                            Filesystem::setFileContent(PATH['pages'] . '/' . Http::post('slug') . '/page.html',
                                                      Http::post('editor-codemirror'));

                            Http::redirect(Http::getBaseUrl().'/admin/pages');

                        } else { die('Request was denied because it contained an invalid security token. Please refresh the page and try again.'); }
                    }

                    $page_content = Filesystem::getFileContent(PATH['pages'] . '/' . Http::get('page') . '/page.html');

                    Themes::view('admin/views/templates/pages/editor-expert')
                        ->assign('page_slug', Http::get('page'))
                        ->assign('page_content', $page_content)
                        ->display();
                } else {

                    $save_page = Http::post('save_page');

                    if (isset($save_page)) {
                        if (Token::check((Http::post('token')))) {

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

                            Http::redirect(Http::getBaseUrl().'/admin/pages');

                        } else { die('Request was denied because it contained an invalid security token. Please refresh the page and try again.'); }
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
            if (Token::check((Http::post('token')))) {
                if (Filesystem::fileExists($_user_file = PATH['site'] . '/accounts/' . Http::post('username') . '.yaml')) {
                    $user_file = Yaml::parseFile($_user_file);

                    if (Text::encryptString(Http::post('password') == $user_file['password'])) {
                        Session::set('username', $user_file['username']);
                        Session::set('role', $user_file['role']);
                        Http::redirect(Http::getBaseUrl().'/admin/pages');
                    }
                }
            } else { die('Request was denied because it contained an invalid security token. Please refresh the page and try again.'); }
        }

        Themes::view('admin/views/templates/auth/login')
            ->display();
    }

    protected static function getRegistrationPage()
    {

        $registration = Http::post('registration');

        if (isset($registration)) {
            if (Token::check((Http::post('token')))) {
                if (Filesystem::fileExists($_user_file = PATH['site'] . '/accounts/' . Text::safeString(Http::post('username')) . '.yaml')) {

                } else {
                    $user = ['username' => Text::safeString(Http::post('username')),
                             'password' => Text::encryptString(Http::post('password')),
                             'email' => Http::post('email'),
                             'role'  => 'admin',
                             'state' => 'enabled'];

                    Filesystem::setFileContent(PATH['site'] . '/accounts/' . Http::post('username') . '.yaml', Yaml::dump($user));

                    Http::redirect(Http::getBaseUrl().'/admin/pages');
                }
            } else { die('Request was denied because it contained an invalid security token. Please refresh the page and try again.'); }
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
