(function ($) {
    const TYPE_NUMBER = 'clz_number';
    const TYPE_DROPDOWN = 'clz_dropdown';
    const TYPE_TEXT = 'clz_text';
    const FORM_INPUT = 'form_input';
    
    const clozeRegex = /├[^┤]*┤/g;

    function updateGapNames($gap_item) {
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
    
    function updateNames() {
        let next_input = 8;
        $(document).find('.form-group:visible').slice(7).each((ix, item) => {
            const $item = $(item);
            if ($item.find('.aot_table').length > 0) {
                $item.find('.aot_table').find('input, select').each((jx, jtem) => {
                    const $jtem = $(jtem);
                    const oldname = $jtem.prop('name');
                    
                    if (oldname.indexOf(FORM_INPUT) === -1) {
                        $jtem.prop('name', oldname.substring(0, 2) + FORM_INPUT + '_' + next_input + oldname.substring(2));
                    }
                    else {
                        $jtem.prop('name', oldname.substring(0, 2) + FORM_INPUT + '_' + next_input + oldname.substring(FORM_INPUT.length + 5));
                    }
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
    
    function createNewGap(i, type = 'text') {
        const template = $(`.cloze_template .${type}`).clone();
        
        template.find('select').eq(0).addClass('js_select_type');
        
        return template.children();
    }

    function addGapItem() {
        $('input[disabled=disabled]').parents('.form-group').remove();
        
        const clozeText = $('input[name=form_input_7]');
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

    function changeGapForm() {
        const selected = $(this);
        const id = selected.prop('id').match(nrRegex);
        let template = null;

        if (selected.val() === TYPE_NUMBER) {
            template = createNewGap(id, 'number');
        } else if (selected.val() === TYPE_TEXT) {
            template = createNewGap(id, 'text');
        } else if (selected.val() === TYPE_DROPDOWN) {
            template = createNewGap(id, 'select');
        }

        const parentItem = selected.parents('.form-group');
        parentItem.nextUntil('.il-standard-form-footer, .il-section-input-header').remove();
        parentItem.after(template);
        parentItem.next().remove();
        parentItem.next().remove();
        
        updateNames();
    }

    function prepareForm() {
        const templateForms = $('.text,.number,.select');

        templateForms.each((index, item) => {
            const form = $(item);
            const template = form.find('.il-section-input');
            form.append(template.children());
            form.children(':not(.il-section-input-header,.form-group)').remove();
            form.find('select:first').addClass('js_select_type');
            form.find('[name]').removeAttr('name');
        });
        
        $('.il-section-input').find('select:first').addClass('js_select_type');
    }
   
    
    function updateClozeText(currentId, replacementId = -1) {
        const clozeText = $('input[name=form_input_7]');
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
    
    function deleteGapItem() {
        const pressedFormItem = $(this).parents('.il-section-input');
        
        const gapCount = $('input[name=form_input_7]').val().match(clozeRegex).length;
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
}(jQuery));
