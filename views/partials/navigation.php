<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Event\Event, I18n\I18n, Token\Token};
?>

<div class="sidebar">
    <div class="logo">
        FLEXTYPE
    </div>
    <div class="accordion sidebar-nav-accordion" id="accordionExample">
      <div class="card">
        <div class="card-header" id="headingOne">
          <h5 class="mb-0">
            <a class="card-header-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <i class="far fa-file"></i> Content
            </a>
          </h5>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
          <div class="card-body">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link active" href="#">
                        <span class="sidebar-mini">P</span>
                        <span class="siderbar-normal">Pages</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="sidebar-mini">B</span>
                        <span class="siderbar-normal">Blocks</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="sidebar-mini">G</span>
                        <span class="siderbar-normal">Gallery</span>
                    </a>
                </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header" id="headingTwo">
          <h5 class="mb-0">
            <a class="card-header-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
              <i class="fas fa-plug"></i> Extends
            </a>
          </h5>
        </div>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
          <div class="card-body">
              <li class="nav-item">
                  <a class="nav-link active" href="#">
                      <span class="sidebar-mini">P</span>
                      <span class="siderbar-normal">Plugins</span>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="#">
                      <span class="sidebar-mini">T</span>
                      <span class="siderbar-normal">Themes</span>
                  </a>
              </li>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header" id="headingThree">
          <h5 class="mb-0">
            <a class="card-header-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
              <i class="fas fa-cog"></i> System
            </a>
          </h5>
        </div>
        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
          <div class="card-body">
              <li class="nav-item">
                  <a class="nav-link active" href="#">
                      <span class="sidebar-mini">S</span>
                      <span class="siderbar-normal">Settings</span>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="#">
                      <span class="sidebar-mini">B</span>
                      <span class="siderbar-normal">Backups</span>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="#">
                      <span class="sidebar-mini">I</span>
                      <span class="siderbar-normal">Information</span>
                  </a>
              </li>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header" id="headingFour">
          <h5 class="mb-0">
            <a class="card-header-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="headingFour">
              <i class="fas fa-info-circle"></i> Help
            </a>
          </h5>
        </div>
        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
          <div class="card-body">
              <li class="nav-item">
                  <a class="nav-link active" href="#">
                      <span class="sidebar-mini">D</span>
                      <span class="siderbar-normal">Documentation</span>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="#">
                      <span class="sidebar-mini">S</span>
                      <span class="siderbar-normal">Support Forum</span>
                  </a>
              </li>
          </div>
        </div>
      </div>
    </div>
</div>

<!--
<nav class="navbar navbar-expand-lg navbar-light border-bottom box-shadow">
    <div class="container">
        <a class="navbar-brand" href="<?php echo Http::getBaseUrl(); ?>/admin"><?php echo Registry::get('site.title'); ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Content</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo Http::getBaseUrl(); ?>/admin/pages"><?php echo I18n::find('admin_menu_pages', 'admin', Registry::get('system.locale')); ?></a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" target="_blank" href="<?php echo Http::getBaseUrl(); ?>"><?php echo I18n::find('admin_menu_view_site', 'admin', Registry::get('system.locale')); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo Http::getBaseUrl(); ?>/admin/logout?token=<?php echo Token::generate(); ?>"><?php echo I18n::find('admin_menu_logout', 'admin', Registry::get('system.locale')); ?></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
-->
