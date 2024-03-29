/**
 * Handle RealText editor
 *
 * @author Adrian Lüthi <adi.l@bluewin.ch>
 */

var il = il || {};
il.UI = il.UI || {};
il.UI.Input = il.UI.Input || {};

il.UI.Input.Markdown = (function () {

	let editors = {};
	 
	const initiateEditor = function(text) {
	    const initialvalue = text.nextElementSibling.value;
	    
		const editor = new toastui.Editor(
		{
            el: text,
            initialValue: initialvalue,
            initialEditType: 'wysiwyg',
			height: 'auto',
            minHeight: '300px'
        });
        
        if (Object.keys(editors).length === 0) {
        	attachSubmitEvent(text);
        }

        editors[text.id] = editor;

        return editor;
	}
	
	const attachSubmitEvent = function(item) {
		for ( ; item && item !== document; item = item.parentNode ) 
		{
			if (item.matches( 'form' )) 
			{
				item.addEventListener('submit', function(e) {
					Object.values(editors).forEach((editor) => {
						const text = editor.getEditorElements().mdEditor;
						const input = text.parentElement.parentElement.parentElement.parentElement.parentElement.nextElementSibling;
						input.value = editor.getMarkdown();
					})
				});
			}
		}
	}

	/**
	 * Public interface
	 */
	return {
		initiateEditor: initiateEditor
	};
})();