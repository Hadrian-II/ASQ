(function ($) {
    const TYPE_NUMBER = 'clz_number';
    const TYPE_DROPDOWN = 'clz_dropdown';
    const TYPE_TEXT = 'clz_text';
    
    const clozeRegex = /{[^}]*}/g;

    function updateGapNames($gap_item, index, oldIndex = 0) {
        $gap_item.find('select, input').each((ix, item) => {
            const $item = $(item);
            if ($item.parents('.aot_table').length > 0) {
                return;
            }
            asqAuthoring.processItem($(item), index);
        });
        
        $gap_item.find('select').eq(0).addClass('js_select_type');

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
        const clozeText = $('input[name=form_input_7]');
        const matches = clozeText.val().match(clozeRegex);
        const gapIndex = matches ? matches.length + 1 : 1;

        const cursor = clozeText[0].selectionStart;
        const text = clozeText.val();
        const beforeCursor = text.substring(0, cursor);
        const afterCursor = text.substring(cursor);
        clozeText.val(`${beforeCursor}{${gapIndex}}${afterCursor}`);

        const lastNonGap = clozeText.parents('.form-group');
        lastNonGap.siblings('.il-standard-form-footer').before(createNewGap(gapIndex));
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
    }

    function prepareForm() {
        const templateForms = $('.cloze_template .il-section-input');

        templateForms.each((index, item) => {
            const form = $(item);
            form.parent().append(form.children());
            form.remove();
        });
    }
    
    function deleteGapUIItems($pressedFormItem) {
        $pressedFormItem.prev().remove();
        $pressedFormItem.nextUntil('.il-standard-form-footer, .il-section-input-header').remove();
        $pressedFormItem.remove();        
    }
    
    function updateClozeText(currentId, replacementId = -1) {
        const clozeText = $('input[name=form_input_7]');
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
        
        updateGapNames($formBeginning.nextUntil('.il-standard-form-footer, .il-section-input-header'), newIndex, oldIndex);
    }
    
    function deleteGapItem() {
        const pressedFormItem = $(this).parents('.form-group');
        
        const gapCount = $('input[name=form_input_7]').val().match(clozeRegex).length;
        const doomedGapId = pressedFormItem.prevAll('.il-section-input-header').length - 1;
        
        deleteGapUIItems(pressedFormItem);
        
        updateClozeText(doomedGapId);
        
        if (gapCount > doomedGapId) {
            for (let i = doomedGapId + 1; i <= gapCount; i += 1) {
                updateGapIndex(i);
            }
        }
    }

    $(document).ready(prepareForm);

    $(document).on('change', '.js_select_type', changeGapForm);
    $(document).on('click', '.js_parse_cloze_question', addGapItem);
    $(document).on('click', '.js_delete_button', deleteGapItem);
}(jQuery));
