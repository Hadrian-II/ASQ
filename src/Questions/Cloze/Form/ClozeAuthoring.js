il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Cloze = (function($) {
    const TYPE_NUMBER = 'clz_number';
    const TYPE_DROPDOWN = 'clz_dropdown';
    const TYPE_TEXT = 'clz_text';
    const FORM_INPUT = 'form_input';
    
    const clozeRegex = /├[^┤]*┤/g;
	const renameRegex = /(\d+_form_input_)(\d+)(_.*)$/;

	let clozeText;
	 
	const setClozeText = function(text) {
		clozeText = $(text);
	}

    const updateGapNames = function($gap_item) {
        $gap_item.find('select, input').each((ix, item) => {
            const $item = $(item);
            if ($item.parents('.aot_table').length > 0) {
                return;
            }
            asqAuthoring.processItem($(item), index);
        });
        
        $($gap_item.find('.aot_table input')).each((ix, item) => {
            const input = $(item);
            input.prop('id', input.prop('id').replace(oldIndex.toString(), index.toString()));
            input.prop('name', input.prop('name').replace(oldIndex.toString(), index.toString()));
        });        
    }
    
    const updateNames = function() {
        let next_input = 8;
        $(document).find('.form-group:visible').slice(7).each((ix, item) => {
            const $item = $(item);
            if ($item.find('.aot_table').length > 0) {
                $item.find('.aot_table').find('input, select').each((jx, jtem) => {
                    const $jtem = $(jtem);
                    const oldName = $jtem.prop('id');
					
					const newName = oldName.replace(renameRegex, '$1' + next_input + '$3')
                    
                    $jtem.prop('name', newName);
                    $item.prop('id', newName);
                });
                
                next_input += 1;
            }
            else {
                $item.find('input, select').each((jx, jtem) => {
                    $jtem = $(jtem);
                    
                    if ($jtem.siblings().length === 1) {
                        //type select has a sibling (the delete button) and is the first of a section
                        //so increase input by +1 for the section input item of the form
                        next_input += 1;
                    }
                    
                    $jtem.attr('name', FORM_INPUT + '_' + next_input);
                    next_input += 1;
                });
            }
        });
    }
    
    const createNewGap = function(i, type = 'text') {
        const template = $(`.cloze_template .${type}`).clone();
        
        template.find('select').eq(0).addClass('js_select_type');
        
        return template.children();
    }

    const addGapItem = function() {
        $('input[disabled=disabled]').parents('.form-group').remove();
        
        const matches = clozeText.val().match(clozeRegex);
        const gapIndex = matches ? matches.length + 1 : 1;

        const cursor = clozeText[0].selectionStart;
        const text = clozeText.val();
        const beforeCursor = text.substring(0, cursor);
        const afterCursor = text.substring(cursor);
        clozeText.val(`${beforeCursor}├${gapIndex}┤${afterCursor}`);

        const lastNonGap = clozeText.parents('.form-group');
        lastNonGap.siblings('.il-standard-form-footer').before(createNewGap(gapIndex));
        
        updateNames();
    }

    const nrRegex = /\d*/;

    const changeGapForm = function() {
        const selected = $(this);
        const id = selected.prop('id').match(nrRegex);
        let template = null;

        if (selected.val() === TYPE_NUMBER) {
            template = createNewGap(id, 'number').children();
        } else if (selected.val() === TYPE_TEXT) {
            template = createNewGap(id, 'text').children();
        } else if (selected.val() === TYPE_DROPDOWN) {
            template = createNewGap(id, 'select').children();
        }

        const parentItem = selected.parents('.form-group');
        parentItem.nextUntil('.il-standard-form-footer, .il-section-input-header').remove();
        parentItem.after(template);
        parentItem.next().remove();
        parentItem.next().remove();
        
        updateNames();
    }

    const prepareForm = function() {
        const templateForms = $('.text,.number,.select');

        templateForms.each((index, item) => {
            const form = $(item);
            const template = form.find('.il-section-input');
            form.append(template);
            form.children(':not(.il-section-input)').remove();
            form.find('select:first').addClass('js_select_type');
            form.find('[name]').removeAttr('name');
        });
        
        $('.il-section-input').find('select:first').addClass('js_select_type');
    }
   
    
    const updateClozeText = function(currentId, replacementId = -1) {
        const clozeTextVal = clozeText.val();
        const gapStr = `├${currentId}┤`;
        let gapIndex = clozeTextVal.indexOf(gapStr);
        const beforeGap = clozeTextVal.substring(0, gapIndex);
        const afterGap = clozeTextVal.substring(gapIndex + gapStr.length);
        if (replacementId > 0) {
            clozeText.val(`${beforeGap}├${replacementId}┤${afterGap}`);
        } else {
            clozeText.val(`${beforeGap}${afterGap}`);
        }
    }
    
    const deleteGapItem = function() {
        const pressedFormItem = $(this).parents('.il-section-input');
        
        const gapCount = clozeText.val().match(clozeRegex).length;
        const doomedGapId = pressedFormItem.prevAll('.il-section-input').length + 1;
        
        pressedFormItem.remove();
        
        updateClozeText(doomedGapId);
        
        if (gapCount > doomedGapId) {
            for (let i = doomedGapId + 1; i <= gapCount; i += 1) {
                updateClozeText(i, i - 1);
            }
        }
        
        updateNames();
    }

    $(document).ready(prepareForm);

    $(document).on('change', '.js_select_type', changeGapForm);
    $(document).on('click', '.js_parse_cloze_question', addGapItem);
    $(document).on('click', '.js_delete_button', deleteGapItem);
    
    return { setClozeText };
})($);
