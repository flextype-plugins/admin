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
        .CodeMirror,
        .medium-editor-element {
            height: auto!important;
            min-height: 500px!important;
            border: 2px solid #000!important;
            border-radius: 0!important;
            font-size: 18px;
            padding: 10px;
            outline: none!important;

        }
        .form-control {
            border: 2px solid #000!important;
            font-size: 18px;
            margin-bottom: 25px;
            color: #000;
        }
        .form-control:focus {
            outline-style: none!important;
            outline-width: 0px!important;
            border-color: none;
            box-shadow: none;
        }
        :focus {
            outline-style: none!important;
            outline-width: 0px!important;
        }

        select.form-control {
             -webkit-appearance: none;
             appearance: none;
             font-size: 18px;
             height: 43px!important;
             outline: 0;
             color: #000;
             border-color: #000;

             border-radius: 0px;
             background: linear-gradient(#000, #000) no-repeat,
                         linear-gradient(-135deg, rgba(255,255,255,0) 50%, white 50%) no-repeat,
                         linear-gradient(-225deg, rgba(255,255,255,0) 50%, white 50%) no-repeat,
                         linear-gradient(#000, #000) no-repeat;
             background-color: white;
             background-size: 2px 100%, 20px 25px, 20px 35px, 20px 60%;
             background-position: right 25px center, right bottom, right bottom, right bottom;
        }

        .admin-panel {
            border: 2px solid #000;
        }

        .admin-panel-header {
            border-bottom: 2px solid #000;
            padding: 11px 10px;
            background: #000;
            color: #fff;
        }

        .admin-panel-header a {
            color: #fff;
        }

        .admin-panel-header .h3 {
            padding: 0;
            margin: 0;
            font-size: 16px;
        }

        .admin-panel-footer {
            padding: 10px;
            border-top: 2px solid #000;
        }

        .admin-panel-body {
            padding: 10px;
        }

        .padding-hard {
            padding: 0;
        }
    </style>

	<?php Event::dispatch('onAdminThemeHeader'); ?>
  </head>
  <body>
  <?php Themes::view('admin/views/partials/navigation')->display(); ?>
  <main role="main" class="container content">
