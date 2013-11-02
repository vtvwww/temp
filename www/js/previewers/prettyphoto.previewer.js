/* previewer-description:text_prettyphoto */

$.loadCss(['/lib/js/prettyphoto/css/prettyPhoto.css']);
$.getScript(current_path + '/' + 'lib/js/prettyphoto/js/jquery.prettyPhoto.js');

$.fn.cePreviewerMethods = {
	display: function(elm) {
		
		var inited = elm.data('inited');
		
		if (inited != true) {
			var rel = elm.attr('rel');
			var elms = $('a[rel="' + rel + '"]');
			elms.data('inited', true);
			
			elms.prettyPhoto();
			elm.click();
		}
	}
}