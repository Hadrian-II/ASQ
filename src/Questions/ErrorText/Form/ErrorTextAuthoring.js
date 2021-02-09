(function ($) {
    class ErrorDefinition {
        constructor(start, length) {
            this.start = start;
            this.length = length;
        }
    }

    function prepareTable(length) {
        $('.aot_table_div').show();
        const table = $('.aot_table tbody');
        const row = table.children().eq(0);

        row.siblings().remove();

        asqAuthoring.clearRow(row);

        while (length > table.children().length) {
            table.append(row.clone());
        }

        asqAuthoring.setInputIds(table);
    }

    function findErrors(text) {
        const errors = [];

        let multiword = false;
        let multilength = 0;

        let i;
        for (i = 0; i < text.length; i += 1) {
            if (text[i].startsWith('#')) {
                errors.push(new ErrorDefinition(i, 1));
            } else if (text[i].startsWith('((')) {
                multiword = true;
                multilength = 0;
            }

            if (multiword) {
                multilength += 1;
            }

            if (multiword && text[i].endsWith('))')) {
                errors.push(new ErrorDefinition(i - (multilength - 1), multilength));
                multiword = false;
            }
        }

        return errors;
    }

    function storeErrors(errors) {
        const name = $('.aot_table').attr('name');
        
        $('.aot_table tbody').children().each((i, rrow) => {
            const error = errors[i];
            const row = $(rrow);

            row.find(`#${i + 1}_${name}_etsd_word_index`).val(error.start);
            row.find(`#${i + 1}_${name}_etsd_word_length`).val(error.length);
        });
    }

    function displayErrors(errors, text) {
        $('.aot_table tbody').children().each((i, rrow) => {
            const error = errors[i];
            const row = $(rrow);
            let label = text.slice(error.start, error.start + error.length).join(' ');
            label = label.replace(/[.,!?#\(\)]/g, '');

            row.find('.etsd_wrong_text').text(label);
        });
    }

    function processErrorText() {
        const text = getErrorText();

        const errors = findErrors(text);

        if (errors.length > 0) {
            prepareTable(errors.length);
        } else {
            $('.aot_table_div').hide();
        }

        storeErrors(errors);
        displayErrors(errors, text);
    }
    
    function getErrorText() {
        return $('#process_error_text').parent().siblings('textarea').val().split(' ');
    }

    $(document).on('click', '#process_error_text', processErrorText);

    $(document).ready(() => {
        const text = getErrorText();

        const errors = findErrors(text);

        if (errors.length === 0) {
            $('.aot_table_div').hide();
        }

        storeErrors(errors);
        displayErrors(errors, text);
    });
}(jQuery));
