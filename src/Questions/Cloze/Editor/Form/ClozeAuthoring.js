(function ($) {
    const clozeRegex = /{[^}]*}/g;

    function updateGapNames($gap_item, index, oldIndex = 0) {
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
    
    function createNewGap(i, type = 'text') {
        const template = $(`.cloze_template .${type}`).clone();

        updateGapNames(template, i);

        return template.children();
    }

    function addGapItem() {
        const clozeText = $('#cze_text');
        const matches = $('#cze_text').val().match(clozeRegex);
        const gapIndex = matches ? matches.length + 1 : 1;

        const cursor = clozeText[0].selectionStart;
        const text = clozeText.val();
        const beforeCursor = text.substring(0, cursor);
        const afterCursor = text.substring(cursor);
        clozeText.val(`${beforeCursor}{${gapIndex}}${afterCursor}`);

        const lastNonGap = $('#il_prop_cont_cze_text');
        lastNonGap.siblings('.ilFormFooter').before(createNewGap(gapIndex));
    }

    const nrRegex = /\d*/;

    function changeGapForm() {
        const selected = $(this);
        const id = selected.prop('id').match(nrRegex);
        let template = null;

        if (selected.val() === 'clz_number') {
            template = createNewGap(id, 'number');
        } else if (selected.val() === 'clz_text') {
            template = createNewGap(id, 'text');
        } else if (selected.val() === 'clz_dropdown') {
            template = createNewGap(id, 'select');
        }

        const parentItem = selected.parents('.form-group');
        parentItem.nextUntil('.ilFormFooter, .ilFormHeader').remove();
        parentItem.after(template);
        parentItem.next().remove();
        parentItem.next().remove();
    }

    function prepareForm() {
        $('.cloze_template .ilFormFooter').remove();
        const templateForms = $('.cloze_template .form-horizontal');

        templateForms.each((index, item) => {
            const form = $(item);
            form.parent().append(form.children());
            form.remove();
        });
    }
    
    function deleteGapUIItems($pressedFormItem) {
        $pressedFormItem.prev().remove();
        $pressedFormItem.nextUntil('.ilFormHeader, .ilFormFooter').remove();
        $pressedFormItem.remove();        
    }
    
    function updateClozeText(currentId, replacementId = -1) {
        const clozeText = $('#cze_text');
        const clozeTextVal = clozeText.val();
        const gapStr = `{${currentId}}`;
        let gapIndex = clozeTextVal.indexOf(gapStr);
        const beforeGap = clozeTextVal.substring(0, gapIndex);
        const afterGap = clozeTextVal.substring(gapIndex + gapStr.length);
        if (replacementId > 0) {
            clozeText.val(`${beforeGap}{${replacementId}}${afterGap}`);
        } else {
            clozeText.val(`${beforeGap}${afterGap}`);
        }
    }
    
    function updateGapIndex(oldIndex) {
        const newIndex = oldIndex - 1;
        updateClozeText(oldIndex, newIndex);
        
        const $formBeginning = $(`div[id$="${oldIndex}cze_gap_type"]`).prev();
        
        updateGapNames($formBeginning.nextUntil('.ilFormHeader, .ilFormFooter'), newIndex, oldIndex);
    }
    
    function deleteGapItem() {
        const $pressedFormItem = $(this).parents('.form-group');
        
        const gapCount = $('#cze_text').val().match(clozeRegex).length;
        const doomedGapId = $pressedFormItem.prevAll('.ilFormHeader').length - 1;
        
        deleteGapUIItems($pressedFormItem);
        
        updateClozeText(doomedGapId);
        
        if (gapCount > doomedGapId) {
            for (let i = doomedGapId + 1; i <= gapCount; i += 1) {
                updateGapIndex(i);
            }
        }
    }

    $(document).ready(prepareForm);

    $(document).on('change', 'select[id$=cze_gap_type]', changeGapForm);
    $(document).on('click', '.js_parse_cloze_question', addGapItem);
    $(document).on('click', '.js_delete_button', deleteGapItem);
}(jQuery));
