il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Choice = (function($) {
    let imageHeader = '';
	let editorSelect;
	 
	const setEditorSelect = function(trigger) {
		editorSelect = $(trigger);
		editorSelect.on('change', updateEditor);
	}

    const showMultilineEditor = function() {
        const tinySettings = tinymce.EditorManager.editors[0].settings;
        il.ASQ.Authoring.clearTiny('input[id$=mcdd_text]');

        tinySettings.mode = '';
        tinySettings.selector = 'input[id$=mcdd_text]';
        tinymce.init(tinySettings);

        $('input[id$=mcdd_image]').each((index, item) => {
            const td = $(item).parents('td');
            td.children().hide();

            if (imageHeader.length === 0) {
                const th = td.closest('table').find('th').eq(td.index())[0];
                imageHeader = th.innerHTML;
                th.innerHTML = '';
            }
        });
    }

    const hideMultilineEditor = function() {
        il.ASQ.Authoring.clearTiny('input[id$=mcdd_text]');

        $('input[id$=mcdd_image]').each((index, item) => {
            const td = $(item).parents('td');
            td.children().show();

            if (imageHeader.length > 0) {
                const th = td.closest('table').find('th').eq(td.index())[0];
                th.innerHTML = imageHeader;
                imageHeader = '';
            }
        });
    }

    const updateEditor = function() {
        if (typeof (tinymce) === 'undefined') {
            return;
        }

        // wait for tiny to load
        if (tinymce.EditorManager.editors.length < 1) {
            setTimeout(updateEditor, 250);
            return;
        }

        if (editorSelect.val() === 'false') {
            showMultilineEditor();
        } else {
            hideMultilineEditor();
        }
    }
    
    return { setEditorSelect };
})($);
