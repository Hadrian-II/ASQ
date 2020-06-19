(function ($) {
    const varRegex = /\$(v|r)\d+/g;

    function clearTable(selector) {
        const firstRow = $(`${selector} .aot_row`).eq(0);
        firstRow.siblings().remove();
        asqAuthoring.clearRow(firstRow);
    }

    function addRowTo(selector) {
        const firstRow = $(`${selector} .aot_row`).eq(0);
        firstRow.after(firstRow.clone());
    }

    function addTableItems() {
        clearTable('#il_prop_cont_fs_variables');
        clearTable('#il_prop_cont_answer_options');

        const variables = $('#fs_formula').val().match(varRegex);

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
            addRowTo('#il_prop_cont_fs_variables');
        }
        asqAuthoring.setInputIds($('#il_prop_cont_fs_variables tbody'));

        for (res; res > 1; res -= 1) {
            addRowTo('#il_prop_cont_answer_options');
        }
        asqAuthoring.setInputIds($('#il_prop_cont_answer_options tbody'));
    }

    $(document).on('click', '.js_parse_question', addTableItems);
}(jQuery));
