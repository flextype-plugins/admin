<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Event\Event};
?>
<script src="<?php echo Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/jquery/dist/jquery.slim.min.js"></script>
<script src="<?php echo Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/codemirror/lib/codemirror.js"></script>
<script src="//cdn.jsdelivr.net/npm/medium-editor@latest/dist/js/medium-editor.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/medium-editor@latest/dist/css/medium-editor.min.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/medium-editor@latest/dist/css/themes/default.css" type="text/css" media="screen" charset="utf-8">
<script>
    $(document).ready(function() {
          var editor = CodeMirror.fromTextArea(document.getElementById("editor-codemirror"), {
              lineNumbers: false,
              styleActiveLine: true,
              matchBrackets: true,
              viewportMargin: Infinity,
              indentUnit: 4,
              mode:  "YAML",
              indentWithTabs: true,
              theme: "default"
          });
          /*var editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
              lineNumbers: false,
              styleActiveLine: true,
              matchBrackets: true,
              viewportMargin: Infinity,
              indentUnit: 4,
              mode:  "HTML",
              indentWithTabs: true,
              theme: "default"
          });*/
      });

</script>
<script>
var editor = new MediumEditor('#pageContent', {
    disableDoubleReturn: true
    /*toolbar: {
        buttons: ['bold', 'italic', 'underline', 'anchor']
    }*/
});
</script>
<?php Event::dispatch('onAdminThemeFooter'); ?>
