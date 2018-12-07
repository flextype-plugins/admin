<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Html\Html, Registry\Registry, Arr\Arr, Event\Event, Token\Token, Session\Session};
use function Flextype\Component\I18n\__;
use Flextype\Navigation;
?>
<div class="sidebar">
    <div class="sidebar-wrapper">
        <div class="flextype-logo">
            <a href="<?php echo Http::getBaseUrl(); ?>/admin">
                FLEXTYPE
            </a>
        </div>

          <ul class="nav">
              <!--
              <li class="nav-item">
                  <a class="nav-link" data-toggle="collapse" href="#menu-user">
                      <i class="fas fa-user-circle"></i>
                      <p>
                          <?php echo Session::get('username'); ?>
                          <b class="caret"></b>
                      </p>
                  </a>
                  <div class="collapse" id="menu-user">
                      <ul class="nav">
                          <li class="nav-item">
                              <a class="nav-link" target="_blank" href="<?php echo Http::getBaseUrl(); ?>">
                                  <span class="sidebar-normal"><?php echo __('admin_view_site'); ?></span>
                              </a>
                          </li>
                          <li class="nav-item ">
                              <a class="nav-link" href="<?php echo Http::getBaseUrl(); ?>/admin/logout?token=<?php echo Token::generate(); ?>">
                                  <span class="sidebar-normal"><?php echo __('admin_menu_logout'); ?></span>
                              </a>
                          </li>
                      </ul>
                  </div>
              </li>
                -->
            <?php
                $active_menu_item = Registry::exists('sidebar_menu_item') ? Registry::get('sidebar_menu_item') : '';
            ?>
            <?php foreach (NavigationManager::getItems('content') as $item) { ?>
                <li class="nav-item <?php echo ($item['item'] == $active_menu_item) ? 'active' : ''; ?>">
                    <?php echo Html::anchor($item['title'], $item['link'], $item['attributes']); ?>
                </li>
            <?php } ?>
            <?php foreach (NavigationManager::getItems('extends') as $item) { ?>
                <li class="nav-item <?php echo ($item['item'] == $active_menu_item) ? 'active' : ''; ?>">
                    <?php echo Html::anchor($item['title'], $item['link'], $item['attributes']); ?>
                </li>
            <?php } ?>
            <?php foreach (NavigationManager::getItems('settings') as $item) { ?>
                <li class="nav-item <?php echo ($item['item'] == $active_menu_item) ? 'active' : ''; ?>">
                    <?php echo Html::anchor($item['title'], $item['link'], $item['attributes']); ?>
                </li>
            <?php } ?>
            <?php foreach (NavigationManager::getItems('help') as $item) { ?>
                <li class="nav-item <?php echo ($item['item'] == $active_menu_item) ? 'active' : ''; ?>">
                    <?php echo Html::anchor($item['title'], $item['link'], $item['attributes']); ?>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
<div class="sidebar-off-canvas"></div>
