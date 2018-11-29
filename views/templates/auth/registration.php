<?php
namespace Flextype;
use Flextype\Component\{Registry\Registry, Http\Http, Form\Form, Token\Token};
use function Flextype\Component\I18n\__;
?>
<?php
    Themes::view('admin/views/partials/head')
            ->assign('main_panel_class', 'width-full')
            ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<?php echo Form::open(); ?>
    <?php echo Form::hidden('token', Token::generate()); ?>
    <div class="row">
        <div class="col-4 float-center">
            <h3 class="h3"><?php echo __('admin_users_create_new'); ?></h3>
            <div class="form-group">
                <?php
                    echo (
                        Form::label('username', __('admin_username'), ['for' => 'inputUsername']).
                        Form::input('username', '', ['class' => 'form-control', 'id' => 'inputUsername', 'placeholder' => 'lowercase chars only, e.g. admin', 'required', 'pattern' => '^[a-z0-9_-]{3,16}$', 'autofocus'])
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo (
                        Form::label('email', __('admin_email'), ['for' => 'inputEmail']).
                        Form::input('email', '', ['class' => 'form-control', 'id' => 'inputEmail', 'placeholder' => 'valid email address', 'required'])
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo (
                        Form::label('password', __('admin_password'), ['for' => 'inputPassword']).
                        Form::password('password', '', ['class' => 'form-control', 'id' => 'inputPassword', 'placeholder' => 'complex string at least 8 chars long', 'pattern' => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}', 'required'])
                    );
                ?>
            </div>
            <div class="form-group">
                <?php echo Form::submit('registration', __('admin_users_create'), ['class' => 'btn btn-black float-left']); ?>
            </div>
        </div>
    </div>

<?php echo Form::open(); ?>

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
