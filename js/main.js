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


var User = function(){
	
	function get(){
		$.get('index.php',{"request":"user"},function(result) {
			if(result.meta.code == 200){
				set(result);
			}
		});
	}

	function set(result){
		console.log(result);
		var place = $("#user-feed");
		$.get('tmpl/tmpl-user-feed.mst',function(template) {
			var rendered = Mustache.render(template, result);
			place.html(rendered);
		});
		
	}


	return {
		get:get
	}
}();

User.get();