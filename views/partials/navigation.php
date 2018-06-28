<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Event\Event, I18n\I18n, Token\Token};
?>
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
                        <a class="dropdown-item" href="<?php echo Http::getBaseUrl(); ?>/admin/pages"><?php echo I18n::find('admin_menu_pages', 'admin', Registry::get('site.locale')); ?></a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" target="_blank" href="<?php echo Http::getBaseUrl(); ?>"><?php echo I18n::find('admin_menu_view_site', 'admin', Registry::get('site.locale')); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo Http::getBaseUrl(); ?>/admin/logout?token=<?php echo Token::generate(); ?>"><?php echo I18n::find('admin_menu_logout', 'admin', Registry::get('site.locale')); ?></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
