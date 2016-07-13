
var ajax_success_handler = function(data, textStatus, jqXHR) {
    $('#returnstatus').html(jqXHR.status);
    $('#returntext').html(jqXHR.responseText);
};

var ajax_error_handler = function(jqXHR, textStatus, errorThown) {
    $('#returnstatus').html(jqXHR.status);
    $('#returntext').html(jqXHR.responseText);
}


var form_submit_handler = function (e) {
    e.preventDefault();
    e.stopPropagation();

    // Collect parameter name/value pairs as data
    var data_pairs = {}
    $('#params div').each(function (i, e) {
	    var pname = $.trim($(e).find('input.pname').val());
	    if (pname != "") {
		data_pairs[$(e).find('input.pname').val()] =
 		    $(e).find('input.pval').val();
	    }
	});
		
    // Get URL from rest_url text input
    var ajax_url = $('#rest_url').val();

    // Set up settings for AJAX call
    var settings = {
	type: $('#methodselect option:selected').val(),
	data: data_pairs,
	success: ajax_success_handler,
	error: ajax_error_handler,
	cache: false
    }

    // Make AJAX call
    $.ajax(ajax_url, settings);
};



$(document).ready(function () {
	$('#restform').on('submit', form_submit_handler);
	$('#clearbutton').on('click', function () {
		$('#params input').each(function (i,e) {
			$(e).val('');
		    });
	    });
	$('#addparam').on('click', function () {
		var new_param = $("<div>Name: <input class='pname' type=text> Value: <input class='pval' type=text></div>");
		$('#params').append(new_param);
	    });
    });
