(function ($) {
    function answerSelected() {
        const parent = $(this).parents('.js_multiple_choice');
        const max = parent.children('.js_max_answers').val();
        const current = parent.find('.js_multiple_choice_answer:checkbox:checked').length;

        if (current > max) {
            $(this).prop('checked', false);
            parent.children('.js_limit').css('color', 'red');
        } else {
            parent.children('.js_limit').css('color', '');
        }
    }

    $(document).on('change', 'input[type=checkbox].js_multiple_choice_answer', answerSelected);
}(jQuery));
