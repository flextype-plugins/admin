<?php
namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Form\Form;
use Flextype\Component\Token\Token;
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links', [
                            'entries'     => [
                                                'link'  => Http::getBaseUrl() . '/admin/entries',
                                                'title' => __('admin_entries_heading'),
                                                'attributes' => ['class' => 'navbar-item']
                                            ],
                            'entries_add' => [
                                                'link' => Http::getBaseUrl() . '/admin/entries/rename',
                                                'title' => __('admin_entries_rename'),
                                                'attributes' => ['class' => 'navbar-item active']
                                            ]
                     ])
    ->assign('entry', $entry)
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">
    <div class="col-md-6">

        <?php echo Form::open(); ?>
        <?php echo Form::hidden('token', Token::generate()); ?>
        <?php echo Form::hidden('entry_path_current', $entry_path_current); ?>
        <?php echo Form::hidden('entry_parent', $entry_parent); ?>
        <?php echo Form::hidden('name_current', $name_current); ?>

        <div class="form-group">
            <?php
                echo(
                    Form::label('name', __('admin_entries_name'), ['for' => 'entryName']).
                    Form::input('name', $name_current, ['class' => 'form-control', 'id' => 'entryName',  'required', 'data-validation' => 'length required', 'data-validation-allowing' => '-_', 'data-validation-length' => 'min1', 'data-validation-error-msg' => __('admin_entries_error_title_empty_input')])
                );
            ?>
        </div>

         <?php echo Form::submit('rename_entry', __('admin_save'), ['class' => 'btn btn-black btn-fill btn-wd']); ?>
     <?php echo Form::close(); ?>

 </div>
</div>

<?php
Themes::view('admin/views/partials/content-end')->display();
Themes::view('admin/views/partials/footer')->display();
?>
