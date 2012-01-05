/**
 * Sends ajax request to get the all application's logs and prints them in the page.
 * Calls the action seelogs
 * Uses the template "logsList.html"
 * @function
 */
function seeLogs() {
	deletePeriodicGetStatus();
	$.ajax({
		url: "?ajax=true&action=seelogs",
		type: 'post',
		success: function(data){
			var saveData = data;
			$.get('templates/logsList.html', function(data) {
				$( "#content" ).html( $.tmpl(data, saveData) );
	  			$("#seecustomlogs").button();
    		});
		},
		dataType: 'json'
	});	
}

/**
 * Gets all the fields on the custom search form, sends the corresponding ajax request, and prints the result.
 * Calls the action seelogs
 * Uses the template "logsList.html"
 * @function
 */
function seeCustomLogs() {
	deletePeriodicGetStatus();
	var idUser = $('select#idUser').val();
	var idEvent = $('select#idEvent').val();
	var date = $('select#date').val();
	var dataString = "";	
	
	if (idUser != 'all')	{
		dataString = dataString + "idUser=" + idUser + "&";
	}
	
	if (idEvent != 'all') {
		dataString = dataString + "idEvent=" + idEvent + "&";
	} 

	if (date != 'all') {
		dataString = dataString + "date=" + date + "&";
	}
	
	if (dataString.length >= 1) dataString = dataString.substring(0, dataString.length - 1);
	
	$.ajax({
		url: "?ajax=true&action=seelogs",
		type: 'post',
		data: dataString,
		success: function(data){
			var saveData = data;
			$.get('templates/logsList.html', function(data) {
				$( "#content" ).html( $.tmpl(data, saveData) );
			   $("#seecustomlogs").button();
    	 	});
		},
		dataType: 'json'
	});
}