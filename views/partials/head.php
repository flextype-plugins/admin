<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Event\Event, Assets\Assets, I18n\I18n};
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">


    <?php Assets::add('css', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/css/bootstrap.min.css', 'admin', 1); ?>
    <?php Assets::add('css', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/css/codemirror.min.css', 'admin', 2); ?>
    <?php Assets::add('css', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/css/admin.min.css', 'admin', 3); ?>
    <?php foreach (Assets::get('css', 'admin') as $assets_by_priorities) { foreach ($assets_by_priorities as $assets) { ?>
        <link href="<?php echo $assets['asset']; ?>" rel="stylesheet">
    <?php } } ?>

    <style media="screen">
        .CodeMirror,
        .medium-editor-element {
            height: auto!important;
            min-height: 500px!important;
            border: 1px solid #000!important;
            border-radius: 0!important;
            font-size: 18px;
            padding: 10px;
            outline: none!important;

        }

        .padding-hard {
            padding: 0!important;
        }

        .no-margin {
            margin: 0!important;
        }
    </style>

	<?php Event::dispatch('onAdminThemeHeader'); ?>
  </head>
  <body>
      <div class="wrapper">
        <?php Admin::isLoggedIn() and Themes::view('admin/views/partials/sidebar')->display(); ?>
        <div class="main-panel">
