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
                        'pages' => [
                                        'link' => Http::getBaseUrl() . '/admin/pages',
                                        'title' => __('admin_pages_heading'),
                                        'attributes' => ['class' => 'navbar-item']
                                    ],
                       'pages_add' => [
                                        'link' => Http::getBaseUrl() . '/admin/pages/add',
                                        'title' => __('admin_pages_create_new'),
                                        'attributes' => ['class' => 'navbar-item active']
                                      ]
                      ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">
    <div class="col-md-6">

        <form action="" method="post">
            <input type="hidden" id="token" name="token" value="287c7255d72e035e1a3d993a1faf07cdab305a32">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="pageTitle" name="title" value="" class="form-control" required="required" data-validation="length" data-validation-length="min1" data-validation-error-msg="Title has to be an alphanumeric value (3-12 chars)">
            </div>
        <div class="form-group">
                    <label for="slug">Name</label><input type="text" id="pageSlug" name="slug" value="" class="form-control" required="required" data-validation="length alphanumeric" data-validation-allowing="-_" data-validation-length="min1" data-validation-error-msg="Name has to be an alphanumeric value (3-12 chars)">        </div>
                <div class="form-group">
                    <label for="formGroupParentPageInput">Page parent</label>
                    <select class="form-control" id="formGroupParentPageInput" name="parent_page">
                    <option value="">/</option>
                        <option value="test">test</option>
                        <option value="download">download</option>
                        <option value="catalog">catalog</option>
                        <option value="blog">blog</option>
                        <option value="about">about</option>
                        <option value="projects">projects</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Template</label>
                    <select class="form-control" name="template">
                                    <option value="catalog">catalog</option>
                                    <option value="catalog-item">catalog-item</option>
                                    <option value="default">default</option>
                                </select>
                </div>
            </div>
        </div>

        <input type="submit" id="create_page" name="create_page" value="Create" class="btn btn-black"></form>




        <?php echo Form::open(); ?>
        <?php echo Form::hidden('token', Token::generate()); ?>
        <div class="form-group">
            <?php
                echo(
                    Form::label('title', __('admin_pages_title'), ['for' => 'pageTitle']).
                    Form::input('title', '', ['class' => 'form-control', 'id' => 'pageTitle', 'required', 'data-validation' => 'length', 'data-validation-length' => 'min1', 'data-validation-error-msg' => __('admin_pages_error_title_empty_input')])
                );
            ?>
        </div>
        <div class="form-group">
            <?php
                echo(
                    Form::label('slug', __('admin_pages_name'), ['for' => 'pageSlug']).
                    Form::input('slug', '', ['class' => 'form-control', 'id' => 'pageSlug', 'required', 'data-validation' => 'length alphanumeric', 'data-validation-allowing' => '-_', 'data-validation-length' => 'min1', 'data-validation-error-msg' => __('admin_pages_error_name_empty_input')])
                );
            ?>
        </div>
        <div class="form-group">
            <label for="formGroupParentPageInput"><?php echo __('admin_pages_parent_page'); ?></label>
            <select class="form-control" id="formGroupParentPageInput" name="parent_page">
            <option value="">/</option>
            <?php foreach ($pages_list as $page) {
                ?>
                <option value="<?php if ($page['slug'] != '') {
                    echo $page['slug'];
                } else {
                    echo Registry::get('system.pages.main');
                } ?>"><?php if ($page['slug'] != '') {
                    echo $page['slug'];
                } else {
                    echo Registry::get('system.pages.main');
                } ?></option>
            <?php
            } ?>
            </select>
        </div>
        <div class="form-group">
            <label><?php echo __('admin_pages_template'); ?></label>
            <select class="form-control" name="template">
            <?php foreach ($templates as $template) {
                ?>
                <option value="<?php echo $template; ?>"><?php echo $template; ?></option>
            <?php
            } ?>
            </select>
        </div>
    </div>
</div>

<?php echo Form::submit('create_page', __('admin_create'), ['class' => 'btn btn-black']); ?>
<?php echo Form::close(); ?>

<?php
Themes::view('admin/views/partials/content-end')->display();
Themes::view('admin/views/partials/footer')->display();
?>
