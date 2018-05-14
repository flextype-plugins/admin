<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Event\Event};
?>
<!doctype html>
<html lang="<?php echo Registry::get('site.locale'); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

	<?php Event::dispatch('onAdminThemeMeta'); ?>

	<title>FLEXTYPE</title>

    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="<?php echo Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/codemirror/lib/codemirror.css">

	<link href="<?php echo Http::getBaseUrl(); ?>/site/plugins/admin/assets/dist/css/simple.css" rel="stylesheet">

    <style media="screen">
        .editor-toolbar {
            border-top: 1px solid #000!important;
            border-left: 1px solid #000!important;
            border-right: 1px solid #000!important;
            border-radius: 0!important;
        }

        .editor-toolbar a {
            color: #000!important;
        }

        .CodeMirror {
            height: auto!important;
            min-height: 10px!important;
            border: 1px solid #000!important;
            border-radius: 0!important;
        }
    </style>

	<?php Event::dispatch('onAdminThemeHeader'); ?>
  </head>
  <body>
  <?php Themes::template('admin/views/partials/navigation')->display(); ?>
  <main role="main" class="container content">
