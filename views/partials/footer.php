<?php
namespace Flextype;
?>
    <?php Themes::view('admin/views/partials/tail')->display(); ?>

<?php if (Admin::isLoggedIn()) { ?>
    <div class="powered float-right">
        Official Support Forum / Documentation / © 2018 Flextype – Version <?php echo Flextype::VERSION; ?>
    </div>
<?php } ?>    
    </main>
  </body>
</html>
