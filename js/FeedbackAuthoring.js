(function () {

    let hasTiny;
    let tinySettings = {
        selector: 'textarea',
        menubar: false
    };

    $(document).ready(() => {
        hasTiny = typeof (tinymce) !== 'undefined';
        
        if (hasTiny) {
            tinymce.init(tinySettings);
        }
    });
}());
