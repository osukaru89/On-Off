/**
 * Gets all the scheduled tasks and prints them in a table.
 * Generates a page to create a new scheduled task, reads and checks all the fields, and if it is correct, sends a request to the server.
 * Calls the actions getScheduledTasks, setrefreshpage, getHostsFarmsAndGroups and schedule
 * Uses the templates "scheduledTasksAdminManage.html" and "scheduleNewTasks.html"
 * @function
 */
function schedule() {
	deletePeriodicGetStatus();
	
	$.ajax({
  		url: "?ajax=true&action=setrefreshpage",
 		type: 'post',
  		data: 'page=schedule'
  	});

	$.ajax({
  		url: "?ajax=true&action=getScheduledTasks",
 		type: 'post',
 		success: function(data){ 					
  			var saveData = data;		
			$.get('templates/scheduledTasksAdminManage.html', function(data) {
				$("#scheduledTasks").remove();	
				$('#content').html( $.tmpl(data, saveData) );			
  						
				$( "#dialog-tasks" ).dialog({
					autoOpen: false,
					resizable: false,
					height:240,
					modal: true,
					buttons: {
						"OK": function() {
							deleteScheduledTask ($(".toDelete")[0].id);						
							$("td").removeClass("ui-state-error");
							$(this).dialog("close");
						},
						Cancel: function() {
							$("td").removeClass("ui-state-error");
							$(this).dialog("close");
						}
					}
				});
						
				$(".delSched").button().click( function() {
					$("tr#" + this.id + " td").addClass("ui-state-error");
					$("tr#" + this.id).addClass("toDelete");
					$("#dialog-tasks").dialog("open");
					return false;
				});  		
			});
		},
		dataType: 'json'
	});	
 			
	$.ajax({
  		url: "?ajax=true&action=getHostsFarmsAndGroups",
 		type: 'post',
 		success: function(data){ 				
  			var saveData = data;		
			$.get('templates/scheduleNewTasks.html', function(data) {
				$("#scheduleNewTasks").remove();			
				$.tmpl(data, saveData).appendTo('#content');
				
				var $dom = $("#dom"); // days of month
				var $dow = $("#dow"); // days of week
				var $hours = $("#hours");
				var $minutes = $("#minutes");	
				var $months = $("#months");
				var $ifHost = $("#ifHost");							
				var $ifFarm = $("#ifFarm");
				var $ifGroup = $("#ifGroup");	
									
				$("#onoff").buttonset();
				$("#dowallcustom").buttonset();
				$("#dowall").click( function (){
					$dow.hide();					
				});
				$("#dowcustom").click( function (){
					$dow.show();						
				});

				var domToAppend = "";
				for (i=1; i<=31;i++) {						
					domToAppend += '<input type="checkbox" name="'+i+'" id="dom'+i+'" /><label for="dom'+i+'">'+i+'</label>';
				}
				$dom.append(domToAppend);
				
				$("#domallcustom").buttonset();
				
				$("#domall").click( function (){
					$dom.hide();					
				});
				
				$("#domcustom").click( function (){
					$dom.show();						
				});
				

				$("#monthsallcustom").buttonset();
				
				$("#monthsall").click( function (){
					$months.hide();					
				});
				
				$("#monthscustom").click( function (){
					$months.show();						
				});

				var hoursToAppend = "";
				for (i=0; i<24;i++) {						
					hoursToAppend += '<option value="'+i+'">'+i+'</option>';
				}
				$hours.append(hoursToAppend);
	
				var minutesToAppend = "";
				for (i=0; i<60;i++) {						
					minutesToAppend += '<option value="'+i+'">'+i+'</option>';
				}
				$minutes.append(minutesToAppend);
						
				$("#hostFarmOrGroup").buttonset();
				$ifFarm.hide();
				$ifGroup.hide();
															
				$("#hostChoice").click( function (){
					$ifHost.show();								
					$ifFarm.hide();
					$ifGroup.hide();								
				});
				
				$("#farmChoice").click( function (){
					$ifFarm.show();								
					$ifHost.hide();
					$ifGroup.hide();								
				});
				
				$("#groupChoice").click( function (){
					$ifGroup.show();								
					$ifFarm.hide();
					$ifHost.hide();								
				});
				
				$dow.buttonset().hide();
				$dom.buttonset().hide();
				$months.buttonset().hide();									
						
				$("#scheduleButton").button().click( function (){
					var data2send = '';					
					wakeon = document.getElementById("wakeon");
					turnoff = document.getElementById("turnoff");
					
					if (wakeon.checked) {
						data2send = data2send + "idTask=1";
					} else if (turnoff.checked) {
						data2send = data2send + "idTask=2";
					} else {
						alert('error');
					}
					
					var thereIsSomethingChecked = false;
					dowcustom = document.getElementById("dowcustom");
					
					if (dowcustom.checked) {
						data2send = data2send + "&dow=";		
						for(i=1 ; i <= 7 ; i++) {
         				dow = document.getElementById("dow" + i);
          				if(dow.checked) { 
								data2send = data2send + dow.name + ',';
								thereIsSomethingChecked = true;
							}
						}
						data2send = data2send.substring(0, data2send.length - 1);
						if (thereIsSomethingChecked == false) {
							alert('You must select at least a day of week ! (or choose "All" option)');
							return false;									
						}
					}
						
					thereIsSomethingChecked = false;
					domcustom = document.getElementById("domcustom");
					if (domcustom.checked) {
						data2send = data2send + "&dom=";			
						for(i=1 ; i <= 31 ; i++) {
         				dom = document.getElementById("dom" + i);
         		 		if(dom.checked) { 
								data2send = data2send + dom.name + ',';
								thereIsSomethingChecked = true;
							}
						}
						data2send = data2send.substring(0, data2send.length - 1);
						if (thereIsSomethingChecked == false) {
							alert('You must select at least a day of month ! (or choose "All" option)');
							return false;											
						}
					}
								
					thereIsSomethingChecked = false;
					monthscustom = document.getElementById("monthscustom");
					if (monthscustom.checked) {
						data2send = data2send + "&month=";				
						for(i=1 ; i <= 12 ; i++) {
    			    		month = document.getElementById("month" + i);
    		      		if(month.checked) { 
								data2send = data2send + month.name + ',';
								thereIsSomethingChecked = true;
							}
						}
						data2send = data2send.substring(0, data2send.length - 1);
						if (thereIsSomethingChecked == false) {
							alert('You must select at least a month ! (or choose "All" option)');
							return false;							
						}
					}
								
					var hours = $("select#hours").val();						
					var minutes = $("select#minutes").val();							
					data2send = data2send + "&hours=" + hours + "&minutes=" + minutes;
							
					hostChoice = document.getElementById("hostChoice");
					farmChoice = document.getElementById("farmChoice");
					groupChoice = document.getElementById("groupChoice");
					if (hostChoice.checked) {
						data2send = data2send + "&typeOfSubject=host" + "&idSubject=" + $("select#allHosts").val();
					} else if (farmChoice.checked) {
						data2send = data2send + "&typeOfSubject=farm" + "&idSubject=" + $("select#allFarms").val();
					} else if (groupChoice.checked) {
						data2send = data2send + "&typeOfSubject=group" + "&idSubject=" + $("select#allGroups").val();
					} else {
						alert('error');
					}	
						
					$.ajax({
  						url: "?ajax=true&action=schedule",
 						type: 'post',
  						data: data2send,
 						error: function(data){
 							console.log('bad scheduling');
 						},
 						success: function(data){
 							alert("Successful Scheduling");
 							schedule();
 						}
 					});
				});
			}); /* end getTemplates */
		},
		dataType: 'json'
	});
}

/**
 * Delete a scheduled task
 * Calls the action deleteScheduledTask
 * @function
 * @param {Number} id The id of the task we want to delete
 */
function deleteScheduledTask (id) {
	$.ajax({
  		url: "?ajax=true&action=deleteScheduledTask",
 		type: 'post',
 		data: "idTask=" + id,
 		success: function(data){ 		
			$('tr#'+id).remove();
		},
		dataType: 'json'
	});	
}
