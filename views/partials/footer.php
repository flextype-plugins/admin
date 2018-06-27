<?php
namespace Flextype;
?>
<?php Themes::view('admin/views/partials/tail')->display(); ?>

<?php if (Admin::isLoggedIn()) { ?>
<div class="powered text-center">
    <a href="http://flextype.org">Flextype</a> was made with love by <a href="http://awilum.github.io" class="highlight">Sergey Romanenko</a> and <a href="https://github.com/flextype/flextype/graphs/contributors" class="highlight">Flextype Community</a></p>
</div>
<?php } ?>
</main>
</body>
</html>
