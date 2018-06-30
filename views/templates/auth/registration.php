<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Http\Http, Form\Form, Token\Token};
?>
<?php Themes::view('admin/views/partials/head')->display(); ?>

<form class="form-signin" method="post">
<?php echo Form::hidden('token', Token::generate()); ?>
<div class="row">
  <div class="col-4" style="margin: 0 auto;">
      <div class="dark-panel">
          <div class="dark-panel-header">
              <h3 class="h3"><?php echo I18n::find('admin_users_create_new', 'admin', Registry::get('system.locale')); ?></h3>
          </div>
          <div class="dark-panel-body">
              <label for="inputUsername"><?php echo I18n::find('admin_username', 'admin', Registry::get('system.locale')); ?></label>
              <input type="input" name="username" placeholder="lowercase chars only, e.g. 'admin'" id="inputUsername" class="form-control" required autofocus>
              <label for="inputUsername"><?php echo I18n::find('admin_email', 'admin', Registry::get('system.locale')); ?></label>
              <input type="email" name="email" placeholder="valid email address" id="inputUsername" class="form-control" required autofocus>
              <label for="inputPassword"><?php echo I18n::find('admin_password', 'admin', Registry::get('system.locale')); ?></label>
              <input type="password" name="password" placeholder="complex string at least 8 chars long" id="inputPassword" class="form-control" required>
          </div>
          <div class="dark-panel-footer">
              <button class="btn btn-black btn-block" name="registration" type="submit"><?php echo I18n::find('admin_users_create', 'admin', Registry::get('system.locale')); ?></button>
          </div>
      </div>
    </div>
</div>
</form>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
