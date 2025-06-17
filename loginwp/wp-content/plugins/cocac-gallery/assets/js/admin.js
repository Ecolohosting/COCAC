jQuery(document).ready(function ($) {
    var frame;

    function updateInput() {
        var ids = [];
        $('.cocac-gallery-preview li').each(function () {
            ids.push($(this).data('id'));
        });
        $('#cocac_gallery_image_ids').val(ids.join(','));
    }

    $('.cocac-gallery-preview').sortable({
        update: updateInput
    }).on('click', '.cocac-remove-image', function () {
        $(this).closest('li').remove();
        updateInput();
    });

    $('.cocac-add-gallery-images').on('click', function (e) {
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Seleccionar imágenes',
            button: { text: 'Usar imágenes' },
            multiple: true
        });
        frame.on('select', function () {
            frame.state().get('selection').each(function (attachment) {
                if ($('.cocac-gallery-preview li[data-id="' + attachment.id + '"]').length === 0) {
                    var url = attachment.url;
                    if (attachment.attributes.sizes && attachment.attributes.sizes.thumbnail) {
                        url = attachment.attributes.sizes.thumbnail.url;
                    }
                    $('.cocac-gallery-preview').append('<li data-id="' + attachment.id + '"><img src="' + url + '" /><button type="button" class="cocac-remove-image">&times;</button></li>');
                }
            });
            updateInput();
        });
        frame.open();
    });
});
