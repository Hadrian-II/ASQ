/**
 * Markup viewer initialization
 *
 * @author Adrian Lüthi <adi.l@bluewin.ch>
 */

var il = il || {};
il.UI = il.UI || {};

il.UI.question = (function () {

	document.addEventListener('DOMContentLoaded', () => {
		const markups = document.querySelectorAll('.question_markup');
		
		markups.forEach((markup) => {
			new toastui.Editor.factory(
			{
		        el: markup,
       	        viewer: true,
		        initialValue: markup.innerHTML
		    });
		});
	}, false);


})();