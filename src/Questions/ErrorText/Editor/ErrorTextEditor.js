(function ($) {
    function wordSelected() {
        const word = $(this);
        const index = word.attr('data-index');
        const selectedInput = word.siblings('input').eq(0);
        let selectedWords = (selectedInput.val().length > 0 ? selectedInput
            .val().split(',') : []);

        word.toggleClass('selected');

        if (word.hasClass('selected')) {
            selectedWords.push(index);
        } else {
            selectedWords = selectedWords.filter((value) => value !== index);
        }

        selectedInput.val(selectedWords.join(','));
    }

    $(document).on('click', '.errortext_word', wordSelected);
}(jQuery));
