jQuery(document).ready(function($) {

    var plpmMediaUploader;
    $('.plpm_upload_pdf_button').on('click', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $urlField = $button.siblings('.plpm_pdf_url_field');
        var $idField = $button.siblings('.plpm_pdf_attachment_id_field');
        if (plpmMediaUploader) {
            plpmMediaUploader.open();
            return;
        }

        plpmMediaUploader = wp.media({
            title: plpm_media_vars.modalTitle || 'Select PDF File',
            button: {
                text: plpm_media_vars.modalButton || 'Use This PDF'
            },
            multiple: false,
            library: {
                type: 'application/pdf'
            }
        });
        plpmMediaUploader.on('select', function() {
            var attachment = plpmMediaUploader.state().get('selection').first().toJSON();

            $urlField.val(attachment.url);
            $idField.val(attachment.id);
            if ($button.siblings('.plpm_clear_pdf_button').length === 0) {
                $('<button type="button" class="button plpm_clear_pdf_button">' + (plpm_media_vars.clearButton || 'Clear PDF') + '</button>')
                    .insertAfter($button);
            }
        });

        plpmMediaUploader.on('open', function() {
            var selection = plpmMediaUploader.state().get('selection');
            var current_id = $idField.val();

            if (current_id) {
                var attachment = wp.media.attachment(current_id);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            }
        });
        plpmMediaUploader.open();
    });

    $(document).on('click', '.plpm_clear_pdf_button', function(e) {
        e.preventDefault();
        var $button = $(this);
        var $urlField = $button.siblings('.plpm_pdf_url_field');
        var $idField = $button.siblings('.plpm_pdf_attachment_id_field');

        $urlField.val('');
        $idField.val('');

        $button.remove();
    });
    $('.plpm_pdf_url_field').each(function() {
        var $urlField = $(this);
        if ($urlField.val()) {
            if ($urlField.siblings('.plpm_clear_pdf_button').length === 0) {
                 $('<button type="button" class="button plpm_clear_pdf_button">' + (plpm_media_vars.clearButton || 'Clear PDF') + '</button>')
                     .insertAfter($urlField.siblings('.plpm_upload_pdf_button'));
            }
        }
    });

});