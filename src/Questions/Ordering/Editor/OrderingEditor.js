$(document).ready(() => {
    $('.order_list').sortable({
        placeholder: 'placeholder',
        tolerance: 'pointer',
        start(e, ui) {
            ui.placeholder.height(ui.item.outerHeight());
            ui.placeholder.width(ui.item.outerWidth());
        },
        stop() {
            const items = $(this).find('[data-id]');

            const ids = [];
            items.each(function () {
                ids.push($(this).attr('data-id'));
            });

            $(this).siblings('input').val(ids.join(','));
        },
    }).disableSelection();
});
