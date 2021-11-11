var il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.ImageMap = (function($) {
    const shapeClick = function() {
        const shape = $(this);

        if (shape.hasClass('multiple_choice')) {
            shape.toggleClass('selected');

            const max = $('#max_answers').val();
            const current = shape.parent().find('.selected').length;

            if (max > 0 && current > max) {
                shape.removeClass('selected');
            }
        } else {
            shape.parents('.imagemap_editor').find('svg .selected')
                .removeClass('selected');
            shape.addClass('selected');
        }

        const selected = [];

        shape.parents('.imagemap_editor').find('svg .selected').each(
            (index, item) => {
                selected.push($(item).attr('data-value'));
            },
        );

        $('#answer').val(selected.join(','));
    }

    $(document)
        .on(
            'click',
            '[data-enabled="true"] .imagemap_editor rect, [data-enabled="true"] .imagemap_editor ellipse, [data-enabled="true"] .imagemap_editor polygon',
            shapeClick,
        );
})($);
