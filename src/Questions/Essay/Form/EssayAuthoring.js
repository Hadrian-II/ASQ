var il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Essay = (function($) {
    const NO_AUTO = '1';
    const AUTO_ANY = '2';
    const AUTO_ALL = '3';
    const AUTO_ONE = '4';
    
    let pointsHeader = '';
    
    let scoringMode;
    let pointsInput;
    let answersInput;
    
    const setScoringMode = function(select) {
		scoringMode = $(select);
		scoringMode.on('change', processForm);
		processIfReady();
	}
    
    const setPointsInput = function(text) {
		pointsInput = $(text).parents('.form-group');
		processIfReady();
	}
    
    const setAnswersInput = function(text) {
		answersInput = $(text).parents('.form-group');
		processIfReady();
	}
    
    const processIfReady = function() {
    	if (scoringMode !== undefined &&
    	    pointsInput !== undefined &&
    	    answersInput !== undefined) {
    		processForm();    
    	}
    }
    
    const processForm = function() {
        const value = scoringMode.find(':checked').val();
        
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
    
    const showPointsRow = function() {
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
    
    const hidePointsRow = function() {
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

	return { setScoringMode, setPointsInput, setAnswersInput };
})($);
