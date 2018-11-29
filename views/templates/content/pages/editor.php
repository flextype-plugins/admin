<?php
namespace Flextype;
use Flextype\Component\{Registry\Registry, Html\Html, Form\Form, Http\Http, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links',   [
                                'pages'               => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages',
                                                            'title'      => __('admin_pages_heading'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                         ],
                                'edit_page'           => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name,
                                                            'title'      => __('admin_pages_editor'),
                                                            'attributes' => ['class' => 'navbar-item active']
                                                         ],
                                'edit_page_media'     => [
                                                            'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&media=true',
                                                            'title'      => __('admin_pages_edit_media'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                         ]
                            ])
        ->assign('buttons', [
                                'save_page' => [
                                                    'link'       => 'javascript:;',
                                                    'title'      => __('admin_pages_save_page'),
                                                    'attributes' => ['class' => 'js-page-save-submit float-right btn']
                                                ],
                                'expert_editor_page' => [
                                                    'link'       => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&expert=true',
                                                    'title'      => __('admin_pages_switch_to_expert_mode'),
                                                    'attributes' => ['class' => 'float-right btn']
                                               ],
                               'settings_page' => [
                                                   'link'       => 'javascript:;',
                                                   'title'      => __('admin_pages_settings'),
                                                   'attributes' => ['class' => 'js-settings-page-modal float-right btn', 'target' => '_blank']
                                              ],
                            ])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<?php echo Form::open(null, ['id' => 'editPage']); ?>
<?php echo Form::hidden('token', Token::generate()); ?>
<?php echo Form::hidden('action', 'edit-page'); ?>
<?php echo Form::hidden('page_name', $page_name); ?>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <?php
                echo (
                    Form::label('page_title', __('admin_pages_title'), ['for' => 'pageTitle']).
                    Form::input('page_title', $page_title, ['class' => 'form-control', 'id' => 'pageTitle', 'required'])
                );
            ?>
        </div>

        <div class="form-group">
            <?php
                echo (
                    Form::label('page_content', __('admin_pages_content'), ['for' => 'pageTitle']).
                    Form::textarea('page_content', $page_content, ['class' => 'form-control margin-hard-bottom', 'style' => 'height:400px;', 'id' => 'pageContent'])
                );
            ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="settingsPageModal" tabindex="-1" role="dialog" aria-labelledby="settingsPageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="settingsPageModalLabel"><?php echo __('admin_pages_settings'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-group">
              <?php
                  echo (
                      Form::label('page_description', __('admin_pages_description', Registry::get('system.locale')), ['for' => 'pageDescription']).
                      Form::input('page_description', $page_description, ['class' => 'form-control', 'id' => 'pageDescription'])
                  );
              ?>
          </div>
          <div class="form-group">
              <?php
                  echo (
                      Form::label('page_visibility', __('admin_pages_visibility', Registry::get('system.locale')),  ['for' => 'pageTitle']).
                      Form::select('page_visibility', ['visible' => 'visible', 'draft' => 'draft'], $page_visibility, ['class' => 'form-control', 'id' => 'pageTitle'])
                  );
              ?>
          </div>
          <div class="form-group">
              <?php
                  echo (
                      Form::label('page_template', __('admin_pages_template', Registry::get('system.locale')),  ['for' => 'pageTemplate']).
                      Form::select('page_template', $templates, $page_template, ['class' => 'form-control', 'id' => 'pageTemplate'])
                  );
              ?>
          </div>
          <div class="form-group">
              <?php
                  echo (
                      Form::label('page_date', __('admin_pages_date', Registry::get('system.locale')), ['for' => 'pageDate']).
                      Form::input('page_date', $page_date, ['class' => 'form-control', 'id' => 'pageDate'])
                  );
              ?>
          </div>
      </div>
    </div>
  </div>
</div>
<?php echo Form::close(); ?>

<?php
    Themes::view('admin/views/partials/content-end')->display();
    Themes::view('admin/views/partials/footer')->display();
?>
