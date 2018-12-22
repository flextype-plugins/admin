<?php
namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Form\Form;
use Flextype\Component\Html\Html;
use Flextype\Component\Token\Token;
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links', [
                        'entries' => [
                                        'link' => Http::getBaseUrl() . '/admin/entries',
                                        'title' => __('admin_entries_heading'),
                                        'attributes' => ['class' => 'navbar-item']
                                    ],
                       'entries_add' => [
                                        'link' => Http::getBaseUrl() . '/admin/entries/add',
                                        'title' => __('admin_entries_create_new'),
                                        'attributes' => ['class' => 'navbar-item active']
                                      ]
                      ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">
    <div class="col-md-6">
        <?= Form::open(); ?>
        <?= Form::hidden('token', Token::generate()); ?>
        <?= Form::hidden('parent_entry', Http::get('entry')); ?>
        <div class="form-group">
            <?php
                echo(
                    Form::label('title', __('admin_entries_title'), ['for' => 'entryTitle']).
                    Form::input('title', '', ['class' => 'form-control', 'id' => 'entryTitle', 'required', 'data-validation' => 'length required', 'data-validation-length' => 'min1', 'data-validation-error-msg' => __('admin_entries_error_title_empty_input')])
                );
            ?>
        </div>
        <div class="form-group">
            <?php
                echo(
                    Form::label('slug', __('admin_entries_name'), ['for' => 'entrySlug']).
                    Form::input('slug', '', ['class' => 'form-control', 'id' => 'entrySlug', 'required', 'data-validation' => 'length required', 'data-validation-allowing' => '-_', 'data-validation-length' => 'min1', 'data-validation-error-msg' => __('admin_entries_error_name_empty_input')])
                );
            ?>
        </div>
        <div class="form-group">
            <label><?php echo __('admin_entries_type'); ?></label>
            <select class="form-control" name="template">
            <?php foreach ($templates as $key => $template) { ?>
                <option value="<?php echo $key; ?>"><?php echo $template; ?></option>
            <?php } ?>
            </select>
        </div>
    </div>
</div>

<?php echo Form::submit('create_entry', __('admin_create'), ['class' => 'btn btn-black']); ?>
<?= Form::close(); ?>

<?php
Themes::view('admin/views/partials/content-end')->display();
Themes::view('admin/views/partials/footer')->display();
?>
