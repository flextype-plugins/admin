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

        .padding-hard {
            padding: 0;
        }

        .navbar-light .navbar-nav .nav-link {
            padding: 16.5px 14px;
        }

        .dropdown-menu {
            background-color: #282828;
            border-top: 2px solid #282828;
            border-left: 2px solid #282828;
            border-bottom: 2px solid #282828;
            border-right: 2px solid #282828;
            padding: 0;
            margin: 0;
            border-radius: 0;

            position: absolute;
            top: 57px;

        }

        .nav-item.dropdown.show {
            background: #282828;
            color: #fff;
        }

        .dropdown-item {
            padding: 16.5px 12px;
        }

        .navbar-light .navbar-nav .show > .nav-link,
        .navbar-light .navbar-text a {
            color: #fff;
        }

        .dropdown-item.active,
        .dropdown-item:active {
            color: #fff;
            text-decoration: none;
            background-color: #282828;
        }

.table td,
.table th,
.table thead th {
border-bottom: 1px solid #333;
}

.table tr:last-of-type td {
border-bottom: none;
}

.table td, .table th {
padding: 9px 10px;
vertical-align: middle;
}

.navbar {
    padding: 0;
}

.table {
    margin: 0;
}

    </style>

	<?php Event::dispatch('onAdminThemeHeader'); ?>
  </head>
  <body>
<?php if (Admin::isLoggedIn()) { ?>
<?php Themes::view('admin/views/partials/navigation')->display(); ?>
<? } ?>
  <main role="main" class="container content">
