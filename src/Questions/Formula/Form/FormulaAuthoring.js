var il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Formula = (function($, asqAuthoring) {
    const varRegex = /\$(v|r)\d+/g;

    let formulaText;
    let variablesTable;
    let resultsTable;
    
    const setFormulaText = function(text) {
		formulaText = $(text);
	}
    
    const setVariablesTable = function(table) {
		variablesTable = $(table).parents('.form-group');
	}
    
    const setResultsTable = function(table) {
		resultsTable = $(table).parents('.form-group');
	}

    const clearTable = function(table) {
        const firstRow = table.find('.aot_row').eq(0);
        firstRow.siblings().remove();
        asqAuthoring.clearRow(firstRow);
    }

    const addRowTo = function(table) {
        const firstRow = table.find('.aot_row').eq(0);
        firstRow.after(firstRow.clone());
    }

    const addTableItems = function() {
        clearTable(variablesTable);
        clearTable(resultsTable);

        const variables = formulaText.val().match(varRegex);

        let vars = 0;
        let res = 0;

        variables.forEach((v) => {
            if (v.charAt(1) === 'v') {
                vars += 1;
            } else {
                res += 1;
            }
        });

        for (vars; vars > 1; vars -= 1) {
            addRowTo(variablesTable);
        }
        asqAuthoring.setInputIds(variablesTable.find('tbody'));

        for (res; res > 1; res -= 1) {
            addRowTo(resultsTable);
        }
        asqAuthoring.setInputIds(resultsTable.find('tbody'));
    }

    $(document).on('click', '.js_parse_question', addTableItems);
    
    return { setFormulaText, setVariablesTable, setResultsTable };
})($, il.ASQ.Authoring);
