(function ($) {
    let imageHeader = '';

    function showMultilineEditor() {
        const tinySettings = tinymce.EditorManager.editors[0].settings;
        asqAuthoring.clearTiny('input[id$=mcdd_text]');

        tinySettings.mode = '';
        tinySettings.selector = 'input[id$=mcdd_text]';
        tinymce.init(tinySettings);

        $('input[id$=mcdd_image]').each((index, item) => {
            const td = $(item).parents('td');
            td.children().hide();

            if (imageHeader.length === 0) {
                const th = td.closest('table').find('th').eq(td.index())[0];
                imageHeader = th.innerHTML;
                th.innerHTML = '';
            }
        });
    }

    function hideMultilineEditor() {
        asqAuthoring.clearTiny('input[id$=mcdd_text]');

        $('input[id$=mcdd_image]').each((index, item) => {
            const td = $(item).parents('td');
            td.children().show();

            if (imageHeader.length > 0) {
                const th = td.closest('table').find('th').eq(td.index())[0];
                th.innerHTML = imageHeader;
                imageHeader = '';
            }
        });
    }

    function updateEditor() {
        if (typeof (tinymce) === 'undefined') {
            return;
        }

        // wait for tiny to load
        if (tinymce.EditorManager.editors.length < 1) {
            setTimeout(updateEditor, 250);
            return;
        }

        if ($('select[name=form_input_8]').val() === 'false') {
            showMultilineEditor();
        } else {
            hideMultilineEditor();
        }
    }

    $(window).load(() => {
        updateEditor();
    });

    $(document).on('change', 'select[name=form_input_8]', updateEditor);
}(jQuery));
