<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry,Filesystem\Filesystem, Token\Token, Text\Text};
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links',   [
                            'entries' => [
                                            'link' => Http::getBaseUrl() . '/admin/entries',
                                            'title' => __('admin_entries_heading'),
                                            'attributes' => ['class' => 'navbar-item active']
                                       ]
                        ])
    ->assign('buttons', [
                            'entries' => [
                                            'link' => Http::getBaseUrl() . '/admin/entries/?entry='.Http::get('entry').'&create_new_entry=1' ,
                                            'title' => __('admin_entries_create_new'),
                                            'attributes' => ['class' => 'float-right btn']
                                       ]
                        ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<?php if (count($entries_list) > 0): ?>
<table class="table no-margin">
    <thead>
        <tr>
            <th><?php echo __('admin_entries_name'); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($entries_list as $entry) { ?>
        <tr>
            <td>
                <a href="<?= Http::getBaseUrl() ?>/admin/entries/?entry=<?php echo $entry['slug']; ?>"><?php echo $entry['title']; ?></a>
            </td>
            <td class="text-right">
                <div class="btn-group">
                  <a class="btn btn-default" href="<?php echo Http::getBaseUrl(); ?>/admin/entries/edit?entry=<?php echo $entry['slug']; ?>"><?php echo __('admin_entries_edit'); ?></a>
                  <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo Http::getBaseUrl(); ?>/admin/entries/add"><?php echo __('admin_entries_add'); ?></a>
                    <a class="dropdown-item" href="<?php echo Http::getBaseUrl(); ?>/admin/entries/clone?entry=<?php echo $entry['slug']; ?>&token=<?php echo Token::generate(); ?>"><?php echo __('admin_entries_clone'); ?></a>
                    <a class="dropdown-item" href="<?php echo Http::getBaseUrl(); ?>/admin/entries/rename?entry=<?php echo $entry['slug']; ?>"><?php echo __('admin_entries_rename'); ?></a>
                    <a class="dropdown-item" href="<?php echo Http::getBaseUrl(); ?>/admin/entries/move?entry=<?php echo $entry['slug']; ?>"><?php echo __('admin_entries_move'); ?></a>
                    <a class="dropdown-item" href="<?php echo Http::getBaseUrl(); ?>/<?php echo $entry['slug']; ?>" target="_blank"><?php echo __('admin_entries_view'); ?></a>
                  </div>
                </div>
                <a class="btn btn-default" href="<?php echo Http::getBaseUrl(); ?>/admin/entries/delete?entry=<?php echo $entry['slug']; ?>&token=<?php echo Token::generate(); ?>"><?php echo __('admin_entries_delete'); ?></a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php else: ?>
    <h3 class="no-data-message"><?= __('admin_entries_create_new') ?></h3>
<?php endif ?>

<?php
Themes::view('admin/views/partials/content-end')->display();
Themes::view('admin/views/partials/footer')->display();
?>
