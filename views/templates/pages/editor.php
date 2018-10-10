<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry, Html\Html, Form\Form, Http\Http, Token\Token};
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links',   ['pages' => ['url' => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name, 'title' => I18n::find('admin_pages_edit_page', Registry::get('system.locale'))]])
        ->assign('buttons', ['pages' =>
                                        ['url' => Http::getBaseUrl() . '/admin/pages/edit?page=' . $page_name . '&expert=true', 'title' => 'Switch to expert mode']])
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>

<?php echo Form::open(); ?>
    <?php echo Form::hidden('token', Token::generate()); ?>
    <?php echo Form::hidden('page_name', $page_name); ?>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <?php
                    echo (
                        Form::label('page_title', I18n::find('admin_pages_title', Registry::get('system.locale')), ['for' => 'pageTitle']).
                        Form::input('page_title', $page_title, ['class' => 'form-control', 'id' => 'pageTitle', 'required'])
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo (
                        Form::label('page_content', I18n::find('admin_pages_content', Registry::get('system.locale')), ['for' => 'pageTitle']).
                        Form::textarea('page_content', $page_content, ['class' => 'form-control margin-hard-bottom', 'id' => 'pageContent'])
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo (
                        Form::label('page_visibility', I18n::find('admin_pages_visibility', Registry::get('system.locale')),  ['for' => 'pageTitle']).
                        Form::select('page_visibility', ['visible' => 'visible', 'draft' => 'draft'], $page_visibility, ['class' => 'form-control', 'id' => 'pageTitle'])
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo (
                        Form::label('page_template', I18n::find('admin_pages_template', Registry::get('system.locale')),  ['for' => 'pageTemplate']).
                        Form::select('page_template', ['default' => 'default'], 'default', ['class' => 'form-control', 'id' => 'pageTemplate'])
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo (
                        Form::label('page_date', I18n::find('admin_pages_date', Registry::get('system.locale')), ['for' => 'pageDate']).
                        Form::input('page_date', $page_date, ['class' => 'form-control', 'id' => 'pageDate'])
                    );
                ?>
            </div>
            <?php echo Form::submit('page_save', I18n::find('admin_save', Registry::get('system.locale')), ['class' => 'btn btn-black']); ?>
        </div>
    </div>
<?php echo Form::close(); ?>

<br><br>

<?php
    echo (
        Form::open(null, array('enctype' => 'multipart/form-data', 'class' => 'form-inline')).
        Form::hidden('token', Token::generate())
    );
?>
<input type="file" name="file">
<?php
    echo (
        Form::submit('upload_file', 'Upload', array('class' => 'btn btn-primary')).
        Form::close()
    )
?>

<div class="card">
    <div class="card-body no-padding">
        <table class="table no-margin">
            <thead>
                <tr>
                    <th><?php echo I18n::find('admin_pages_files', Registry::get('system.locale')); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file) { ?>
                <tr>
                    <td><a href="<?php echo Http::getBaseUrl() . '/site/pages/' . Http::get('page') . '/' . basename($file); ?>"><?php echo basename($file); ?></a></td>
                    <td class="text-right">
                        <a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/edit?page=<?php echo Http::get('page'); ?>&delete_file=<?php echo basename($file); ?>&token=<?php echo Token::generate(); ?>"><?php echo I18n::find('admin_pages_delete', Registry::get('system.locale')); ?></a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
  Themes::view('admin/views/partials/content-end')->display();
  Themes::view('admin/views/partials/footer')->display();
?>
