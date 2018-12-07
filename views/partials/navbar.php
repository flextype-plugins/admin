<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Html\Html, Registry\Registry, Token\Token};
use function Flextype\Component\I18n\__;
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-fixed">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <?php if(isset($links)) { ?>
                <?php foreach ($links as $link) { ?>
                    <?php echo Html::anchor($link['title'], $link['link'], $link['attributes']); ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</nav>
<!-- End Navbar -->

<?php if (Http::get('page') !== null) { ?>
<div class="page-editor-heading">
     <a target="_blank" href="<?php echo $page['url']; ?>">/<?php if($page['slug'] == '') echo Registry::get('system.pages.main'); else echo $page['slug']; ?></a>
</div>
<?php } ?>

<nav class="navbar navbar-expand-lg navbar-fixed fixed-bottom">
  <div class="container-fluid">
      <div class="navbar-buttons">
      <?php if(isset($buttons)) { ?>
          <?php foreach ($buttons as $button) { ?>
              <?php echo Html::anchor($button['title'], $button['link'], $button['attributes']); ?>
          <?php } ?>
      <?php } ?>
      </div>
  </div>
</nav>
