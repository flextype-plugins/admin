<?php
namespace Flextype;
use Flextype\Component\{I18n\I18n, Registry\Registry};
?>
<?php Themes::template('admin/views/partials/head')->display(); ?>

<h2 class="page-heading">
    Edit page: <?php echo $page_frontmatter_data['title']; ?>
</h2>

<form method="post">
    <input id="slug" name="slug" type="hidden" value="<?php echo $page_slug; ?>">
    <h3>Frontmatter</h3>
    <textarea id="frontmatter" name="frontmatter" rows="0" cols="0"><?php echo $page_frontmatter; ?></textarea>
    <br>
    <h3>Content</h3>
    <textarea id="editor" name="editor" rows="8" cols="80"><?php echo $page_content; ?></textarea>
    <button class="btn btn-lg btn-dark" name="save_page" type="submit"><?php echo I18n::find('admin_save', 'admin', Registry::get('site.locale')); ?></button>
</form>

<?php Themes::template('admin/views/partials/footer')->display(); ?>
