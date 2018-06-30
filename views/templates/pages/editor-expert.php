<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Html\Html, Form\Form, Http\Http};
?>

<?php Themes::view('admin/views/partials/head')->display(); ?>

<form method="post">
    <div class="row">
      <div class="col-12">
        <?php echo Form::hidden('slug', $page_slug); ?>
        <div class="dark-panel">
            <div class="dark-panel-header">
                <h3 class="h3">
                    <?php echo I18n::find('admin_pages_edit', 'admin', Registry::get('system.locale')); ?>
                    <a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/edit?page=<?php echo $page_slug; ?>" class="float-right">Switch back to editor mode</a>
                </h3>
            </div>
            <div class="dark-panel-body">
                <div class="form-group">
                  <?php echo Form::textarea('editor-codemirror', $page_content); ?>
                </div>
            </div>
            <div class="dark-panel-footer text-center">
                <button class="btn btn-black btn-editor col-4" name="save_page_expert" type="submit"><?php echo I18n::find('admin_save', 'admin', Registry::get('system.locale')); ?></button>
            </div>
        </div>
      </div>
      </div>
</form>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
