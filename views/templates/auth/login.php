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
              <h3 class="h3"><?php echo I18n::find('admin_login', 'admin', Registry::get('system.locale')); ?></h3>
          </div>
          <div class="dark-panel-body">
              <label for="inputUsername"><?php echo I18n::find('admin_username', 'admin', Registry::get('system.locale')); ?></label>
              <input type="input" name="username" id="inputUsername" class="form-control" placeholder="<?php echo I18n::find('admin_username', 'admin', Registry::get('system.locale')); ?>" required autofocus>
              <label for="inputPassword"><?php echo I18n::find('admin_password', 'admin', Registry::get('system.locale')); ?></label>
              <input type="password" name="password" id="inputPassword" class="form-control" placeholder="<?php echo I18n::find('admin_password', 'admin', Registry::get('system.locale')); ?>" required>
          </div>
          <div class="dark-panel-footer">
              <button class="btn btn-black btn-block" name="login" type="submit"><?php echo I18n::find('admin_login', 'admin', Registry::get('system.locale')); ?></button>
          </div>
      </div>
     </div>
</div>
</form>


<?php Themes::view('admin/views/partials/footer')->display(); ?>
