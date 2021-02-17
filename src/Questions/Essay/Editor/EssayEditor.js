il = il || {};
il.ASQ = il.ASQ || {};
il.ASQ.Essay = (function($) {
    let textLength;
    let maxLength;
    let hasMaxLength = false;

    const updateCounts = function() {
        if (typeof (tinymce) === 'undefined') {
            textLength = $('js_essay').val().length;
        }
        else {
            const body = tinymce.editors[0].getBody();
            const text = tinymce.trim(body.innerText || body.textContent);
            textLength = text.length;            
        }

		if (textLength > maxLength) {
			$('.js_letter_count').parent().addClass('essay_alert');
		}
		else {
			$('.js_letter_count').parent().removeClass('essay_alert');
		}

        $('.js_letter_count').html(textLength);
    }

    const checkValues = function() {
        if (hasMaxLength) {
            if (textLength > maxLength) {
                alert($('.js_error').val());
                return false;
            }
        }

        return true;
    }

    $(document).on('keyup', '.js_essay', updateCounts);
    $(document).on('submit', 'main form', checkValues);

    $(document).ready(() => {
        if (typeof (tinymce) === 'undefined') {
            return;
        }

        tinymce.init({
            selector: 'textarea',
            menubar: false,
            init_instance_callback(editor) {
                editor.on('keyup', updateCounts);
                updateCounts();
            },
        });

        if ($('.js_maxlength').length > 0) {
            maxLength = parseInt($('.js_maxlength').val(), 10);
            hasMaxLength = true;
        }
    });
})($);
