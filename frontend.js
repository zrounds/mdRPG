$(document).ready(function(){
	
	var success_handler = function(data, textStatus, jqXHR){
		alert("success");
		$('#content').html(jqXHR.status);
		$('#content').html(jqXHR.responseText);
	};
	
	var error_handler = function(jqXHR, textStatus, errorThrown){
		alert("failure");
		$('#content2').html(jqXHR.status);
		$('#content2').html(jqXHR.responseText);
	};
	
	var ajax_settings = {
		url: "backend.php",
		type: "GET",
		data: {"ObjectType":"Story"},
		success: success_handler,
		error: error_handler,
		cache: false
	};
	
	
	$.ajax(ajax_settings);
	
	$('#complete').html("<p>Script finished</p>");
});