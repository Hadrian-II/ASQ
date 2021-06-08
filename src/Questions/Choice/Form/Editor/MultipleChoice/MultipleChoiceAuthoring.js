var il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Choice = (function($) {
    let imageHeader = '';
	let editorSelect;
	 
	const setEditorSelect = function(trigger) {
		editorSelect = $(trigger);
		editorSelect.on('change', updateEditor);
		updateEditor();
	}

    const showMultilineEditor = function() {
        $('input[id$=mcdd_image]').each((index, item) => {
            const td = $(item).parents('td');
            td.children().hide();

            if (imageHeader.length === 0) {
                const th = td.closest('table').find('th').eq(td.index())[0];
                imageHeader = th.innerHTML;
                th.innerHTML = '';
            }
        });
        
        $('input[id$=mcdd_text]').each((index, item) => {
        	const input = $(item);
        
            input.before(`<div id="${input.attr('id')}_editor" class="rte_field"></div>`);
            
            input.hide();
            
            il.UI.input.realtext.initiateEditor(input.prev()[0]);
        });
    }

    const hideMultilineEditor = function() {
        $('input[id$=mcdd_image]').each((index, item) => {
            const td = $(item).parents('td');
            td.children().show();

            if (imageHeader.length > 0) {
                const th = td.closest('table').find('th').eq(td.index())[0];
                th.innerHTML = imageHeader;
                imageHeader = '';
            }
        });
        
        $('input[id$=mcdd_text]').each((index, item) => {
        	const input = $(item);
        	
        	il.UI.input.realtext.storeInputOfId(input.attr('id') + '_editor');
        
        	input.siblings().remove();
        
			input.show();
        });
    }

    const updateEditor = function() {
        if (editorSelect.val() === 'false') {
            showMultilineEditor();
        } else {
            hideMultilineEditor();
        }
    }
    
    return { setEditorSelect };
})($);
