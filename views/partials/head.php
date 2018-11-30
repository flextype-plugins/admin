<?php
namespace Flextype;

use Flextype\Component\Http\Http;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Event\Event;
use Flextype\Component\Assets\Assets;
use function Flextype\Component\I18n\__;

?>
<!doctype html>
<html lang="<?php echo Registry::get('system.locale'); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

	<?php Event::dispatch('onAdminThemeMeta'); ?>

	<title>FLEXTYPE</title>

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">

    <?php Assets::add('css', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/css/bootstrap.min.css', 'admin', 1); ?>
    <?php Assets::add('css', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/css/trumbowyg.min.css', 'admin', 2); ?>
    <?php Assets::add('css', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/css/admin.min.css', 'admin', 3); ?>
    <?php Assets::add('css', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/css/animate.min.css', 'admin', 4); ?>
    <?php Assets::add('css', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/codemirror/lib/codemirror.css', 'admin', 5); ?>
    <?php Assets::add('css', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/codemirror/theme/twilight.css', 'admin', 6); ?>
    <?php foreach (Assets::get('css', 'admin') as $assets_by_priorities) {
    foreach ($assets_by_priorities as $assets) {
        ?>
        <link href="<?php echo $assets['asset']; ?>" rel="stylesheet">
    <?php
    }
} ?>

    <style media="screen">
        .CodeMirror {
            height: auto;
            margin: -30px;
            font-size: 14px;
        }

        .CodeMirror-sizer {
            background: #292d34;
        }

        .cm-s-twilight .CodeMirror-gutters {
            background: #292d34;
            border-right: none;
            color: #9ca4b2;
        }

        .cm-s-twilight .CodeMirror-linenumber {
            color: #9ca4b2;
        }

        .cm-s-twilight.CodeMirror {
            background: #292d34;
        }

        .CodeMirror pre {
            padding: 0 14px;
        }

    </style>
	<?php Event::dispatch('onAdminThemeHeader'); ?>
  </head>
  <body>
      <div class="wrapper">
        <?php UsersManager::isLoggedIn() and Themes::view('admin/views/partials/sidebar')->display(); ?>
        <div class="main-panel <?php if (isset($main_panel_class)) {
    echo $main_panel_class;
}?>">
