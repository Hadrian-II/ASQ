const asqAuthoring = (function () {
    //number used to create newlines and swap lines to prevent radiogroup clashes
    const RADIO_SAFE_OFFSET = 1234567890;

    let hasTiny;
    let tinySettings = {
        selector: '[name=form_input_5]:first,textarea[name$=_hint_content]',
        menubar: false
    };

    function clearTiny(selector = null) {
    	if (selector) {
    		tinySettings.selector = '[name=form_input_5], ' + selector;
    	}
    
        let i;
        const editors = tinymce.editors.map((x) => x);
        for (i = 0; i < editors.length; i += 1) {
            const editor = editors[i];
            const element = $(editor.getElement());

            if (selector && !element.is(selector)) {
                continue;
            }

            element.val(editor.getContent());
            element.show();

            tinymce.EditorManager.remove(editor);

            element.siblings('.mceEditor').remove();
        }
    }

    function clearRow(row) {
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

    function updateInputName(oldName, currentRow) {
        return currentRow + oldName.match(/\D.*/);
    }

    function processItem(input, currentRow) {
        input.attr('name', updateInputName(input.attr('name'), currentRow));
        input.prop('id', updateInputName(input.prop('id'), currentRow));
    }

    function processRow(row, currentRow) {
        row.find('input[name],textarea[name],select').each((index, item) => {
            processItem($(item), currentRow);
        });
    }

    function setInputIds(table) {
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

    function addRow() {
        const row = $(this).parents('.aot_row').eq(0);
        const table = $(this).parents('.aot_table').children('tbody');

		const containsTiny = table.find('.tox').length > 0;

        if (hasTiny && containsTiny) {
            clearTiny();
        }

        let newRow = row.clone();

        newRow = clearRow(newRow);
        //set ids to big number to prevent clash of ids/radiogroups
        processRow(newRow, RADIO_SAFE_OFFSET);
        row.after(newRow);
        setInputIds(table);

        if (hasTiny && containsTiny) {
            tinymce.init(tinySettings);
        }

        return false;
    }

    function saveTiny() {
        if (!hasTiny) {
            return;
        }

        let i;
        for (i = 0; i < tinymce.editors.length; i += 1) {
            const editor = tinymce.editors[i];
            const element = $(editor.getElement());

            element.val(editor.getContent());
        }
    }

    function removeRow() {
        const row = $(this).parents('.aot_row');
        const table = $(this).parents('.aot_table').children('tbody');

		const containsTiny = table.find('.tox').length > 0;

        if (hasTiny && containsTiny) {
            clearTiny();
        }

        if (table.children().length > 1) {
            row.remove();
            setInputIds(table);
        } else {
            clearRow(row);
        }

        if (hasTiny && containsTiny) {
            tinymce.init(tinySettings);
        }
    }

    function upRow() {
        const row = $(this).parents('.aot_row');
        row.prev('.aot_row').before(row);
        setInputIds(row.parents('.aot_table').children('tbody'));
    }

    function downRow() {
        const row = $(this).parents('.aot_row');
        row.next('.aot_row').after(row);
        setInputIds(row.parents('.aot_table').children('tbody'));
    }

    $(document).ready(() => {
        // hack to prevent image verification error
        $('[name=ilfilehash]').remove();
        hasTiny = typeof (tinymce) !== 'undefined';
        
        if (hasTiny) {
            tinymce.init(tinySettings);
        }
    });

    $(document).on('click', '.js_add', addRow);
    $(document).on('click', '.js_remove', removeRow);
    $(document).on('click', '.js_up', upRow);
    $(document).on('click', '.js_down', downRow);
    $(document).on('submit', 'form', saveTiny);

    return {
        clearTiny, clearRow, setInputIds, processItem, processRow,
    };
}());
