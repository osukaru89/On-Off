/**
 * Gets all the data about invitations, and generates a page to add or remove them.
 * Calls the actions viewinvit and addinvit
 * Uses the template "invitations.html"
 * @function
 */
function manageInvits() {
	deletePeriodicGetStatus();
	$.ajax({
		url: "?ajax=true&action=viewinvit",
		type: 'post',
		success: function(data){	
			var saveData = data;

			$.get('templates/invitations.html', function(data) {
				$('#content').html( $.tmpl(data, saveData) );

				var $ifHost = $("#ifHost");							
				var $ifFarm = $("#ifFarm");
				$("#hostOrFarm").buttonset();
				$ifFarm.hide();										
				$("#hostChoice").click( function (){
					$ifHost.show();								
					$ifFarm.hide();							
				});
				$("#farmChoice").click( function (){
					$ifFarm.show();								
					$ifHost.hide();							
				});		
				
				$("#dateBegin").datepicker();
				$("#dateEnd").datepicker();

				$( "#dialog-invitation" ).dialog({
					autoOpen: false,
					resizable: false,
					height:240,
					modal: true,
					buttons: {
						"OK": function() {
							deleteInvit ($(".toDelete")[0].id);						
							$("td").removeClass("ui-state-error");
							$(this).dialog("close");
						},
						Cancel: function() {
							$("td").removeClass("ui-state-error");
							$(this).dialog("close");
						}
					}
				});
				
				$(".delInvit").button().click( function() {
					$("tr#" + this.id + " td").addClass("ui-state-error");
					$("tr#" + this.id).addClass("toDelete");
					$("#dialog-invitation").dialog("open");
					return false;
				}); 
				
				$("#createInvit").button().click( function() {

					var people = $("input#people").val();
					var project = $("input#project").val();
					var email = $("input#email").val();
					var dateBegin = $("input#dateBegin").val();
					var dateEnd = $("input#dateEnd").val();

					hostChoice = document.getElementById("hostChoice");
					farmChoice = document.getElementById("farmChoice");

					if (hostChoice.checked) {
						data2send = "&typeOfSubject=host" + "&idSubject=" + $("select#allHosts").val();
					} else if (farmChoice.checked) {
						data2send = "&typeOfSubject=farm" + "&idSubject=" + $("select#allFarms").val();
					} else {
						alert('error');
						return false;
					}
					
					if (people.length <= 0) {
						$("#error").html('Please, enter the name of the person you want to invit');
					} else if (project.length <= 0) {
						$("#error").html('Please, enter the name of the project');
					} else if (email.length <= 0) {
						$("#error").html('Please, enter the email of the person you want to invit');
					} else if (dateEnd.length <= 0) {
						$("#error").html('Please, enter the date you want the invitation ends');
					} else {
						console.log(dateBegin);
						data2send += "&people="+people + "&project="+project + "&email="+email;
						if (dateBegin != '') {
							data2send += "&beginMonth="+dateBegin.substring(0,2) + "&beginDay="+dateBegin.substring(3,5) + "&beginYear="+dateBegin.substring(6,10);
						}
						data2send += "&endMonth="+dateEnd.substring(0,2) + "&endDay="+dateEnd.substring(3,5) + "&endYear="+dateEnd.substring(6,10);
						
						$.ajax({
  							url: "?ajax=true&action=addinvit",
 							type: 'post',
 							data: data2send,
 							success: function(data){ 
 								alert("Invitation sent !");	
								manageInvits();
							},
							dataType: 'json'
						});
					}
					return false;
				});
				 		
			});
		},
		dataType: 'json'
	});		
}

/**
 * Sends ajax request to delete an invitation
 * Calls the action delinvit
 * @function
 * @param {Number} id The id of the invitation to delete
 */
function deleteInvit (id) {
	$.ajax({
  		url: "?ajax=true&action=delinvit",
 		type: 'post',
 		data: "id=" + id,
 		success: function(data){ 		
			$('tr#'+id).remove();
		},
		dataType: 'json'
	});	
}