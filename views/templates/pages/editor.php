<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Html\Html, Form\Form, Http\Http, Token\Token};
?>

<?php Themes::view('admin/views/partials/head')->display(); ?>

<form method="post">
    <?php echo Form::hidden('token', Token::generate()); ?>
    <div class="row">
      <div class="col-9">
        <?php echo Form::hidden('slug', $page_slug); ?>
        <div class="dark-panel">
            <div class="dark-panel-header">
                <h3 class="h3">
                    <?php echo I18n::find('admin_pages_edit', 'admin', Registry::get('system.locale')); ?>
                    <a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/edit?page=<?php echo $page_slug; ?>&expert=true" class="float-right">Switch to expert mode</a>
                </h3>
            </div>
            <div class="dark-panel-body">
                <div class="form-group">
                    <?php
                        echo (
                            Form::label('title', I18n::find('admin_pages_title', 'admin', Registry::get('system.locale')), ['for' => 'pageTitle']).
                            Form::input('title', $page_title, ['class' => 'form-control', 'id' => 'pageTitle', 'required'])
                        );
                    ?>
                </div>

                <div class="form-group">
                    <?php
                        echo (
                            Form::label('editor', I18n::find('admin_pages_content', 'admin', Registry::get('system.locale')), ['for' => 'pageTitle']).
                            Form::textarea('editor', $page_content, ['class' => 'form-control', 'id' => 'pageContent'])
                        );
                    ?>
                </div>
            </div>
        </div>
      </div>
      <div class="col-3">
          <div class="dark-panel">
              <div class="dark-panel-header">
                  <h3 class="h3">
                      <?php echo I18n::find('admin_pages_publish', 'admin', Registry::get('system.locale')); ?>
                  </h3>
              </div>
              <div class="dark-panel-body">
                  <div class="form-group">
                  <label for="formGroupParentPageInput"><?php echo I18n::find('admin_pages_visibility', 'admin', Registry::get('system.locale')); ?></label>
                  <?php echo Form::select('visibility', ['visible' => 'visible', 'draft' => 'draft'], $page_visibility, array('class' => 'form-control', 'id' => 'formGroupParentPageInput')); ?>
                  </div>

                  <div class="form-group">
                  <label for="formGroupParentPageInput"><?php echo I18n::find('admin_pages_template', 'admin', Registry::get('system.locale')); ?></label>
                  <select class="form-control" id="formGroupParentPageInput" name="template">
                  <option value="default">default</option>
                  </select>
                  </div>

                  <div class="form-group">
                  <label for="formGroupPageTitleInput"><?php echo I18n::find('admin_pages_date', 'admin', Registry::get('system.locale')); ?></label>
                  <input type="text" name="date" class="form-control" id="formGroupPageTitleInput" value="<?php echo $page_date; ?>" placeholder="">
                  </div>

              </div>
              <div class="dark-panel-footer text-center">
                  <button class="btn btn-black btn-editor btn-block" name="save_page" type="submit"><?php echo I18n::find('admin_save', 'admin', Registry::get('system.locale')); ?></button>
              </div>
          </div>
      </div>
      </div>
</form>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
