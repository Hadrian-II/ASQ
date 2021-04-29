il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Authoring = (function($) {
    //number used to create newlines and swap lines to prevent radiogroup clashes
    const RADIO_SAFE_OFFSET = 1234567890;

    const clearRow = function(row) {
        row.find('input[type!="Button"], textarea').each((index, item) => {
            const input = $(item);

            if (input.attr('type') === 'radio'
                    || input.attr('type') === 'checkbox') {
                input.prop('checked', false);
            } else {
                input.val('');
            }
        });

        row.find('span').each((index, item) => {
            const span = $(item);
            if (span.children().length === 0) {
                span.html('');
            }
        });

        return row;
    }

    const updateInputName = function(oldName, currentRow) {
        return currentRow + oldName.match(/\D.*/);
    }

    const processItem = function(input, currentRow) {
        input.attr('name', updateInputName(input.attr('name'), currentRow));
        input.prop('id', updateInputName(input.prop('id'), currentRow));
    }

    const processRow = function(row, currentRow) {
        row.find('input[name],textarea[name],select').each((index, item) => {
            processItem($(item), currentRow);
        });
    }

    const setInputIds = function(table) {
        if (table.find('input[type=radio]').length > 0) {
            // create row ids that are outside existing values, as radio group would be the same over multiple lines
            // (one row would loose its value when adding/switching rows with radios)
            const offset = RADIO_SAFE_OFFSET;

            table.children().each((index, item) => {
                processRow($(item), index + offset);
            });
        }

        table.children().each((index, item) => {
            processRow($(item), index + 1);
        });
    }

    const addRow = function() {
        const row = $(this).parents('.aot_row').eq(0);
        const table = $(this).parents('.aot_table').children('tbody');

        let newRow = row.clone();

        newRow = clearRow(newRow);
        //set ids to big number to prevent clash of ids/radiogroups
        processRow(newRow, RADIO_SAFE_OFFSET);
        row.after(newRow);
        setInputIds(table);

        return false;
    }

    const removeRow = function() {
        const row = $(this).parents('.aot_row');
        const table = $(this).parents('.aot_table').children('tbody');

        if (table.children().length > 1) {
            row.remove();
            setInputIds(table);
        } else {
            clearRow(row);
        }
    }

    const upRow = function() {
        const row = $(this).parents('.aot_row');
        row.prev('.aot_row').before(row);
        setInputIds(row.parents('.aot_table').children('tbody'));
    }

    const downRow = function() {
        const row = $(this).parents('.aot_row');
        row.next('.aot_row').after(row);
        setInputIds(row.parents('.aot_table').children('tbody'));
    }

    $(document).on('click', '.js_add', addRow);
    $(document).on('click', '.js_remove', removeRow);
    $(document).on('click', '.js_up', upRow);
    $(document).on('click', '.js_down', downRow);

    return {
        clearRow, setInputIds, processItem, processRow,
    };
})($);
