var Jumbotron = function(){

	var place = $('#jumbotron');

	function init(){
		var jumbotron = {"title":"Hello","text":"This is a template for a simple marketing or informational website. ","action":"test"};
		
		$.get('tmpl/tmpl-jumbotron.mst', function(template) {
			var rendered = Mustache.render(template, jumbotron);
			place.html(rendered);
		});


	}


	return {
		init:init
	}
}();


Jumbotron.init();