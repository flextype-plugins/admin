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
              <div class="sidebar">
                  <div class="sidebar-wrapper">
                      <div class="flextype-logo">
                          <a href="#">
                              FLEXTYPE
                          </a>
                      </div>
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="collapse" href="#user">
                                    <i class="fas fa-user-circle"></i>
                                    <p>
                                        Sergey Romanenko
                                        <b class="caret"></b>
                                    </p>
                                </a>
                                <div class="collapse " id="user">
                                    <ul class="nav">
                                        <li class="nav-item ">
                                            <a class="nav-link" href="#">
                                                <span class="sidebar-normal">Edit Profile</span>
                                            </a>
                                        </li>
                                        <li class="nav-item ">
                                            <a class="nav-link" href="#">
                                                <span class="sidebar-normal">Logout</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                          <li class="nav-item">
                              <a class="nav-link" data-toggle="collapse" href="#content">
                                  <i class="far fa-file"></i>
                                  <p>
                                      Content
                                      <b class="caret"></b>
                                  </p>
                              </a>
                              <div class="collapse " id="content">
                                  <ul class="nav">
                                      <li class="nav-item ">
                                          <a class="nav-link" href="#">
                                              <span class="sidebar-normal">Pages</span>
                                          </a>
                                      </li>
                                      <li class="nav-item ">
                                          <a class="nav-link" href="#">
                                              <span class="sidebar-normal">Blocks</span>
                                          </a>
                                      </li>
                                  </ul>
                              </div>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link" data-toggle="collapse" href="#extends">
                                  <i class="fas fa-plug"></i>
                                  <p>
                                      Extends
                                      <b class="caret"></b>
                                  </p>
                              </a>
                              <div class="collapse" id="extends">
                                  <ul class="nav">
                                      <li class="nav-item ">
                                          <a class="nav-link" href="#">
                                              <span class="sidebar-normal">Plugins</span>
                                          </a>
                                      </li>
                                      <li class="nav-item ">
                                          <a class="nav-link" href="#">
                                              <span class="sidebar-normal">Themes</span>
                                          </a>
                                      </li>
                                  </ul>
                              </div>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link" data-toggle="collapse" href="#system">
                                  <i class="fas fa-cog"></i>
                                  <p>
                                      System
                                      <b class="caret"></b>
                                  </p>
                              </a>
                              <div class="collapse" id="system">
                                  <ul class="nav">
                                      <li class="nav-item active">
                                          <a class="nav-link" href="#">
                                              <span class="sidebar-normal">Settings</span>
                                          </a>
                                      </li>
                                      <li class="nav-item ">
                                          <a class="nav-link" href="#">
                                              <span class="sidebar-normal">Backups</span>
                                          </a>
                                      </li>
                                      <li class="nav-item ">
                                          <a class="nav-link" href="#">
                                              <span class="sidebar-normal">Information</span>
                                          </a>
                                      </li>
                                  </ul>
                              </div>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link" data-toggle="collapse" href="#help">
                                  <i class="fas fa-info-circle"></i>
                                  <p>
                                      Help
                                      <b class="caret"></b>
                                  </p>
                              </a>
                              <div class="collapse " id="help">
                                  <ul class="nav">
                                      <li class="nav-item ">
                                          <a class="nav-link" href="#">
                                              <span class="sidebar-normal">Documentation</span>
                                          </a>
                                      </li>
                                      <li class="nav-item ">
                                          <a class="nav-link" href="#">
                                              <span class="sidebar-normal">Support Forum</span>
                                          </a>
                                      </li>
                                  </ul>
                              </div>
                          </li>
                      </ul>
                  </div>
          </div>
          <div class="sidebar-off-canvas">

          </div>

          <div class="main-panel">



<?php if (Admin::isLoggedIn()) { ?>
<?php //Themes::view('admin/views/partials/navigation')->display(); ?>
<? } ?>
