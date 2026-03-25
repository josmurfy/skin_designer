EJS.Helpers.prototype.vie_style_font = function(font_value) {
	var fonts = font_value.split(',');

	for (var i = 0; i < fonts.length; i++) {
		fonts[i] = fonts[i].trim();

		$('head').append('<link href="//fonts.googleapis.com/css?family='+ fonts[i].trim().replace(/\s+/g, '+') +'" rel="stylesheet" type="text/css" />');

		if (/\s/.test(fonts[i])) {
			fonts[i] = "'" + fonts[i] + "'";
		}
	}

	return fonts.join(',');
}

EJS.Helpers.prototype.vie_style_image = function(value) {
	return 'image/' + value;
}

var $head = $('head');

if (!$('#vie-theme-editor-style').length) {
	$head.append('<style id="vie-theme-editor-style"></style>')
}

window.addEventListener("message", function (event) {
	if (event.data) {
		var eventData = event.data;

		if (eventData.skin_options) {
			var skin_id = event.data.skin_id;

			// Use default if skin file is not exist
			if (!$('#skin-' + skin_id).length) {
				skin_id = 'default';
			}
			
			var template = new EJS({element: 'skin-' + skin_id, type: '['});

			var skinCss = template.render({
				o: event.data.skin_options
			});

			document.getElementById('vie-theme-editor-style').innerHTML = skinCss;
		} else if (eventData.url) {
			event.source.postMessage({
				url: window.location.href
			}, '*');
		} else if (eventData.skin_css) {
			event.source.postMessage({
				skin_css: $('#vie-theme-editor-style').html()
			}, '*');
		}
	}
}, false);

$(document).ready(function () {
	if (location.href.indexOf('https') === 0) {
		$('a').each(function () {
			this.href = this.href.replace('http:', 'https:');
		});
	}
});