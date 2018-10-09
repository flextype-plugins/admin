<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Event\Event};
?>
<script src="<?php echo Http::getBaseUrl(); ?>/site/plugins/admin/assets/dist/js/admin.min.js"></script>
<script src="<?php echo Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/codemirror/lib/codemirror.js"></script>

<script>
    $(document).ready(function() {
        CodeMirror.fromTextArea(document.getElementById("pageContent"), {
            lineNumbers: false,
            styleActiveLine: true,
            matchBrackets: true,
            viewportMargin: Infinity,
            indentUnit: 4,
            mode:  "HTML",
            indentWithTabs: true,
            theme: "default"
        });
        $('.CodeMirror').addClass('form-control'); // tmp
     });
</script>

<script>
$(document).ready(function() {
    $('.navbar-toggler').click(function () {
        $('.sidebar').addClass('show-sidebar');
        $('.sidebar-off-canvas').addClass('show-sidebar-off-canvas');
    });

    $('.sidebar-off-canvas').click(function () {
        $('.sidebar').removeClass('show-sidebar');
        $('.sidebar-off-canvas').removeClass('show-sidebar-off-canvas');
    });
});

</script>
<?php Event::dispatch('onAdminThemeFooter'); ?>
