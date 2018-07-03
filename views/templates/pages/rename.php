<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Http\Http, Form\Form, Token\Token};
?>
<?php Themes::view('admin/views/partials/head')->display(); ?>

<form method="post">
    <?php echo Form::hidden('token', Token::generate()); ?>
    <?php echo Form::hidden('page_path_current', $page_path_current); ?>
    <?php echo Form::hidden('page_name_current', $page_name); ?>
    <div class="row">
      <div class="col-4" style="margin: 0 auto;">
          <div class="dark-panel">
              <div class="dark-panel-header">
                  <h3 class="h3"><?php echo I18n::find('admin_pages_rename_page', 'admin', Registry::get('system.locale')); ?></h3>
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
                              Form::label('name', I18n::find('admin_pages_name', 'admin', Registry::get('system.locale')), ['for' => 'pageName']).
                              Form::input('name', $page_name, ['class' => 'form-control', 'id' => 'pageName', 'required'])
                          );
                      ?>
                  </div>
                  <div class="form-group">
                     <?php
                         echo (
                             Form::label('parent_page', I18n::find('admin_pages_parent_page', 'admin', Registry::get('system.locale'))).
                             Form::select('parent_page', $pages_list, $page_parent, array('class' => 'form-control'))
                         );
                     ?>
                   </div>
              </div>
              <div class="dark-panel-footer">
                  <button class="btn btn-block btn-black" name="rename_page" type="submit"><?php echo I18n::find('admin_save', 'admin', Registry::get('system.locale')); ?></button>
              </div>
          </div>
      </div>
    </div>
</form>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
