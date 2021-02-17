il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Matching = (function($) {
    const MATCHING_ONE_TO_ONE = '0';
    const MATCHING_MANY_TO_ONE = '1';
    const MATCHING_MANY_TO_MANY = '2';
    let matchingMode;

    let usedTerms = [];
    let usedDefinitions = [];

    const updateValues = function(source, destination, useds) {
        const values = {};

        $(`input[id$="${source}"]`).each((index, item) => {
            values[index] = $(item).val();
        });

        $(`select[id$="${destination}"]`).each((index, item) => {
            const that = $(item);
            const selectedVal = that.val();
            that.empty();

            Object.keys(values).forEach((key) => {
                if (!useds.includes(key) || key === selectedVal) {
                    that.append(new Option(values[key], key));
                }
            });

            that.val(selectedVal);
        });
    }

    const updateDefinitions = function() {
        updateValues('me_definition_text', 'me_match_definition',
            usedDefinitions);
    }

    const updateTerms = function() {
        updateValues('me_term_text', 'me_match_term', usedTerms);
    }

    const updateUsed = function(selects, useds) {
        useds.splice(0, useds.length);

        $(`select[id$="${selects}"]`).each((index, item) => {
            const val = $(item).val();
            if (val !== null) {
                useds.push(val);
            }
        });
    }


    const updateUsedDefinitions = function() {
        if (matchingMode === MATCHING_ONE_TO_ONE) {
            updateUsed('me_match_definition', usedDefinitions);
        } else {
            usedDefinitions = [];
        }

        updateValues('me_definition_text', 'me_match_definition',
            usedDefinitions);
    }

    const updateUsedTerms = function() {
        if (matchingMode === MATCHING_ONE_TO_ONE
                || matchingMode === MATCHING_MANY_TO_ONE) {
            updateUsed('me_match_term', usedTerms);
        } else {
            usedTerms = [];
        }

        updateValues('me_term_text', 'me_match_term', usedTerms);
    }

    const cleanAddedRow = function() {
        $('table[name=form_input_12]').find('tr').last().find('select')
            .each((index, item) => {
                $(item).empty();
            });

        updateDefinitions();
        updateTerms();
    }

    const setMatchingMode = function() {
        matchingMode = $('input[name=form_input_9]:checked').val();
        updateUsedDefinitions();
        updateUsedTerms();
    }

    $(document).ready(() => {
        if ($('input[name=form_input_9]').length > 0) {
            setMatchingMode();
        }
    });

    $(document).on('change', 'input[name=form_input_9]', setMatchingMode);

    $(document).on('change', 'input[id$="me_definition_text"]', updateDefinitions);
    $(document).on('change', 'input[id$="me_term_text"]', updateTerms);
    $(document).on('change', 'select[id$=me_match_definition]', updateUsedDefinitions);
    $(document).on('change', 'select[id$=me_match_term]', updateUsedTerms);

    // remove/add needs to trigger after remove event that actually removes the row
    $(document).on('click', 'table[name=form_input_12] .js_add', () => {
        setTimeout(cleanAddedRow, 1);
    });
    $(document).on('click', 'table[name=form_input_12] .js_remove', () => {
        setTimeout(updateUsedDefinitions, 1);
        setTimeout(updateUsedTerms, 1);
    });
    $(document).on('click', 'table[name=form_input_11] .js_remove', () => {
        setTimeout(updateTerms, 1);
    });
    $(document).on('click', 'table[name=form_input_10] .js_remove', () => {
        setTimeout(updateDefinitions, 1);
    });
})($);
