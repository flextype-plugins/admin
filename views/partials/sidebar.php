<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Event\Event, I18n\I18n, Token\Token};
?>
<div class="sidebar">
    <div class="sidebar-wrapper">
        <div class="flextype-logo">
            <a href="<?php echo Http::getBaseUrl(); ?>/admin">
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
                              <a class="nav-link" href="<?php echo Http::getBaseUrl(); ?>/admin/logout?token=<?php echo Token::generate(); ?>">
                                  <span class="sidebar-normal"><?php echo I18n::find('admin_menu_logout', 'admin', Registry::get('system.locale')); ?></span>
                              </a>
                          </li>
                      </ul>
                  </div>
              </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#content">
                    <i class="far fa-file"></i>
                    <p>
                        <?php echo I18n::find('admin_menu_content', 'admin', Registry::get('system.locale')); ?>
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse " id="content">
                    <ul class="nav">
                        <li class="nav-item ">
                            <a class="nav-link" href="<?php echo Http::getBaseUrl(); ?>/admin/pages">
                                <span class="sidebar-normal"><?php echo I18n::find('admin_menu_pages', 'admin', Registry::get('system.locale')); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#extends">
                    <i class="fas fa-plug"></i>
                    <p>
                        <?php echo I18n::find('admin_menu_extends', 'admin', Registry::get('system.locale')); ?>
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse" id="extends">
                    <ul class="nav">
                        <li class="nav-item ">
                            <a class="nav-link" href="#">
                                <span class="sidebar-normal"><?php echo I18n::find('admin_menu_extends_plugins', 'admin', Registry::get('system.locale')); ?></span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="#">
                                <span class="sidebar-normal"><?php echo I18n::find('admin_menu_extends_themes', 'admin', Registry::get('system.locale')); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#system">
                    <i class="fas fa-cog"></i>
                    <p>
                        <?php echo I18n::find('admin_menu_system', 'admin', Registry::get('system.locale')); ?>
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse" id="system">
                    <ul class="nav">
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo Http::getBaseUrl(); ?>/admin/settings">
                                <span class="sidebar-normal"><?php echo I18n::find('admin_menu_system_settings', 'admin', Registry::get('system.locale')); ?></span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="<?php echo Http::getBaseUrl(); ?>/admin/information">
                                <span class="sidebar-normal"><?php echo I18n::find('admin_menu_system_information', 'admin', Registry::get('system.locale')); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#help">
                    <i class="fas fa-info-circle"></i>
                    <p>
                        <?php echo I18n::find('admin_menu_help', 'admin', Registry::get('system.locale')); ?>
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse " id="help">
                    <ul class="nav">
                        <li class="nav-item ">
                            <a class="nav-link" href="#">
                                <span class="sidebar-normal"><?php echo I18n::find('admin_menu_help_documentaion', 'admin', Registry::get('system.locale')); ?></span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="#">
                                <span class="sidebar-normal"><?php echo I18n::find('admin_menu_help_support_forum', 'admin', Registry::get('system.locale')); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>
<div class="sidebar-off-canvas"></div>