jQuery(document).ready(function ($) {
    var frame;
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
            var ids = [];
            var preview = $('.cocac-gallery-preview').empty();
            frame.state().get('selection').each(function (attachment) {
                ids.push(attachment.id);
                if (attachment.attributes.sizes && attachment.attributes.sizes.thumbnail) {
                    preview.append('<li data-id="' + attachment.id + '"><img src="' + attachment.attributes.sizes.thumbnail.url + '" /></li>');
                } else {
                    preview.append('<li data-id="' + attachment.id + '"><img src="' + attachment.url + '" /></li>');
                }
            });
            $('#cocac_gallery_image_ids').val(ids.join(','));
        });
        frame.open();
    });
});
