jQuery(document).ready(function() {
    jQuery('.upload_image_button').live('click', function () {
        var cherry_uploader,
            button = jQuery(this);
        cherry_uploader = wp.media.frames.file_frame = wp.media({
            multiple: false
        });
        cherry_uploader.on('select', function() {
            var attachment = cherry_uploader.state().get('selection').first().toJSON();
            button.prev('input[type="text"]').val(attachment.url)
        }).open();
        return !1;
    });
});