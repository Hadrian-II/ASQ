il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Matching = (function($) {
    const MATCHING_ONE_TO_ONE = '0';
    const MATCHING_MANY_TO_ONE = '1';
    const MATCHING_MANY_TO_MANY = '2';
    let matchingMode;

    let usedTerms = [];
    let usedDefinitions = [];

    let modeSelect;
    let matchTable;
    
    const setModeSelect = function(select) {
    	$(select).on('change', setMatchingMode);
		modeSelect = $(select);
		setMatchingMode();
	}
    
    const setDefinitionsTable = function(table) {
		$(`#${table.attr('id')}`).on('click', '.js_remove', () => {
	        setTimeout(updateDefinitions, 1);
	    });
	}
    
    const setTermsTable = function(table) {
		$(`#${table.attr('id')}`).on('click', '.js_remove', () => {
	        setTimeout(updateTerms, 1);
	    });
	}
	
	const setMatchTable = function(table) {
		$(`#${table.attr('id')}`).on('click', '.js_remove', () => {
	        setTimeout(updateUsedDefinitions, 1);
	        setTimeout(updateUsedTerms, 1);
	    });
		$(`#${table.attr('id')}`).on('click', '.js_add', () => {
	        setTimeout(cleanAddedRow, 1);
	    });
		matchTable = $(table);
	}

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
        updateValues(
        	'me_definition_text', 
        	'me_match_definition',
            usedDefinitions
        );
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

        updateValues(
        	'me_definition_text', 
        	'me_match_definition',
            usedDefinitions
        );
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
        matchTable.find('tr').last().find('select')
            .each((index, item) => {
                $(item).empty();
            });

        updateDefinitions();
        updateTerms();
    }

    const setMatchingMode = function() {
        matchingMode = modeSelect.find(':checked').val();
        updateUsedDefinitions();
        updateUsedTerms();
    }

    $(document).on('change', 'input[id$="me_definition_text"]', updateDefinitions);
    $(document).on('change', 'input[id$="me_term_text"]', updateTerms);
    $(document).on('change', 'select[id$=me_match_definition]', updateUsedDefinitions);
    $(document).on('change', 'select[id$=me_match_term]', updateUsedTerms);
    
    return { setModeSelect, setDefinitionsTable, setTermsTable, setMatchTable };
})($);
