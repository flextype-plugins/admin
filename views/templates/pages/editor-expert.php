<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Html\Html, Form\Form, Http\Http, Token\Token};
?>

<?php Themes::view('admin/views/partials/head')->display(); ?>

<?php echo Form::open(); ?>
    <?php echo Form::hidden('token', Token::generate()); ?>
    <?php echo Form::hidden('page_name', $page_name); ?>
    <div class="row">
        <div class="col-12">
            <div class="dark-panel">
                <div class="dark-panel-header">
                    <h3 class="h3">
                        <?php echo I18n::find('admin_pages_edit', 'admin', Registry::get('system.locale')); ?>
                        <a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/edit?page=<?php echo $page_name; ?>" class="float-right">Switch back to editor mode</a>
                    </h3>
                </div>
                <div class="dark-panel-body">
                    <div class="form-group">
                        <?php echo Form::textarea('page_content', $page_content, ['class' => 'form-control margin-hard-bottom', 'id' => 'pageContentExpert']); ?>
                    </div>
                </div>
                <div class="dark-panel-footer text-center">
                    <button class="btn btn-black btn-editor col-4" name="page_save_expert" type="submit"><?php echo I18n::find('admin_save', 'admin', Registry::get('system.locale')); ?></button>
                </div>
            </div>
        </div>
    </div>
<?php echo Form::close(); ?>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
