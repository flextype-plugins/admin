<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Token\Token;
use Flextype\Component\Text\Text;
use Flextype\Component\Notification\Notification;
use function Flextype\Component\I18n\__;

class TemplatesManager
{
    public static function getTemplatesManager()
    {
        Registry::set('sidebar_menu_item', 'templates');

        switch (Http::getUriSegment(2)) {
            case 'add':
                $create_template = Http::post('create_template');

                if (isset($create_template)) {
                    if (Token::check((Http::post('token')))) {

                        $file = PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/templates/' . Text::safeString(Http::post('name'), '-', true) . '.php';

                        if (!Filesystem::fileExists($file)) {

                            // Create a template!
                            if (Filesystem::setFileContent(
                                  $file,
                                  ""
                            )) {
                                Notification::set('success', __('admin_message_template_created'));
                                Http::redirect(Http::getBaseUrl() . '/admin/templates');
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }

                Themes::view('admin/views/templates/extends/templates/add')
                    ->display();
            break;
            case 'delete':
                if (Http::get('template') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::deleteFile(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/templates/' . Http::get('template') . '.php');
                        Notification::set('success', __('admin_message_template_deleted'));
                        Http::redirect(Http::getBaseUrl() . '/admin/templates');
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }
            break;
            case 'rename':
                $rename_template = Http::post('rename_template');

                if (isset($rename_template)) {
                    if (Token::check((Http::post('token')))) {
                        if (!Filesystem::fileExists(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/templates/' . Http::post('name') . '.php')) {
                            if (rename(
                                PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/templates/' . Http::post('name_current') . '.php',
                                PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/templates/' . Http::post('name') . '.php')
                            ) {
                                Notification::set('success', __('admin_message_templates_renamed'));
                                Http::redirect(Http::getBaseUrl() . '/admin/templates');
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }

                Themes::view('admin/views/templates/extends/templates/rename')
                    ->assign('name_current', Http::get('template'))
                    ->display();
            break;
            case 'duplicate':
                if (Http::get('template') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::copy(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/templates/' . Http::get('template') . '.php',
                                         PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/templates/' . Http::get('template') . '-duplicate-' . date("Ymd_His") . '.php');
                        Notification::set('success', __('admin_message_entry_duplicated'));
                        Http::redirect(Http::getBaseUrl().'/admin/templates');
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }
            break;
            default:
                $templates_list = Themes::getTemplates();

                Themes::view('admin/views/templates/extends/templates/list')
                ->assign('templates_list', $templates_list)
                ->display();
            break;
        }
    }
}
