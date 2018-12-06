<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Html\Html, Registry\Registry, Token\Token};
use function Flextype\Component\I18n\__;
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-fixed">
    <div class="container-fluid">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
        </button>
        <div class="navbar-wrapper">
            <?php if(isset($links)) { ?>
                <?php foreach ($links as $link) { ?>
                    <?php echo Html::anchor($link['title'], $link['link'], $link['attributes']); ?>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="navbar-buttons">
        <?php if(isset($buttons)) { ?>
            <?php foreach ($buttons as $button) { ?>
                <?php echo Html::anchor($button['title'], $button['link'], $button['attributes']); ?>
            <?php } ?>
        <?php } ?>
        </div>
    </div>
</nav>
<!-- End Navbar -->

<?php if (Http::get('page') !== null) { ?>
<div class="page-editor-heading">
    page: <?php if($page['slug'] == '') echo Registry::get('system.pages.main'); else echo $page['slug']; ?> <span class="delimeter">|</span>
    template: <?php echo $page['template']; ?> <span class="delimeter">|</span>
    visibility: <?php if (isset($page['visibility']) && $page['visibility'] === 'draft') echo 'draft'; else echo 'visible'; ?>
</div>
<?php } ?>
