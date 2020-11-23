const asqAuthoring = (function () {
    let hasTiny;
    let tinySettings = {
        selector: 'textarea',
        menubar: false
    };

    function clearTiny(selector = null) {
    	if (selector) {
    		tinySettings.selector = 'textarea, ' + selector;
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
                input.attr('checked', false);
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
        const newName = updateInputName(input.attr('name'), currentRow);

        // if already an item with the new name exists
        // (when swapping) set the other element name
        // to current oldname to prevent name collision
        // and losing of radio values
        if (input.attr('type') === 'radio') {
            const existingGroup = $(`[name="${newName}"]`);

            if (existingGroup.length > 0) {
                const myName = input.attr('name');
                const myGroup = $(`name="${myName}"]`);
                myGroup.attr('name', 'totally_random');
                existingGroup.attr('name', myName);
                myGroup.attr('name', newName);
            }
        } else {
            input.attr('name', newName);
        }

        input.prop('id', updateInputName(input.prop('id'), currentRow));
    }

    function processRow(row, currentRow) {
        row.find('input[name],textarea[name],select').each((index, item) => {
            processItem($(item), currentRow);
        });
    }

    function setInputIds(table) {
        let currentRow = 1;

        table.children().each((index, item) => {
            processRow($(item), currentRow);
            currentRow += 1;
        });
    }

    function addRow() {
        const row = $(this).parents('.aot_row').eq(0);
        const table = $(this).parents('.aot_table').children('tbody');

        if (hasTiny) {
            clearTiny();
        }

        let newRow = row.clone();

        newRow = clearRow(newRow);
        row.after(newRow);
        setInputIds(table);

        if (hasTiny) {
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

        if (hasTiny) {
            clearTiny();
        }

        if (table.children().length > 1) {
            row.remove();
            setInputIds(table);
        } else {
            clearRow(row);
        }

        if (hasTiny) {
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
