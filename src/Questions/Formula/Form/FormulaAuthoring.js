il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Formula = (function($, asqAuthoring) {
    const varRegex = /\$(v|r)\d+/g;

    const clearTable = function(selector) {
        const firstRow = $(`${selector} .aot_row`).eq(0);
        firstRow.siblings().remove();
        asqAuthoring.clearRow(firstRow);
    }

    const addRowTo = function(selector) {
        const firstRow = $(`${selector} .aot_row`).eq(0);
        firstRow.after(firstRow.clone());
    }

    const addTableItems = function() {
        clearTable('table[name=form_input_12]');
        clearTable('table[name=form_input_13]');

        const variables = $('input[name=form_input_7]').val().match(varRegex);

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
            addRowTo('table[name=form_input_12]');
        }
        asqAuthoring.setInputIds($('table[name=form_input_12] tbody'));

        for (res; res > 1; res -= 1) {
            addRowTo('table[name=form_input_13]');
        }
        asqAuthoring.setInputIds($('table[name=form_input_13] tbody'));
    }

    $(document).on('click', '.js_parse_question', addTableItems);
})($, il.ASQ.Authoring);
