(function ($) {
    const NO_AUTO = '1';
    const AUTO_ANY = '2';
    const AUTO_ALL = '3';
    const AUTO_ONE = '4';
    
    let pointsHeader = '';
    
    function processForm() {
        const value = $('input[name=form_input_9]:checked').val();
        const pointsInput = $('input[name=form_input_10]').parents('.form-group');
        const answersInput = $('table[name=form_input_11]').parents('.form-group');
        
        switch (value) {
            case AUTO_ANY:
                pointsInput.hide();
                answersInput.show();
                showPointsRow();
                break;
            case AUTO_ALL:
            case AUTO_ONE:
                pointsInput.show();
                answersInput.show();
                hidePointsRow();
                break;
            case NO_AUTO:
            default:
                pointsInput.hide();
                answersInput.hide();
        }
    }
    
    function showPointsRow() {
        $('input[id$=es_def_points').each((index, item) => {
            const td = $(item).parents('td');
            td.children().show();

            if (pointsHeader.length > 0) {
                const th = td.closest('table').find('th').eq(td.index())[0];
                th.innerHTML = pointsHeader;
                pointsHeader = '';
            }
        }); 
      
    }
    
    function hidePointsRow() {
        $('input[id$=es_def_points]').each((index, item) => {
            const td = $(item).parents('td');
            td.children().hide();

            if (pointsHeader.length === 0) {
                const th = td.closest('table').find('th').eq(td.index())[0];
                pointsHeader = th.innerHTML;
                th.innerHTML = '';
            }
        });         
    }

    $(document).ready(processForm);
    $(document).on('change', 'input[name=form_input_9]', processForm);
}(jQuery));
