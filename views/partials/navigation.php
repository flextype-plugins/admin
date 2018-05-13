<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Event\Event};
?>
<nav class="navbar navbar-expand-lg navbar-light border-bottom box-shadow">
<div class="container">
  <a class="navbar-brand" href="<?php echo Http::getBaseUrl(); ?>/admin"><?php echo Registry::get('site.title'); ?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link <?php if (Http::getUriSegment(1) == 'pages') echo 'active'; ?>" href="<?php echo Http::getBaseUrl(); ?>/admin/pages">Pages</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php if (Http::getUriSegment(1) == 'settings') echo 'active'; ?>" href="<?php echo Http::getBaseUrl(); ?>/admin/settings">Settings</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo Http::getBaseUrl(); ?>/admin/pages">View Site</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo Http::getBaseUrl(); ?>/admin/pages">Logout</a>
      </li>
    </ul>
  </div>
</div>
</nav>
