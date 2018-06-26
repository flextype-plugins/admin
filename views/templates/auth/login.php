<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Http\Http};
?>
<?php Themes::view('admin/views/partials/head')->display(); ?>

    <form class="form-signin" method="post">
      <label for="inputUsername" class="sr-only"><?php echo I18n::find('admin_username', 'admin', Registry::get('site.locale')); ?></label>
      <input type="input" name="username" id="inputUsername" class="form-control" placeholder="<?php echo I18n::find('admin_username', 'admin', Registry::get('site.locale')); ?>" required autofocus>
      <label for="inputPassword" class="sr-only"><?php echo I18n::find('admin_password', 'admin', Registry::get('site.locale')); ?></label>
      <input type="password" name="password" id="inputPassword" class="form-control" placeholder="<?php echo I18n::find('admin_password', 'admin', Registry::get('site.locale')); ?>" required>
      <button class="btn btn-lg btn-dark btn-block" name="login" type="submit"><?php echo I18n::find('admin_login', 'admin', Registry::get('site.locale')); ?></button>
    </form>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
