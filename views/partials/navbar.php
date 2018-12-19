<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Html\Html, Registry\Registry, Token\Token, Arr\Arr};
use function Flextype\Component\I18n\__;
?>

<?php if (isset($links) || isset($buttons)): ?>
<nav class="navbar navbar-expand-lg navbar-fixed">
    <div class="container-fluid">
        <?php if (isset($links)): ?>
        <div class="navbar-wrapper">
            <?php foreach ($links as $link):  ?>
                <?= Html::anchor($link['title'], $link['link'], $link['attributes']) ?>
            <?php endforeach ?>
        </div>
        <?php endif ?>
        <?php if (isset($buttons)): ?>
        <div class="navbar-buttons">
            <?php foreach ($buttons as $button): ?>
                <?= Html::anchor($button['title'], $button['link'], $button['attributes']) ?>
            <?php endforeach ?>
        </div>
        <?php endif ?>
    </div>
</nav>
<?php endif ?>

<?php if ((Http::get('page') !== null) && (Arr::last(Http::getUriSegments()) !== 'move') && (Arr::last(Http::getUriSegments()) !== 'rename')): ?>
<div class="page-editor-heading">
     <a target="_blank" href="<?= $page['url'] ?>">/<?php if ($page['slug'] == '') echo Registry::get('settings.pages.main'); else echo $page['slug']; ?></a>
</div>
<?php endif ?>
