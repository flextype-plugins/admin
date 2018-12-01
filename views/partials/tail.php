<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Event\Event, Registry\Registry, Assets\Assets};
?>

<?php Assets::add('js', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/js/admin.min.js', 'admin', 1); ?>
<?php Assets::add('js', Http::getBaseUrl() . '/site/plugins/admin/assets/js/trumbowyg/dist/trumbowyg.min.js', 'admin', 2); ?>
<?php Assets::add('js', Http::getBaseUrl() . '/site/plugins/admin/assets/js/trumbowyg/dist/plugins/base64/trumbowyg.base64.js', 'admin', 3); ?>
<?php Assets::add('js', Http::getBaseUrl() . '/site/plugins/admin/assets/js/trumbowyg/dist/plugins/noembed/trumbowyg.noembed.js', 'admin', 3); ?>
<?php Assets::add('js', Http::getBaseUrl() . '/site/plugins/admin/assets/js/codemirror/lib/codemirror.js', 'admin', 3); ?>
<?php Assets::add('js', Http::getBaseUrl() . '/site/plugins/admin/assets/js/codemirror/mode/javascript/javascript.js', 'admin', 3); ?>
<?php Assets::add('js', Http::getBaseUrl() . '/site/plugins/admin/assets/js/codemirror/mode/htmlmixed/htmlmixed.js', 'admin', 3); ?>
<?php if (Registry::get("system.locale") != 'en') Assets::add('js', Http::getBaseUrl() . '/site/plugins/admin/assets/js/trumbowyg/dist/langs/'.Registry::get("system.locale").'.min.js', 'admin', 10); ?>
<?php foreach (Assets::get('js', 'admin') as $assets_by_priorities) { foreach ($assets_by_priorities as $assets) { ?>
    <script type="text/javascript" src="<?php echo $assets['asset']; ?>"></script>
<?php } } ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>

<script>

    if (typeof $.flextype == 'undefined') $.flextype = {};

    $.flextype.plugins = {

        init: function() {
            this.changeStatusProcess();
        },

        changeStatus: function(plugin, status, token) {
            $.ajax({
                type: "post",
                data: "plugin_change_status=1&plugin="+plugin+"&status="+status+"&token="+token,
                url: $('form input[name="url"]').val()
            });
        },

        changeStatusProcess: function() {
            $(".js-switch").click(function() {
                if ($(this).is(':checked')) {
                    $.flextype.plugins.changeStatus($(this).data("plugin"), "true", $(this).data("token"));
                } else {
                    $.flextype.plugins.changeStatus($(this).data("plugin"), "false", $(this).data("token"));
                }
            });
        }
    };

    $(document).ready(function() {

        $.trumbowyg.svgPath = '<?php echo Http::getBaseUrl(); ?>/site/plugins/admin/assets/js/trumbowyg/dist/ui/icons.svg';
        $('.js-editor').trumbowyg({
            btnsDef: {
                // Customizables dropdowns
                image: {
                    dropdown: ['insertImage', 'base64', 'noembed'],
                    ico: 'insertImage'
                }
            },
            btns: [
                ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['strong', 'em', 'del'],
                ['superscript', 'subscript'],
                ['link'],
                ['image'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['removeformat'],
                ['fullscreen']
            ],
            lang: '<?php echo Registry::get("system.locale"); ?>',
            autogrow: true,
            removeformatPasted: true
        });

        $.flextype.plugins.init();

        $('.js-page-save-submit').click(function() {
            $("#editPage" ).submit();
        });

        $('.js-page-save-submit').click(function() {
            $("#editPageExpert" ).submit();
        });

        $('.navbar-toggler').click(function () {
            $('.sidebar').addClass('show-sidebar');
            $('.sidebar-off-canvas').addClass('show-sidebar-off-canvas');
        });

        $('.sidebar-off-canvas').click(function () {
            $('.sidebar').removeClass('show-sidebar');
            $('.sidebar-off-canvas').removeClass('show-sidebar-off-canvas');
        });

        $('.js-plugins-info').click(function () {
            $('#pluginInfoModal').modal();
            $('.js-plugin-name-placeholder').html($(this).attr('data-name'));
            $('.js-plugin-version-placeholder').html($(this).attr('data-version'));
            $('.js-plugin-description-placeholder').html($(this).attr('data-description'));
            $('.js-plugin-author-name-placeholder').html($(this).attr('data-author-name'));
            $('.js-plugin-author-email-placeholder').html($(this).attr('data-author-email'));
            $('.js-plugin-author-url-placeholder').html($(this).attr('data-author-url'));
            $('.js-plugin-homepage-placeholder').html($(this).attr('data-homepage'));
            $('.js-plugin-bugs-placeholder').html($(this).attr('data-bugs'));
            $('.js-plugin-license-placeholder').html($(this).attr('data-license'));
        });

        $('.js-pages-image-preview').click(function () {
            $('#pagesImagePreview').modal();
            $('.js-page-image-preview-placeholder').css('background-image', 'url(' + $(this).attr('data-image-url') + ')');
            $('.js-page-image-url-placeholder').val($(this).attr('data-image-url'));
            $('.js-page-image-delete-url-placeholder').attr('href', $(this).attr('data-image-delete-url'));
        });

        $('.js-settings-page-modal').click(function () {
            $('#settingsPageModal').modal();
        });

        $.validate({});

        var editor = CodeMirror.fromTextArea(document.getElementById("pageExpertEditor"), {
            lineNumbers: true,
            matchBrackets: true,
            indentUnit: 4,
            mode:  "htmlmixed",
            indentWithTabs: true,
            theme: "twilight",
            smartIndent: false
        });

        Messenger.options = {
            extraClasses: 'messenger-fixed messenger-on-bottom messenger-on-right',
            theme: 'flat'
        }

        Messenger().post({
            type: "success",
            message : "'.$message.'",
            hideAfter: '.$seconds.'
        });

    });
</script>

<?php Event::dispatch('onAdminThemeFooter'); ?>
