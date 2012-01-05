/**
 * Send an ajax request to get the status of an host.
 * When the response is received, the status image is changed, and the good button (on/off) is shown
 * Calls the action getStatus
 * @function
 * @param {Number} idHost The id of the host we want to get the status.
 * @param {Number} idElem A string corresponding to the situation of the host (individual, in a farm or a group).
 */
function getStatus(idHost, idElem) {
	$.ajax({
		url: "?ajax=true&action=getStatus",
		type: 'post',
		data: '&id=' + idHost,
		error: function(data){
			console.log('get status failed');
		},
  		success: function(data) {
			if (data['state'] == "0") {
				$('#'+idElem+idHost).html("<img src=img/online.png></img>");
				$('#wb'+idElem+idHost).hide(); //WoL Button
				$('#tob'+idElem+idHost).show(); //Wake On Lan Button
			} else if (data['state'] == "1") {
				$('#'+idElem+idHost).html("<img src=img/outline.png></img>"); 
				$('#wb'+idElem+idHost).show();
				$('#tob'+idElem+idHost).hide();
			} else {
				logout();
			}
		},
		dataType: 'json'
	});
}

/// For the periodic calls of getStatus

/**
 * Resets the global array where are the id of hosts we must get status every XX seconds.  
 * @function
 */
function resetHostTab () {
	window.toRefreshStatus = Array();
	window.toRefreshStatusSize = 0;	
}

/**
 * Adds a new host (with his id string) to the global array where are the id of hosts we must get status every XX seconds.  
 * @function
 * @param {Number} idHost The id of the host
 * @param {String} stringHost A string corresponding to the situation of the host (individual, in a farm or a group).
 */
function add2tab (idHost, stringHost) {
	window.toRefreshStatus[window.toRefreshStatusSize++] = Array(idHost, stringHost)
}

/**
 * Runs a periodic function to refresh the status of hosts in the global tab every minute. 
 * @function
*/

function runPeriodicGetStatus () {
	deletePeriodicGetStatus();
	window.intervalReference=setInterval("periodicGetStatus()", 60000);
}


/**
 * Deletes the periodic function to refresh the status of hosts.
 * @function
*/

function deletePeriodicGetStatus() {
	clearInterval(window.intervalReference);
}

/**
 * For each element in the global array of host, calls the function "getStatus"
 * @function
 */
function periodicGetStatus () {
	for (var i in window.toRefreshStatus) {
		getStatus(window.toRefreshStatus[i][0], window.toRefreshStatus[i][1]);
	}
}


/// Hosts management functions :

/**
 * Gets the data of all hosts, and generates a page to manage them.
 * Calls the actions managehosts and addhost
 * Uses the templates "hostsAdmin.html" and "addHostForm.html"
 * @function
 */
function manageHosts () {
	deletePeriodicGetStatus();
	$.ajax({
	  	url: "?ajax=true&action=managehosts",
	  	type: 'post',
	  	success: function(data){
	  		
	  		if (data == null) { 
 				logout(); 
 				return false; 
 			}
 			
			var saveData=data;
			
		  	var users = data['users'];  	
			var hosts = data['hosts'];		
			
			$("#content").html('<div id="generalWrapper">');

			$.get('templates/hostsAdmin.html', function(data) {	
				$("#generalWrapper").html( $.tmpl(data, saveData) );
  				
  				$( "#dialog-hostsAdmin" ).dialog({
					autoOpen: false,
					resizable: false,
					height:240,
					modal: true,
					buttons: {
						"OK": function() {
							deleteHost ($(".toDelete")[0].id);
							$("td").removeClass("ui-state-error");
							$(this).dialog("close");
						},
						Cancel: function() {
							$("td").removeClass("ui-state-error");
							$(this).dialog("close");
						}
					}
				});
				
				$(".delHost").button().click( function() {
					$("tr#" + this.id + " td").addClass("ui-state-error");
					$("tr#" + this.id).addClass("toDelete");
					$("#dialog-hostsAdmin").dialog("open");
					return false;
				});
				
  				$.get('templates/addHostForm.html', function(data) {
					$.tmpl( data , saveData ).appendTo( "#generalWrapper" );
  					$('button').button();

					$("#hostsDiscover").button().click( function () {
						hostsDiscover();  					
						$(this).remove();
					});
  					
  					$('.buttonAddHost').button().click(function() {  
 						var hname = $("input#hostname").val();
  						var mac = $("input#mac").val();
						var ip = $("input#ip").val();
 						var owner = $("select#user").val();
						var regexpmac = /^((([0-9a-f]{2})[:]){5})[0-9a-f]{2}$/i;
						var regexpIP = /^((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})$/i;
    	 	      
						if ( (hname.length < 3) || (hname.length > 20) ) {
							$("#formaddhost .ui-state-error").html('<p>Hostname must be between 3 and 20 chars </p>'); return false; 
						} else if (! regexpmac.test(mac)) {
							$("#formaddhost .ui-state-error").html("<p>Please, enter a valid mac address</p>"); return false; 
 						} else if (! regexpIP.test(ip)) {
 							$("#formaddhost .ui-state-error").html("<p>Please, enter a valid IP address</p>");  return false; 
    		     		} else {
  	  		     			$.ajax ({
   	      			 	url: "?ajax=true&action=addhost",
							 	type: 'post',
							 	data : "hostname=" + hname + "&mac=" + mac + "&ip=" + ip + "&owner=" + owner,
							 	success : function (data) {
									manageHosts();
							 	},    
						    	dataType: 'json'
     	   				});
							$(".ui-state-error").html('');
     	  	    			return false; 
     	 				}     
     				});  						
  				});	
			});						
   	},
  		dataType: 'json'
	});
}

/**
 * Send an ajax request to delete the host. 
 * When the response is received, the page is refreshed.
 * Calls the action delhost
 * @function
 * @param {Number} id The id of the host we want to delete
 */
function deleteHost(id) {
	$.ajax({
 		url: "?ajax=true&action=delhost",
 		type: 'post',
 		data : "&id=" + id,
 		success :function (data) { 
 			manageHosts();
 		},    
  		dataType: 'json'
	});
}

/**
 * Opens a form in a dialog window to change the host's mac address. Sends an ajax request to update the address if the new mac is valid.
 * When the response of ajax request is received, the page is refreshed.
 * Calls the action changemac
 * Uses the template "changeMacForm.html"
 * @function
 * @param {Number} idHost The id of the host
 * @param {String} hname The hostname
 */
function changeMac (idHost, hname) {
	$.get('templates/changeMacForm.html', function(data) {
		$("#formChangeMac").remove();
	  	$('#content').append( $.tmpl(data, {"hname" : hname, "oldval" : $("#macHostAdmin"+ idHost).text() }) );
		$("#" + idHost + " td").addClass("ui-state-error");

 		$("#changeMac").dialog({
	   	autoOpen: false,
			resizable: false,
			height:280,
			width:300,
			modal: true,
			title: false,
			buttons: {
				"Change": function() {
 	    			var mac = $("#macform").val();	
 	    			var regexpmac = /^((([0-9a-f]{2})[:]){5})[0-9a-f]{2}$/i;    	      	
  	   			if (! regexpmac.test(mac)) {
 	   		 	   $("#error").html("<p>Please, enter a valid mac address</p>"); 
 	  		  		   return false; 
 				  	} else {
  		 			   $.ajax ({
 	    			 		url: "?ajax=true&action=changemac",
					 		type: 'post',
					 		data : "idHost=" + idHost + "&newMac=" + mac ,
					 		success : function (data) { 
					 			manageHosts ();
					 		},    
		  		  		dataType: 'json'
 	   				});      
 		  			} 
					$(this).dialog("close");
					$("td").removeClass("ui-state-error");
   				return false; 
				},
				Cancel: function() {
					$(this).dialog("close");
					$("td").removeClass("ui-state-error");
				}
			}
		}).dialog( "open" );
	});
}

/**
 * Opens a form in a dialog window to change the host's IP address. Sends an ajax request to update the address if the new IP is valid.
 * When the response of ajax request is received, the page is refreshed.
 * Calls the action changeip
 * Uses the template "changeIpForm.html"
 * @function
 * @param {Number} idHost The id of the host
 * @param {String} hname The hostname
 */
function changeIP (idHost, hname) {
	$.get('templates/changeIpForm.html', function(data) {
		$("#changeIP").remove();
	  	$('#content').append( $.tmpl(data, {"hname" : hname, "oldval" : $("#ipHostAdmin"+ idHost).text()}) );	
		$("#" + idHost + " td").addClass("ui-state-error");
		
   	$("#changeIP").dialog({
	   	autoOpen: false,
			resizable: false,
			height:280,
			width:300,
			modal: true,
			title: false,
			buttons: {
				"Change": function() {
 		    		var ip = $("#ipform").val();
					var regexpIP = /^((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})$/i;
	      	
  			  		if (! regexpIP.test(ip)) {
   		  		   $("#error").html("<p>Please, enter a valid IP address</p>"); 
   		  		   return false; 
   				} else {
   				   $.ajax ({
    		 	 			url: "?ajax=true&action=changeip",
				 			type: 'post',
				 			data : "idHost=" + idHost + "&newIP=" + ip ,
				 			success : function (data) { 
				 				manageHosts();
				 			},    
	  			  			dataType: 'json'
    					});      
   				} 
					$(this).dialog("close");
					$("td").removeClass("ui-state-error");
   				return false; 
				},
				Cancel: function() {
					$(this).dialog("close");
					$("td").removeClass("ui-state-error");
				}
			}
		}).dialog( "open" );
   });	
}

/**
 * Opens a form in a dialog window to change the host's owner. Sends an ajax request to update the database.
 * When the response of ajax request is received, the page is refreshed.
 * Calls the actions getuserlist, changeowner
 * Uses the template "changeOwnerForm.html"
 * @function
 * @param {Number} idHost The id of the host
 * @param {String} hname The hostname
 */

function changeOwner (idHost, hname) {
  
	$.get('templates/changeOwnerForm.html', function(data) {
		$("#changeOwner").remove();
	  	$('#content').append( $.tmpl(data, {"hname" : hname}) );	
		$("#" + idHost + " td").addClass("ui-state-error");
		
		$.ajax ({
			url: "?ajax=true&action=getuserlist",
			type: 'post',
			success : function (data) { 
				var users=data;
				var usersToAppend = "";
				var oldVal = $("#ownerHostAdmin"+ idHost).text();
				for (var u in users) {
					usersToAppend += (oldVal == users[u].login) ? '' : ('<option value=' + users[u].id + '>' + users[u].login + '</option>');
				}
				$("#usersSelect").append(usersToAppend);
			},
	 	  dataType: 'json'
   	});  
		$("#changeOwner").dialog({
	   	autoOpen: false,
			resizable: false,
			height:280,
			width:300,
			modal: true,
			title: false,
			buttons: {
				"Change": function() {
      			var newOwner = $("select#usersSelect").val();	    	      	
     				$.ajax ({
     					url: "?ajax=true&action=changeowner",
						type: 'post',
			 			data : "idHost=" + idHost + "&idNewOwner=" + newOwner ,
			 			success : function (data) { 
			 				manageHosts();
			 			},    
	 	    			dataType: 'json'
    				});   
					$(this).dialog("close");
					$("td").removeClass("ui-state-error");
   				return false; 
				},
				Cancel: function() {
					$(this).dialog("close");
					$("td").removeClass("ui-state-error");
				}
			}
		}).dialog( "open" );
	});
}

/**
 * Gets the name and status of all the hosts and prints them.
 * Then, runs a function to refresh the hosts' status every minute.
 * Calls the action seeallhosts
 * Uses the template "hostsList.html"
 * @function
 */
function seeAllHosts () {
	deletePeriodicGetStatus();
	resetHostTab();
	$.ajax({
 		url: "?ajax=true&action=seeallhosts",
 		type: 'post',
 		success :function (data) {
 			
	  		if (data == null) {
 				logout(); 
 				return false; 
 			}
 			
			var saveData = data;
			var hosts = data['hosts'];

			$.get('templates/hostsList.html', function(data) {
				$('#content').html('<div id=userHosts>');	
  				$.tmpl( data , saveData ).appendTo( "#userHosts" );
  				$('button').button();
  				$('.wolButton').hide();
  				$('.turnOffButton').hide();
  				  				
  				for (var h in hosts) {
					/* Gets the Status of each host, and updates the list */
					getStatus(hosts[h].idH, "ho");
					add2tab(hosts[h].idH, "ho");					
				}  
			}); 
			
			runPeriodicGetStatus ();
   	},    
  		dataType: 'json'
	});	
}

/**
 * Asks the server to scan the local network and return the hosts' information. 
 * Prints the list of these hosts on a dialog window to add them more easily in the on/off application.
 * Calls the action hostsdiscover, addhost
 * Uses the templates "hostsDiscoverList.html", "addDiscoveredHostForm.html"
 * @function
 */
function hostsDiscover () {
	deletePeriodicGetStatus();
	$('#content').append('<div id=wait> <img src="img/discoverwait.gif"/>');	
	$.ajax({
 		url: "?ajax=true&action=hostsdiscover",
 		type: 'post',
 		success :function (data) {
	  		if (data == null) {
 				logout(); 
 				return false; 
 			}
			var saveData = data;
			console.log(data);
			$("#wait").remove();
			
			$.get('templates/hostsDiscoverList.html', function(data) {
				$("#hostsDiscover").remove();
  				$.tmpl(data, saveData).appendTo("#content");
  				$("#hostsDiscover").dialog({
		 		  	autoOpen: false,
					resizable: true,
					modal: true,
					title: false,
					minWidth: 600,
					minHeight: 400,
					buttons: {
						"Close": function() {
							$(this).dialog("close");
							$("#hostsDiscover").remove();
							$(".formadddiscovered").remove();
							manageHosts();
						}
					}
				}).dialog("open");
				$('.addDisco').button().click( function () {
					idOfThis = $(this)[0].id;
					$("hd#"+idOfThis).addClass("ui-state-error");

					$.get('templates/addDiscoveredHostForm.html', function(data) {
						saveData['mac'] = $(".mac#hd"+idOfThis).text();
						saveData['ip'] = $(".ip#hd"+idOfThis).text();
						saveData['name'] = $(".name#hd"+idOfThis).text();
						console.log(saveData);
						$("#formadddiscovered").remove();
						$.tmpl( data , saveData ).appendTo("#content");
  						$('button').button();
  						$('.buttonAddHost').button();
  						$("#formadddiscovered").dialog({
		 		  			autoOpen: false,
							resizable: true,
							modal: true,
							title: false,
							minWidth: 300,
							minHeight: 200,
							buttons: {
								"Add host": function () {
									var hostName = $("input#nameDiscover").val();
									console.log($("input#nameDiscover"));
  									var mac = $("input#macDiscover").val();
									var ip = $("input#ipDiscover").val();
 									var owner = $("select#userDiscover").val();
									var regexpmac = /^((([0-9a-f]{2})[:]){5})[0-9a-f]{2}$/i;
									var regexpIP = /^((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})$/i;
    	 	      				console.log($("input#nameDiscover").val()) ; console.log (hostName);
									if ( (hostName.length < 3) || (hostName.length > 40) ) {
										$("#formadddiscovered .ui-state-error").html('<p>Hostname must be between 3 and 40 chars </p>'); return false; 
									} else if (! regexpmac.test(mac)) {
										$("#formadddiscovered .ui-state-error").html("<p>Please, enter a valid mac address</p>"); return false; 
 									} else if (! regexpIP.test(ip)) {
 										$("#formadddiscovered .ui-state-error").html("<p>Please, enter a valid IP address</p>");  return false; 
    		     					} else {
  	  		     						$.ajax ({
   	      						 	url: "?ajax=true&action=addhost",
										 	type: 'post',
										 	data : "hostname=" + hostName + "&mac=" + mac + "&ip=" + ip + "&owner=" + owner,
										 	error: function (data) { 
										 		alert("error"); 
										 	},
										 	success : function (data) {
												alert("Added host !");
										 	},    
									    	dataType: 'json'
     	   							});
										$(".ui-state-error").html('');
										$(".formadddiscovered").remove();
										$(this).dialog("close");
     	  	    						return false; 
     	 							} 
								},
								Cancel: function() {				
									$(".formadddiscovered").remove();
									$(this).dialog("close");													
									return false;
								}
							}
						}).dialog("open");
					});
				});  						
			}); 
   	},    
  		dataType: 'json'
	});
}
