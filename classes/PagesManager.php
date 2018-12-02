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

class PagesManager
{
    public static function getPagesManagerPage()
    {
        switch (Http::getUriSegment(2)) {
            case 'delete':
                if (Http::get('page') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::deleteDir(PATH['pages'] . '/' . Http::get('page'));
                        Http::redirect(Http::getBaseUrl().'/admin/pages');
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }
            break;
            case 'add':
                $create_page = Http::post('create_page');

                if (isset($create_page)) {
                    if (Token::check((Http::post('token')))) {
                        $file = PATH['pages'] . '/' . Http::post('parent_page') . '/' . Text::safeString(Http::post('slug'), '-', true) . '/page.html';
                        if (!Filesystem::fileExists($file)) {
                            if (Filesystem::setFileContent($file,
                                                              '---'."\n".
                                                              'title: '.Http::post('title')."\n".
                                                              '---'."\n")) {
                                Http::redirect(Http::getBaseUrl().'/admin/pages/');
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }

                Themes::view('admin/views/templates/content/pages/add')
                    ->assign('pages_list', Content::getPages('', false, 'slug'))
                    ->display();
            break;
            case 'clone':
                if (Http::get('page') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::recursiveCopy(PATH['pages'] . '/' . Http::get('page'), PATH['pages'] . '/' . Http::get('page') . '-clone-' . date("Ymd_His"));
                        Http::redirect(Http::getBaseUrl().'/admin/pages/');
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }
            break;
            case 'rename':
                $rename_page = Http::post('rename_page');

                if (isset($rename_page)) {
                    if (Token::check((Http::post('token')))) {

                        if (!Filesystem::dirExists(PATH['pages'] . '/' . Http::post('name'))) {
                            if (rename(PATH['pages'] . '/' . Http::post('page_path_current'),
                                       PATH['pages'] . '/' . Http::post('page_parent') . '/' . Http::post('name'))) {
                                Http::redirect(Http::getBaseUrl().'/admin/pages');
                            }
                        }

                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }

                Themes::view('admin/views/templates/content/pages/rename')
                    ->assign('name_current', Arr::last(explode("/", Http::get('page'))))
                    ->assign('page_path_current', Http::get('page'))
                    ->assign('page_parent', implode('/', array_slice(explode("/", Http::get('page')), 0, -1)))
                    ->display();
            break;
            case 'move':
                $move_page = Http::post('move_page');

                if (isset($move_page)) {
                    if (Token::check((Http::post('token')))) {
                        if (!Filesystem::dirExists(realpath(PATH['pages'] . '/' . Http::post('parent_page') . '/' . Http::post('name_current')))) {
                            if (rename(PATH['pages'] . '/' . Http::post('page_path_current'),
                                       PATH['pages'] . '/' . Http::post('parent_page') . '/' . Http::post('name_current'))) {
                                Http::redirect(Http::getBaseUrl().'/admin/pages');
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }

                $_pages_list = Content::getPages('', false, 'slug');
                $pages_list['/'] = '/';
                foreach ($_pages_list as $_page) {
                    if ($_page['slug'] != '') {
                        $pages_list[$_page['slug']] = $_page['slug'];
                    } else {
                        $pages_list[Registry::get('system.pages.main')] = Registry::get('system.pages.main');
                    }
                }

                Themes::view('admin/views/templates/content/pages/move')
                    ->assign('page_path_current', Http::get('page'))
                    ->assign('pages_list', $pages_list)
                    ->assign('name_current', Arr::last(explode("/", Http::get('page'))))
                    ->assign('page_parent', implode('/', array_slice(explode("/", Http::get('page')), 0, -1)))
                    ->display();
            break;
            case 'edit':
                if (Http::get('media') && Http::get('media') == 'true') {
                    PagesManager::processFilesManager();
                    Themes::view('admin/views/templates/content/pages/media')
                        ->assign('page_name', Http::get('page'))
                        ->assign('files', PagesManager::getMediaList(Http::get('page')), true)
                        ->display();
                } elseif (Http::get('blueprint') && Http::get('blueprint') == 'true') {

                    $action = Http::post('action');

                    if (isset($action) && $action == 'save-blueprint') {

                        if (Token::check((Http::post('token')))) {
                            Filesystem::setFileContent(
                                PATH['themes'] . '/' . Registry::get('system.theme') . '/blueprints/' . Http::get('blueprint_name') . '.yaml',
                                Http::post('blueprint')
                            );


                            Http::redirect(Http::getBaseUrl().'/admin/pages/edit?page='.Http::post('page_name').'&blueprint=true&blueprint_name='.Http::get('blueprint_name'));
                        } else {
                            die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                        }
                    }

                    $blueprint = Filesystem::getFileContent(PATH['themes'] . '/' . Registry::get('system.theme') . '/blueprints/' . Http::get('blueprint_name') . '.yaml');

                    Themes::view('admin/views/templates/content/pages/blueprint')
                        ->assign('page_name', Http::get('page'))
                        ->assign('blueprint_name', Http::get('blueprint_name'))
                        ->assign('blueprint', $blueprint)
                        ->display();
                } elseif (Http::get('template') && Http::get('template') == 'true') {

                    $action = Http::post('action');

                    if (isset($action) && $action == 'save-template') {

                        if (Token::check((Http::post('token')))) {
                            Filesystem::setFileContent(
                                PATH['themes'] . '/' . Registry::get('system.theme') . '/views/templates/' . Http::get('template_name') . '.php',
                                Http::post('template')
                            );


                            Http::redirect(Http::getBaseUrl().'/admin/pages/edit?page='.Http::post('page_name').'&template=true&template_name='.Http::get('template_name'));
                        } else {
                            die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                        }
                    }

                    $template = Filesystem::getFileContent(PATH['themes'] . '/' . Registry::get('system.theme') . '/views/templates/' . Http::get('template_name') . '.php');

                    Themes::view('admin/views/templates/content/pages/template')
                        ->assign('page_name', Http::get('page'))
                        ->assign('template_name', Http::get('template_name'))
                        ->assign('template', $template)
                        ->display();
                } else {
                    if (Http::get('expert') && Http::get('expert') == 'true') {

                        $action = Http::post('action');

                        if (isset($action) && $action == 'edit-page-expert') {
                            if (Token::check((Http::post('token')))) {
                                Filesystem::setFileContent(
                                    PATH['pages'] . '/' . Http::post('page_name') . '/page.html',
                                                          Http::post('page_content')
                                );


                                Http::redirect(Http::getBaseUrl().'/admin/pages/edit?page='.Http::post('page_name').'&expert=true');
                            } else {
                                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                            }
                        }

                        $page_content = Filesystem::getFileContent(PATH['pages'] . '/' . Http::get('page') . '/page.html');

                        Themes::view('admin/views/templates/content/pages/editor-expert')
                            ->assign('page_name', Http::get('page'))
                            ->assign('page_content', $page_content)
                            ->assign('files', PagesManager::getMediaList(Http::get('page')), true)
                            ->display();
                    } else {
                        $page = Content::processPage(PATH['pages'] . '/' . Http::get('page') . '/page.html', false, true);
                        $action = Http::post('save');
                        $indenter = new Indenter();

                        if (isset($action)) {
                            if (Token::check((Http::post('token')))) {
                                $page = Content::processPage(PATH['pages'] . '/' . Http::get('page') . '/page.html', false, true);
                                Arr::delete($page, 'content');
                                Arr::delete($page, 'url');
                                Arr::delete($page, 'slug');

                                $frontmatter = $_POST;
                                Arr::delete($frontmatter, 'token');
                                Arr::delete($frontmatter, 'save');
                                Arr::delete($frontmatter, 'content');
                                $frontmatter = Yaml::dump(array_merge($page, $frontmatter), 10, 4);

                                $content = Http::post('content');
                                $content = (isset($content)) ? $indenter->indent($content) : '';

                                Filesystem::setFileContent(
                                    PATH['pages'] . '/' . Http::get('page') . '/page.html',
                                                          '---'."\n".
                                                          $frontmatter."\n".
                                                          '---'."\n".
                                                          $content
                                );

                                Http::redirect(Http::getBaseUrl().'/admin/pages/edit?page='.Http::get('page'));
                            }
                        }

                        Themes::view('admin/views/templates/content/pages/editor')
                            ->assign('page_name', Http::get('page'))
                            ->assign('page', $page)
                            ->assign('blueprint_name', $page['template'])
                            ->assign('template_name', $page['template'])
                            ->assign('blueprint', Yaml::parse(Filesystem::getFileContent(PATH['themes'] . '/' . Registry::get('system.theme') . '/blueprints/' . $page['template'] . '.yaml')))
                            ->assign('templates', PagesManager::getTemplatesList())
                            ->assign('files', PagesManager::getMediaList(Http::get('page')), true)
                            ->display();
                    }
                }
            break;
            default:
                Themes::view('admin/views/templates/content/pages/list')
                    ->assign('pages_list', Content::getPages('', false, 'slug', 'ASC'))
                    ->display();
            break;
        }
    }

    public static function getTemplatesList()
    {
        $_templates = Filesystem::getFilesList(PATH['themes'] . '/' . Registry::get('system.theme') . '/views/templates/', 'php');
        foreach ($_templates as $template) {
            if (!is_bool(PagesManager::_strrevpos($template, '/templates/'))) {
                $_t = str_replace('.php', '', substr($template, PagesManager::_strrevpos($template, '/templates/')+strlen('/templates/')));
                $templates[$_t] = $_t;
            }
        }
        return $templates;
    }

    public static function getMediaList($page)
    {
        $files = [];

        foreach (array_diff(scandir(PATH['pages'] . '/' . $page), array('..', '.')) as $file) {
            if (in_array($file_ext = substr(strrchr($file, '.'), 1), ['jpeg', 'png', 'gif', 'jpg'])) {
                if (strpos($file, $file_ext, 1)) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    public static function displayPageForm(array $form, array $values = [], string $content)
    {
        echo Form::open();
        echo Form::hidden('token', Token::generate());

        if (isset($form) > 0) {

            foreach ($form as $element => $property) {

                Arr::set($property, 'attributes.class', 'form-control');

                $pos = strpos($element, '.');

                if ($pos === false) {
                    $form_element_name = $element;
                } else {
                    $form_element_name = str_replace(".", "][", "$element").']';
                }

                $pos = strpos($form_element_name, ']');

                if ($pos !== false) {
                    $form_element_name = substr_replace($form_element_name, '', $pos, strlen(']'));
                }

                $form_value = Arr::keyExists($values, $element) ? Arr::get($values, $element) : '';

                $form_label = Form::label($element, I18n::find($property['title'], Registry::get('system.locale')));

                if ($property['type'] == 'textarea') {
                    $form_element = $form_label . Form::textarea($element, $form_value, $property['attributes']);
                } elseif ($property['type'] == 'hidden') {
                    $form_element = Form::hidden($element, $form_value);
                } elseif ($property['type'] == 'content') {
                    $form_element = $form_label . Form::textarea($element, $content, $property['attributes']);
                } elseif ($property['type'] == 'template') {
                    $form_element = $form_label . Form::select($form_element_name, PagesManager::getTemplatesList(), $form_value, $property['attributes']);
                } else {
                    // type: text, email, etc
                    $form_element =  $form_label . Form::input($form_element_name, $form_value, $property['attributes']);
                }

                echo '<div class="form-group">';
                echo $form_element;
                echo '</div>';
            }
        }

        echo Form::submit('save', __('admin_save'), ['class' => 'btn']);
        echo Form::close();
    }

    protected static function processFilesManager()
    {
        $files_directory = PATH['pages'] . '/' . Http::get('page') . '/';

        if (Http::get('delete_file') != '') {
            if (Token::check((Http::get('token')))) {
                Filesystem::deleteFile($files_directory . Http::get('delete_file'));
                Http::redirect(Http::getBaseUrl().'/admin/pages/edit?page='.Http::get('page').'&media=true');
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
            }
        }

        if (Http::post('upload_file')) {
            if (Token::check(Http::post('token'))) {
                Filesystem::uploadFile($_FILES['file'], $files_directory, ['jpeg', 'png', 'gif', 'jpg'], 5000000);
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
            }
        }
    }

    private static function _strrevpos($instr, $needle)
    {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos===false) {
            return false;
        } else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }
}
