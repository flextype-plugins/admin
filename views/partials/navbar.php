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

<?php if (Registry::get('sidebar_menu_item') == 'pages'): ?>
<div class="page-editor-heading">
    <?php $parts = explode("/", Http::get('page')) ?>
    <?php $i = count($parts) ?>
    <?php foreach ($parts as $part): ?>
        <?php $i-- ?>
        <?php if ($part == Arr::last($parts)): ?>
            / <?= $part ?>
        <?php else: ?>
            <a href="<?= Http::getBaseUrl() ?>/admin/pages/?page=<?= implode(array_slice($parts, 0, -$i), '/') ?>"> / <?= $part ?></a>
        <?php endif ?>
    <?php endforeach ?>
</div>
<?php endif ?>
